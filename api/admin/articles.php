<?php
/**
 * API de Gestión de Artículos del Blog
 */

require_once __DIR__ . '/../cors_headers.php';
require_once __DIR__ . '/../db_connection.php';

session_start();

// Verificar autenticación para métodos que modifican datos
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    $db = getDb();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Listar artículos
        $id = $_GET['id'] ?? null;
        $public = isset($_GET['public']) && $_GET['public'] == '1';
        
        if ($id) {
            // Obtener un artículo específico
            $stmt = $db->prepare("SELECT * FROM articulos WHERE id = ?");
            $stmt->execute([$id]);
            $articulo = $stmt->fetch();
            
            if (!$articulo) {
                throw new Exception('Artículo no encontrado');
            }
            
            // Incrementar vistas si es petición pública
            if ($public) {
                $stmt = $db->prepare("UPDATE articulos SET vistas = vistas + 1 WHERE id = ?");
                $stmt->execute([$id]);
            }
            
            echo json_encode([
                'success' => true,
                'articulo' => $articulo
            ]);
        } else {
            // Listar todos los artículos
            $where = $public ? "WHERE activo = 1" : "";
            $stmt = $db->query("SELECT id, titulo, imagen_principal, resumen, texto, fecha_creacion, fecha_actualizacion, vistas FROM articulos $where ORDER BY fecha_creacion DESC");
            $articulos = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'articulos' => $articulos
            ]);
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Crear o actualizar artículo
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Datos inválidos');
        }
        
        $id = $input['id'] ?? null;
        $titulo = trim($input['titulo'] ?? '');
        $imagen_principal = trim($input['imagen_principal'] ?? '');
        $texto = trim($input['texto'] ?? '');
        
        if (empty($titulo) || empty($texto)) {
            throw new Exception('Título y texto son requeridos');
        }
        
        // Generar resumen automático (primeros 200 caracteres)
        $resumen = mb_substr(strip_tags($texto), 0, 200) . '...';
        
        if ($id) {
            // Verificar que el artículo existe antes de actualizar
            $stmt = $db->prepare("SELECT id, vistas FROM articulos WHERE id = ?");
            $stmt->execute([$id]);
            $existingArticle = $stmt->fetch();
            
            if (!$existingArticle) {
                throw new Exception('Artículo no encontrado');
            }
            
            // Actualizar artículo existente preservando vistas y fecha_creacion
            // NO actualizamos vistas, fecha_creacion, activo - solo los campos editables
            $stmt = $db->prepare("UPDATE articulos SET titulo = ?, imagen_principal = ?, texto = ?, resumen = ?, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$titulo, $imagen_principal, $texto, $resumen, $id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Artículo actualizado correctamente'
            ]);
        } else {
            // Crear nuevo artículo
            $stmt = $db->prepare("INSERT INTO articulos (titulo, imagen_principal, texto, resumen) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titulo, $imagen_principal, $texto, $resumen]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Artículo creado correctamente',
                'id' => $db->lastInsertId()
            ]);
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Eliminar artículo
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            throw new Exception('ID de artículo requerido');
        }
        
        $stmt = $db->prepare("DELETE FROM articulos WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Artículo eliminado correctamente'
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

