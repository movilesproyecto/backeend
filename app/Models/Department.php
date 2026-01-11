<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;
use App\Models\User;

class Department extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'address',
        'bedrooms',
        'price_per_night',
        'rating_avg',
        'amenities',
        'images',
        'published',
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'published' => 'boolean',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
}
