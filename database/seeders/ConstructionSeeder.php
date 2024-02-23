<?php

namespace Database\Seeders;

use App\Models\Construction;
use Domain\ModuleLanguageConstructions\Models\Construction as DomainConstruction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ConstructionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var \Database\Factories\ConstructionFactory $factory */
        $factory = Construction::factory();
        $factory
            ->count(10)
            ->create();
        Cache::tags([DomainConstruction::class])->flush();
    }
}
