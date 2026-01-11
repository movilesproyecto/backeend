<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users de prueba
        User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@demo.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Johan Palma',
            'email' => 'johan11gamerez@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Root',
            'email' => 'root@demo.com',
            'password' => \Illuminate\Support\Facades\Hash::make('root123'),
            'role' => 'superadmin',
        ]);

        $this->call([
            DepartmentSeeder::class,
        ]);
    }
}
