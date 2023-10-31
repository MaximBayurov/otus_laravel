<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * @var \Database\Factories\UserFactory $factory
         */
        $factory = User::factory();
        $factory
            ->count(10)
            ->create();

        $factory->create()->assignRole(RolesEnum::MODERATOR);
        $factory->create()->assignRole(RolesEnum::ADMIN);
    }
}
