# Guía de Despliegue en Render

## ⚙️ Configuración en Render

### 1. Configuración del Servicio

- **Tipo**: Web Service
- **Nombre**: legisaudit (o el que prefieras)
- **Entorno**: Node
- **Versión de Node**: 20.x o 22.x

### 2. Comandos de Build y Start

En la configuración de Render, establece:

**Build Command:**
```bash
npm install && npm run build
```

**Start Command:**
```bash
npm start
```

### 3. Variables de Entorno

Agrega todas las variables desde `env.render.txt` usando "Add from .env"

### 4. Configuración Adicional

- **Root Directory**: Dejar vacío (raíz del proyecto)
- **Auto-Deploy**: Yes (si quieres despliegue automático desde Git)

## 🔧 Solución de Problemas

### Error 127: "Command not found"

Este error generalmente ocurre cuando:
1. El comando de build no se encuentra
2. Las dependencias no se instalan correctamente
3. El script usa comandos que no están disponibles

**Solución**: Usa los comandos simplificados:
- Build: `npm install && npm run build`
- Start: `npm start`

### Error: "Cannot find module"

Si ves errores de módulos no encontrados:
1. Verifica que todas las dependencias estén en `package.json`
2. Asegúrate de que `npm install` se ejecute antes del build
3. Verifica que el archivo `server.js` exista en la raíz

### Error de Base de Datos

Si hay errores de conexión a la base de datos:
1. Verifica que `DB_HOST` no sea `localhost` (usa el hostname real)
2. Asegúrate de que el servidor MySQL permita conexiones externas
3. Verifica las credenciales en las variables de entorno

## 📝 Checklist Pre-Despliegue

- [ ] Variables de entorno configuradas
- [ ] `DB_HOST` apunta al servidor MySQL correcto (no localhost)
- [ ] `SESSION_SECRET` tiene una clave aleatoria segura
- [ ] Build command: `npm install && npm run build`
- [ ] Start command: `npm start`
- [ ] Node version: 20.x o 22.x
- [ ] Repositorio conectado correctamente

## 🚀 Después del Despliegue

1. Verifica que el servicio esté "Live"
2. Prueba la URL proporcionada por Render
3. Revisa los logs si hay errores
4. Prueba el login de administrador
5. Verifica que las APIs funcionen
