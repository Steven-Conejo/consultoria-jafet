<?php
/**
 * API de Gestión de Usuarios Administradores
 */

require_once __DIR__ . '/../cors_headers.php';
require_once __DIR__ . '/../db_connection.php';

session_start();

// Verificar autenticación
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    $db = getDb();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Listar usuarios
        $stmt = $db->query("SELECT id, nombre_completo, usuario, fecha_creacion, activo FROM usuarios ORDER BY fecha_creacion DESC");
        $usuarios = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'usuarios' => $usuarios
        ]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Crear nuevo usuario
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Datos inválidos');
        }
        
        $nombre_completo = trim($input['nombre_completo'] ?? '');
        $usuario = trim($input['usuario'] ?? '');
        $password = $input['password'] ?? '';
        
        if (empty($nombre_completo) || empty($usuario) || empty($password)) {
            throw new Exception('Todos los campos son requeridos');
        }
        
        if (strlen($password) < 4) {
            throw new Exception('La contraseña debe tener al menos 4 caracteres');
        }
        
        // Verificar si el usuario ya existe
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        if ($stmt->fetch()) {
            throw new Exception('El usuario ya existe');
        }
        
        // Crear usuario
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO usuarios (nombre_completo, usuario, password) VALUES (?, ?, ?)");
        $stmt->execute([$nombre_completo, $usuario, $passwordHash]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Usuario creado correctamente',
            'id' => $db->lastInsertId()
        ]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Eliminar usuario
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            throw new Exception('ID de usuario requerido');
        }
        
        // No permitir eliminar el propio usuario
        if ($id == $_SESSION['admin_id']) {
            throw new Exception('No puedes eliminar tu propio usuario');
        }
        
        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ]);
        
    } else {
        throw new Exception('Método no permitido');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

