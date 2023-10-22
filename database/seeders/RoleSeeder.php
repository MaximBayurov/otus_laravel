<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all();
        if (count($roles) > 0) {
            return;
        }
        /** @var \Database\Factories\RoleFactory $factory */
        $factory = Role::factory();
        $factory
            ->count(count($factory::AVAILABLE_ROLES))
            ->create();
    }
}
