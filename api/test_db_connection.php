<?php
/**
 * Script de diagnóstico de conexión a base de datos
 * Usar solo para debugging - eliminar en producción
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/db_connection.php';

$result = [
    'success' => false,
    'tests' => []
];

try {
    // Test 1: Verificar si db_config.php existe y se puede cargar
    $config = getDbConfig();
    $result['tests']['config_loaded'] = [
        'status' => 'OK',
        'message' => 'Configuración cargada correctamente',
        'config' => [
            'host' => $config['host'],
            'dbname' => $config['dbname'],
            'username' => $config['username'],
            'charset' => $config['charset'],
            'password_set' => !empty($config['password'])
        ]
    ];
    
    // Test 2: Intentar conectar sin especificar base de datos (para verificar credenciales)
    try {
        $dsn_no_db = "mysql:host={$config['host']};charset={$config['charset']}";
        $pdo_test = new PDO($dsn_no_db, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $result['tests']['mysql_connection'] = [
            'status' => 'OK',
            'message' => 'Conexión a MySQL exitosa (sin base de datos)'
        ];
        $pdo_test = null;
    } catch (PDOException $e) {
        $result['tests']['mysql_connection'] = [
            'status' => 'ERROR',
            'message' => 'No se pudo conectar a MySQL: ' . $e->getMessage(),
            'suggestions' => [
                'Verificar que MySQL/MariaDB esté corriendo',
                'Verificar host, usuario y contraseña',
                'Verificar firewall/red'
            ]
        ];
        throw $e;
    }
    
    // Test 3: Verificar si la base de datos existe
    try {
        $dsn_no_db = "mysql:host={$config['host']};charset={$config['charset']}";
        $pdo_test = new PDO($dsn_no_db, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $stmt = $pdo_test->query("SHOW DATABASES LIKE '{$config['dbname']}'");
        $db_exists = $stmt->fetch();
        
        if ($db_exists) {
            $result['tests']['database_exists'] = [
                'status' => 'OK',
                'message' => "La base de datos '{$config['dbname']}' existe"
            ];
        } else {
            $result['tests']['database_exists'] = [
                'status' => 'ERROR',
                'message' => "La base de datos '{$config['dbname']}' NO existe",
                'suggestions' => [
                    'Crear la base de datos manualmente',
                    'Verificar el nombre en db_config.php',
                    'Ejecutar el script de instalación si existe'
                ]
            ];
            
            // Listar bases de datos disponibles
            $stmt = $pdo_test->query("SHOW DATABASES");
            $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $result['tests']['database_exists']['available_databases'] = $dbs;
        }
        $pdo_test = null;
    } catch (PDOException $e) {
        $result['tests']['database_exists'] = [
            'status' => 'SKIP',
            'message' => 'No se pudo verificar (depende de conexión MySQL)'
        ];
    }
    
    // Test 4: Intentar conectar con la base de datos completa
    try {
        $db = getDb();
        $result['tests']['full_connection'] = [
            'status' => 'OK',
            'message' => 'Conexión completa exitosa'
        ];
        $result['success'] = true;
    } catch (Exception $e) {
        $result['tests']['full_connection'] = [
            'status' => 'ERROR',
            'message' => $e->getMessage()
        ];
    }
    
} catch (Exception $e) {
    $result['error'] = $e->getMessage();
    $result['trace'] = explode("\n", $e->getTraceAsString());
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
