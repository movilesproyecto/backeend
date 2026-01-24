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
        ];

        foreach ($images as $image) {
            Image::create($image);
        }
    }
}
