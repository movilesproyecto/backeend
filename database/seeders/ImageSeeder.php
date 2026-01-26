<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Image;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // URLs de imágenes libres de derechos (Unsplash)
        $images = [
            // Departamento 1 - Apartamento Frente al Mar
            [
                'department_id' => 1,
                'file_path' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
                'file_name' => 'apartment-beachfront-1.jpg',
                'file_size' => 150000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => true,
            ],
            [
                'department_id' => 1,
                'file_path' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
                'file_name' => 'apartment-beachfront-2.jpg',
                'file_size' => 160000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => false,
            ],
            [
                'department_id' => 1,
                'file_path' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&q=80',
                'file_name' => 'apartment-beachfront-3.jpg',
                'file_size' => 155000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => false,
            ],

            // Departamento 2 - Loft Moderno Downtown
            [
                'department_id' => 2,
                'file_path' => 'https://images.unsplash.com/photo-1493857671505-72967e2e2760?w=800&q=80',
                'file_name' => 'loft-downtown-1.jpg',
                'file_size' => 145000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => true,
            ],
            [
                'department_id' => 2,
                'file_path' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=80',
                'file_name' => 'loft-downtown-2.jpg',
                'file_size' => 150000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => false,
            ],
            [
                'department_id' => 2,
                'file_path' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
                'file_name' => 'loft-downtown-3.jpg',
                'file_size' => 148000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => false,
            ],

            // Departamento 3 - Casa Colonial Histórica
            [
                'department_id' => 3,
                'file_path' => 'https://images.unsplash.com/photo-1480074568153-71106f2fc6a7?w=800&q=80',
                'file_name' => 'colonial-house-1.jpg',
                'file_size' => 155000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => true,
            ],
            [
                'department_id' => 3,
                'file_path' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
                'file_name' => 'colonial-house-2.jpg',
                'file_size' => 152000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => false,
            ],
            [
                'department_id' => 3,
                'file_path' => 'https://images.unsplash.com/photo-1493857671505-72967e2e2760?w=800&q=80',
                'file_name' => 'colonial-house-3.jpg',
                'file_size' => 150000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => false,
            ],

            // Departamento 4 - Penthouse Lujo
            [
                'department_id' => 4,
                'file_path' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
                'file_name' => 'penthouse-luxury-1.jpg',
                'file_size' => 160000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => true,
            ],
            [
                'department_id' => 4,
                'file_path' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=80',
                'file_name' => 'penthouse-luxury-2.jpg',
                'file_size' => 158000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => false,
            ],
            [
                'department_id' => 4,
                'file_path' => 'https://images.unsplash.com/photo-1493857671505-72967e2e2760?w=800&q=80',
                'file_name' => 'penthouse-luxury-3.jpg',
                'file_size' => 156000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => false,
            ],

            // Departamento 5 - Estudio Boutique
            [
                'department_id' => 5,
                'file_path' => 'https://images.unsplash.com/photo-1480074568153-71106f2fc6a7?w=800&q=80',
                'file_name' => 'studio-boutique-1.jpg',
                'file_size' => 140000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 3,
                'is_primary' => true,
            ],
            [
                'department_id' => 5,
                'file_path' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&q=80',
                'file_name' => 'studio-boutique-2.jpg',
                'file_size' => 142000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 3,
                'is_primary' => false,
            ],
            [
                'department_id' => 5,
                'file_path' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
                'file_name' => 'studio-boutique-3.jpg',
                'file_size' => 141000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 3,
                'is_primary' => false,
            ],

            // Departamento 6 - Apartamento Familiar Acogedor
            [
                'department_id' => 6,
                'file_path' => 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=800&q=80',
                'file_name' => 'family-apartment-1.jpg',
                'file_size' => 148000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => true,
            ],
            [
                'department_id' => 6,
                'file_path' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
                'file_name' => 'family-apartment-2.jpg',
                'file_size' => 146000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => false,
            ],
            [
                'department_id' => 6,
                'file_path' => 'https://images.unsplash.com/photo-1493857671505-72967e2e2760?w=800&q=80',
                'file_name' => 'family-apartment-3.jpg',
                'file_size' => 145000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => false,
            ],

            // Departamento 7 - Estudio Downtown Compacto
            [
                'department_id' => 7,
                'file_path' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=80',
                'file_name' => 'studio-downtown-1.jpg',
                'file_size' => 135000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => true,
            ],
            [
                'department_id' => 7,
                'file_path' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&q=80',
                'file_name' => 'studio-downtown-2.jpg',
                'file_size' => 136000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => false,
            ],
            [
                'department_id' => 7,
                'file_path' => 'https://images.unsplash.com/photo-1480074568153-71106f2fc6a7?w=800&q=80',
                'file_name' => 'studio-downtown-3.jpg',
                'file_size' => 137000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => false,
            ],

            // Departamento 8 - Casa Campestre con Jardín
            [
                'department_id' => 8,
                'file_path' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
                'file_name' => 'countryside-house-1.jpg',
                'file_size' => 155000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 3,
                'is_primary' => true,
            ],
            [
                'department_id' => 8,
                'file_path' => 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=800&q=80',
                'file_name' => 'countryside-house-2.jpg',
                'file_size' => 154000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 3,
                'is_primary' => false,
            ],
            [
                'department_id' => 8,
                'file_path' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
                'file_name' => 'countryside-house-3.jpg',
                'file_size' => 153000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 3,
                'is_primary' => false,
            ],

            // Departamento 9 - Loft Artístico Bohemio
            [
                'department_id' => 9,
                'file_path' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=80',
                'file_name' => 'bohemian-loft-1.jpg',
                'file_size' => 152000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => true,
            ],
            [
                'department_id' => 9,
                'file_path' => 'https://images.unsplash.com/photo-1493857671505-72967e2e2760?w=800&q=80',
                'file_name' => 'bohemian-loft-2.jpg',
                'file_size' => 151000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => false,
            ],
            [
                'department_id' => 9,
                'file_path' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
                'file_name' => 'bohemian-loft-3.jpg',
                'file_size' => 150000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => false,
            ],

            // Departamento 10 - Departamento Frente al Parque
            [
                'department_id' => 10,
                'file_path' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&q=80',
                'file_name' => 'park-view-1.jpg',
                'file_size' => 149000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => true,
            ],
            [
                'department_id' => 10,
                'file_path' => 'https://images.unsplash.com/photo-1480074568153-71106f2fc6a7?w=800&q=80',
                'file_name' => 'park-view-2.jpg',
                'file_size' => 148000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => false,
            ],
            [
                'department_id' => 10,
                'file_path' => 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=800&q=80',
                'file_name' => 'park-view-3.jpg',
                'file_size' => 147000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => false,
            ],

            // Departamento 11 - Villa de Lujo con Piscina
            [
                'department_id' => 11,
                'file_path' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
                'file_name' => 'luxury-villa-1.jpg',
                'file_size' => 162000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 3,
                'is_primary' => true,
            ],
            [
                'department_id' => 11,
                'file_path' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=80',
                'file_name' => 'luxury-villa-2.jpg',
                'file_size' => 161000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 3,
                'is_primary' => false,
            ],
            [
                'department_id' => 11,
                'file_path' => 'https://images.unsplash.com/photo-1493857671505-72967e2e2760?w=800&q=80',
                'file_name' => 'luxury-villa-3.jpg',
                'file_size' => 160000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 3,
                'is_primary' => false,
            ],

            // Departamento 12 - Apartamento Minimalista Moderno
            [
                'department_id' => 12,
                'file_path' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
                'file_name' => 'minimalist-modern-1.jpg',
                'file_size' => 138000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => true,
            ],
            [
                'department_id' => 12,
                'file_path' => 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=800&q=80',
                'file_name' => 'minimalist-modern-2.jpg',
                'file_size' => 139000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => false,
            ],
            [
                'department_id' => 12,
                'file_path' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&q=80',
                'file_name' => 'minimalist-modern-3.jpg',
                'file_size' => 140000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 1,
                'is_primary' => false,
            ],

            // Departamento 13 - Cabaña Rústica Montaña
            [
                'department_id' => 13,
                'file_path' => 'https://images.unsplash.com/photo-1480074568153-71106f2fc6a7?w=800&q=80',
                'file_name' => 'cabin-mountain-1.jpg',
                'file_size' => 144000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => true,
            ],
            [
                'department_id' => 13,
                'file_path' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
                'file_name' => 'cabin-mountain-2.jpg',
                'file_size' => 143000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => false,
            ],
            [
                'department_id' => 13,
                'file_path' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=80',
                'file_name' => 'cabin-mountain-3.jpg',
                'file_size' => 142000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 2,
                'is_primary' => false,
            ],

            // Departamento 14 - Departamento Turístico Beachfront
            [
                'department_id' => 14,
                'file_path' => 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=800&q=80',
                'file_name' => 'beachfront-tourist-1.jpg',
                'file_size' => 158000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 3,
                'is_primary' => true,
            ],
            [
                'department_id' => 14,
                'file_path' => 'https://images.unsplash.com/photo-1493857671505-72967e2e2760?w=800&q=80',
                'file_name' => 'beachfront-tourist-2.jpg',
                'file_size' => 157000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 3,
                'is_primary' => false,
            ],
            [
                'department_id' => 14,
                'file_path' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
                'file_name' => 'beachfront-tourist-3.jpg',
                'file_size' => 156000,
                'mime_type' => 'image/jpeg',
                'uploaded_by' => 3,
                'is_primary' => false,
            ],
        ];

        foreach ($images as $image) {
            Image::create($image);
        }
    }
}
