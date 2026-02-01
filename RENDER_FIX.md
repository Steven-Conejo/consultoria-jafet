# Solución Error 127 en Render

## 🔍 Diagnóstico

El error 127 significa "Command not found". Esto puede ocurrir por:

1. **Render no detecta Node.js correctamente**
2. **El comando de build falla en algún paso**
3. **Falta alguna dependencia o herramienta**

## ✅ Soluciones a Probar

### Opción 1: Build Command Simplificado (RECOMENDADO)

En Render, en la sección "Build Command", usa **SOLO**:

```bash
npm install
```

Y en "Start Command":

```bash
npm start
```

Luego, en el script `postinstall` de `package.json`, el build se ejecutará automáticamente después de `npm install`.

### Opción 2: Build Command Completo

Si la Opción 1 no funciona, usa:

```bash
npm ci && npm run build
```

### Opción 3: Build Command con Verificación

```bash
which node && which npm && npm --version && npm install && npm run build
```

## ⚙️ Configuración en Render

### Settings Básicos:

- **Environment**: Node
- **Node Version**: 20 (o 22)
- **Build Command**: `npm install` (o `npm ci && npm run build`)
- **Start Command**: `npm start`
- **Root Directory**: (dejar vacío)

### Verificaciones:

1. ✅ Asegúrate de que `package.json` esté en la raíz del repositorio
2. ✅ Verifica que `server.js` exista en la raíz
3. ✅ Confirma que todas las dependencias estén en `package.json`
4. ✅ Revisa los logs completos en Render para ver dónde falla exactamente

## 🐛 Debug

Si sigue fallando, revisa los logs completos en Render y busca:

- ¿En qué línea exacta falla?
- ¿Qué comando está intentando ejecutar?
- ¿Hay algún error antes del 127?

## 📝 Alternativa: Build Manual

Si nada funciona, puedes:

1. Compilar localmente: `npm run build`
2. Subir la carpeta `dist/` al repositorio (temporalmente)
3. Usar solo `npm install` como build command
4. El servidor servirá los archivos ya compilados
