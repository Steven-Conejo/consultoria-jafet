# Variables de Entorno para Render

## 🔐 Variables OBLIGATORIAS

### Base de Datos
```
DB_HOST=localhost
DB_NAME=zssrpjcp_consultoria_jafet
DB_USER=zssrpjcp_Dario
DB_PASSWORD=MasterYDario
DB_CHARSET=utf8mb4
```

### SMTP (Correos)
```
SMTP_HOST=mail.legisaudit-abogados.cu.ma
SMTP_PORT=587
SMTP_USER=servicioprofesionalabogadojcgy@legisaudit-abogados.cu.ma
SMTP_PASSWORD=MasterYDario2803
SMTP_FROM_EMAIL=servicioprofesionalabogadojcgy@legisaudit-abogados.cu.ma
SMTP_FROM_NAME=LegisAudit - Plataforma Legal
SMTP_TO_EMAIL=sevicioprofesionalabogadojcgy@gmail.com
SMTP_ENCRYPTION=tls
SMTP_TIMEOUT=30
```

### Sesión (Seguridad)
```
SESSION_SECRET=tu-secret-key-super-segura-aqui-cambiar-en-produccion
```
⚠️ **IMPORTANTE**: Genera una clave secreta aleatoria y segura para producción.

## ⚙️ Variables OPCIONALES

### Servidor
```
PORT=3000
NODE_ENV=production
CORS_ORIGIN=*
```

### SMTP Alternativo
```
SMTP_USE_NATIVE=false
```
Si es `true`, usará sendmail en lugar de SMTP.

## 📝 Instrucciones para Render

1. **Ve a la sección "Variables de entorno"** en tu servicio de Render
2. **Agrega cada variable** usando el botón "+ Agregar variable de entorno"
3. **Para SESSION_SECRET**, usa el botón "Generar" o crea una clave aleatoria:
   ```bash
   # Puedes generar una con:
   node -e "console.log(require('crypto').randomBytes(32).toString('hex'))"
   ```
4. **Verifica que todas las variables estén correctas** antes de desplegar

## 🔒 Seguridad

- ✅ **NUNCA** subas el archivo `.env` a Git
- ✅ Las variables en Render están encriptadas
- ✅ Cambia `SESSION_SECRET` por una clave única y segura
- ✅ Verifica que `DB_PASSWORD` y `SMTP_PASSWORD` sean correctos

## 🚨 Nota sobre Base de Datos

Si tu base de datos MySQL está en otro servidor (no en Render), asegúrate de:
- Que `DB_HOST` apunte al hostname correcto (no `localhost`)
- Que el servidor MySQL permita conexiones externas
- Que el firewall permita conexiones desde Render
