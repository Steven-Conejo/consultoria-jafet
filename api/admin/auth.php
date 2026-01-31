<?php
/**
 * API de Autenticación de Administradores
 */

// Configurar sesión antes de cualquier output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../cors_headers.php';
require_once __DIR__ . '/../db_connection.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Soportar JSON y form-url-encoded (más robusto en dev/proxy)
        $rawBody = file_get_contents('php://input');
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        $input = null;
        if (stripos($contentType, 'application/json') !== false && !empty($rawBody)) {
            $input = json_decode($rawBody, true);
        }

        // Fallback a POST (por si llega como form-data / x-www-form-urlencoded)
        if (!is_array($input)) {
            $input = $_POST;
        }

        if (!is_array($input) || (empty($input['usuario']) && empty($input['password']))) {
            throw new Exception('Datos inválidos');
        }
        
        $usuario = trim($input['usuario'] ?? '');
        $password = $input['password'] ?? '';
        
        if (empty($usuario) || empty($password)) {
            throw new Exception('Usuario y contraseña son requeridos');
        }
        
        $db = getDb();
        $stmt = $db->prepare("SELECT id, nombre_completo, usuario, password FROM usuarios WHERE usuario = ? AND activo = 1");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception('Usuario o contraseña incorrectos');
        }
        
        // Crear sesión
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_usuario'] = $user['usuario'];
        $_SESSION['admin_nombre'] = $user['nombre_completo'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Login exitoso',
            'user' => [
                'id' => $user['id'],
                'nombre_completo' => $user['nombre_completo'],
                'usuario' => $user['usuario']
            ]
        ]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check'])) {
        // Verificar si hay sesión activa
        // No lanzar excepción si no hay sesión, es normal
        if (isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id'])) {
            echo json_encode([
                'success' => true,
                'authenticated' => true,
                'user' => [
                    'id' => $_SESSION['admin_id'],
                    'nombre_completo' => $_SESSION['admin_nombre'] ?? '',
                    'usuario' => $_SESSION['admin_usuario'] ?? ''
                ]
            ]);
        } else {
            // No hay sesión activa - esto es normal, no es un error
            echo json_encode([
                'success' => true,
                'authenticated' => false
            ]);
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['logout'])) {
        // Cerrar sesión
        session_destroy();
        echo json_encode([
            'success' => true,
            'message' => 'Sesión cerrada'
        ]);
        
    } else {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
        exit;
    }
    
} catch (PDOException $e) {
    // Error específico de base de datos
    http_response_code(500);
    error_log("Error PDO en auth.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al conectar con la base de datos',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    // Otros errores
    $message = $e->getMessage();
    http_response_code(400);
    
    // Si el mensaje es sobre conexión a BD, usar código 500
    if (stripos($message, 'base de datos') !== false || stripos($message, 'conectar') !== false) {
        http_response_code(500);
    }
    
    error_log("Error en auth.php: " . $message);
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
} catch (Error $e) {
    // Capturar errores fatales de PHP
    http_response_code(500);
    error_log("Error fatal en auth.php: " . $e->getMessage() . " en línea " . $e->getLine());
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
}

