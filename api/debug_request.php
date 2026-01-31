<?php
/**
 * DEBUG: inspecciona lo que realmente está llegando en la petición.
 * Borra este archivo cuando termines de diagnosticar.
 */

require_once __DIR__ . '/cors_headers.php';

$rawBody = file_get_contents('php://input');
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

echo json_encode([
    'method' => $_SERVER['REQUEST_METHOD'] ?? null,
    'content_type' => $contentType,
    'raw_len' => strlen($rawBody),
    'raw_preview' => substr($rawBody, 0, 200),
    '_POST' => $_POST,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


