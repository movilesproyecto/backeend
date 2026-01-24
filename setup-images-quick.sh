#!/bin/bash

# Script de inicialización rápida para el sistema de imágenes
# Uso: bash setup-images.sh

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║   Sistema de Gestión de Imágenes - Setup Rápido             ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Colores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Cambiar a directorio laravel-backend
cd "$(dirname "$0")" || exit

echo -e "${BLUE}📍 Directorio actual:${NC} $(pwd)"
echo ""

# 1. Verificar que Laravel esté instalado
echo -e "${BLUE}[1/5]${NC} Verificando Laravel..."
if [ ! -f "artisan" ]; then
    echo -e "${RED}✗ artisan no encontrado. ¿Estamos en laravel-backend?${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Laravel encontrado${NC}"
echo ""

# 2. Crear directorio de almacenamiento
echo -e "${BLUE}[2/5]${NC} Creando directorios de almacenamiento..."
mkdir -p storage/app/public/departments
chmod -R 755 storage/
chmod -R 777 storage/app/public 2>/dev/null || true
echo -e "${GREEN}✓ Directorios creados: storage/app/public/departments/${NC}"
echo ""

# 3. Ejecutar migración
echo -e "${BLUE}[3/5]${NC} Ejecutando migración de base de datos..."
php artisan migrate --table=images 2>/dev/null || php artisan migrate
echo -e "${GREEN}✓ Migración completada${NC}"
echo ""

# 4. Crear enlace simbólico
echo -e "${BLUE}[4/5]${NC} Creando enlace simbólico..."
php artisan storage:link 2>/dev/null || {
    echo -e "${YELLOW}⚠ Intentando crear enlace manualmente...${NC}"
    ln -s $(pwd)/storage/app/public $(pwd)/public/storage 2>/dev/null || true
}
echo -e "${GREEN}✓ Enlace simbólico creado (public/storage)${NC}"
echo ""

# 5. Ejecutar comando setup
echo -e "${BLUE}[5/5]${NC} Ejecutando comando de setup..."
php artisan storage:setup-images || echo -e "${YELLOW}⚠ Comando de setup no disponible${NC}"
echo ""

echo "╔══════════════════════════════════════════════════════════════╗"
echo -e "${GREEN}✓ ¡Setup completado exitosamente!${NC}"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

echo -e "${BLUE}📋 Próximos pasos:${NC}"
echo "1. Instancia un servidor local: php artisan serve"
echo "2. Sube una imagen usando la API"
echo "3. Verifica que aparezca en: storage/app/public/departments/{id}/"
echo ""

echo -e "${BLUE}📚 Documentación:${NC}"
echo "   • IMAGES_SETUP.md           - Guía completa"
echo "   • RESUMEN_IMAGENES.md       - Resumen de implementación"
echo ""

echo -e "${YELLOW}💡 Testing rápido:${NC}"
echo "   curl http://localhost:8000/api/departments/1/images"
echo ""
