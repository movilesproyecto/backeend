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

        Department::create([
            'name' => 'Apartamento Familiar Acogedor',
            'description' => 'Espacioso apartamento perfecto para familias, con todos los servicios necesarios.',
            'address' => 'Calle Familia 555, Barrio Residencial',
            'bedrooms' => 3,
            'price_per_night' => 130.00,
            'rating_avg' => 4.4,
            'published' => true,
            'amenities' => ['Parque Infantil', 'WiFi', 'Cocina Completa', 'Patio'],
        ]);

        Department::create([
            'name' => 'Estudio Downtown Compacto',
            'description' => 'Perfecto para ejecutivos y viajeros, ubicado en pleno centro.',
            'address' => 'Paseo Centro 222, Downtown',
            'bedrooms' => 1,
            'price_per_night' => 95.00,
            'rating_avg' => 4.1,
            'published' => true,
            'amenities' => ['Ubicación Central', 'WiFi', 'Gym', 'Transporte Público'],
        ]);

        Department::create([
            'name' => 'Casa Campestre con Jardín',
            'description' => 'Tranquila casa con amplio jardín, ideal para descanso.',
            'address' => 'Camino Rural 888, Zona Verde',
            'bedrooms' => 4,
            'price_per_night' => 160.00,
            'rating_avg' => 4.7,
            'published' => true,
            'amenities' => ['Jardín Grande', 'BBQ', 'Piscina', 'Tranquilidad'],
        ]);

        Department::create([
            'name' => 'Loft Artístico Bohemio',
            'description' => 'Espacioso loft con decoración artística en zona creativa.',
            'address' => 'Calle del Arte 777, Barrio Bohemio',
            'bedrooms' => 2,
            'price_per_night' => 140.00,
            'rating_avg' => 4.3,
            'published' => true,
            'amenities' => ['Galerías Cercanas', 'Cafés Artísticos', 'Estudio', 'WiFi Premium'],
        ]);

        Department::create([
            'name' => 'Departamento Frente al Parque',
            'description' => 'Hermoso departamento con vistas directas al parque principal.',
            'address' => 'Avenida del Parque 444, Centro',
            'bedrooms' => 2,
            'price_per_night' => 170.00,
            'rating_avg' => 4.6,
            'published' => true,
            'amenities' => ['Balcón con Vista', 'Parque Cercano', 'Comercios', 'WiFi'],
        ]);

        Department::create([
            'name' => 'Villa de Lujo con Piscina',
            'description' => 'Exclusiva villa con piscina privada y servicios de limpieza.',
            'address' => 'Condominio Exclusivo 111, Zona de Villas',
            'bedrooms' => 5,
            'price_per_night' => 450.00,
            'rating_avg' => 4.9,
            'published' => true,
            'amenities' => ['Piscina Privada', 'Seguridad 24/7', 'Jardín', 'Servicio Concierge'],
        ]);

        Department::create([
            'name' => 'Apartamento Minimalista Moderno',
            'description' => 'Minimalista y funcional con tecnología inteligente.',
            'address' => 'Torres Futuro 333, Zona Tech',
            'bedrooms' => 1,
            'price_per_night' => 115.00,
            'rating_avg' => 4.5,
            'published' => true,
            'amenities' => ['Casa Inteligente', 'Diseño Moderno', 'WiFi 5G', 'Parking'],
        ]);

        Department::create([
            'name' => 'Cabaña Rústica Montaña',
            'description' => 'Acogedora cabaña rodeada de naturaleza con chimenea.',
            'address' => 'Montaña Verde 666, Área Rural',
            'bedrooms' => 2,
            'price_per_night' => 125.00,
            'rating_avg' => 4.8,
            'published' => true,
            'amenities' => ['Chimenea', 'Naturaleza', 'Aire Puro', 'Senderismo'],
        ]);

        Department::create([
            'name' => 'Departamento Turístico Beachfront',
            'description' => 'Directo a la playa con acceso privado y vista al mar.',
            'address' => 'Paseo Marítimo 999, Playa Sur',
            'bedrooms' => 2,
            'price_per_night' => 210.00,
            'rating_avg' => 4.7,
            'published' => true,
            'amenities' => ['Acceso Playa', 'Vista al Mar', 'Piscina', 'Restaurante'],
        ]);
    }
}
