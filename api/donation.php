<?php
/**
 * API de Donaciones
 * Manejo seguro de donaciones voluntarias
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración
$logFile = __DIR__ . '/../logs/donations.log';
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

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Datos inválidos');
    }
    
    $amount = floatval($input['amount'] ?? 0);
    $currency = sanitizeInput($input['currency'] ?? 'USD');
    
    // Validaciones
    if ($amount <= 0) {
        throw new Exception('El monto debe ser mayor a cero');
    }
    
    if ($amount > 10000) {
        throw new Exception('El monto máximo permitido es $10,000');
    }
    
    if (!in_array($currency, ['USD', 'EUR', 'MXN'])) {
        throw new Exception('Moneda no soportada');
    }
    
    // En producción, aquí integrarías con un gateway de pagos
    // Por ahora, solo guardamos el registro
    
    $logEntry = date('Y-m-d H:i:s') . " | " . $amount . " " . $currency . " | IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    echo json_encode([
        'success' => true,
        'message' => 'Gracias por tu donación. En un entorno de producción, aquí se procesaría el pago.',
        'amount' => $amount,
        'currency' => $currency
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

