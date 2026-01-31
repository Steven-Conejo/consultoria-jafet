<?php
/**
 * API de Subida Segura de Archivos
 * Sistema de seguridad extrema para validación de documentos Word y PDF
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración de seguridad
$allowedTypes = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];

$allowedExtensions = ['pdf', 'doc', 'docx'];
$maxFileSize = 10 * 1024 * 1024; // 10MB
$uploadDir = __DIR__ . '/../uploads/';
$quarantineDir = __DIR__ . '/../quarantine/';

// Crear directorios si no existen
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
if (!file_exists($quarantineDir)) {
    mkdir($quarantineDir, 0755, true);
}

function sanitizeFilename($filename) {
    // Eliminar caracteres peligrosos
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    // Limitar longitud
    $filename = substr($filename, 0, 255);
    return $filename;
}

function validateFileSignature($filePath, $extension) {
    // Validar firma de archivo (magic bytes)
    $handle = fopen($filePath, 'rb');
    if (!$handle) {
        return false;
    }
    
    $signatures = [
        'pdf' => ['%PDF'],
        'doc' => ['\xD0\xCF\x11\xE0', '\x0D\x44\x4F\x43'], // MS Office formats
        'docx' => ['PK\x03\x04'] // ZIP-based format (DOCX is a ZIP)
    ];
    
    if (!isset($signatures[$extension])) {
        fclose($handle);
        return false;
    }
    
    $firstBytes = fread($handle, 4);
    fclose($handle);
    
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

try {
    // Verificar método
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Verificar que se haya subido un archivo
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error al subir el archivo');
    }
    
    $file = $_FILES['file'];
    $originalName = $file['name'];
    $tmpPath = $file['tmp_name'];
    $fileSize = $file['size'];
    $mimeType = $file['type'];
    
    // Validación 1: Tamaño del archivo
    if ($fileSize > $maxFileSize) {
        throw new Exception('El archivo excede el tamaño máximo permitido (10MB)');
    }
    
    if ($fileSize === 0) {
        throw new Exception('El archivo está vacío');
    }
    
    // Validación 2: Tipo MIME
    if (!in_array($mimeType, $allowedTypes)) {
        // Intentar detectar tipo real
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);
        
        if (!in_array($detectedType, $allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido');
        }
        $mimeType = $detectedType;
    }
    
    // Validación 3: Extensión
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        throw new Exception('Extensión de archivo no permitida');
    }
    
    // Validación 4: Nombre del archivo
    $sanitizedName = sanitizeFilename($originalName);
    if (empty($sanitizedName)) {
        throw new Exception('Nombre de archivo inválido');
    }
    
    // Validación 5: Firma de archivo (Magic Bytes)
    if (!validateFileSignature($tmpPath, $extension)) {
        // Mover a cuarentena
        $quarantineFile = $quarantineDir . uniqid('quarantine_') . '_' . $sanitizedName;
        move_uploaded_file($tmpPath, $quarantineFile);
        throw new Exception('Firma de archivo no válida. El archivo puede estar corrupto o no ser del tipo esperado.');
    }
    
    // Validación 6: Escaneo de malware básico
    if (!scanForMalware($tmpPath)) {
        $quarantineFile = $quarantineDir . uniqid('quarantine_') . '_' . $sanitizedName;
        move_uploaded_file($tmpPath, $quarantineFile);
        throw new Exception('El archivo contiene patrones sospechosos y ha sido rechazado por seguridad.');
    }
    
    // Validación 7: Validación específica por tipo
    if ($extension === 'pdf') {
        if (!validatePDF($tmpPath)) {
            $quarantineFile = $quarantineDir . uniqid('quarantine_') . '_' . $sanitizedName;
            move_uploaded_file($tmpPath, $quarantineFile);
            throw new Exception('El PDF no pasó la validación de seguridad.');
        }
    } elseif ($extension === 'docx') {
        if (!validateDOCX($tmpPath)) {
            $quarantineFile = $quarantineDir . uniqid('quarantine_') . '_' . $sanitizedName;
            move_uploaded_file($tmpPath, $quarantineFile);
            throw new Exception('El documento Word no pasó la validación de seguridad.');
        }
    }
    
    // Generar nombre único y seguro
    $uniqueName = uniqid('secure_') . '_' . time() . '.' . $extension;
    $finalPath = $uploadDir . $uniqueName;
    
    // Mover archivo validado
    if (!move_uploaded_file($tmpPath, $finalPath)) {
        throw new Exception('Error al guardar el archivo');
    }
    
    // Establecer permisos seguros
    chmod($finalPath, 0644);
    
    // Enviar notificación por correo (opcional)
    try {
        require_once __DIR__ . '/smtp_mailer.php';
        $smtpConfig = getSMTPConfig();
        $mailer = new SMTPMailer();
        
        $emailSubject = 'Nuevo Archivo Subido - LegisAudit';
        $emailMessage = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f7fafc; padding: 30px; border-radius: 0 0 8px 8px; }
                .success { background: #f0fff4; border-left: 4px solid #48bb78; padding: 15px; margin: 20px 0; border-radius: 4px; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #1a365d; }
                .value { color: #4a5568; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #718096; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Nuevo Archivo Verificado</h1>
                    <p>LegisAudit - Sistema de Seguridad</p>
                </div>
                <div class='content'>
                    <div class='success'>
                        <strong>✓ Archivo verificado y almacenado de forma segura</strong>
                    </div>
                    <div class='field'>
                        <span class='label'>Nombre del archivo:</span>
                        <div class='value'>" . htmlspecialchars($originalName) . "</div>
                    </div>
                    <div class='field'>
                        <span class='label'>Nombre de almacenamiento:</span>
                        <div class='value'>" . htmlspecialchars($uniqueName) . "</div>
                    </div>
                    <div class='field'>
                        <span class='label'>Tipo:</span>
                        <div class='value'>" . htmlspecialchars($mimeType) . "</div>
                    </div>
                    <div class='field'>
                        <span class='label'>Tamaño:</span>
                        <div class='value'>" . number_format($fileSize / 1024, 2) . " KB</div>
                    </div>
                    <div class='field'>
                        <span class='label'>Fecha de recepción:</span>
                        <div class='value'>" . date('d/m/Y H:i:s') . "</div>
                    </div>
                    <div class='field'>
                        <span class='label'>IP de origen:</span>
                        <div class='value'>" . htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'N/A') . "</div>
                    </div>
                    <div class='footer'>
                        <p>Este es un correo automático del sistema LegisAudit</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mailer->send($smtpConfig['to_email'], $emailSubject, $emailMessage, true);
    } catch (Exception $emailError) {
        // No fallar si el correo no se puede enviar, solo registrar
        error_log("Error al enviar notificación de archivo: " . $emailError->getMessage());
    }
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Archivo verificado y almacenado de forma segura',
        'filename' => $uniqueName,
        'originalName' => $originalName,
        'size' => $fileSize,
        'type' => $mimeType
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

