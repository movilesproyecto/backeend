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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->string('file_path'); // ruta del archivo en storage
            $table->string('file_name'); // nombre original del archivo
            $table->unsignedBigInteger('file_size'); // tamaño en bytes
            $table->string('mime_type'); // tipo MIME (image/jpeg, etc)
            $table->unsignedBigInteger('uploaded_by')->nullable(); // usuario que subió
            $table->boolean('is_primary')->default(false); // imagen principal del departamento
            $table->timestamps();

            // Foreign keys
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');

            // Índices
            $table->index('department_id');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
