# Configuración SMTP - LegisAudit

## 📧 Servicio de Correos

El sistema utiliza SMTP para el envío de correos electrónicos a través de Brevo (Sendinblue).

### Configuración Actual

- **Servidor SMTP**: `smtp-relay.brevo.com`
- **Puerto**: `587`
- **Encriptación**: TLS
- **Usuario**: `90fd97001@smtp-brevo.com`
- **Email de Envío**: `sdavidce@gmail.com`
- **Email de Recepción**: `servicioprofesionalabogadojcgy@gmail.com`

### Funcionalidades

El sistema envía correos electrónicos en las siguientes situaciones:

1. **Formulario de Contacto** (`api/contact.php`):
   - Envía notificación al destinatario configurado con los datos del formulario
   - Envía confirmación al usuario que envió el formulario

2. **Subida de Archivos** (`api/upload.php`):
   - Envía notificación cuando un archivo es validado y almacenado exitosamente

### Archivos Relacionados

- `smtp_config.php` - Configuración de la cuenta SMTP
- `smtp_mailer.php` - Clase para envío de correos SMTP
- `test_smtp.php` - Script de prueba (eliminar en producción)

### Prueba del Servicio

Para probar que el servicio SMTP funciona correctamente:

1. Accede a: `http://tudominio.com/api/test_smtp.php`
2. Haz clic en "Enviar Correo de Prueba"
3. Revisa la bandeja de entrada de `servicioprofesionalabogadojcgy@gmail.com`

**⚠️ IMPORTANTE**: Eliminar o proteger `test_smtp.php` en producción.

### Personalización

Para cambiar la configuración SMTP, edita el archivo `api/smtp_config.php`:

```php
return [
    'host' => 'tu-servidor-smtp.com',
    'port' => 587,
    'username' => 'tu-usuario',
    'password' => 'tu-contraseña',
    'from_email' => 'tu-email@ejemplo.com',
    'from_name' => 'Tu Nombre',
    'to_email' => 'destinatario@ejemplo.com',
    'encryption' => 'tls',
    'timeout' => 30
];
```

### Seguridad

- Las credenciales están almacenadas en `smtp_config.php`
- Este archivo NO debe ser accesible públicamente
- El servidor web debe estar configurado para no servir archivos `.php` directamente
- Usa `.htaccess` para proteger el directorio `api/`

### Solución de Problemas

**Error: "No se pudo conectar al servidor SMTP"**
- Verifica que el servidor tenga acceso a internet
- Verifica que el puerto 587 no esté bloqueado por firewall
- Verifica la configuración del servidor SMTP

**Error: "Error en autenticación"**
- Verifica que el usuario y contraseña sean correctos
- Verifica que la cuenta SMTP esté activa en Brevo

**Los correos llegan a spam**
- Verifica los registros SPF, DKIM y DMARC del dominio
- Asegúrate de que el dominio tenga buena reputación
- Considera usar un dominio dedicado para envío de correos

### Logs

Los errores de envío de correos se registran en:
- Logs del servidor PHP
- Archivo `logs/contact.log` (para formularios de contacto)

