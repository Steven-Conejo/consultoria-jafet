<?php
/**
 * API de Gestión de Comentarios
 */

require_once __DIR__ . '/../cors_headers.php';
require_once __DIR__ . '/../db_connection.php';

session_start();

// Verificar autenticación para eliminar
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && !isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
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
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Eliminar comentario
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            throw new Exception('ID de comentario requerido');
        }
        
        $stmt = $db->prepare("DELETE FROM comentarios WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Comentario eliminado correctamente'
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

