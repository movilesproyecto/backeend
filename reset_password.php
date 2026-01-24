<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'admin@demo.com')->first();

if ($user) {
    $user->update([
        'password' => Hash::make('password')
    ]);
    echo "✓ Contraseña actualizada para admin@demo.com" . PHP_EOL;
    echo "Email: admin@demo.com" . PHP_EOL;
    echo "Contraseña: password" . PHP_EOL;
} else {
    echo "✗ Usuario no encontrado" . PHP_EOL;
}
