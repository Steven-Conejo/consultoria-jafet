# Cómo Encontrar la Carpeta public_html en GoogieHost

## 📁 Ubicación de los Archivos del Sitio Web

En GoogieHost (CloudLinux), los archivos del sitio web están dentro de la carpeta **`domains`**.

### Paso 1: Entrar a la carpeta `domains`

1. En el File Manager, haz doble clic en la carpeta **`domains`**
2. Dentro encontrarás una o más carpetas, una por cada dominio configurado

### Paso 2: Buscar tu dominio

Dentro de `domains/`, busca una carpeta con el nombre de tu dominio o similar:
- `legisaudit-abogados.cu.ma`
- `legisaudit-abogados.cu.ma`
- O algún nombre similar

### Paso 3: Entrar al dominio

1. Haz doble clic en la carpeta de tu dominio
2. Dentro encontrarás la carpeta **`public_html`** ← **Esta es donde debes subir los archivos**

## 📂 Estructura Esperada

```
/ (raíz del hosting)
└── domains/
    └── legisaudit-abogados.cu.ma/  (o nombre similar)
        └── public_html/  ← AQUÍ SUBES TUS ARCHIVOS
```

## 🔍 Si no encuentras public_html

### Opción A: Crear la carpeta public_html

Si no existe la carpeta `public_html`, créala:

1. Dentro de `domains/tu-dominio/`, haz clic en el botón **"Crear directorio"** o **"New Directory"**
2. Nombre: `public_html`
3. Permisos: `755`

### Opción B: Verificar la configuración del dominio

1. Ve al panel de control de GoogieHost
2. Busca la sección de "Dominios" o "Domains"
3. Verifica dónde está apuntando tu dominio `legisaudit-abogados.cu.ma`
4. Puede que esté configurado para usar una carpeta diferente

## ✅ Una vez encontrada public_html

Cuando encuentres la carpeta `public_html`, sigue la guía de despliegue (`GUIA_DESPLIEGUE_PRODUCCION.md`) pero sube los archivos a:

```
domains/legisaudit-abogados.cu.ma/public_html/
```

O la ruta que corresponda según tu estructura.

## 📝 Nota Importante

- La ruta completa desde la raíz sería: `/domains/legisaudit-abogados.cu.ma/public_html/`
- Todos los archivos del sitio (HTML, PHP, imágenes, etc.) deben ir dentro de `public_html`
- La carpeta `api/` debe estar en `public_html/api/`
- Los archivos de `dist/` deben estar directamente en `public_html/`
