<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = fake();
        for ($i=1; $i <= 10; $i++) {
            $email = $faker->unique()->safeEmail();
            DB::table('users')->insert([
                'name' => $faker->name,
                'email' => $email,
                'password' => \Hash::make($email),
            ]);
        }
    }
}
