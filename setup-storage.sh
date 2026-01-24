#!/bin/bash

# Script para preparar el almacenamiento de imágenes

echo "=== Configurando almacenamiento de imágenes ==="

# Crear directorio base si no existe
mkdir -p storage/app/public/departments

# Asegurar que storage/app/public sea escribible
chmod -R 755 storage/app/public
chmod -R 777 storage/app/public 2>/dev/null || true

# Crear enlace simbólico para acceder a los archivos públicos
php artisan storage:link 2>/dev/null || echo "Enlace simbólico posiblemente ya existe"

echo "✓ Almacenamiento configurado correctamente"
echo "✓ Los archivos se guardarán en: storage/app/public/departments/{id}"
echo "✓ Se accederán vía: /storage/departments/{id}/..."
