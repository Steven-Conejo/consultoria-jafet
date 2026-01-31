# Guía de Instalación - Sistema de Blog y Administración

## Requisitos Previos

- PHP 7.4 o superior
- MySQL 5.7 o superior (o MariaDB)
- Servidor web (Apache o Nginx)
- Extensiones PHP requeridas:
  - PDO
  - PDO_MySQL
  - session

## Pasos de Instalación

### 1. Configurar la Base de Datos

Edita el archivo `api/db_config.php` con tus credenciales de base de datos:

```php
return [
    'host' => 'localhost',
    'dbname' => 'consultoria_jafet',
    'username' => 'root',      // Cambia por tu usuario de MySQL
    'password' => '',          // Cambia por tu contraseña de MySQL
    'charset' => 'utf8mb4'
];
```

### 2. Crear la Base de Datos

Tienes dos opciones:

#### Opción A: Usando el script SQL manualmente

1. Abre phpMyAdmin o tu cliente MySQL preferido
2. Importa el archivo `database.sql`
3. O ejecuta manualmente:
```sql
CREATE DATABASE consultoria_jafet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE consultoria_jafet;
-- Luego ejecuta el resto del contenido de database.sql
```

#### Opción B: Usando el script de instalación automática

1. Accede a: `http://localhost/api/install.php`
2. El script creará automáticamente la base de datos y el usuario por defecto

### 3. Usuario por Defecto

Después de la instalación, puedes iniciar sesión con:

- **Usuario:** Steven
- **Contraseña:** Dario

**⚠️ IMPORTANTE:** Cambia la contraseña después del primer inicio de sesión por seguridad.

### 4. Permisos de Carpetas

Asegúrate de que las siguientes carpetas tengan permisos de escritura:

```bash
chmod 755 uploads/
chmod 755 uploads/blog/
```

O en Windows, asegúrate de que la carpeta tenga permisos de escritura.

### 5. Acceso al Sistema

- **Blog público:** `http://localhost:5173/blog`
- **Panel de administración:** `http://localhost:5173/admin`
  - Usuario: Steven
  - Contraseña: Dario

## Estructura de Archivos Creados

### Base de Datos
- `database.sql` - Script SQL para crear la base de datos

### Configuración
- `api/db_config.php` - Configuración de conexión a BD
- `api/db_connection.php` - Clase de conexión a BD
- `api/install.php` - Script de instalación automática

### APIs de Administración
- `api/admin/auth.php` - Autenticación de administradores
- `api/admin/users.php` - Gestión de usuarios
- `api/admin/articles.php` - Gestión de artículos
- `api/admin/comments.php` - Gestión de comentarios
- `api/admin/upload_image.php` - Subida de imágenes

### APIs Públicas del Blog
- `api/blog/articles.php` - Obtener artículos públicos
- `api/blog/comments.php` - Comentarios públicos

### Vistas Vue
- `src/views/BlogView.vue` - Vista principal del blog
- `src/views/BlogArticleView.vue` - Vista de artículo individual
- `src/views/AdminLoginView.vue` - Login de administración
- `src/views/AdminDashboardView.vue` - Panel de administración

## Funcionalidades

### Panel de Administración

1. **Gestión de Artículos:**
   - Crear nuevos artículos con título, imagen y texto
   - Editar artículos existentes
   - Eliminar artículos
   - Subir imágenes para los artículos

2. **Gestión de Usuarios:**
   - Crear nuevos usuarios administradores
   - Eliminar usuarios (no puedes eliminar tu propio usuario)

3. **Gestión de Comentarios:**
   - Ver comentarios de los artículos
   - Eliminar comentarios inapropiados

### Blog Público

1. **Listado de Artículos:**
   - Vista de resumen de todos los artículos
   - Mensaje atractivo cuando no hay artículos

2. **Vista de Artículo:**
   - Visualización completa del artículo
   - Sistema de comentarios
   - Respuestas a comentarios (comentarios anidados)

## Solución de Problemas

### Error de conexión a la base de datos

1. Verifica que MySQL esté corriendo
2. Revisa las credenciales en `api/db_config.php`
3. Asegúrate de que la base de datos existe

### Error al subir imágenes

1. Verifica que la carpeta `uploads/blog/` existe
2. Verifica los permisos de escritura
3. Revisa el tamaño máximo de archivo en PHP (php.ini)

### Error de sesión

1. Verifica que la extensión `session` esté habilitada en PHP
2. Revisa los permisos de la carpeta de sesiones de PHP

## Seguridad

- Las contraseñas se almacenan usando `password_hash()` de PHP
- Las sesiones se validan en cada petición de administración
- Los inputs se sanitizan antes de guardarse en la BD
- Las imágenes se validan antes de subirse

## Notas Adicionales

- El sistema genera automáticamente un resumen de los artículos (primeros 200 caracteres)
- Los comentarios pueden tener respuestas anidadas
- Las imágenes se guardan en `uploads/blog/`
- El sistema cuenta las vistas de cada artículo

