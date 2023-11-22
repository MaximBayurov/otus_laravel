<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var \Database\Factories\LanguageFactory $factory */
        $factory = Language::factory();
        $factory
            ->count(10)
            ->create();
    }
}
