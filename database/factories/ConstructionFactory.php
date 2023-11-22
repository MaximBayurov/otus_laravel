<?php

namespace Database\Factories;

use App\Models\Construction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Construction>
 */
class ConstructionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->text(20);
        return [
            'slug' => str_slug($title),
            'title' => $title,
            'description' => fake()->text,
        ];
    }
}
