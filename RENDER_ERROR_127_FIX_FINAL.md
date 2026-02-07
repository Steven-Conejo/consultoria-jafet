# Solución Final Error 127 en Render

## 🔍 Cambios Aplicados

1. **`render.yaml`**: Cambiado `startCommand` de `npm start` a `node server.js` directamente
2. **`.nvmrc`**: Creado archivo para especificar versión de Node (20)
3. **`start.sh`**: Script alternativo de inicio (opcional)

## ⚙️ Configuración Actual

### render.yaml
```yaml
services:
  - type: web
    name: legisaudit
    env: node
    nodeVersion: 20
    buildCommand: npm install && npm run build
    startCommand: node server.js  # ← Cambiado directamente a node
```

## 🚀 Pasos para Aplicar

1. **Haz commit de los cambios:**
   ```bash
   git add render.yaml .nvmrc package.json start.sh
   git commit -m "Fix error 127: Use direct node command and add .nvmrc"
   git push
   ```

2. **En Render Dashboard** (si no usas render.yaml):
   - Ve a Settings → Build & Deploy
   - **Build Command**: `npm install && npm run build`
   - **Start Command**: `node server.js` (NO `npm start`)
   - **Node Version**: 20 (explícitamente seleccionado)

## 🔄 Si Aún Falla

### Opción 1: Verificar Build
El error 127 podría estar ocurriendo en el BUILD, no en el START. Prueba:

**Build Command:**
```bash
npm install && npm run build 2>&1 || exit 1
```

Esto mostrará errores del build si los hay.

### Opción 2: Build Separado
**Build Command:**
```bash
npm install
```

**Start Command:**
```bash
npm run build && node server.js
```

### Opción 3: Usar Script Shell
**Start Command:**
```bash
bash start.sh
```

O directamente:
```bash
chmod +x start.sh && ./start.sh
```

## 🐛 Debug Detallado

Si el error persiste, agrega esto al **Build Command** para ver dónde falla:

```bash
echo "=== DEBUG INFO ===" && \
node --version && \
npm --version && \
pwd && \
ls -la && \
echo "=== INSTALLING ===" && \
npm install && \
echo "=== BUILDING ===" && \
npm run build && \
echo "=== BUILD SUCCESS ==="
```

Y en **Start Command**:
```bash
echo "=== STARTING ===" && \
node --version && \
ls -la dist/ && \
node server.js
```

## 📋 Checklist Final

- [ ] `render.yaml` actualizado con `startCommand: node server.js`
- [ ] `.nvmrc` creado con versión 20
- [ ] Build Command: `npm install && npm run build`
- [ ] Start Command: `node server.js` (NO npm start)
- [ ] Node version en Render: **20** (explícitamente)
- [ ] Cambios commiteados y pusheados

## ⚠️ Nota Importante

El error 127 puede ocurrir en dos momentos:
1. **Durante el BUILD** - Si `npm run build` falla
2. **Durante el START** - Si `node server.js` no se encuentra

Revisa los logs completos en Render para ver en qué paso exacto falla.
