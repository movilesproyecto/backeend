<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Department;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'address' => $this->faker->address(),
            'bedrooms' => $this->faker->numberBetween(1,5),
            'price_per_night' => $this->faker->randomFloat(2, 20, 300),
            'rating_avg' => $this->faker->randomFloat(1, 1, 5),
            'amenities' => [ 'wifi', 'kitchen', 'air_conditioning' ],
            'images' => [ $this->faker->imageUrl(800,600), $this->faker->imageUrl(800,600) ],
            'published' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
