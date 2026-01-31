<?php
/**
 * Clase SMTP Mailer
 * Envío de correos usando SMTP con autenticación
 * Usa sockets TCP para comunicación directa con servidor SMTP
 */

require_once __DIR__ . '/smtp_config.php';

class SMTPMailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $fromEmail;
    private $fromName;
    private $encryption;
    private $timeout;
    private $socket = null;
    
    public function __construct() {
        $config = getSMTPConfig();
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->fromEmail = $config['from_email'];
        $this->fromName = $config['from_name'];
        $this->encryption = $config['encryption'];
        $this->timeout = $config['timeout'];
    }
    
    /**
     * Envía un correo electrónico usando SMTP
     * @param string $to Email del destinatario
     * @param string $subject Asunto del correo
     * @param string $message Mensaje del correo
     * @param bool $html Si el mensaje es HTML
     * @param string|null $replyTo Email de respuesta
     * @param string|null $attachmentPath Ruta al archivo adjunto
     * @param string|null $attachmentName Nombre del archivo adjunto
     */
    public function send($to, $subject, $message, $html = true, $replyTo = null, $attachmentPath = null, $attachmentName = null) {
        try {
            // Conectar al servidor SMTP
            $this->connect();
            
            // Iniciar sesión
            $this->login();
            
            // Enviar correo
            $this->sendMail($to, $subject, $message, $html, $replyTo, $attachmentPath, $attachmentName);
            
            // Cerrar conexión
            $this->disconnect();
            
            // Eliminar archivo temporal después de enviar
            if ($attachmentPath && file_exists($attachmentPath)) {
                @unlink($attachmentPath);
            }
            
            return true;
        } catch (Exception $e) {
            if ($this->socket) {
                $this->disconnect();
            }
            // Intentar eliminar archivo temporal en caso de error
            if ($attachmentPath && file_exists($attachmentPath)) {
                @unlink($attachmentPath);
            }
            throw new Exception('Error al enviar correo: ' . $e->getMessage());
        }
    }
    
    /**
     * Conecta al servidor SMTP
     */
    private function connect() {
        if ($this->encryption === 'ssl') {
            // Para SSL (puerto 465), usar contexto SSL desde el inicio
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            
            $this->socket = @stream_socket_client(
                'ssl://' . $this->host . ':' . $this->port,
                $errno,
                $errstr,
                $this->timeout,
                STREAM_CLIENT_CONNECT,
                $context
            );
        } elseif ($this->encryption === 'none' || $this->port == 25) {
            // Para SMTP sin encriptación (puerto 25, común en hosting interno)
            $this->socket = @stream_socket_client(
                $this->host . ':' . $this->port,
                $errno,
                $errstr,
                $this->timeout,
                STREAM_CLIENT_CONNECT
            );
        } else {
            // Para TLS (puerto 587), conectar sin cifrado primero
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'allow_self_signed' => false
                ]
            ]);
            
            $this->socket = @stream_socket_client(
                $this->host . ':' . $this->port,
                $errno,
                $errstr,
                $this->timeout,
                STREAM_CLIENT_CONNECT,
                $context
            );
        }
        
        if (!$this->socket) {
            $detailedError = "No se pudo conectar al servidor SMTP: $errstr ($errno)";
            $detailedError .= " | Host: {$this->host}, Puerto: {$this->port}, Encriptación: {$this->encryption}";
            
            // Agregar información sobre posibles causas
            if ($errno === 110 || $errno === 111) {
                $detailedError .= " | Posible causa: Firewall bloqueando conexión o puerto cerrado";
            } elseif ($errno === 0 && empty($errstr)) {
                $detailedError .= " | Posible causa: Extensión OpenSSL no disponible o restricciones de red del hosting";
            }
            
            throw new Exception($detailedError);
        }
        
        // Configurar timeout
        stream_set_timeout($this->socket, $this->timeout);
        
        // Leer respuesta inicial
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '220') {
            throw new Exception("Error al conectar: $response");
        }
        
        // Enviar EHLO
        $this->writeCommand('EHLO ' . $this->host);
        $response = $this->readResponse();
        
        // Iniciar TLS si es necesario (puerto 587)
        if ($this->encryption === 'tls') {
            $this->writeCommand('STARTTLS');
            $response = $this->readResponse();
            if (substr($response, 0, 3) !== '220') {
                throw new Exception("Error al iniciar TLS: $response");
            }
            
            // Habilitar cifrado TLS - intentar múltiples métodos
            $cryptoMethods = [
                STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT,
                STREAM_CRYPTO_METHOD_TLS_CLIENT
            ];
            
            $tlsSuccess = false;
            foreach ($cryptoMethods as $method) {
                if (@stream_socket_enable_crypto($this->socket, true, $method)) {
                    $tlsSuccess = true;
                    break;
                }
            }
            
            if (!$tlsSuccess) {
                throw new Exception("Error al establecer TLS. Posibles causas: hosting bloquea TLS o OpenSSL mal configurado");
            }
            
            // Enviar EHLO nuevamente después de TLS
            $this->writeCommand('EHLO ' . $this->host);
            $this->readResponse();
        }
    }
    
    /**
     * Autentica en el servidor SMTP
     */
    private function login() {
        // Algunos servidores SMTP internos no requieren autenticación
        // Si el puerto es 25 y encryption es 'none', intentar sin autenticación primero
        if ($this->encryption === 'none' || $this->port == 25) {
            // Verificar si el servidor requiere autenticación
            $this->writeCommand('AUTH LOGIN');
            $response = $this->readResponse();
            
            // Si el servidor no soporta AUTH (código 502 o 500), continuar sin autenticación
            if (substr($response, 0, 3) === '502' || substr($response, 0, 3) === '500') {
                // Servidor no requiere autenticación, continuar
                return;
            }
            
            // Si responde 334, requiere autenticación
            if (substr($response, 0, 3) !== '334') {
                throw new Exception("Error al iniciar autenticación: $response");
            }
        } else {
            // Para TLS/SSL, siempre requiere autenticación
            $this->writeCommand('AUTH LOGIN');
            $response = $this->readResponse();
            if (substr($response, 0, 3) !== '334') {
                throw new Exception("Error al iniciar autenticación: $response");
            }
        }
        
        // Enviar usuario (base64)
        $this->writeCommand(base64_encode($this->username));
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '334') {
            throw new Exception("Error en autenticación de usuario: $response");
        }
        
        // Enviar contraseña (base64)
        $this->writeCommand(base64_encode($this->password));
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '235') {
            throw new Exception("Error en autenticación: $response");
        }
    }
    
    /**
     * Envía el correo
     */
    private function sendMail($to, $subject, $message, $html, $replyTo, $attachmentPath = null, $attachmentName = null) {
        // MAIL FROM
        $this->writeCommand('MAIL FROM: <' . $this->fromEmail . '>');
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '250') {
            throw new Exception("Error en MAIL FROM: $response");
        }
        
        // RCPT TO
        $this->writeCommand('RCPT TO: <' . $to . '>');
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '250') {
            throw new Exception("Error en RCPT TO: $response");
        }
        
        // DATA
        $this->writeCommand('DATA');
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '354') {
            throw new Exception("Error en DATA: $response");
        }
        
        // Generar boundary único para multipart
        $boundary = '----=_NextPart_' . md5(uniqid(time()));
        
        // Construir encabezados del correo
        $headers = "From: " . $this->encodeHeader($this->fromName) . " <" . $this->fromEmail . ">\r\n";
        $headers .= "To: <" . $to . ">\r\n";
        $headers .= "Subject: " . $this->encodeHeader($subject) . "\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        if ($replyTo) {
            $headers .= "Reply-To: <" . $replyTo . ">\r\n";
        }
        
        // Si hay adjunto, usar multipart/mixed
        if ($attachmentPath && file_exists($attachmentPath)) {
            $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            $headers .= "\r\n";
            
            // Parte 1: Mensaje
            $headers .= "--$boundary\r\n";
            if ($html) {
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            } else {
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            }
            $headers .= "Content-Transfer-Encoding: 7bit\r\n";
            $headers .= "\r\n";
            $headers .= $message . "\r\n\r\n";
            
            // Parte 2: Archivo adjunto
            $headers .= "--$boundary\r\n";
            
            // Determinar tipo MIME del archivo
            $fileMimeType = mime_content_type($attachmentPath);
            if (!$fileMimeType) {
                $ext = strtolower(pathinfo($attachmentPath, PATHINFO_EXTENSION));
                $mimeTypes = [
                    'pdf' => 'application/pdf',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];
                $fileMimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
            }
            
            $attachmentData = file_get_contents($attachmentPath);
            $attachmentBase64 = chunk_split(base64_encode($attachmentData));
            $attachmentFileName = $attachmentName ?: basename($attachmentPath);
            
            $headers .= "Content-Type: $fileMimeType; name=\"" . $this->encodeHeader($attachmentFileName) . "\"\r\n";
            $headers .= "Content-Disposition: attachment; filename=\"" . $this->encodeHeader($attachmentFileName) . "\"\r\n";
            $headers .= "Content-Transfer-Encoding: base64\r\n";
            $headers .= "\r\n";
            $headers .= $attachmentBase64 . "\r\n";
            $headers .= "--$boundary--\r\n";
        } else {
            // Sin adjunto, correo simple
            if ($html) {
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            } else {
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            }
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            $headers .= "\r\n";
            $headers .= $message . "\r\n";
        }
        
        // Cuerpo del mensaje con terminador
        $emailData = $headers . "\r\n.\r\n";
        
        // Enviar correo
        fwrite($this->socket, $emailData);
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '250') {
            throw new Exception("Error al enviar correo: $response");
        }
    }
    
    /**
     * Codifica encabezados con caracteres especiales
     */
    private function encodeHeader($str) {
        if (preg_match('/[^\x20-\x7E]/', $str)) {
            return '=?UTF-8?B?' . base64_encode($str) . '?=';
        }
        return $str;
    }
    
    /**
     * Escribe un comando al servidor
     */
    private function writeCommand($command) {
        if (!fwrite($this->socket, $command . "\r\n")) {
            throw new Exception("Error al escribir comando: $command");
        }
    }
    
    /**
     * Lee la respuesta del servidor
     */
    private function readResponse() {
        $response = '';
        while ($line = fgets($this->socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return trim($response);
    }
    
    /**
     * Desconecta del servidor SMTP
     */
    private function disconnect() {
        if ($this->socket) {
            $this->writeCommand('QUIT');
            $this->readResponse();
            fclose($this->socket);
            $this->socket = null;
        }
    }
}

/**
 * Función helper para obtener configuración SMTP
 */
function getSMTPConfig() {
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/smtp_config.php';
    }
    return $config;
}

