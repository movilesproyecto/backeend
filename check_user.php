<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('email', 'admin@demo.com')->first();

if ($user) {
    echo "✓ Usuario existe: admin@demo.com (ID: {$user->id})" . PHP_EOL;
} else {
    echo "✗ Usuario NO existe" . PHP_EOL;
    echo "Creando usuario admin@demo.com..." . PHP_EOL;

    $newUser = User::create([
        'name' => 'Admin',
        'email' => 'admin@demo.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
    ]);

    echo "✓ Usuario creado (ID: {$newUser->id})" . PHP_EOL;
}
