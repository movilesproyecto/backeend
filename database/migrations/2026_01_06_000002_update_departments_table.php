<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('departments', 'bedrooms')) {
                $table->integer('bedrooms')->default(1);
            }
            if (!Schema::hasColumn('departments', 'price_per_night')) {
                $table->decimal('price_per_night', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('departments', 'rating_avg')) {
                $table->decimal('rating_avg', 3, 2)->default(0);
            }
            if (!Schema::hasColumn('departments', 'amenities')) {
                $table->json('amenities')->nullable();
            }
            if (!Schema::hasColumn('departments', 'images')) {
                $table->json('images')->nullable();
            }
            if (!Schema::hasColumn('departments', 'published')) {
                $table->boolean('published')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $cols = ['address','bedrooms','price_per_night','rating_avg','amenities','images','published'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('departments', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
