# Sistema de Gestión de Imágenes - Backend

## Descripción General

Se ha implementado un sistema completo de gestión de imágenes para departamentos que incluye:

- ✅ Modelo `Image` con relación a `Department`
- ✅ Migración para tabla `images`
- ✅ Controlador `ImageController` con CRUD completo
- ✅ Rutas API documentadas
- ✅ Almacenamiento en `storage/app/public/departments/`
- ✅ Registro completo en base de datos

---

## Estructura de Carpetas

```
laravel-backend/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           └── ImageController.php (NUEVO)
│   └── Models/
│       └── Image.php (NUEVO)
├── database/
│   └── migrations/
│       └── 2026_01_17_000001_create_images_table.php (NUEVO)
├── storage/
│   └── app/
│       └── public/
│           └── departments/
│               ├── 1/        <- Imágenes del departamento 1
│               ├── 2/        <- Imágenes del departamento 2
│               └── ...
└── ...
```

---

## Base de Datos

### Tabla: `images`

```sql
CREATE TABLE images (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    department_id BIGINT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_size BIGINT UNSIGNED NOT NULL,
    mime_type VARCHAR(50) NOT NULL,
    uploaded_by BIGINT NULLABLE,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_department_id (department_id),
    INDEX idx_uploaded_by (uploaded_by)
);
```

---

## Rutas API

### Rutas Públicas (sin autenticación)

#### 1. Listar imágenes de un departamento
```http
GET /api/departments/{department}/images
```

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "departmentId": 5,
            "fileName": "salon_principal.jpg",
            "url": "http://localhost:8000/storage/departments/5/salon_principal.jpg",
            "fileSize": 2048576,
            "mimeType": "image/jpeg",
            "isPrimary": true,
            "createdAt": "2026-01-17T10:30:00Z"
        }
    ],
    "total": 3
}
```

#### 2. Obtener imagen primaria de un departamento
```http
GET /api/departments/{department}/images/primary
```

**Response:**
```json
{
    "id": 1,
    "fileName": "salon_principal.jpg",
    "url": "http://localhost:8000/storage/departments/5/salon_principal.jpg",
    "fileSize": 2048576,
    "mimeType": "image/jpeg"
}
```

---

### Rutas Protegidas (requieren autenticación)

#### 3. Subir imágenes (POST)
```http
POST /api/departments/{department}/images
Authorization: Bearer {token}
Content-Type: multipart/form-data

Form Data:
- images[] (file) - Múltiples archivos de imagen (máx 5MB cada uno)
- is_primary[] (boolean, opcional) - Marcar una imagen como primaria
```

**Response:**
```json
{
    "message": "Imágenes subidas correctamente",
    "uploaded": 2,
    "images": [
        {
            "id": 1,
            "fileName": "cocina.jpg",
            "url": "http://localhost:8000/storage/departments/5/1705498200_123abc.jpg",
            "fileSize": 1024000,
            "mimeType": "image/jpeg",
            "isPrimary": false,
            "createdAt": "2026-01-17T10:30:00Z"
        }
    ]
}
```

#### 4. Obtener imagen específica
```http
GET /api/departments/{department}/images/{image}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "id": 1,
    "departmentId": 5,
    "fileName": "salon.jpg",
    "url": "http://localhost:8000/storage/departments/5/salon.jpg",
    "fileSize": 2048576,
    "mimeType": "image/jpeg",
    "isPrimary": true,
    "uploadedBy": 2,
    "createdAt": "2026-01-17T10:30:00Z",
    "updatedAt": "2026-01-17T10:30:00Z"
}
```

#### 5. Actualizar imagen (marcar como primaria)
```http
PUT /api/departments/{department}/images/{image}
Authorization: Bearer {token}
Content-Type: application/json

{
    "is_primary": true
}
```

**Response:**
```json
{
    "id": 1,
    "departmentId": 5,
    "fileName": "salon.jpg",
    "url": "http://localhost:8000/storage/departments/5/salon.jpg",
    "isPrimary": true,
    "updatedAt": "2026-01-17T11:00:00Z"
}
```

#### 6. Eliminar imagen
```http
DELETE /api/departments/{department}/images/{image}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "Imagen eliminada correctamente",
    "deleted": "salon.jpg"
}
```

---

## Instalación y Setup

### 1. Migrar la base de datos
```bash
cd laravel-backend
php artisan migrate
```

### 2. Preparar almacenamiento
```bash
# En Windows (PowerShell)
New-Item -ItemType Directory -Path "storage\app\public\departments" -Force

# En Linux/Mac
mkdir -p storage/app/public/departments
chmod -R 755 storage/app/public
```

### 3. Crear enlace simbólico
```bash
php artisan storage:link
```

Esto crea un enlace de `public/storage` → `storage/app/public` para acceder a las imágenes públicamente.

---

## Especificaciones Técnicas

### Límites
- **Tamaño máximo por imagen:** 5 MB
- **Máximo de imágenes por petición:** 10
- **Formatos permitidos:** JPEG, PNG, JPG, GIF, WebP
- **Tamaño máximo total:** 50 MB

### Almacenamiento
- **Ruta de almacenamiento:** `storage/app/public/departments/{department_id}/{filename}`
- **URL pública:** `/storage/departments/{department_id}/{filename}`
- **Nombres de archivo:** Generados con timestamp + uniqid para evitar conflictos

### Base de Datos
- Cada imagen registra:
  - `file_path`: Ruta relativa en storage
  - `file_name`: Nombre original del archivo
  - `file_size`: Tamaño en bytes
  - `mime_type`: Tipo MIME del archivo
  - `uploaded_by`: ID del usuario que subió
  - `is_primary`: Si es la imagen principal del departamento
  - `created_at`, `updated_at`: Timestamps

---

## Relaciones en Modelos

### Department → Images
```php
// En Department.php
public function images()
{
    return $this->hasMany(Image::class);
}

// Uso:
$department->images(); // Todas las imágenes
$department->images()->where('is_primary', true)->first(); // Imagen primaria
```

### Image → Department
```php
// En Image.php
public function department()
{
    return $this->belongsTo(Department::class);
}
```

### Image → User
```php
// En Image.php
public function uploadedBy()
{
    return $this->belongsTo(User::class, 'uploaded_by');
}
```

---

## Ejemplos de Uso con cURL

### Subir imágenes
```bash
curl -X POST \
  http://localhost:8000/api/departments/5/images \
  -H 'Authorization: Bearer TOKEN' \
  -F 'images[]=@/path/to/image1.jpg' \
  -F 'images[]=@/path/to/image2.jpg' \
  -F 'is_primary[0]=true'
```

### Listar imágenes
```bash
curl -X GET http://localhost:8000/api/departments/5/images
```

### Obtener imagen primaria
```bash
curl -X GET http://localhost:8000/api/departments/5/images/primary
```

### Eliminar imagen
```bash
curl -X DELETE \
  http://localhost:8000/api/departments/5/images/1 \
  -H 'Authorization: Bearer TOKEN'
```

---

## Autorización

- **Listar imágenes:** Público
- **Obtener imagen primaria:** Público
- **Subir imágenes:** Solo propietario del departamento (PolicyAuthorization)
- **Actualizar imagen:** Solo propietario del departamento
- **Eliminar imagen:** Solo propietario del departamento

---

## Próximos Pasos (Opcional)

1. **Optimización de imágenes:**
   - Redimensionar a múltiples tamaños (thumbnail, medium, large)
   - Comprimir automáticamente
   - Convertir a WebP para mejor rendimiento

2. **Validaciones adicionales:**
   - Detectar imágenes duplicadas
   - Validar contenido (EXIF, metadatos)

3. **Caché:**
   - Guardar URLs de imágenes en caché
   - Invalidar caché al subir/eliminar

4. **CDN:**
   - Integrar con servicios como S3, Cloudinary, etc.

---

## Archivos Creados/Modificados

### Nuevos
- ✅ `app/Models/Image.php`
- ✅ `app/Http/Controllers/Api/ImageController.php`
- ✅ `database/migrations/2026_01_17_000001_create_images_table.php`

### Modificados
- ✅ `app/Models/Department.php` - Agregada relación `images()`
- ✅ `routes/api.php` - Agregadas rutas de imágenes

---

## Troubleshooting

### Error: "Storage link already exists"
```bash
php artisan storage:link --force
```

### Error: "Permission denied" al guardar archivos
```bash
chmod -R 755 storage/
chmod -R 777 storage/app/public
```

### Las imágenes no se muestran
- Verificar que el enlace simbólico existe: `public/storage`
- Verificar que `APP_URL` es correcto en `.env`
- Verificar permisos de carpeta `storage/app/public`

---

**Versión:** 1.0  
**Fecha:** 17 de Enero, 2026
