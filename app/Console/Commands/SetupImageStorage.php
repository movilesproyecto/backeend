<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SetupImageStorage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'storage:setup-images';

    /**
     * The description of the console command.
     */
    protected $description = 'Configura el almacenamiento de imágenes de departamentos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🖼️  Configurando almacenamiento de imágenes...');

        // Crear directorio base
        $basePath = 'departments';
        if (!Storage::disk('public')->exists($basePath)) {
            Storage::disk('public')->makeDirectory($basePath);
            $this->info("✓ Directorio creado: storage/app/public/{$basePath}");
        } else {
            $this->info("✓ Directorio ya existe: storage/app/public/{$basePath}");
        }

        // Verificar enlace simbólico
        $linkPath = public_path('storage');
        $storagePath = storage_path('app/public');

        if (is_link($linkPath)) {
            $this->info('✓ Enlace simbólico ya existe: public/storage');
        } else {
            try {
                symlink($storagePath, $linkPath);
                $this->info('✓ Enlace simbólico creado: public/storage → storage/app/public');
            } catch (\Exception $e) {
                $this->warn('⚠️  No se pudo crear el enlace simbólico automáticamente.');
                $this->line('   Ejecuta manualmente: php artisan storage:link');
            }
        }

        $this->info('');
        $this->info('📁 Estructura de almacenamiento:');
        $this->line('   storage/app/public/departments/{department_id}/');
        $this->line('   └── image1.jpg');
        $this->line('   └── image2.jpg');
        $this->line('');
        $this->info('🌐 URLs públicas:');
        $this->line('   /storage/departments/{department_id}/image1.jpg');
        $this->line('');
        $this->info('✨ ¡Almacenamiento de imágenes configurado exitosamente!');
    }
}
