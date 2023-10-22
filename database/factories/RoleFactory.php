<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    const AVAILABLE_ROLES = [
        [
            'code' => 'admin',
            'name' => 'Администратор',
        ]
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roleData = fake()->unique()->randomElement(self::AVAILABLE_ROLES);
        return [
            'code' => $roleData['code'],
            'name' => $roleData['name'],
            'description' => fake()->text,
        ];
    }
}
