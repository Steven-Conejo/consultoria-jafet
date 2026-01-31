# LegisAudit - Plataforma de Auditoría Legal

Plataforma de vanguardia para la auditoría y seguridad de instrumentos legales. Servicio gratuito orientado a jóvenes y adultos para revisar y auditar contratos de arrendamiento y anticrético, identificando cláusulas abusivas.

## 🚀 Características

- **Revisión Gratuita de Contratos**: Servicio completamente gratuito para revisar contratos legales
- **Blog Legal**: Artículos informativos sobre temas legales y jurídicos
- **Panel de Administración**: Gestión de contenido y consultas
- **Formulario de Contacto**: Sistema de envío de correos con fallback automático
- **Diseño Responsive**: Optimizado para dispositivos móviles y escritorio

## 🛠️ Tecnologías

- **Frontend**: Vue.js 3, TypeScript, Vue Router
- **Backend**: PHP 8+
- **Base de Datos**: MySQL/MariaDB
- **Build Tool**: Vite
- **Estilos**: CSS3 con diseño moderno

## 📋 Requisitos

- PHP 8.0 o superior
- MySQL/MariaDB
- Node.js 20.19.0+ o 22.12.0+
- npm o yarn

## 🔧 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/consultoria-jafet.git
cd consultoria-jafet
```

### 2. Instalar dependencias

```bash
npm install
```

### 3. Configurar base de datos

1. Copia `api/db_config.php.example` a `api/db_config.php`
2. Edita `api/db_config.php` con tus credenciales de base de datos
3. Importa el esquema de base de datos (si existe un archivo SQL)

### 4. Configurar SMTP (Opcional)

1. Copia `api/smtp_config.php.example` a `api/smtp_config.php`
2. Edita `api/smtp_config.php` con tus credenciales SMTP
3. Si no configuras SMTP, el sistema usará `mail()` nativo de PHP automáticamente

### 5. Compilar el proyecto

```bash
npm run build
```

### 6. Configurar servidor web

- Apunta tu servidor web a la carpeta `public_html/` o `dist/`
- Asegúrate de que PHP tenga acceso a la carpeta `api/`
- Configura las reglas de reescritura (ver `.htaccess`)

## 📁 Estructura del Proyecto

```
Consultoria_Jafet/
├── api/                 # Backend PHP
│   ├── admin/          # Endpoints de administración
│   ├── db_config.php   # Configuración de BD (no incluido en git)
│   └── ...
├── src/                # Frontend Vue.js
│   ├── components/     # Componentes Vue
│   ├── views/          # Vistas/páginas
│   ├── router/         # Configuración de rutas
│   └── ...
├── dist/               # Build de producción
├── public_html/        # Archivos públicos
└── package.json       # Dependencias Node.js
```

## 🔐 Seguridad

- **NO** subas archivos con credenciales a Git
- Usa los archivos `.example` como plantilla
- Los archivos `db_config.php` y `smtp_config.php` están en `.gitignore`
- Cambia las contraseñas por defecto en producción

## 🚀 Desarrollo

```bash
# Modo desarrollo
npm run dev

# Compilar para producción
npm run build

# Vista previa de producción
npm run preview
```

## 📝 Licencia

Este proyecto es de uso educativo y personal.

## 👤 Autor

Steven Conejo Elizondo

## 📧 Contacto

Para más información, visita [legisaudit-abogados.cu.ma](http://legisaudit-abogados.cu.ma)
