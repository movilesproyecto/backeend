<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Las imágenes ahora se almacenan como URLs en el column images (JSON array)
        // No hacer nada, mantener el column
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hacer nada
    }
};
