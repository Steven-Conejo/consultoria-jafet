<?php
/**
 * Script para crear el usuario por defecto
 * Ejecuta este archivo una vez para crear el usuario Steven
 */

require_once __DIR__ . '/db_connection.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $db = getDb();
    
    // Generar hash de la contraseña "Dario"
    $passwordHash = password_hash('Dario', PASSWORD_DEFAULT);
    
    // Insertar o actualizar el usuario
    $stmt = $db->prepare("INSERT INTO usuarios (nombre_completo, usuario, password) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE password = ?");
    $stmt->execute(['Steven', 'Steven', $passwordHash, $passwordHash]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Usuario creado correctamente',
        'usuario' => 'Steven',
        'password' => 'Dario',
        'hash' => $passwordHash
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ], JSON_PRETTY_PRINT);
}

