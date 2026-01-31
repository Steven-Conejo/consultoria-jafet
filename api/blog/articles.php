<?php
/**
 * API Pública de Artículos del Blog
 */

require_once __DIR__ . '/../cors_headers.php';
require_once __DIR__ . '/../db_connection.php';

try {
    $db = getDb();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            // Obtener un artículo específico
            $stmt = $db->prepare("SELECT * FROM articulos WHERE id = ? AND activo = 1");
            $stmt->execute([$id]);
            $articulo = $stmt->fetch();
            
            if (!$articulo) {
                throw new Exception('Artículo no encontrado');
            }
            
            // Incrementar vistas
            $stmt = $db->prepare("UPDATE articulos SET vistas = vistas + 1 WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode([
                'success' => true,
                'articulo' => $articulo
            ]);
        } else {
            // Listar todos los artículos activos
            $stmt = $db->query("SELECT id, titulo, imagen_principal, resumen, fecha_creacion, vistas FROM articulos WHERE activo = 1 ORDER BY fecha_creacion DESC");
            $articulos = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'articulos' => $articulos
            ]);
        }
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

