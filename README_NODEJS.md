# LegisAudit - Versión Node.js

Este proyecto ha sido convertido para funcionar completamente con Node.js, manteniendo Vue.js como frontend.

## 🚀 Inicio Rápido

### 1. Instalar dependencias

```bash
npm install
```

### 2. Configurar base de datos

**Opción A: Variables de entorno (Recomendado)**

Crea un archivo `.env` en la raíz del proyecto:

```env
# Base de Datos
DB_HOST=localhost
DB_NAME=zssrpjcp_consultoria_jafet
DB_USER=zssrpjcp_Dario
DB_PASSWORD=MasterYDario
DB_CHARSET=utf8mb4

# SMTP
SMTP_HOST=mail.legisaudit-abogados.cu.ma
SMTP_PORT=587
SMTP_USER=servicioprofesionalabogadojcgy@legisaudit-abogados.cu.ma
SMTP_PASSWORD=MasterYDario2803
SMTP_FROM_EMAIL=servicioprofesionalabogadojcgy@legisaudit-abogados.cu.ma
SMTP_FROM_NAME=LegisAudit - Plataforma Legal
SMTP_TO_EMAIL=sevicioprofesionalabogadojcgy@gmail.com
SMTP_ENCRYPTION=tls
SMTP_TIMEOUT=30

# Sesión
SESSION_SECRET=tu-secret-key-super-segura-aqui

# Servidor
PORT=3000
NODE_ENV=development
```

**Opción B: Archivos de configuración**

Copia y edita:
- `server/api/db_config.js.example` → `server/api/db_config.js`
- `server/api/smtp_config.js.example` → `server/api/smtp_config.js`

### 3. Compilar frontend

```bash
npm run build
```

### 4. Iniciar servidor

```bash
npm start
```

El servidor estará disponible en `http://localhost:3000`

## 📜 Scripts Disponibles

- `npm run dev` - Modo desarrollo (solo frontend con Vite)
- `npm run dev:server` - Modo desarrollo (solo servidor Node.js)
- `npm run dev:full` - Modo desarrollo completo (frontend + servidor)
- `npm run build` - Compilar frontend para producción
- `npm start` - Iniciar servidor Node.js (requiere build previo)
- `npm run preview` - Vista previa del frontend compilado

## 🏗️ Estructura del Proyecto

```
Consultoria_Jafet/
├── server/              # Backend Node.js
│   ├── config/         # Configuración (DB, SMTP)
│   ├── routes/         # Rutas de API
│   ├── middleware/     # Middleware (auth, etc.)
│   ├── utils/          # Utilidades (mailer, etc.)
│   └── api/           # Archivos de configuración
├── src/               # Frontend Vue.js
├── dist/              # Frontend compilado (generado)
├── server.js          # Servidor principal
└── package.json       # Dependencias
```

## 🔄 Migración desde PHP

### Cambios principales:

1. **Backend**: PHP → Node.js (Express)
2. **Base de datos**: PDO → mysql2
3. **Sesiones**: PHP sessions → express-session
4. **Emails**: PHP mail()/SMTP → nodemailer
5. **Archivos**: PHP upload → multer

### APIs convertidas:

- ✅ `/api/contact` - Formulario de contacto
- ✅ `/api/admin/auth` - Autenticación
- ✅ `/api/blog/articles` - Artículos públicos
- ✅ `/api/blog/comments` - Comentarios
- ✅ `/api/admin/articles` - Gestión de artículos
- ✅ `/api/admin/comments` - Gestión de comentarios
- ✅ `/api/admin/users` - Gestión de usuarios
- ✅ `/api/admin/upload_image` - Subida de imágenes
- ✅ `/api/donation` - Donaciones
- ✅ `/api/upload` - Subida de archivos

## 🔐 Seguridad

- Las credenciales están en `.gitignore`
- Usa variables de entorno en producción
- Las sesiones están protegidas con httpOnly cookies
- CORS configurado correctamente

## 📝 Notas

- El frontend Vue.js se compila y se sirve como archivos estáticos
- El servidor Node.js maneja todas las APIs
- Compatible con la misma base de datos MySQL
- Mantiene la misma funcionalidad que la versión PHP

## 🐛 Solución de Problemas

### Error de conexión a base de datos
- Verifica las credenciales en `.env` o `server/api/db_config.js`
- Asegúrate de que MySQL esté corriendo

### Error al enviar emails
- Verifica la configuración SMTP
- El sistema intenta SMTP primero, luego sendmail como fallback

### Puerto en uso
- Cambia el puerto en `.env`: `PORT=3001`
