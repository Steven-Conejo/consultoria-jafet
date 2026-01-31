<?php
/**
 * Mailer Nativo usando mail() de PHP
 * Fallback cuando SMTP está bloqueado por el hosting
 */

class NativeMailer {
    private $fromEmail;
    private $fromName;
    
    public function __construct() {
        $config = require __DIR__ . '/smtp_config.php';
        $this->fromEmail = $config['from_email'];
        $this->fromName = $config['from_name'];
    }
    
    /**
     * Envía un correo usando mail() nativo de PHP
     */
    public function send($to, $subject, $message, $html = true, $replyTo = null, $attachmentPath = null, $attachmentName = null) {
        // Verificar que mail() esté disponible
        if (!function_exists('mail')) {
            throw new Exception('La función mail() no está disponible en este servidor.');
        }
        
        // Validar email de destino
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email de destino inválido: $to");
        }
        
        // Preparar reply-to
        $replyToEmail = $replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL) 
            ? $replyTo 
            : $this->fromEmail;
        
        // Headers optimizados para evitar spam
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "From: {$this->fromName} <{$this->fromEmail}>";
        $headers[] = "Reply-To: {$replyToEmail}";
        $headers[] = "Return-Path: <{$this->fromEmail}>";
        $headers[] = "Message-ID: <" . md5(uniqid(time())) . "@legisaudit-abogados.cu.ma>";
        $headers[] = "Date: " . date('r');
        $headers[] = "X-Mailer: LegisAudit/1.0";
        $headers[] = "X-Auto-Response-Suppress: All"; // Suprimir respuestas automáticas
        $headers[] = "Importance: Normal";
        
        // Procesar adjunto si existe
        if ($attachmentPath && file_exists($attachmentPath) && is_readable($attachmentPath)) {
            // Con adjunto: usar multipart/mixed
            $boundary = '----=_NextPart_' . md5(uniqid(time()));
            $headers[] = "Content-Type: multipart/mixed; boundary=\"$boundary\"";
            
            $body = "--$boundary\r\n";
            $body .= "Content-Type: " . ($html ? "text/html" : "text/plain") . "; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $body .= $message . "\r\n\r\n";
            
            // Adjuntar archivo
            $attachmentData = @file_get_contents($attachmentPath);
            if ($attachmentData === false) {
                throw new Exception("No se pudo leer el archivo adjunto: $attachmentPath");
            }
            
            $attachmentBase64 = chunk_split(base64_encode($attachmentData));
            $fileMimeType = mime_content_type($attachmentPath) ?: 'application/octet-stream';
            $safeFileName = $attachmentName ?: basename($attachmentPath);
            
            $body .= "--$boundary\r\n";
            $body .= "Content-Type: $fileMimeType; name=\"" . addslashes($safeFileName) . "\"\r\n";
            $body .= "Content-Disposition: attachment; filename=\"" . addslashes($safeFileName) . "\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $body .= $attachmentBase64 . "\r\n";
            $body .= "--$boundary--\r\n";
        } else {
            // Sin adjunto: mensaje simple
            if ($html) {
                $headers[] = "Content-Type: text/html; charset=UTF-8";
            } else {
                $headers[] = "Content-Type: text/plain; charset=UTF-8";
            }
            $body = $message;
        }
        
        // Codificar subject para caracteres especiales
        $encodedSubject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n");
        
        // Headers como string
        $headersString = implode("\r\n", $headers);
        
        // Parámetros adicionales: -f para establecer envelope sender
        $additionalParams = "-f{$this->fromEmail}";
        
        // Log antes de enviar (para debugging)
        error_log("Intentando enviar correo con mail() nativo a: $to | Subject: $subject | From: {$this->fromEmail}");
        
        // Intentar enviar
        $result = @mail($to, $encodedSubject, $body, $headersString, $additionalParams);
        
        // Verificar resultado
        if (!$result) {
            $lastError = error_get_last();
            $errorMsg = $lastError ? $lastError['message'] : 'Error desconocido';
            throw new Exception("Error al enviar correo usando mail() nativo: $errorMsg");
        }
        
        // Log de éxito
        error_log("Correo enviado exitosamente con mail() nativo a: $to");
        
        return true;
    }
}
