# Guía de Despliegue a Producción - LegisAudit

## 🚀 Preparación del Proyecto para Producción

### ✅ Estado Actual de Configuración

- ✅ Base de datos configurada: `zssrpjcp_consultoria_jafet`
- ✅ Usuario BD: `Dario`
- ✅ Contraseña BD: `MasterYDario`
- ✅ Archivo de prueba SMTP protegido
- ✅ Configuración de API con rutas relativas

---

## 📋 PASO 1: Compilar el Proyecto Vue.js

**En tu máquina local**, ejecuta los siguientes comandos:

```bash
# Asegúrate de estar en la carpeta del proyecto
cd "C:\Users\PC\Downloads\Respaldo final del Trabajo de Master\Consultoria_Jafet"

# Instalar dependencias (si aún no lo has hecho)
npm install

# Compilar para producción
npm run build
```

Este comando generará la carpeta `dist/` con los archivos estáticos del frontend optimizados para producción.

---

## 📋 PASO 2: Estructura de Archivos a Subir

Necesitas subir la siguiente estructura a `public_html` en GoogieHost:

```
public_html/
├── api/                    # Carpeta completa con todos los archivos PHP
│   ├── admin/
│   ├── blog/
│   ├── .htaccess
│   ├── cors_headers.php
│   ├── db_config.php      # ✅ Ya configurado con credenciales de producción
│   ├── db_connection.php
│   └── ... (todos los archivos PHP)
├── uploads/               # Carpetas para archivos (se crearán automáticamente si no existen)
│   └── blog/
├── quarantine/            # Carpeta para archivos en cuarentena
├── logs/                  # Carpeta para logs
├── public/                # Archivos estáticos (favicon, logo)
│   ├── favicon.ico
│   └── logo.png
├── dist/                  # ⚠️ IMPORTANTE: Contenido de la carpeta dist/ generada por npm run build
│   ├── index.html
│   ├── assets/
│   └── ...
├── .htaccess              # Archivo de configuración de Apache
└── database.sql           # (Opcional, para referencia)
```

---

## 📋 PASO 3: Subir Archivos al Servidor

### Opción A: Usando el File Manager de GoogieHost

1. **Accede al File Manager:**
   - URL: https://client.googiehost.com/clientarea.php?action=productdetails&id=534527&mg-page=FileManager
   - O desde el panel de control de GoogieHost

2. **Navega a la carpeta `public_html`:**
   - Esta es la carpeta raíz donde se sirven los archivos web

3. **Elimina el contenido existente:**
   - Elimina el archivo `index.html` que muestra "Something amazing will be constructed here..."

4. **Sube los archivos en este orden:**
   
   **a) Primero sube la carpeta `api/` completa:**
   - En el File Manager, haz clic en "Subir" o el botón de upload
   - Selecciona toda la carpeta `api/` de tu proyecto local
   - Asegúrate de que se mantenga la estructura de subcarpetas (`admin/`, `blog/`)

   **b) Sube los archivos de la carpeta `dist/`:**
   - ⚠️ IMPORTANTE: NO subas la carpeta `dist/`, sino su CONTENIDO
   - Dentro de `dist/` encontrarás:
     - `index.html`
     - Carpeta `assets/` con archivos JS, CSS, etc.
   - Sube estos archivos directamente a `public_html/` (nivel raíz)
   
   **c) Sube los archivos estáticos:**
   - Sube el archivo `.htaccess` de la raíz del proyecto
   - Sube la carpeta `public/` (o solo `favicon.ico` y `logo.png` si prefieres)

   **d) Crea las carpetas necesarias:**
   - Crea las carpetas `uploads/`, `quarantine/`, y `logs/` si no existen
   - Estas carpetas se crearán automáticamente al usar la aplicación, pero es mejor crearlas antes
   - **Configura permisos 755** para estas carpetas (escribible por el servidor)

---

## 📋 PASO 4: Configuración de Permisos

En el File Manager de GoogieHost, establece los siguientes permisos:

```
uploads/        → 755 (o 777 si 755 no funciona)
uploads/blog/   → 755 (o 777 si 755 no funciona)
quarantine/     → 755 (o 777 si 755 no funciona)
logs/           → 755 (o 777 si 755 no funciona)
```

**Cómo cambiar permisos en el File Manager:**
1. Haz clic derecho en la carpeta
2. Selecciona "Cambiar permisos" o "Change Permissions"
3. Marca los permisos de escritura para el propietario y el grupo

---

## 📋 PASO 5: Verificar la Base de Datos

1. **Accede a phpMyAdmin:**
   - URL: `https://cloud3.googiehost.com/phpMyAdmin/`
   - O desde el panel de control de GoogieHost

2. **Verifica que la base de datos existe:**
   - Debe existir: `zssrpjcp_consultoria_jafet`
   - Debe tener las tablas: `articulos`, `comentarios`, `usuarios`

3. **Si las tablas no existen, importa el archivo `database.sql`:**
   - En phpMyAdmin, selecciona la base de datos `zssrpjcp_consultoria_jafet`
   - Ve a la pestaña "Importar"
   - Selecciona el archivo `database.sql` de tu proyecto
   - Haz clic en "Continuar"

4. **Verifica el usuario de la base de datos:**
   - El usuario `Dario` debe tener permisos completos sobre `zssrpjcp_consultoria_jafet`
   - Si no tienes un usuario administrador creado, créalo ejecutando el script SQL o manualmente

---

## 📋 PASO 6: Verificar Configuración del Servidor

### Verificar que PHP esté configurado correctamente:

1. Crea un archivo temporal `info.php` en `public_html/` con este contenido:
```php
<?php phpinfo(); ?>
```

2. Accede a: `http://legisaudit-abogados.cu.ma/info.php`
3. Verifica que PHP esté instalado y funcionando
4. **IMPORTANTE:** Elimina este archivo después de verificar

### Verificaciones importantes:

- ✅ PHP versión 7.4 o superior
- ✅ Extensiones habilitadas: `PDO`, `PDO_MySQL`, `session`, `mbstring`, `openssl`
- ✅ `mod_rewrite` habilitado (para el `.htaccess`)
- ✅ Límites de upload: `upload_max_filesize` y `post_max_size` (mínimo 10M)

---

## 📋 PASO 7: Probar la Aplicación

1. **Accede al dominio:**
   - URL: `http://legisaudit-abogados.cu.ma/`
   - Debe cargar la página principal del sitio

2. **Prueba las funcionalidades principales:**
   - ✅ Navegación entre páginas
   - ✅ Formulario de contacto: `http://legisaudit-abogados.cu.ma/contact`
   - ✅ Blog: `http://legisaudit-abogados.cu.ma/blog`
   - ✅ Login de administración: `http://legisaudit-abogados.cu.ma/admin`
     - Usuario: `Steven`
     - Contraseña: `Dario`

3. **Verifica las APIs:**
   - Abre la consola del navegador (F12)
   - Verifica que no haya errores 404 o 500
   - Las peticiones a `/api/*` deben funcionar correctamente

---

## 📋 PASO 8: Configuración de Dominio y SSL (Opcional pero Recomendado)

Si tu hosting permite SSL gratuito (Let's Encrypt):

1. **Habilita SSL/HTTPS:**
   - Desde el panel de control de GoogieHost
   - Busca la opción "SSL" o "Let's Encrypt"
   - Activa SSL para `legisaudit-abogados.cu.ma`

2. **Redireccionar HTTP a HTTPS (Opcional):**
   - Si necesitas forzar HTTPS, puedes agregar esto al `.htaccess`:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 🔒 Seguridad - Checklist Final

Antes de considerar el despliegue completo, verifica:

- ✅ `api/test_smtp.php` está protegido (solo localhost)
- ✅ `api/db_config.php` tiene las credenciales correctas de producción
- ✅ Carpetas `uploads/`, `quarantine/`, `logs/` tienen permisos correctos
- ✅ Archivo `.htaccess` está en su lugar
- ✅ No hay archivos de desarrollo expuestos (como `node_modules/`, archivos `.md`)
- ✅ La base de datos está protegida con contraseña segura

---

## 🐛 Solución de Problemas Comunes

### Error: "404 Not Found" en las rutas
**Solución:** Verifica que el archivo `.htaccess` esté en `public_html/` y que `mod_rewrite` esté habilitado.

### Error: "500 Internal Server Error"
**Solución:** 
- Verifica los permisos de las carpetas (755 o 777)
- Revisa los logs de PHP en el panel de control
- Verifica que las extensiones PHP necesarias estén habilitadas

### Error: "Cannot connect to database"
**Solución:**
- Verifica las credenciales en `api/db_config.php`
- Verifica que el usuario `Dario` tenga permisos sobre la base de datos
- Verifica que el host sea `localhost` (puede ser diferente según el hosting)

### Error: "Permission denied" al subir archivos
**Solución:**
- Verifica permisos 755 o 777 en la carpeta `uploads/`
- Verifica que el usuario del servidor web tenga permisos de escritura

### La aplicación carga pero las imágenes no aparecen
**Solución:**
- Verifica que la carpeta `public/` esté subida correctamente
- Verifica las rutas de las imágenes en el código (deben ser relativas)

---

## 📞 Contacto y Soporte

Si encuentras problemas durante el despliegue:
1. Revisa los logs en `logs/` (si están accesibles)
2. Revisa los logs del servidor en el panel de GoogieHost
3. Verifica la consola del navegador para errores JavaScript

---

## ✅ Lista de Verificación Final

- [ ] Proyecto compilado (`npm run build` ejecutado)
- [ ] Carpeta `dist/` generada correctamente
- [ ] Archivos PHP subidos a `public_html/api/`
- [ ] Contenido de `dist/` subido a `public_html/`
- [ ] Archivo `.htaccess` en `public_html/`
- [ ] Carpetas `uploads/`, `quarantine/`, `logs/` creadas con permisos 755
- [ ] Base de datos `zssrpjcp_consultoria_jafet` existe y tiene las tablas
- [ ] Configuración de BD en `api/db_config.php` correcta
- [ ] Sitio accesible en `http://legisaudit-abogados.cu.ma/`
- [ ] Login de administración funciona
- [ ] Formulario de contacto funciona
- [ ] Blog carga correctamente

---

**¡Listo! Tu aplicación debería estar funcionando en producción. 🎉**
