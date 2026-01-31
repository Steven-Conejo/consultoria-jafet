<?php
/**
 * Script de Instalación
 * Crea la base de datos y el usuario por defecto
 */

header('Content-Type: application/json');

require_once __DIR__ . '/db_config.php';

$config = require __DIR__ . '/db_config.php';

try {
    // Conectar sin especificar base de datos para crearla
    $pdo = new PDO(
        "mysql:host={$config['host']};charset={$config['charset']}",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear la base de datos si no existe
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['dbname']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$config['dbname']}`");
    
    // Leer y ejecutar el script SQL
    $sql = file_get_contents(__DIR__ . '/../database.sql');
    
    // Dividir por sentencias y filtrar comentarios y líneas vacías
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            $stmt = trim($stmt);
            return !empty($stmt) && 
                   !preg_match('/^--/', $stmt) && 
                   !preg_match('/^CREATE DATABASE/i', $stmt) &&
                   !preg_match('/^USE/i', $stmt);
        }
    );
    
    // Ejecutar cada sentencia
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Ignorar errores de tablas que ya existen
                if (strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }
    }
    
    // Crear usuario por defecto con contraseña hasheada
    $passwordHash = password_hash('Dario', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_completo, usuario, password) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE password = ?");
    $stmt->execute(['Steven', 'Steven', $passwordHash, $passwordHash]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Base de datos creada e instalada correctamente. Usuario: Steven, Contraseña: Dario'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

