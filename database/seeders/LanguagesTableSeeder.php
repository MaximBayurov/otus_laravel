<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'slug' => 'go',
                'title' => 'Go/golang',
                'description' => 'Компилируемый многопоточный язык программирования, разработанный внутри компании Google, для создания высокоэффективных программ, работающих на современных распределённых системах и многоядерных процессорах.',
            ],
            [
                'slug' => 'c',
                'title' => 'Си',
                'description' => 'Компилируемый статически типизированный язык программирования общего назначения, разработанный в 1969—1973 годах. Первоначально был разработан для реализации операционной системы UNIX, но впоследствии был перенесён на множество других платформ.',
            ],
            [
                'slug' => 'php',
                'title' => 'PHP: Hypertext Preprocessor',
                'description' => 'Язык программирования общего назначения с открытым исходным кодом. PHP специально сконструирован для веб-разработок и его код может внедряться непосредственно в HTML.',
            ],
        ];

        foreach ($languages as $language) {
            DB::table('languages')->insert($language);
        }
    }
}
