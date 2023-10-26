<?php

namespace Database\Seeders;

use App\Models\Construction;
use Database\Factories\ConstructionFactory;
use Illuminate\Database\Seeder;

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
            ->count(count($factory::ITEMS))
            ->create();
    }
}
