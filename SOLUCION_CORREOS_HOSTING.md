# Solución al Problema de Envío de Correos

## ❌ Problema Confirmado

El hosting **GoogieHost** bloquea completamente conexiones SMTP salientes externas:
- ✅ Puerto 465 (SSL) - BLOQUEADO
- ✅ Puerto 587 (TLS) - BLOQUEADO  
- ❓ mail() nativo - NECESITA PRUEBA

## 🔍 Paso 1: Verificar mail() nativo

1. **Sube el archivo** `api/test_mail_nativo.php` al servidor
2. **Accede desde el navegador**: `http://legisaudit-abogados.cu.ma/api/test_mail_nativo.php`
3. **Haz clic en "Probar mail() nativo"**
4. **Revisa el resultado**:
   - Si dice "TRUE": mail() está disponible pero puede que no lleguen los correos
   - Si dice "FALSE": mail() no está configurado en el hosting

## ✅ Soluciones Posibles

### Opción 1: Usar SMTP del Hosting (RECOMENDADO)

La mayoría de hosts compartidos ofrecen SMTP propio. Busca en el panel de control de GoogieHost:

1. Ve al panel de control de GoogieHost
2. Busca la sección "Email" o "Correo Electrónico"
3. Crea una cuenta de correo como: `contacto@legisaudit-abogados.cu.ma`
4. Busca las credenciales SMTP del hosting (suelen ser algo como):
   - **Host**: `mail.legisaudit-abogados.cu.ma` o `localhost`
   - **Puerto**: 25, 465, o 587
   - **Usuario**: `contacto@legisaudit-abogados.cu.ma`
   - **Contraseña**: la que configuraste

5. Actualiza `api/smtp_config.php` con estos datos

### Opción 2: Contactar Soporte del Hosting

Si no encuentras SMTP del hosting, contacta a GoogieHost y pregunta:
- "¿Cómo puedo enviar correos desde PHP?"
- "¿Tienen servicio SMTP interno que pueda usar?"
- "¿Pueden habilitar envío de correos para mi dominio?"

### Opción 3: Usar Servicio de Correo Externo

Si el hosting no ofrece solución, considera servicios como:
- **SendGrid** (gratis hasta 100 emails/día)
- **Mailgun** (gratis hasta 5,000 emails/mes)
- **Amazon SES** (muy económico)

Estos servicios tienen APIs REST que funcionan aunque el hosting bloquee SMTP.

## 📋 Qué Hacer Ahora

1. **Primero**: Ejecuta `api/test_mail_nativo.php` para verificar
2. **Segundo**: Busca SMTP del hosting en el panel de control
3. **Tercero**: Si no encuentras nada, contacta al soporte técnico

## 🔧 Si encuentras SMTP del hosting

Cuando tengas las credenciales, actualiza `api/smtp_config.php`:

```php
return [
    'host' => 'mail.legisaudit-abogados.cu.ma', // SMTP del hosting
    'port' => 587, // o el puerto que te den
    'username' => 'contacto@legisaudit-abogados.cu.ma',
    'password' => 'tu_contraseña',
    'from_email' => 'contacto@legisaudit-abogados.cu.ma',
    'from_name' => 'LegisAudit - Plataforma Legal',
    'to_email' => 'sevicioprofesionalabogadojcgy@gmail.com',
    'encryption' => 'tls',
    'timeout' => 30
];
```

---

**El código ya está preparado para usar SMTP interno del hosting si funciona. Solo necesitas las credenciales correctas.**
