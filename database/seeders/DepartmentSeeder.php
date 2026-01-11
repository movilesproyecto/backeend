<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::create([
            'name' => 'Departamento Centro',
            'description' => 'Hermoso departamento en el centro de la ciudad con vista al parque principal.',
            'address' => 'Calle Principal 123, Centro',
            'bedrooms' => 2,
            'price_per_night' => 150.00,
            'rating_avg' => 4.5,
            'published' => true,
            'amenities' => ['WiFi', 'TV', 'Cocina', 'Baño Privado'],
        ]);

        Department::create([
            'name' => 'Apartamento Moderno',
            'description' => 'Moderno apartamento con todas las comodidades para tu estadía.',
            'address' => 'Avenida Moderna 456, Zona Norte',
            'bedrooms' => 1,
            'price_per_night' => 120.00,
            'rating_avg' => 4.2,
            'published' => true,
            'amenities' => ['WiFi', 'Aire Acondicionado', 'Estacionamiento'],
        ]);

        Department::create([
            'name' => 'Suite Ejecutiva',
            'description' => 'Elegante suite diseñada para ejecutivos con home office completo.',
            'address' => 'Torre Ejecutiva 789, Zona Comercial',
            'bedrooms' => 1,
            'price_per_night' => 200.00,
            'rating_avg' => 4.8,
            'published' => true,
            'amenities' => ['WiFi Premium', 'Escritorio', 'Minibar', 'Servicio Concierge'],
        ]);

        Department::create([
            'name' => 'Loft Industrial',
            'description' => 'Espacioso loft con diseño industrial y acabados premium.',
            'address' => 'Zona Artística 321, Barrio Creativo',
            'bedrooms' => 2,
            'price_per_night' => 180.00,
            'rating_avg' => 4.6,
            'published' => true,
            'amenities' => ['Cocina Abierta', 'Techo Alto', 'Áreas Comunes'],
        ]);

        Department::create([
            'name' => 'Penthouse Lujo',
            'description' => 'Lujo absoluto con terraza panorámica y servicios VIP.',
            'address' => 'Torre Presidencial 999, Piso 50',
            'bedrooms' => 3,
            'price_per_night' => 500.00,
            'rating_avg' => 5.0,
            'published' => true,
            'amenities' => ['Terraza Panorámica', 'Spa Privado', 'Cine Hogar', 'Chef Personal'],
        ]);
    }
}
