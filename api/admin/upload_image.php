<?php
/**
 * API para Subir Imágenes de Artículos
 */

require_once __DIR__ . '/../cors_headers.php';

session_start();

// Verificar autenticación
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error al subir la imagen');
    }
    
    $file = $_FILES['image'];
    
    // Validar tipo de archivo
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Tipo de archivo no permitido. Solo se aceptan imágenes (JPG, PNG, GIF, WEBP)');
    }
    
    // Validar tamaño (5MB máximo)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        throw new Exception('La imagen es demasiado grande. Tamaño máximo: 5MB');
    }
    
    // Crear directorio de imágenes si no existe
    $uploadsDir = __DIR__ . '/../../uploads/blog/';
    if (!file_exists($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }
    
    // Generar nombre único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueName = uniqid() . '_' . time() . '.' . $extension;
    $targetPath = $uploadsDir . $uniqueName;
    
    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Error al guardar la imagen');
    }
    
    // Retornar URL relativa
    $url = '/uploads/blog/' . $uniqueName;
    
    echo json_encode([
        'success' => true,
        'url' => $url,
        'message' => 'Imagen subida correctamente'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

