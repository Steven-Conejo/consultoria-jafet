#!/bin/bash
set -e

echo "📦 Instalando dependencias..."
npm install

echo "🔨 Compilando frontend..."
npm run build

echo "✅ Build completado exitosamente"
