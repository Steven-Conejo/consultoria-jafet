<?php
/**
 * Test de múltiples configuraciones SMTP
 * Prueba diferentes hosts y puertos comunes en hosting compartido
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=UTF-8');

$config = require __DIR__ . '/smtp_config.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test SMTP Múltiple</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 10px 0; }
        .error { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 10px 0; }
        .info { background: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; margin: 10px 0; }
        .test-item { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 4px; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; font-size: 12px; }
    </style>
</head>
<body>
    <h1>Test de Múltiples Configuraciones SMTP</h1>
    
    <?php
    echo '<div class="info">';
    echo '<h2>Credenciales</h2>';
    echo '<pre>';
    echo "Usuario: " . htmlspecialchars($config['username']) . "\n";
    echo "Contraseña: " . str_repeat('*', strlen($config['password'])) . "\n";
    echo '</pre>';
    echo '</div>';
    
    // Configuraciones a probar
    $configsToTest = [
        ['host' => 'localhost', 'port' => 25, 'encryption' => 'none', 'name' => 'localhost:25 (sin encriptación)'],
        ['host' => 'localhost', 'port' => 587, 'encryption' => 'tls', 'name' => 'localhost:587 (TLS)'],
        ['host' => 'localhost', 'port' => 465, 'encryption' => 'ssl', 'name' => 'localhost:465 (SSL)'],
        ['host' => 'mail.legisaudit-abogados.cu.ma', 'port' => 25, 'encryption' => 'none', 'name' => 'mail.legisaudit-abogados.cu.ma:25'],
        ['host' => 'mail.legisaudit-abogados.cu.ma', 'port' => 587, 'encryption' => 'tls', 'name' => 'mail.legisaudit-abogados.cu.ma:587'],
        ['host' => 'smtp.legisaudit-abogados.cu.ma', 'port' => 25, 'encryption' => 'none', 'name' => 'smtp.legisaudit-abogados.cu.ma:25'],
        ['host' => 'smtp.legisaudit-abogados.cu.ma', 'port' => 587, 'encryption' => 'tls', 'name' => 'smtp.legisaudit-abogados.cu.ma:587'],
    ];
    
    echo '<div class="info">';
    echo '<h2>Probando ' . count($configsToTest) . ' configuraciones comunes...</h2>';
    echo '</div>';
    
    $workingConfig = null;
    
    foreach ($configsToTest as $testConfig) {
        echo '<div class="test-item">';
        echo '<h3>' . htmlspecialchars($testConfig['name']) . '</h3>';
        
        $errorCode = 0;
        $errorMessage = '';
        $socket = null;
        
        try {
            if ($testConfig['encryption'] === 'ssl') {
                $context = stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ]);
                $socket = @stream_socket_client(
                    'ssl://' . $testConfig['host'] . ':' . $testConfig['port'],
                    $errorCode,
                    $errorMessage,
                    5,
                    STREAM_CLIENT_CONNECT,
                    $context
                );
            } else {
                $socket = @stream_socket_client(
                    $testConfig['host'] . ':' . $testConfig['port'],
                    $errorCode,
                    $errorMessage,
                    5
                );
            }
            
            if ($socket) {
                // Leer respuesta inicial
                $response = '';
                while ($line = @fgets($socket, 515)) {
                    $response .= $line;
                    if (substr($line, 3, 1) === ' ') {
                        break;
                    }
                }
                
                if (substr($response, 0, 3) === '220') {
                    echo '<div class="success">✓ Conexión exitosa</div>';
                    echo '<pre>Respuesta: ' . htmlspecialchars(trim($response)) . '</pre>';
                    $workingConfig = $testConfig;
                    @fclose($socket);
                    break; // Encontramos una que funciona
                } else {
                    echo '<div class="error">✗ Respuesta inesperada: ' . htmlspecialchars(trim($response)) . '</div>';
                    @fclose($socket);
                }
            } else {
                echo '<div class="error">✗ No se pudo conectar</div>';
                echo '<p>Error: ' . $errorCode . ' - ' . htmlspecialchars($errorMessage) . '</p>';
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Excepción: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
    }
    
    if ($workingConfig) {
        echo '<div class="success" style="margin-top: 30px;">';
        echo '<h2>✓ Configuración que funciona encontrada:</h2>';
        echo '<pre>';
        echo "Host: " . htmlspecialchars($workingConfig['host']) . "\n";
        echo "Puerto: " . htmlspecialchars($workingConfig['port']) . "\n";
        echo "Encriptación: " . htmlspecialchars($workingConfig['encryption']) . "\n";
        echo '</pre>';
        echo '<p><strong>Actualiza api/smtp_config.php con estos valores.</strong></p>';
        echo '</div>';
    } else {
        echo '<div class="error" style="margin-top: 30px;">';
        echo '<h2>✗ Ninguna configuración funcionó</h2>';
        echo '<p>Posibles causas:</p>';
        echo '<ul>';
        echo '<li>El hosting no permite conexiones SMTP desde PHP</li>';
        echo '<li>El host SMTP es diferente (contacta al soporte)</li>';
        echo '<li>El hosting solo permite usar mail() nativo</li>';
        echo '</ul>';
        echo '<p><strong>Recomendación:</strong> Usa mail() nativo que ya sabemos que funciona.</p>';
        echo '</div>';
    }
    ?>
</body>
</html>
