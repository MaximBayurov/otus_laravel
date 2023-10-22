<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Language>
 */
class LanguageFactory extends Factory
{
    const LANGUAGE_TITLES = [
        'php',
        'Go/golang',
        'Си'
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->randomElement(self::LANGUAGE_TITLES);
        return [
            'slug' => str_slug($title),
            'title' => $title,
            'description' => fake()->text,
        ];
    }
}
