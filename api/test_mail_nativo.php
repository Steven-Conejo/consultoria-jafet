<?php
/**
 * Test de mail() nativo de PHP
 * Para verificar si el servidor puede enviar correos
 */

header('Content-Type: text/html; charset=UTF-8');

$testEmail = $_GET['email'] ?? 'sevicioprofesionalabogadojcgy@gmail.com';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test mail() nativo</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0; }
        .error { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0; }
        .info { background: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; margin: 20px 0; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Test de mail() nativo de PHP</h1>
    
    <?php
    echo '<div class="info">';
    echo '<h2>Información del servidor</h2>';
    echo '<pre>';
    echo "PHP Version: " . phpversion() . "\n";
    echo "Función mail() disponible: " . (function_exists('mail') ? 'SÍ' : 'NO') . "\n";
    echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
    echo "SMTP: " . ini_get('SMTP') . "\n";
    echo "smtp_port: " . ini_get('smtp_port') . "\n";
    echo '</pre>';
    echo '</div>';
    
    if (isset($_GET['send']) && $_GET['send'] === '1') {
        echo '<div class="info"><h2>Enviando correo de prueba...</h2></div>';
        
        $subject = 'Test mail() nativo - ' . date('Y-m-d H:i:s');
        $message = "Este es un correo de prueba desde mail() nativo de PHP.\n\n";
        $message .= "Fecha: " . date('d/m/Y H:i:s') . "\n";
        $message .= "Servidor: " . ($_SERVER['SERVER_NAME'] ?? 'Desconocido') . "\n";
        
        $headers = [];
        $headers[] = "From: noreply@legisaudit-abogados.cu.ma";
        $headers[] = "Reply-To: sevicioprofesionalabogadojcgy@gmail.com";
        $headers[] = "X-Mailer: PHP/" . phpversion();
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/plain; charset=UTF-8";
        
        // Lista de destinatarios
        $recipients = [
            $testEmail,
            'steven@staygold.cr'
        ];
        
        $results = [];
        $allSuccess = true;
        
        foreach ($recipients as $to) {
            $result = @mail($to, $subject, $message, implode("\r\n", $headers));
            $results[$to] = $result;
            if (!$result) {
                $allSuccess = false;
            }
        }
        
        if ($allSuccess) {
            echo '<div class="success">';
            echo '<h2>✓ mail() retornó TRUE para todos los destinatarios</h2>';
            echo '<p>El correo fue aceptado por el servidor para todos los destinatarios. Sin embargo, esto NO garantiza que llegue al destinatario.</p>';
            echo '<p><strong>Destinatarios:</strong></p>';
            echo '<ul>';
            foreach ($recipients as $to) {
                echo '<li>' . htmlspecialchars($to) . ' - ' . ($results[$to] ? '✓ Enviado' : '✗ Falló') . '</li>';
            }
            echo '</ul>';
            echo '<p>Revisa la bandeja de entrada (y spam) de ambos correos.</p>';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<h2>✗ mail() retornó FALSE para algunos destinatarios</h2>';
            echo '<p>Resultados por destinatario:</p>';
            echo '<ul>';
            foreach ($results as $to => $result) {
                echo '<li>' . htmlspecialchars($to) . ' - ' . ($result ? '✓ Enviado' : '✗ Falló') . '</li>';
            }
            echo '</ul>';
            echo '<p>El servidor rechazó enviar el correo a algunos destinatarios. Esto significa que mail() puede no estar configurado correctamente.</p>';
            echo '</div>';
        }
        
        // Verificar logs si es posible
        $error = error_get_last();
        if ($error && strpos($error['message'], 'mail') !== false) {
            echo '<div class="error">';
            echo '<h3>Último error:</h3>';
            echo '<pre>' . htmlspecialchars($error['message']) . '</pre>';
            echo '</div>';
        }
    } else {
        echo '<div class="info">';
        echo '<h2>Instrucciones</h2>';
        echo '<p>Haz clic en el botón para probar el envío usando mail() nativo:</p>';
        echo '<form method="GET">';
        echo '<label>Email de prueba: <input type="email" name="email" value="' . htmlspecialchars($testEmail) . '" style="padding: 5px; width: 300px;"></label><br><br>';
        echo '<input type="hidden" name="send" value="1">';
        echo '<button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">📧 Probar mail() nativo</button>';
        echo '</form>';
        echo '</div>';
    }
    ?>
    
    <div class="info" style="margin-top: 30px;">
        <h3>⚠️ Nota importante</h3>
        <p>Si mail() no funciona, el hosting probablemente requiere:</p>
        <ul>
            <li>Configuración adicional de correo en el panel de control</li>
            <li>Un servicio SMTP del hosting (no externo)</li>
            <li>O contactar al soporte técnico para habilitar envío de correos</li>
        </ul>
    </div>
</body>
</html>
