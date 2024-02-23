<?php

namespace Database\Seeders;

use App\Models\Construction;
use App\Models\Language;
use Illuminate\Database\Seeder;

class ConstructionLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        /** @var \Database\Factories\LanguageFactory $factory */
        $factory = Language::factory();
        $languages = $factory
            ->count(10)
            ->create()->toArray();

        /** @var \Database\Factories\ConstructionFactory $factory */
        $factory = Construction::factory();
        $constructions = $factory
            ->count(50)
            ->create();

        $constructions->map(function (Construction $construction) use ($languages) {
            $implementedInLanguages = fake()->randomElements($languages, fake()->numberBetween(0, 5));
            foreach ($implementedInLanguages as $language) {
                $construction->languages()->attach($language['id'], [
                    'code' => fake()->paragraph,
                ]);
            }
        });
    }
}
