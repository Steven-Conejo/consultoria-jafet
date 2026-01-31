-- Script para crear el usuario por defecto manualmente
-- Ejecuta esto en phpMyAdmin después de crear las tablas

USE consultoria_jafet;

-- Crear usuario Steven con contraseña Dario
-- Nota: Este hash es para la contraseña "Dario"
-- Si necesitas generar uno nuevo, usa: SELECT PASSWORD_HASH('Dario', PASSWORD_DEFAULT) en PHP

INSERT INTO usuarios (nombre_completo, usuario, password) VALUES 
('Steven', 'Steven', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE password = VALUES(password);

