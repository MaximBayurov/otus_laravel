<?php

namespace Database\Seeders;

use App\Models\Language;
use Domain\ModuleLanguageConstructions\Models\Language as DomainLanguage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

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
        Cache::tags([DomainLanguage::class])->flush();
    }
}
