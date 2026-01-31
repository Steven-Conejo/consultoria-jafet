# Solución: Error de Conexión a Base de Datos

## ❌ Error Detectado
```
Access denied for user 'Dario'@'localhost' (using password: YES)
```

## 🔍 Diagnóstico
El usuario de MySQL `Dario` no existe o no tiene permisos. En GoogieHost (hosting compartido), el usuario de MySQL generalmente requiere el prefijo de tu cuenta de cPanel.

## ✅ Solución

### Paso 1: Obtener las Credenciales Correctas desde cPanel

1. **Accede a tu panel de control de GoogieHost:**
   - URL: `https://cloud3.googiehost.com/`
   - Inicia sesión con tus credenciales

2. **Abre phpMyAdmin:**
   - Busca la sección "Bases de datos" o "Databases"
   - Haz clic en "phpMyAdmin" o accede directamente a: `https://cloud3.googiehost.com/phpMyAdmin/`

3. **Verifica el nombre de usuario correcto:**
   - En phpMyAdmin, en la parte superior verás el usuario con el que estás conectado
   - O ve a la pestaña "Usuarios" / "Users" para ver los usuarios disponibles
   - El usuario probablemente será: `zssrpjcp_Dario` o `zssrpjcp_dario` (con prefijo en minúsculas)

4. **Obtén la contraseña correcta:**
   - Si no recuerdas la contraseña, ve a "Bases de datos MySQL" en el cPanel
   - Busca la base de datos `zssrpjcp_consultoria_jafet`
   - Allí deberías ver el usuario asociado y poder cambiar/ver la contraseña

### Paso 2: Actualizar `api/db_config.php`

Una vez que tengas las credenciales correctas, actualiza el archivo `api/db_config.php`:

**Opción A: Si el usuario requiere prefijo**
```php
return [
    'host' => 'localhost',
    'dbname' => 'zssrpjcp_consultoria_jafet',
    'username' => 'zssrpjcp_Dario',  // ← Agregar el prefijo 'zssrpjcp_'
    'password' => 'MasterYDario',     // ← Verificar que sea la correcta
    'charset' => 'utf8mb4'
];
```

**Opción B: Si puedes usar el usuario del cPanel directamente**
```php
return [
    'host' => 'localhost',
    'dbname' => 'zssrpjcp_consultoria_jafet',
    'username' => 'zssrpjcp_root',    // ← Usuario principal del cPanel
    'password' => 'TU_CONTRASEÑA_CPANEL',
    'charset' => 'utf8mb4'
];
```

### Paso 3: Crear el Usuario si No Existe

Si necesitas crear el usuario `Dario` con permisos, ejecuta este SQL en phpMyAdmin:

```sql
-- Crear el usuario (ajusta el nombre según tu prefijo)
CREATE USER 'zssrpjcp_Dario'@'localhost' IDENTIFIED BY 'MasterYDario';

-- Otorgar todos los permisos sobre la base de datos
GRANT ALL PRIVILEGES ON zssrpjcp_consultoria_jafet.* TO 'zssrpjcp_Dario'@'localhost';

-- Aplicar los cambios
FLUSH PRIVILEGES;
```

### Paso 4: Verificar la Conexión

1. Actualiza el archivo `api/db_config.php` con las credenciales correctas
2. Ejecuta nuevamente el script de diagnóstico: `http://legisaudit-abogados.cu.ma/api/test_db_connection.php`
3. Deberías ver "OK" en todos los tests

## 🔐 Información Importante

### En Hosting Compartido:
- **Usuario MySQL:** Generalmente tiene el formato `prefijo_usuario` (ejemplo: `zssrpjcp_Dario`)
- **Base de datos:** Generalmente tiene el formato `prefijo_nombre` (ejemplo: `zssrpjcp_consultoria_jafet`)
- **Host:** Normalmente es `localhost`, pero algunos hostings usan `127.0.0.1` o un host específico

### Verificar en cPanel:
1. Ve a "Bases de datos MySQL" o "MySQL Databases"
2. Verás una lista de bases de datos y usuarios
3. El formato será algo como:
   ```
   Base de datos: zssrpjcp_consultoria_jafet
   Usuario: zssrpjcp_Dario
   ```

## ⚠️ Notas de Seguridad

- **NUNCA** subas `api/test_db_connection.php` a producción (solo úsalo para diagnóstico)
- **NUNCA** muestres las credenciales de base de datos públicamente
- Una vez resuelto el problema, elimina o protege `test_db_connection.php`

## 📝 Si el Problema Persiste

1. Verifica que la base de datos `zssrpjcp_consultoria_jafet` exista
2. Verifica que el usuario tenga permisos sobre esa base de datos
3. Intenta conectar desde phpMyAdmin usando las mismas credenciales
4. Contacta al soporte de GoogieHost si las credenciales del cPanel no funcionan
