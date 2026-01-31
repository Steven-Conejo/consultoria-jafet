<?php
/**
 * Conexión a Base de Datos
 * LegisAudit - Consultoría Jafet
 */

require_once __DIR__ . '/db_config.php';

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $config = getDbConfig();
        
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5,
            ];
            
            $this->connection = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            $errorMsg = $e->getMessage();
            error_log("Error de conexión a BD: " . $errorMsg);
            error_log("Intento de conexión con: host={$config['host']}, dbname={$config['dbname']}, username={$config['username']}");
            
            // Proporcionar mensaje más descriptivo en desarrollo
            $debugMsg = "Error al conectar con la base de datos";
            if (defined('DEBUG') && DEBUG) {
                $debugMsg .= ": " . $errorMsg;
            }
            throw new Exception($debugMsg);
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function __clone() {
        throw new Exception("No se puede clonar la conexión");
    }
    
    public function __wakeup() {
        throw new Exception("No se puede deserializar la conexión");
    }
}

function getDbConfig() {
    return require __DIR__ . '/db_config.php';
}

function getDb() {
    return Database::getInstance()->getConnection();
}

