<?php

namespace Database\Factories;

use App\Models\Construction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Construction>
 */
class ConstructionFactory extends Factory
{
    const ITEMS = [
        [
            'slug' => 'if',
            'title' => 'Условная конструкция если',
        ],
        [
            'slug' => 'for',
            'title' => 'Цикл со счётчиком',
        ],
        [
            'slug' => 'comment',
            'title' => 'Комментарий',
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $constructionData = fake()->unique()->randomElement(self::ITEMS);
        return [
            'slug' => $constructionData['slug'],
            'title' => $constructionData['title'],
            'description' => fake()->text,
        ];
    }
}
