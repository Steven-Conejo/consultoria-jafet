<?php
/**
 * API de Contacto
 * Manejo seguro de formularios de contacto
 */

// Incluir headers CORS (maneja OPTIONS automáticamente)
require_once __DIR__ . '/cors_headers.php';

// Evitar output antes del JSON (deshabilitar errores visibles en producción)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Configuración
$logFile = __DIR__ . '/../logs/contact.log';
$logDir = dirname($logFile);

if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    // Validación básica de teléfono
    return preg_match('/^[\+]?[0-9\s\-\(\)]{10,}$/', $phone) || empty($phone);
}

// Funciones de seguridad para archivos
function validateFileSignature($filePath, $extension) {
    // Validar firma de archivo (magic bytes)
    if (!file_exists($filePath) || !is_readable($filePath)) {
        return false;
    }
    
    $handle = @fopen($filePath, 'rb');
    if (!$handle) {
        return false;
    }
    
    $signatures = [
        'pdf' => ['%PDF'],
        'doc' => ["\xD0\xCF\x11\xE0", "\x0D\x44\x4F\x43"], // MS Office formats (OLE2)
        'docx' => ["PK"] // ZIP-based format (DOCX es un ZIP) - solo verificar PK para ser más flexible
    ];
    
    if (!isset($signatures[$extension])) {
        fclose($handle);
        return false;
    }
    
    // Leer bytes suficientes para validar
    $bytesToRead = ($extension === 'docx') ? 2 : 4;
    $firstBytes = fread($handle, $bytesToRead);
    fclose($handle);
    
    if (strlen($firstBytes) < 2) {
        return false;
    }
    
    foreach ($signatures[$extension] as $signature) {
        if (strpos($firstBytes, $signature) === 0) {
            return true;
        }
    }
    
    return false;
}

function scanForMalware($filePath) {
    // Verificación básica de patrones sospechosos
    $content = file_get_contents($filePath);
    
    // Patrones comunes de malware en documentos
    $malwarePatterns = [
        '/\b(eval|exec|system|shell_exec|passthru)\s*\(/i',
        '/javascript:/i',
        '/<script/i',
        '/vbscript:/i',
        '/onload\s*=/i',
        '/onerror\s*=/i',
    ];
    
    foreach ($malwarePatterns as $pattern) {
        if (preg_match($pattern, $content)) {
            return false;
        }
    }
    
    return true;
}

function validatePDF($filePath) {
    // Validación adicional para PDFs
    $content = file_get_contents($filePath);
    
    // Verificar que es un PDF válido
    if (strpos($content, '%PDF') !== 0) {
        return false;
    }
    
    // Verificar que no contenga JavaScript peligroso
    if (preg_match('/\/JavaScript/i', $content) && preg_match('/\/JS/i', $content)) {
        // PDFs con JavaScript pueden ser peligrosos
        return false;
    }
    
    return true;
}

function validateDOCX($filePath) {
    // DOCX es un archivo ZIP, validar estructura
    if (!class_exists('ZipArchive')) {
        return false;
    }
    
    $zip = new ZipArchive();
    if ($zip->open($filePath) === true) {
        // Verificar estructura básica
        $expectedFiles = ['[Content_Types].xml', '_rels/.rels'];
        foreach ($expectedFiles as $expectedFile) {
            if ($zip->locateName($expectedFile) === false) {
                $zip->close();
                return false;
            }
        }
        
        // Verificar que no contenga macros peligrosos
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (preg_match('/\.(vbs|js|exe|bat|cmd)$/i', $filename)) {
                $zip->close();
                return false;
            }
        }
        
        $zip->close();
        return true;
    }
    
    return false;
}

// Rate limiting
function getClientIP() {
    $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ips = explode(',', $_SERVER[$key]);
            return trim($ips[0]);
        }
    }
    return '0.0.0.0';
}

function checkRateLimit($email, $logFile, $logDir) {
    $ip = getClientIP();
    $rateLimitConfig = [
        'max_attempts_email' => 5,      // Máximo 5 envíos por email
        'time_window_email' => 3600,    // En 1 hora
        'max_attempts_ip' => 10,        // Máximo 10 envíos por IP
        'time_window_ip' => 3600        // En 1 hora
    ];
    
    $ipLogFile = $logDir . '/rate_limit_ip.log';
    
    // Rate limiting por email
    $recentLogs = [];
    if (file_exists($logFile)) {
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $now = time();
        
        foreach (array_reverse($lines) as $line) {
            if (strpos($line, $email) !== false) {
                if (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $line, $matches)) {
                    $logTime = strtotime($matches[1]);
                    if (($now - $logTime) < $rateLimitConfig['time_window_email']) {
                        $recentLogs[] = $line;
                    }
                }
            }
        }
    }
    
    if (count($recentLogs) >= $rateLimitConfig['max_attempts_email']) {
        return false;
    }
    
    // Rate limiting por IP
    $ipAttempts = 0;
    if (file_exists($ipLogFile)) {
        $lines = file($ipLogFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $now = time();
        
        foreach (array_reverse($lines) as $line) {
            if (strpos($line, $ip) !== false) {
                if (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}).*?' . preg_quote($ip, '/') . '/', $line, $matches)) {
                    $logTime = strtotime($matches[1]);
                    if (($now - $logTime) < $rateLimitConfig['time_window_ip']) {
                        $ipAttempts++;
                    }
                }
            }
        }
    }
    
    if ($ipAttempts >= $rateLimitConfig['max_attempts_ip']) {
        return false;
    }
    
    // Registrar intento de IP
    file_put_contents($ipLogFile, date('Y-m-d H:i:s') . " | IP: $ip" . PHP_EOL, FILE_APPEND);
    
    return true;
}

// Configurar manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido. Solo se acepta POST.'
        ]);
        exit;
    }
    
    // Manejar FormData o JSON
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $isMultipart = strpos($contentType, 'multipart/form-data') !== false;
    
    if ($isMultipart) {
        // FormData (con posible archivo adjunto)
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $subject = sanitizeInput($_POST['subject'] ?? '');
        $message = sanitizeInput($_POST['message'] ?? '');
        $attachedFile = $_FILES['file'] ?? null;
    } else {
        // JSON (compatibilidad hacia atrás)
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Datos inválidos');
        }
        
        $name = sanitizeInput($input['name'] ?? '');
        $email = sanitizeInput($input['email'] ?? '');
        $phone = sanitizeInput($input['phone'] ?? '');
        $subject = sanitizeInput($input['subject'] ?? '');
        $message = sanitizeInput($input['message'] ?? '');
        $attachedFile = null;
    }
    
    // Validaciones
    if (empty($name) || strlen($name) < 2) {
        throw new Exception('El nombre es requerido y debe tener al menos 2 caracteres');
    }
    
    if (empty($email) || !validateEmail($email)) {
        throw new Exception('Email inválido');
    }
    
    if (!empty($phone) && !validatePhone($phone)) {
        throw new Exception('Teléfono inválido');
    }
    
    if (empty($subject)) {
        throw new Exception('El asunto es requerido');
    }
    
    if (empty($message) || strlen($message) < 10) {
        throw new Exception('El mensaje es requerido y debe tener al menos 10 caracteres');
    }
    
    // Rate limiting (prevenir spam automatizado)
    if (!checkRateLimit($email, $logFile, $logDir)) {
        throw new Exception('Has excedido el límite de mensajes. Por favor, intenta más tarde.');
    }
    
    // Prevenir spam básico
    $spamKeywords = ['http://', 'https://', 'www.', '[url]', 'viagra', 'casino'];
    $messageLower = strtolower($message);
    foreach ($spamKeywords as $keyword) {
        if (strpos($messageLower, $keyword) !== false) {
            throw new Exception('El mensaje contiene contenido no permitido');
        }
    }
    
    // Guardar en log
    $logEntry = date('Y-m-d H:i:s') . " | " . $email . " | " . $name . " | " . $subject . " | " . $message . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Enviar correo electrónico - intentar SMTP primero, luego mail() nativo
    require_once __DIR__ . '/smtp_mailer.php';
    require_once __DIR__ . '/mailer_nativo.php';
    
    // Cargar configuración
    $smtpConfig = getSMTPConfig();
    $useNativeMail = false;
    
    // Sistema mejorado de fallback: Intentar SMTP primero, luego mail() nativo si falla
    $useNativeMail = false;
    $smtpTestFailed = false;
    
    // Intento 1: Probar conexión SMTP
    try {
        $testSocket = @stream_socket_client(
            $smtpConfig['host'] . ':' . $smtpConfig['port'],
            $errno,
            $errstr,
            5
        );
        if (!$testSocket || in_array($errno, [101, 110, 111, 61, 60])) {
            // SMTP bloqueado o no disponible
            $smtpTestFailed = true;
            error_log("SMTP bloqueado por hosting (error $errno: $errstr), se usará mail() nativo como fallback");
            file_put_contents($logFile, date('Y-m-d H:i:s') . " | SMTP bloqueado (error $errno: $errstr), usando mail() nativo" . PHP_EOL, FILE_APPEND);
        } else {
            @fclose($testSocket);
            file_put_contents($logFile, date('Y-m-d H:i:s') . " | Conexión SMTP OK: " . $smtpConfig['host'] . ":" . $smtpConfig['port'] . PHP_EOL, FILE_APPEND);
        }
    } catch (Exception $e) {
        $smtpTestFailed = true;
        error_log("SMTP no disponible (excepción), usando mail() nativo: " . $e->getMessage());
        file_put_contents($logFile, date('Y-m-d H:i:s') . " | SMTP no disponible (excepción), usando mail() nativo: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
    
    // Si SMTP falló en la prueba, usar mail() nativo directamente
    if ($smtpTestFailed) {
        $useNativeMail = true;
    }
    
    try {
        
        // Construir asunto del correo
        $subjectMap = [
            'auditoria' => 'Consulta sobre Auditoría de Documentos',
            'arrendamiento' => 'Consulta sobre Revisión de Contratos de Arrendamiento',
            'anticredito' => 'Consulta sobre Revisión de Contratos de Anticredito',
            'redaccion' => 'Consulta sobre Redacción de Contratos',
            'general' => 'Consulta General',
            'otro' => 'Consulta - Otro'
        ];
        $emailSubject = $subjectMap[$subject] ?? 'Nueva Consulta desde LegisAudit';
        
        // Construir mensaje HTML del correo
        $htmlMessage = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f7fafc; padding: 30px; border-radius: 0 0 8px 8px; }
                .field { margin-bottom: 20px; }
                .label { font-weight: bold; color: #1a365d; display: block; margin-bottom: 5px; }
                .value { background: white; padding: 10px; border-radius: 4px; border-left: 3px solid #1a365d; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #718096; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Nuevo Mensaje de Contacto</h1>
                    <p>LegisAudit - Plataforma de Seguridad Legal</p>
                </div>
                <div class='content'>
                    <div class='field'>
                        <span class='label'>Nombre Completo:</span>
                        <div class='value'>" . htmlspecialchars($name) . "</div>
                    </div>
                    <div class='field'>
                        <span class='label'>Email:</span>
                        <div class='value'>" . htmlspecialchars($email) . "</div>
                    </div>
                    " . (!empty($phone) ? "
                    <div class='field'>
                        <span class='label'>Teléfono:</span>
                        <div class='value'>" . htmlspecialchars($phone) . "</div>
                    </div>
                    " : "") . "
                    <div class='field'>
                        <span class='label'>Asunto:</span>
                        <div class='value'>" . htmlspecialchars($emailSubject) . "</div>
                    </div>
                    <div class='field'>
                        <span class='label'>Mensaje:</span>
                        <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
                    </div>
                    " . ($attachmentPath && $attachmentName ? "
                    <div class='field'>
                        <span class='label'>Archivo Adjunto:</span>
                        <div class='value'><strong>" . htmlspecialchars($attachmentName) . "</strong></div>
                    </div>
                    " : "") . "
                    <div class='footer'>
                        <p>Este mensaje fue enviado desde el formulario de contacto de LegisAudit</p>
                        <p>Fecha: " . date('d/m/Y H:i:s') . "</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Procesar archivo adjunto si existe
        $attachmentPath = null;
        $attachmentName = null;
        $uploadsDir = __DIR__ . '/../uploads/';
        
        if ($attachedFile && $attachedFile['error'] === UPLOAD_ERR_OK) {
            // Validación 1: Tamaño del archivo
            $maxSize = 10 * 1024 * 1024; // 10MB
            if ($attachedFile['size'] > $maxSize) {
                throw new Exception('El archivo es demasiado grande. Tamaño máximo: 10MB');
            }
            
            if ($attachedFile['size'] === 0) {
                throw new Exception('El archivo está vacío');
            }
            
            // Validación 2: Tipo MIME y extensión
            $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $fileExtension = strtolower(pathinfo($attachedFile['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['pdf', 'doc', 'docx'];
            
            // Verificar extensión
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception('Extensión de archivo no permitida. Solo se aceptan PDF, DOC y DOCX');
            }
            
            // Verificar tipo MIME real (no confiar solo en el reportado)
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detectedType = finfo_file($finfo, $attachedFile['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($detectedType, $allowedTypes)) {
                    throw new Exception('Tipo de archivo no permitido. Solo se aceptan PDF, DOC y DOCX');
                }
            } elseif (!in_array($attachedFile['type'], $allowedTypes)) {
                // Si finfo no está disponible, usar el tipo reportado como fallback
                throw new Exception('Tipo de archivo no permitido. Solo se aceptan PDF, DOC y DOCX');
            }
            
            // Validación 3: Magic Bytes (firma de archivo)
            if (!validateFileSignature($attachedFile['tmp_name'], $fileExtension)) {
                throw new Exception('Firma de archivo no válida. El archivo puede estar corrupto o no ser del tipo esperado.');
            }
            
            // Validación 4: Escaneo de malware básico
            if (!scanForMalware($attachedFile['tmp_name'])) {
                throw new Exception('El archivo contiene patrones sospechosos y ha sido rechazado por seguridad.');
            }
            
            // Validación 5: Validación específica por tipo
            if ($fileExtension === 'pdf') {
                if (!validatePDF($attachedFile['tmp_name'])) {
                    throw new Exception('El PDF no pasó la validación de seguridad.');
                }
            } elseif ($fileExtension === 'docx') {
                if (!validateDOCX($attachedFile['tmp_name'])) {
                    throw new Exception('El documento Word no pasó la validación de seguridad.');
                }
            }
            
            // Mover archivo a carpeta temporal (solo después de todas las validaciones)
            if (!file_exists($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            
            $uniqueName = uniqid() . '_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $attachedFile['name']);
            $attachmentPath = $uploadsDir . $uniqueName;
            $attachmentName = $attachedFile['name'];
            
            if (!move_uploaded_file($attachedFile['tmp_name'], $attachmentPath)) {
                throw new Exception('Error al procesar el archivo adjunto');
            }
            
            // Establecer permisos seguros
            chmod($attachmentPath, 0644);
        }
        
        // Enviar correo a los destinatarios configurados
        // Lista de todos los destinatarios (tanto con SMTP como con mail() nativo)
        $recipients = [
            $smtpConfig['to_email'],  // servicioprofesionalabogadojcgy@gmail.com
            'steven@staygold.cr'      // Correo adicional
        ];
        
        // Nota: mail() nativo puede tener problemas enviando a múltiples destinatarios en un solo correo
        // Por eso enviamos correos separados a cada destinatario (tanto con SMTP como con mail() nativo)
        
        // Si hay archivo adjunto, crear copias para cada destinatario antes de enviar
        $attachmentCopies = [];
        if ($attachmentPath && file_exists($attachmentPath)) {
            foreach ($recipients as $recipient) {
                $copyPath = $uploadsDir . 'copy_' . uniqid() . '_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $attachmentName);
                if (copy($attachmentPath, $copyPath)) {
                    $attachmentCopies[$recipient] = $copyPath;
                }
            }
        }
        
        // Registrar inicio del proceso de envío
        file_put_contents($logFile, date('Y-m-d H:i:s') . " | Iniciando envío de correos. Destinatarios: " . implode(', ', $recipients) . PHP_EOL, FILE_APPEND);
        
        $sendErrors = [];
        $lastSuccessfulSend = false;
        
        foreach ($recipients as $recipient) {
            try {
                file_put_contents($logFile, date('Y-m-d H:i:s') . " | Intentando enviar a: $recipient" . PHP_EOL, FILE_APPEND);
                
                // Sistema de fallback robusto: Intentar SMTP primero, si falla usar mail() nativo
                $mailerInstance = null;
                $sendSuccess = false;
                
                // Intento 1: SMTP (si no se detectó fallo previo)
                if (!$useNativeMail) {
                    try {
                        $mailerInstance = new SMTPMailer();
                        $currentAttachmentPath = isset($attachmentCopies[$recipient]) ? $attachmentCopies[$recipient] : $attachmentPath;
                        $mailerInstance->send($recipient, $emailSubject, $htmlMessage, true, $email, $currentAttachmentPath, $attachmentName);
                        $sendSuccess = true;
                        file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✓ Correo enviado exitosamente vía SMTP a: $recipient" . PHP_EOL, FILE_APPEND);
                    } catch (Exception $smtpError) {
                        // SMTP falló, intentar con mail() nativo como fallback
                        $errorMsg = "SMTP falló para $recipient: " . $smtpError->getMessage();
                        file_put_contents($logFile, date('Y-m-d H:i:s') . " | ⚠ SMTP falló, intentando mail() nativo: $errorMsg" . PHP_EOL, FILE_APPEND);
                        error_log($errorMsg);
                        
                        // Fallback: Intentar con mail() nativo
                        try {
                            $mailerInstance = new NativeMailer();
                            $currentAttachmentPath = isset($attachmentCopies[$recipient]) ? $attachmentCopies[$recipient] : $attachmentPath;
                            $mailerInstance->send($recipient, $emailSubject, $htmlMessage, true, $email, $currentAttachmentPath, $attachmentName);
                            $sendSuccess = true;
                            file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✓ Correo enviado exitosamente vía mail() nativo (fallback) a: $recipient" . PHP_EOL, FILE_APPEND);
                        } catch (Exception $nativeError) {
                            // Ambos métodos fallaron
                            $finalError = "Error con mail() nativo (fallback): " . $nativeError->getMessage();
                            file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✗ ERROR: $finalError" . PHP_EOL, FILE_APPEND);
                            error_log($finalError);
                            $sendErrors[] = "Ambos métodos fallaron para $recipient: SMTP -> " . $smtpError->getMessage() . " | mail() -> " . $nativeError->getMessage();
                        }
                    }
                } else {
                    // Usar mail() nativo directamente (SMTP ya detectado como no disponible)
                    try {
                        $mailerInstance = new NativeMailer();
                        $currentAttachmentPath = isset($attachmentCopies[$recipient]) ? $attachmentCopies[$recipient] : $attachmentPath;
                        $mailerInstance->send($recipient, $emailSubject, $htmlMessage, true, $email, $currentAttachmentPath, $attachmentName);
                        $sendSuccess = true;
                        file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✓ Correo enviado exitosamente vía mail() nativo a: $recipient" . PHP_EOL, FILE_APPEND);
                    } catch (Exception $nativeError) {
                        $errorMsg = "Error al enviar a $recipient con mail() nativo: " . $nativeError->getMessage();
                        file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✗ ERROR: $errorMsg" . PHP_EOL, FILE_APPEND);
                        error_log($errorMsg);
                        $sendErrors[] = $errorMsg;
                    }
                }
                
                if ($sendSuccess) {
                    $lastSuccessfulSend = true;
                }
            } catch (Exception $sendError) {
                // Error general no capturado arriba
                $errorMsg = "Error inesperado al enviar a $recipient: " . $sendError->getMessage();
                file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✗ ERROR INESPERADO: $errorMsg" . PHP_EOL, FILE_APPEND);
                error_log($errorMsg);
                $sendErrors[] = $errorMsg;
            } catch (Error $sendError) {
                // Capturar errores fatales de PHP también
                $errorMsg = "Error fatal al enviar a $recipient: " . $sendError->getMessage();
                file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✗ ERROR FATAL: $errorMsg" . PHP_EOL, FILE_APPEND);
                error_log($errorMsg);
                $sendErrors[] = $errorMsg;
            }
        }
        
        // Eliminar todas las copias del archivo adjunto después de todos los envíos
        foreach ($attachmentCopies as $copyPath) {
            if (file_exists($copyPath)) {
                @unlink($copyPath);
            }
        }
        // Eliminar el archivo original si existe
        if ($attachmentPath && file_exists($attachmentPath)) {
            @unlink($attachmentPath);
        }
        
        // Si todos los envíos fallaron, intentar mail() nativo una vez más como último recurso
        if (!$lastSuccessfulSend && !empty($sendErrors)) {
            // Último intento: usar mail() nativo directamente con configuración simple
            file_put_contents($logFile, date('Y-m-d H:i:s') . " | ⚠ Todos los métodos fallaron, intento final con mail() nativo simple" . PHP_EOL, FILE_APPEND);
            
            try {
                $finalRecipient = $smtpConfig['to_email'];
                $simpleSubject = $emailSubject;
                $simpleMessage = $htmlMessage;
                
                // Intentar con mail() directamente (sin clase wrapper)
                $simpleHeaders = "From: {$smtpConfig['from_name']} <{$smtpConfig['from_email']}>\r\n";
                $simpleHeaders .= "Reply-To: " . ($email ?: $smtpConfig['from_email']) . "\r\n";
                $simpleHeaders .= "MIME-Version: 1.0\r\n";
                $simpleHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";
                
                $simpleResult = @mail($finalRecipient, mb_encode_mimeheader($simpleSubject, 'UTF-8'), $simpleMessage, $simpleHeaders, "-f{$smtpConfig['from_email']}");
                
                if ($simpleResult) {
                    $lastSuccessfulSend = true;
                    file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✓✓ ÉXITO con mail() nativo simple a: $finalRecipient" . PHP_EOL, FILE_APPEND);
                    error_log("Correo enviado exitosamente con mail() nativo simple a: $finalRecipient");
                } else {
                    $finalError = 'Error al enviar correos a todos los destinatarios. Errores: ' . implode('; ', $sendErrors);
                    file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✗✗ FALLO TOTAL (incluyendo intento final): $finalError" . PHP_EOL, FILE_APPEND);
                    throw new Exception($finalError);
                }
            } catch (Exception $finalError) {
                $finalErrorMsg = 'Error al enviar correos. Errores: ' . implode('; ', $sendErrors) . ' | Intento final: ' . $finalError->getMessage();
                file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✗✗ FALLO TOTAL: $finalErrorMsg" . PHP_EOL, FILE_APPEND);
                throw new Exception('Error al enviar el correo. Por favor, intenta nuevamente más tarde o contacta directamente.');
            }
        } elseif (!$lastSuccessfulSend) {
            $finalError = 'Error al enviar correos. No se pudo enviar a ningún destinatario.';
            file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✗✗ FALLO TOTAL: $finalError" . PHP_EOL, FILE_APPEND);
            throw new Exception($finalError);
        }
        
        file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✓✓ Proceso de envío completado exitosamente" . PHP_EOL, FILE_APPEND);
        
        // También enviar confirmación al usuario (opcional)
        try {
            $confirmMessage = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                    .content { background: #f7fafc; padding: 30px; border-radius: 0 0 8px 8px; }
                    .message { background: white; padding: 20px; border-radius: 4px; border-left: 4px solid #48bb78; margin: 20px 0; }
                    .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #718096; text-align: center; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Mensaje Recibido</h1>
                    </div>
                    <div class='content'>
                        <div class='message'>
                            <p><strong>Hola " . htmlspecialchars($name) . ",</strong></p>
                            <p>Hemos recibido tu mensaje correctamente. Nuestro equipo revisará tu consulta y te contactará a la brevedad posible.</p>
                            <p>Detalles de tu mensaje:</p>
                            <ul>
                                <li><strong>Asunto:</strong> " . htmlspecialchars($emailSubject) . "</li>
                                <li><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</li>
                            </ul>
                            <p>Gracias por contactarnos.</p>
                            <p>Atentamente,<br><strong>Equipo LegisAudit</strong></p>
                        </div>
                        <div class='footer'>
                            <p>Si tienes alguna pregunta, puedes responder a este correo o contactarnos directamente.</p>
                            <p>Este es un correo automático de confirmación de LegisAudit.</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            // Enviar confirmación al usuario con los MISMOS headers que el correo principal (para evitar spam)
            // Usar siempre NativeMailer que tiene headers optimizados idénticos a los correos principales
            try {
                // Siempre usar NativeMailer para confirmación (tiene headers optimizados idénticos a los correos principales)
                // No usar el email del cliente como Reply-To, usar el mismo que los correos principales
                $confirmMailer = new NativeMailer();
                // Usar from_email como reply-to (igual que los correos principales) en lugar del email del cliente
                $confirmMailer->send($email, 'Confirmación de Recepción - LegisAudit', $confirmMessage, true, $smtpConfig['from_email']);
                file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✓ Confirmación enviada con headers optimizados a cliente: $email" . PHP_EOL, FILE_APPEND);
            } catch (Exception $confirmError) {
                // Si NativeMailer falla, intentar con mail() directo usando EXACTAMENTE los mismos headers optimizados
                try {
                    // Headers IDÉNTICOS a los que usa NativeMailer (para evitar spam)
                    $confirmHeaders = [];
                    $confirmHeaders[] = "MIME-Version: 1.0";
                    $confirmHeaders[] = "From: {$smtpConfig['from_name']} <{$smtpConfig['from_email']}>";
                    $confirmHeaders[] = "Reply-To: {$smtpConfig['from_email']}"; // Mismo que correos principales
                    $confirmHeaders[] = "Return-Path: <{$smtpConfig['from_email']}>";
                    $confirmHeaders[] = "Message-ID: <" . md5(uniqid(time())) . "@legisaudit-abogados.cu.ma>";
                    $confirmHeaders[] = "Date: " . date('r');
                    $confirmHeaders[] = "X-Mailer: LegisAudit/1.0";
                    $confirmHeaders[] = "X-Auto-Response-Suppress: All";
                    $confirmHeaders[] = "Importance: Normal";
                    $confirmHeaders[] = "Content-Type: text/html; charset=UTF-8";
                    
                    $confirmSubject = 'Confirmación de Recepción - LegisAudit';
                    $encodedConfirmSubject = function_exists('mb_encode_mimeheader') 
                        ? mb_encode_mimeheader($confirmSubject, 'UTF-8', 'B', "\r\n")
                        : '=?UTF-8?B?' . base64_encode($confirmSubject) . '?=';
                    
                    $confirmHeadersString = implode("\r\n", $confirmHeaders);
                    $confirmResult = @mail($email, $encodedConfirmSubject, $confirmMessage, $confirmHeadersString, "-f{$smtpConfig['from_email']}");
                    
                    if ($confirmResult) {
                        file_put_contents($logFile, date('Y-m-d H:i:s') . " | ✓ Confirmación enviada con mail() directo (headers optimizados) a cliente: $email" . PHP_EOL, FILE_APPEND);
                    } else {
                        $errorMsg = "Error al enviar confirmación a usuario: " . $confirmError->getMessage();
                        error_log($errorMsg);
                        file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERROR CONFIRMACIÓN: $errorMsg" . PHP_EOL, FILE_APPEND);
                    }
                } catch (Exception $finalConfirmError) {
                    // Si todo falla, solo registrar (no es crítico)
                    $errorMsg = "Error al enviar confirmación a usuario: " . $finalConfirmError->getMessage();
                    error_log($errorMsg);
                    file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERROR CONFIRMACIÓN (todos los métodos fallaron): $errorMsg" . PHP_EOL, FILE_APPEND);
                }
            }
        } catch (Exception $confirmError) {
            // Si falla la confirmación, solo registrar (no es crítico)
            $errorMsg = "Error al enviar confirmación a usuario: " . $confirmError->getMessage();
            error_log($errorMsg);
            file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERROR CONFIRMACIÓN: $errorMsg" . PHP_EOL, FILE_APPEND);
        }
        
    } catch (Exception $emailError) {
        // Si falla el envío de correo principal, lanzar excepción para que el usuario lo sepa
        $errorMsg = "Error al enviar correo: " . $emailError->getMessage();
        error_log($errorMsg);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERROR GENERAL: $errorMsg" . PHP_EOL, FILE_APPEND);
        
        // Lanzar excepción para que el usuario sepa que hubo un problema
        throw new Exception('Error al enviar el correo. Por favor, intenta nuevamente más tarde o contacta directamente por email.');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Mensaje recibido correctamente. Te contactaremos pronto.'
    ]);
    
} catch (Exception $e) {
    // Log del error para debugging
    $errorMsg = $e->getMessage();
    error_log("Error en contact.php: " . $errorMsg);
    
    // Registrar en el log de contacto
    if (isset($logFile) && isset($logDir)) {
        @file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERROR: $errorMsg" . PHP_EOL, FILE_APPEND);
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $errorMsg
    ]);
} catch (Error $e) {
    // Capturar errores fatales de PHP
    $errorMsg = 'Error interno del servidor: ' . $e->getMessage();
    error_log("Error fatal en contact.php: " . $errorMsg);
    
    if (isset($logFile) && isset($logDir)) {
        @file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERROR FATAL: $errorMsg" . PHP_EOL, FILE_APPEND);
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor. Por favor, intenta más tarde.'
    ]);
}

