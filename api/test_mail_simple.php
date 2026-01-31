<?php
/**
 * Script de prueba simple para verificar que mail() funciona
 * Acceder desde navegador: http://legisaudit-abogados.cu.ma/api/test_mail_simple.php
 */

header('Content-Type: application/json; charset=utf-8');

// Manejar errores para ver qué está pasando
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$result = [
    'success' => false,
    'tests' => [],
    'message' => ''
];

try {
    // Test 1: Verificar que mail() esté disponible
    if (!function_exists('mail')) {
        $result['tests']['mail_function'] = [
            'status' => 'ERROR',
            'message' => 'La función mail() no está disponible en este servidor'
        ];
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    } else {
        $result['tests']['mail_function'] = [
            'status' => 'OK',
            'message' => 'La función mail() está disponible'
        ];
    }
    
    // Test 2: Obtener configuración
    // Cargar configuración directamente (smtp_config.php retorna un array)
    $config = require __DIR__ . '/smtp_config.php';
    
    if (!is_array($config) || empty($config)) {
        throw new Exception('No se pudo cargar la configuración SMTP');
    }
    $toEmail = $config['to_email'];
    $fromEmail = $config['from_email'];
    $fromName = $config['from_name'];
    
    $result['tests']['config'] = [
        'status' => 'OK',
        'message' => 'Configuración cargada correctamente',
        'from' => "$fromName <$fromEmail>",
        'to' => $toEmail
    ];
    
    // Test 3: Intentar enviar correo simple
    $testSubject = "Test de Mail - " . date('Y-m-d H:i:s');
    $testMessage = "
    <html>
    <head>
        <meta charset='UTF-8'>
    </head>
    <body>
        <h2>Prueba de Correo</h2>
        <p>Este es un correo de prueba enviado con mail() nativo de PHP.</p>
        <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
        <p><strong>Servidor:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'Desconocido') . "</p>
    </body>
    </html>
    ";
    
    // Headers simples y compatibles
    $headers = [];
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "From: $fromName <$fromEmail>";
    $headers[] = "Reply-To: <$fromEmail>";
    $headers[] = "Content-Type: text/html; charset=UTF-8";
    $headers[] = "Return-Path: <$fromEmail>";
    
    $headersString = implode("\r\n", $headers);
    
    // Codificar subject (verificar si mb_encode_mimeheader está disponible)
    if (function_exists('mb_encode_mimeheader')) {
        $encodedSubject = mb_encode_mimeheader($testSubject, 'UTF-8', 'B', "\r\n");
    } else {
        // Fallback si mb_encode_mimeheader no está disponible
        $encodedSubject = '=?UTF-8?B?' . base64_encode($testSubject) . '?=';
    }
    
    // Parámetros adicionales (-f para envelope sender)
    $additionalParams = "-f{$fromEmail}";
    
    // Intentar enviar
    error_log("Intentando enviar correo de prueba a: $toEmail");
    
    $mailResult = @mail($toEmail, $encodedSubject, $testMessage, $headersString, $additionalParams);
    
    if ($mailResult) {
        $result['tests']['send_mail'] = [
            'status' => 'OK',
            'message' => "Correo de prueba enviado exitosamente a: $toEmail",
            'note' => 'NOTA: mail() puede retornar true incluso si el correo no llega. Verifica tu bandeja de entrada y spam.'
        ];
        $result['success'] = true;
        $result['message'] = "Correo de prueba enviado. Revisa $toEmail (incluyendo spam).";
    } else {
        $lastError = error_get_last();
        $errorMsg = $lastError ? $lastError['message'] : 'Error desconocido al enviar correo';
        
        $result['tests']['send_mail'] = [
            'status' => 'ERROR',
            'message' => "Error al enviar correo: $errorMsg"
        ];
        $result['message'] = "Error al enviar correo: $errorMsg";
    }
    
} catch (Exception $e) {
    $result['error'] = $e->getMessage();
    $result['message'] = "Error: " . $e->getMessage();
    $result['trace'] = explode("\n", $e->getTraceAsString());
} catch (Error $e) {
    // Capturar errores fatales de PHP
    $result['error'] = $e->getMessage();
    $result['message'] = "Error fatal: " . $e->getMessage();
    $result['file'] = $e->getFile();
    $result['line'] = $e->getLine();
}

// Si hay un error y no se ha enviado JSON, asegurarse de enviarlo
if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
