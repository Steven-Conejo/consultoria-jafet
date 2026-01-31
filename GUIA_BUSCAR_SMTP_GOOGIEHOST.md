# Guía para Encontrar Configuración SMTP en GoogieHost

## 📧 Paso 1: Ver Detalles del Email

En la página de **Email Accounts** que estás viendo:

1. **Haz clic en el ícono de documento/list** (primer ícono a la derecha del email)
   - Este ícono generalmente muestra los detalles o configuración del email
   - Puede llamarse "View Details", "Email Settings", o "Configure"

2. **O haz clic en el ícono del candado** (segundo ícono)
   - Este puede mostrar la configuración de seguridad o conexión

3. **O haz clic en el ícono del lápiz** (tercer ícono - editar)
   - Puede mostrar opciones de configuración avanzada

## 📧 Paso 2: Buscar "Email Client Configuration" o "Mail Client Setup"

En los detalles del email, busca secciones como:
- **"Email Client Configuration"**
- **"Mail Client Setup"**
- **"IMAP/POP3 Settings"**
- **"SMTP Settings"**
- **"Connection Details"**

## 📧 Paso 3: Información que Buscas

Cuando encuentres la configuración, deberías ver algo como:

```
SMTP Server: mail.legisaudit-abogados.cu.ma (o localhost)
SMTP Port: 587, 465, o 25
SMTP Username: servicioprofesionalabogadojcgy@legisaudit-abogados.cu.ma
SMTP Password: (tu contraseña)
Encryption: TLS, SSL, o None
```

## 📧 Paso 4: Alternativa - Buscar en el Panel Principal

Si no encuentras la configuración en los detalles del email:

1. En el panel principal de GoogieHost, busca:
   - **"Email"** en el menú lateral
   - **"Email Accounts"** → luego haz clic en tu email
   - **"Email Client Configuration"** o **"Mail Setup"**

2. O busca en:
   - **"cPanel"** (si GoogieHost usa cPanel)
   - **"Email"** → **"Email Accounts"** → **"Configure Mail Client"**

## ⚠️ Si NO Encuentras Configuración SMTP

Si después de buscar no encuentras la configuración SMTP, significa que:

1. **El hosting solo permite usar `mail()` nativo** (que ya funciona)
2. **No hay SMTP interno disponible** para conexiones desde PHP
3. **La única opción es usar `mail()` nativo** que ya está configurado

## ✅ Estado Actual

- ✅ `mail()` nativo **FUNCIONA** (confirmado por pruebas)
- ✅ Los correos **SE ENVÍAN** correctamente
- ⚠️ Pueden llegar a **spam** (se soluciona con SPF)

## 🎯 Recomendación

**Si no encuentras la configuración SMTP en 5 minutos:**
- Usa `mail()` nativo (ya está configurado)
- Configura SPF para reducir spam
- El sistema ya está funcionando correctamente

---

**Prueba hacer clic en los íconos del email y dime qué ves. Si aparece alguna configuración SMTP, compártela y la actualizo en el código.**
