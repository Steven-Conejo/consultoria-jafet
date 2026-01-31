<?php
/**
 * Script de Diagnóstico SMTP para Producción
 * Este script verifica las capacidades del servidor para enviar correos SMTP
 */

// Permitir acceso desde cualquier IP (temporalmente para diagnóstico)
// TODO: Eliminar o proteger este archivo después del diagnóstico

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico SMTP - LegisAudit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
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
        .warning {
            background: #fffaf0;
            border-left: 4px solid #ed8936;
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
            font-size: 12px;
        }
        .test-item {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico SMTP - Servidor de Producción</h1>
        
        <?php
        echo '<div class="test-item">';
        echo '<h2>1. Información del Servidor</h2>';
        echo '<pre>';
        echo "PHP Version: " . phpversion() . "\n";
        echo "Sistema Operativo: " . PHP_OS . "\n";
        echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido') . "\n";
        echo "Server IP: " . ($_SERVER['SERVER_ADDR'] ?? 'Desconocido') . "\n";
        echo '</pre>';
        echo '</div>';
        
        echo '<div class="test-item">';
        echo '<h2>2. Extensiones PHP Requeridas</h2>';
        $required = ['openssl', 'sockets', 'curl'];
        foreach ($required as $ext) {
            $loaded = extension_loaded($ext);
            if ($loaded) {
                echo '<div class="success">✓ ' . $ext . ' está habilitado</div>';
            } else {
                echo '<div class="error">✗ ' . $ext . ' NO está habilitado</div>';
            }
        }
        echo '</div>';
        
        echo '<div class="test-item">';
        echo '<h2>3. Funciones PHP Requeridas</h2>';
        $functions = ['stream_socket_client', 'stream_socket_enable_crypto', 'base64_encode', 'fwrite', 'fgets'];
        foreach ($functions as $func) {
            $exists = function_exists($func);
            if ($exists) {
                echo '<div class="success">✓ ' . $func . '() está disponible</div>';
            } else {
                echo '<div class="error">✗ ' . $func . '() NO está disponible</div>';
            }
        }
        echo '</div>';
        
        echo '<div class="test-item">';
        echo '<h2>4. Prueba de Conexión de Red</h2>';
        
        // Probar conexión a Gmail SMTP
        $config = require __DIR__ . '/smtp_config.php';
        $host = $config['host'];
        $port = $config['port'];
        
        echo "<p>Intentando conectar a: <strong>$host:$port</strong></p>";
        
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        $errorCode = 0;
        $errorMessage = '';
        
        if ($config['encryption'] === 'ssl') {
            $socket = @stream_socket_client(
                'ssl://' . $host . ':' . $port,
                $errorCode,
                $errorMessage,
                10,
                STREAM_CLIENT_CONNECT,
                $context
            );
        } else {
            $socket = @stream_socket_client(
                $host . ':' . $port,
                $errorCode,
                $errorMessage,
                10,
                STREAM_CLIENT_CONNECT,
                $context
            );
        }
        
        if ($socket) {
            echo '<div class="success">✓ Conexión exitosa al servidor SMTP</div>';
            fclose($socket);
        } else {
            echo '<div class="error">✗ No se pudo conectar al servidor SMTP</div>';
            echo '<div class="warning">';
            echo '<strong>Código de error:</strong> ' . $errorCode . '<br>';
            echo '<strong>Mensaje:</strong> ' . htmlspecialchars($errorMessage) . '<br><br>';
            echo '<strong>Posibles causas:</strong><ul>';
            echo '<li>El firewall del hosting bloquea conexiones salientes al puerto ' . $port . '</li>';
            echo '<li>El servidor no tiene acceso a internet</li>';
            echo '<li>La extensión OpenSSL no está configurada correctamente</li>';
            echo '<li>El hosting tiene restricciones de red</li>';
            echo '</ul>';
            echo '</div>';
        }
        echo '</div>';
        
        echo '<div class="test-item">';
        echo '<h2>5. Configuración SMTP Actual</h2>';
        echo '<pre>';
        echo "Host: " . htmlspecialchars($config['host']) . "\n";
        echo "Puerto: " . htmlspecialchars($config['port']) . "\n";
        echo "Encriptación: " . htmlspecialchars($config['encryption']) . "\n";
        echo "Usuario: " . htmlspecialchars($config['username']) . "\n";
        echo "From Email: " . htmlspecialchars($config['from_email']) . "\n";
        echo "To Email: " . htmlspecialchars($config['to_email']) . "\n";
        echo '</pre>';
        echo '</div>';
        
        echo '<div class="test-item">';
        echo '<h2>6. Prueba de Envío Real</h2>';
        if (isset($_GET['test']) && $_GET['test'] === 'send') {
            echo '<div class="info">Intentando enviar correo de prueba...</div>';
            try {
                require_once __DIR__ . '/smtp_mailer.php';
                $mailer = new SMTPMailer();
                $testSubject = 'Test SMTP Diagnóstico - ' . date('Y-m-d H:i:s');
                $testMessage = "Este es un correo de prueba del diagnóstico SMTP.<br><br>Fecha: " . date('d/m/Y H:i:s');
                
                $mailer->send($config['to_email'], $testSubject, $testMessage, true);
                
                echo '<div class="success">';
                echo '<h3>✓ Correo Enviado Exitosamente</h3>';
                echo '<p>El correo de prueba ha sido enviado a: <strong>' . htmlspecialchars($config['to_email']) . '</strong></p>';
                echo '</div>';
            } catch (Exception $e) {
                echo '<div class="error">';
                echo '<h3>✗ Error al Enviar Correo</h3>';
                echo '<p><strong>Mensaje:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<p><strong>Archivo:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
                echo '<p><strong>Línea:</strong> ' . htmlspecialchars($e->getLine()) . '</p>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                echo '</div>';
            }
        } else {
            echo '<p><a href="?test=send" style="display: inline-block; padding: 12px 24px; background: #1a365d; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">📧 Probar Envío Real</a></p>';
        }
        echo '</div>';
        
        echo '<div class="warning" style="margin-top: 30px;">';
        echo '<h3>⚠️ Importante</h3>';
        echo '<p>Después de usar este diagnóstico, elimina o protege este archivo por seguridad.</p>';
        echo '</div>';
        ?>
    </div>
</body>
</html>
