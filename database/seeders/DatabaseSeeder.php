<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         for ($i=1; $i <= 10; $i++) {
             $email = "user{$i}@admin.admin";
             DB::table('users')->insert([
                 'name' => fake()->name,
                 'email' => $email,
                 'password' => \Hash::make($email),
             ]);
         }
    }
}
