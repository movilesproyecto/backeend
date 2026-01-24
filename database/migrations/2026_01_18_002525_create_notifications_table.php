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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['success', 'warning', 'info', 'error'])->default('info');
            $table->string('icon')->default('bell');
            $table->boolean('read')->default(false);
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action_type')->nullable(); // reservation, review, payment, etc.
            $table->unsignedBigInteger('action_id')->nullable(); // ID de la reserva, reseña, etc.
            $table->timestamps();

            $table->index(['user_id', 'read']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
