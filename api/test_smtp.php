<?php
/**
 * Script de prueba para el servicio SMTP
 * Usar este archivo para verificar que la configuración SMTP funcione correctamente
 * 
 * NOTA: En producción, eliminar o proteger este archivo
 * 
 * Protección: Solo permite acceso desde localhost
 */

// Protección: Solo permitir acceso desde localhost en producción
$allowedIPs = ['127.0.0.1', '::1'];
$clientIP = $_SERVER['REMOTE_ADDR'] ?? '';

// Protección habilitada para producción
if (!in_array($clientIP, $allowedIPs) && $clientIP !== '::1') {
    die('Acceso denegado. Este archivo solo puede ser accedido desde localhost.');
}

require_once __DIR__ . '/smtp_mailer.php';

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test SMTP - LegisAudit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f7fafc;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1a365d;
        }
        .success {
            background: #f0fff4;
            border-left: 4px solid #48bb78;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .error {
            background: #fff5f5;
            border-left: 4px solid #f56565;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info {
            background: #ebf8ff;
            border-left: 4px solid #4299e1;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        pre {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test de Configuración SMTP</h1>
        
        <?php
        try {
            $config = getSMTPConfig();
            
            echo '<div class="info">';
            echo '<h2>Configuración Actual</h2>';
            echo '<pre>';
            echo "Servidor SMTP: " . htmlspecialchars($config['host']) . "\n";
            echo "Puerto: " . htmlspecialchars($config['port']) . "\n";
            echo "Usuario: " . htmlspecialchars($config['username']) . "\n";
            echo "Desde: " . htmlspecialchars($config['from_email']) . "\n";
            echo "Hacia: " . htmlspecialchars($config['to_email']) . "\n";
            echo "Encriptación: " . htmlspecialchars($config['encryption']) . "\n";
            echo '</pre>';
            echo '</div>';
            
            // Intentar enviar correo de prueba
            if (isset($_GET['send']) && $_GET['send'] === '1') {
                echo '<div class="info"><h2>Enviando correo de prueba...</h2></div>';
                
                $mailer = new SMTPMailer();
                $testSubject = 'Test SMTP - LegisAudit - ' . date('Y-m-d H:i:s');
                $testMessage = "
                <html>
                <body>
                    <h2>Correo de Prueba</h2>
                    <p>Este es un correo de prueba desde LegisAudit.</p>
                    <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
                    <p><strong>Servidor:</strong> " . htmlspecialchars($config['host']) . "</p>
                    <p>Si recibes este correo, la configuración SMTP está funcionando correctamente.</p>
                </body>
                </html>
                ";
                
                $mailer->send($config['to_email'], $testSubject, $testMessage, true);
                
                echo '<div class="success">';
                echo '<h2>✓ Correo Enviado Exitosamente</h2>';
                echo '<p>El correo de prueba ha sido enviado a: <strong>' . htmlspecialchars($config['to_email']) . '</strong></p>';
                echo '<p>Revisa tu bandeja de entrada (y spam) para verificar la recepción.</p>';
                echo '</div>';
            } else {
                echo '<div class="info">';
                echo '<h2>Instrucciones</h2>';
                echo '<p>Haz clic en el botón de abajo para enviar un correo de prueba.</p>';
                echo '<p><a href="?send=1" style="display: inline-block; padding: 12px 24px; background: #1a365d; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">📧 Enviar Correo de Prueba</a></p>';
                echo '</div>';
            }
            
        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<h2>✗ Error</h2>';
            echo '<p><strong>Mensaje:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>Archivo:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
            echo '<p><strong>Línea:</strong> ' . htmlspecialchars($e->getLine()) . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</div>';
        }
        ?>
        
        <div class="info" style="margin-top: 30px;">
            <h3>⚠️ Nota de Seguridad</h3>
            <p>Este archivo debe ser eliminado o protegido en producción. No dejar acceso público a este script.</p>
        </div>
    </div>
</body>
</html>

