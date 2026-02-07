#!/bin/bash
set -e

# Verificar que Node.js está disponible
if ! command -v node &> /dev/null; then
    echo "Error: Node.js no está disponible"
    exit 1
fi

# Mostrar versión de Node
echo "Node version: $(node --version)"
echo "NPM version: $(npm --version)"

# Iniciar el servidor
exec node server.js
