<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This is a no-op migration: 2026_01_06_000004_create_reviews_table.php already creates the reviews table.
     * This file exists to maintain migration history integrity (prevents duplicate table creation errors).
     */
    public function up(): void
    {
        // Intentionally left blank - reviews table is created by earlier migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank - do not modify existing table
    }
};
