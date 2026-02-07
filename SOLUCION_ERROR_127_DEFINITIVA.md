# Solución DEFINITIVA Error 127 en Render

## 🎯 Estrategia Cambiada

El error 127 puede ocurrir en dos momentos:
1. **Durante el BUILD** - Si algún comando del build falla
2. **Durante el START** - Si el comando de inicio no se encuentra

## ✅ Solución Aplicada

### Cambio Principal: Build en postinstall

He movido el build al script `postinstall` en `package.json`. Esto significa:

1. **Build Command**: Solo `npm install`
   - Esto ejecutará `npm install`
   - Que automáticamente ejecutará `postinstall`
   - Que ejecutará `npm run build`

2. **Start Command**: `npm start`
   - Usa el script estándar de npm
   - Más confiable que comandos directos

## ⚙️ Configuración Actual

### render.yaml
```yaml
services:
  - type: web
    name: legisaudit
    env: node
    nodeVersion: 20
    buildCommand: npm install  # ← Solo install, build se hace en postinstall
    startCommand: npm start     # ← Usa script de npm
```

### package.json
```json
{
  "scripts": {
    "postinstall": "npm run build || echo 'Build failed, continuing...'",
    "start": "node server.js"
  }
}
```

## 🚀 Pasos para Aplicar

1. **Haz commit y push:**
   ```bash
   git add render.yaml package.json
   git commit -m "Fix error 127: Move build to postinstall script"
   git push
   ```

2. **Si usas render.yaml**: Render detectará los cambios automáticamente

3. **Si NO usas render.yaml**, configura manualmente en Render Dashboard:
   - **Build Command**: `npm install`
   - **Start Command**: `npm start`
   - **Node Version**: 20

## 🔄 Si Aún Falla - Alternativas

### Alternativa 1: Build Explícito (Si postinstall no funciona)

**Build Command:**
```bash
npm install && npm run build
```

**Start Command:**
```bash
npm start
```

### Alternativa 2: Sin Build en Render (Solución Temporal)

1. **Compila localmente:**
   ```bash
   npm install
   npm run build
   ```

2. **Agrega `dist/` al repositorio:**
   ```bash
   git add dist/
   git commit -m "Add pre-built dist folder"
   git push
   ```

3. **En Render:**
   - Build Command: `npm install --production`
   - Start Command: `npm start`

4. **Después del deploy exitoso**, remueve `dist/` y vuelve a intentar el build normal.

### Alternativa 3: Verificación Completa

**Build Command:**
```bash
echo "Starting build..." && \
node --version && \
npm --version && \
npm install && \
npm run build && \
echo "Build completed successfully"
```

**Start Command:**
```bash
echo "Starting server..." && \
node --version && \
ls -la dist/ && \
npm start
```

## 🐛 Debug

Si el error persiste:

1. **Ve a los logs completos en Render Dashboard**
2. **Busca el mensaje exacto antes del "Exited with status 127"**
3. **Verifica:**
   - ¿Falla en `npm install`?
   - ¿Falla en `npm run build`?
   - ¿Falla en `npm start`?
   - ¿Qué comando específico está intentando ejecutar?

## 📋 Checklist Final

- [ ] `package.json` tiene `postinstall: "npm run build"`
- [ ] `render.yaml` tiene `buildCommand: npm install`
- [ ] `render.yaml` tiene `startCommand: npm start`
- [ ] Node version: 20 (explícitamente)
- [ ] Cambios commiteados y pusheados
- [ ] Revisar logs completos en Render

## ⚠️ Nota Importante

Si el build falla en `postinstall`, el script continuará (por el `|| echo`). Esto permite que veas el error específico en los logs sin que el proceso se detenga completamente.

Si necesitas que el build sea obligatorio, cambia:
```json
"postinstall": "npm run build"
```

Sin el `|| echo`, el proceso fallará si el build falla, pero verás el error específico.
