<?php
/**
 * Test de SMTP del hosting
 * Para verificar que la configuración SMTP del hosting funcione correctamente
 */

// Habilitar errores para debugging (solo en test)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test SMTP Hosting</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0; }
        .error { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0; }
        .info { background: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; margin: 20px 0; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Test SMTP del Hosting</h1>
    
    <?php
    // Cargar configuración directamente desde el archivo
    $config = require __DIR__ . '/smtp_config.php';
    
    echo '<div class="info">';
    echo '<h2>Configuración Actual</h2>';
    echo '<pre>';
    echo "Host: " . htmlspecialchars($config['host']) . "\n";
    echo "Puerto: " . htmlspecialchars($config['port']) . "\n";
    echo "Usuario: " . htmlspecialchars($config['username']) . "\n";
    echo "From Email: " . htmlspecialchars($config['from_email']) . "\n";
    echo "Encryption: " . htmlspecialchars($config['encryption']) . "\n";
    echo '</pre>';
    echo '</div>';
    
    // Probar conexión
    echo '<div class="info">';
    echo '<h2>1. Prueba de Conexión</h2>';
    
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);
    
    $errorCode = 0;
    $errorMessage = '';
    
    $testSocket = @stream_socket_client(
        $config['host'] . ':' . $config['port'],
        $errorCode,
        $errorMessage,
        10,
        STREAM_CLIENT_CONNECT,
        $context
    );
    
    if ($testSocket) {
        echo '<div class="success">✓ Conexión exitosa al servidor SMTP del hosting</div>';
        @fclose($testSocket);
    } else {
        echo '<div class="error">✗ No se pudo conectar</div>';
        echo '<p><strong>Error:</strong> ' . $errorCode . ' - ' . htmlspecialchars($errorMessage) . '</p>';
        echo '<p><strong>Posibles soluciones:</strong></p>';
        echo '<ul>';
        echo '<li>Verifica que el host sea correcto (puede ser "localhost" o "smtp.legisaudit-abogados.cu.ma")</li>';
        echo '<li>Verifica el puerto (puede ser 25, 465, o 587)</li>';
        echo '<li>Contacta al soporte del hosting para obtener los datos SMTP exactos</li>';
        echo '</ul>';
    }
    echo '</div>';
    
    // Probar envío real
    if (isset($_GET['send']) && $_GET['send'] === '1') {
        echo '<div class="info"><h2>2. Prueba de Envío Real</h2></div>';
        try {
            require_once __DIR__ . '/smtp_mailer.php';
            $mailer = new SMTPMailer();
            
            $testSubject = 'Test SMTP Hosting - ' . date('Y-m-d H:i:s');
            $testMessage = "
            <html>
            <body>
                <h2>Correo de Prueba</h2>
                <p>Este es un correo de prueba desde el SMTP del hosting.</p>
                <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
                <p><strong>Servidor:</strong> " . htmlspecialchars($config['host']) . "</p>
                <p>Si recibes este correo, la configuración SMTP del hosting está funcionando correctamente.</p>
            </body>
            </html>
            ";
            
            $recipients = [
                $config['to_email'],
                'steven@staygold.cr'
            ];
            
            foreach ($recipients as $recipient) {
                try {
                    $mailer->send($recipient, $testSubject, $testMessage, true);
                    echo '<div class="success">✓ Correo enviado exitosamente a: ' . htmlspecialchars($recipient) . '</div>';
                } catch (Exception $e) {
                    echo '<div class="error">✗ Error al enviar a ' . htmlspecialchars($recipient) . ': ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<h3>✗ Error</h3>';
            echo '<p><strong>Mensaje:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>Archivo:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
            echo '<p><strong>Línea:</strong> ' . htmlspecialchars($e->getLine()) . '</p>';
            echo '</div>';
        }
    } else {
        echo '<div class="info">';
        echo '<h2>2. Prueba de Envío Real</h2>';
        echo '<p>Haz clic en el botón para probar el envío:</p>';
        echo '<p><a href="?send=1" style="display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">📧 Probar Envío SMTP</a></p>';
        echo '</div>';
    }
    ?>
    
    <div class="info" style="margin-top: 30px;">
        <h3>⚠️ Nota</h3>
        <p>Si la conexión falla, puede ser que el host SMTP sea diferente. Comúnmente puede ser:</p>
        <ul>
            <li><code>localhost</code></li>
            <li><code>mail.legisaudit-abogados.cu.ma</code></li>
            <li><code>smtp.legisaudit-abogados.cu.ma</code></li>
        </ul>
        <p>Contacta al soporte de GoogieHost para obtener el host SMTP exacto.</p>
    </div>
</body>
</html>
