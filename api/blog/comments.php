<?php
/**
 * API Pública de Comentarios del Blog
 */

require_once __DIR__ . '/../cors_headers.php';
require_once __DIR__ . '/../db_connection.php';

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

try {
    $db = getDb();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Listar comentarios de un artículo
        $articulo_id = $_GET['articulo_id'] ?? null;
        
        if (!$articulo_id) {
            throw new Exception('ID de artículo requerido');
        }
        
        $stmt = $db->prepare("
            SELECT c.*, 
                   (SELECT COUNT(*) FROM comentarios WHERE comentario_padre_id = c.id AND activo = 1) as respuestas_count
            FROM comentarios c 
            WHERE c.articulo_id = ? AND c.comentario_padre_id IS NULL AND c.activo = 1 
            ORDER BY c.fecha_creacion DESC
        ");
        $stmt->execute([$articulo_id]);
        $comentarios = $stmt->fetchAll();
        
        // Obtener respuestas para cada comentario
        foreach ($comentarios as &$comentario) {
            $stmtResp = $db->prepare("
                SELECT * FROM comentarios 
                WHERE comentario_padre_id = ? AND activo = 1 
                ORDER BY fecha_creacion ASC
            ");
            $stmtResp->execute([$comentario['id']]);
            $comentario['respuestas'] = $stmtResp->fetchAll();
        }
        
        echo json_encode([
            'success' => true,
            'comentarios' => $comentarios
        ]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Crear nuevo comentario o respuesta
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Datos inválidos');
        }
        
        $articulo_id = intval($input['articulo_id'] ?? 0);
        $nombre = sanitizeInput($input['nombre'] ?? '');
        $email = sanitizeInput($input['email'] ?? '');
        $comentario = sanitizeInput($input['comentario'] ?? '');
        $comentario_padre_id = isset($input['comentario_padre_id']) ? intval($input['comentario_padre_id']) : null;
        
        // Validaciones
        if (empty($articulo_id)) {
            throw new Exception('ID de artículo requerido');
        }
        
        if (empty($nombre) || strlen($nombre) < 2) {
            throw new Exception('El nombre es requerido y debe tener al menos 2 caracteres');
        }
        
        if (empty($email) || !validateEmail($email)) {
            throw new Exception('Email inválido');
        }
        
        if (empty($comentario) || strlen($comentario) < 5) {
            throw new Exception('El comentario es requerido y debe tener al menos 5 caracteres');
        }
        
        // Verificar que el artículo existe
        $stmt = $db->prepare("SELECT id FROM articulos WHERE id = ? AND activo = 1");
        $stmt->execute([$articulo_id]);
        if (!$stmt->fetch()) {
            throw new Exception('Artículo no encontrado');
        }
        
        // Si es una respuesta, verificar que el comentario padre existe
        if ($comentario_padre_id) {
            $stmt = $db->prepare("SELECT id FROM comentarios WHERE id = ? AND articulo_id = ? AND activo = 1");
            $stmt->execute([$comentario_padre_id, $articulo_id]);
            if (!$stmt->fetch()) {
                throw new Exception('Comentario padre no encontrado');
            }
        }
        
        // Insertar comentario
        $stmt = $db->prepare("INSERT INTO comentarios (articulo_id, nombre, email, comentario, comentario_padre_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$articulo_id, $nombre, $email, $comentario, $comentario_padre_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Comentario publicado correctamente',
            'id' => $db->lastInsertId()
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

