# Comandos para subir a GitHub

## Después de crear el repositorio en GitHub, ejecuta estos comandos:

```bash
# 1. Agregar el remoto (reemplaza TU-USUARIO y NOMBRE-REPO)
git remote add origin https://github.com/TU-USUARIO/NOMBRE-REPO.git

# 2. Renombrar la rama principal a 'main' (si es necesario)
git branch -M main

# 3. Subir el código a GitHub
git push -u origin main
```

## Si GitHub te pide autenticación:

### Opción 1: Personal Access Token (Recomendado)
1. Ve a: https://github.com/settings/tokens
2. Click en "Generate new token" > "Generate new token (classic)"
3. Dale un nombre y selecciona el scope `repo`
4. Copia el token generado
5. Cuando Git te pida la contraseña, usa el token en lugar de tu contraseña

### Opción 2: GitHub CLI
```bash
# Instalar GitHub CLI (si no lo tienes)
# Luego autenticarte:
gh auth login
```

## Verificar que todo está bien:

```bash
# Ver el remoto configurado
git remote -v

# Ver el estado
git status
```
