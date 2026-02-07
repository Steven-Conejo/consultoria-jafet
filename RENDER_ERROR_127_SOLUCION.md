# Solución Error 127 en Render - Actualizada

## 🔍 El Error 127

El error 127 significa "Command not found" en sistemas Unix/Linux. En Render, esto generalmente ocurre cuando:

1. **Node.js no está disponible en el PATH durante el build**
2. **El comando de build falla silenciosamente**
3. **Falta alguna dependencia crítica**

## ✅ Solución Aplicada

### 1. Actualización de `render.yaml`

He actualizado el archivo `render.yaml` con:
- `nodeVersion: 20` - Especifica explícitamente la versión de Node
- `buildCommand: npm ci && npm run build` - Usa `npm ci` para instalación limpia

### 2. Scripts Adicionales en `package.json`

Se agregaron scripts específicos para Render:
- `render-build`: Para debugging del build
- `render-start`: Para debugging del start

## ⚙️ Configuración en Render Dashboard

### Opción 1: Usar render.yaml (Recomendado)

Si usas `render.yaml`, los cambios ya están aplicados. Solo asegúrate de:
- Tener el archivo `render.yaml` en la raíz del repositorio
- Render esté configurado para usar el archivo YAML

### Opción 2: Configuración Manual en Dashboard

Si no usas `render.yaml`, configura manualmente:

**Build Command:**
```bash
npm ci && npm run build
```

**Start Command:**
```bash
npm start
```

**Node Version:**
- Selecciona explícitamente **Node 20** (no "Latest")

## 🔄 Alternativas si Aún Falla

### Alternativa 1: Build con Verificación

**Build Command:**
```bash
node --version && npm --version && npm ci && npm run build
```

Esto mostrará las versiones y dónde falla exactamente.

### Alternativa 2: Build Separado

**Build Command:**
```bash
npm ci
```

**Start Command:**
```bash
npm run build && npm start
```

### Alternativa 3: Build Local y Deploy

Si el build sigue fallando en Render:

1. **Localmente:**
   ```bash
   npm install
   npm run build
   ```

2. **En Render:**
   - Build Command: `npm install --production`
   - Start Command: `npm start`
   - **IMPORTANTE**: Agrega `dist/` al repositorio (temporalmente)

3. **Después del deploy exitoso**, puedes remover `dist/` del repo y volver a intentar el build en Render.

## 📋 Checklist

- [ ] `render.yaml` actualizado con `nodeVersion: 20`
- [ ] Build Command: `npm ci && npm run build`
- [ ] Start Command: `npm start`
- [ ] Node version en Render: **20** (explícitamente seleccionado)
- [ ] Variables de entorno configuradas
- [ ] `package.json` está en la raíz del repo
- [ ] `server.js` existe en la raíz

## 🐛 Debug Avanzado

Si el error persiste, revisa los logs completos en Render:

1. **Ve a los logs del build** en Render Dashboard
2. **Busca el error específico** antes del "Exited with status 127"
3. **Verifica:**
   - ¿Qué comando está intentando ejecutar?
   - ¿Hay algún error de módulo no encontrado?
   - ¿Falla en `npm ci` o en `npm run build`?

### Comandos de Debug en Build Command

```bash
which node
which npm
node --version
npm --version
pwd
ls -la
npm ci
npm run build
```

Esto te dará información detallada de dónde falla exactamente.

## 🎯 Solución Rápida (Si Urgente)

Si necesitas desplegar urgentemente:

1. **Build local:**
   ```bash
   npm install
   npm run build
   ```

2. **Commit y push `dist/`:**
   ```bash
   git add dist/
   git commit -m "Add dist for Render deployment"
   git push
   ```

3. **En Render:**
   - Build Command: `npm install --production`
   - Start Command: `npm start`

4. **Después**, intenta el build normal nuevamente.
