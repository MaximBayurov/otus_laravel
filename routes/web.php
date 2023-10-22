<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {

    $generateAdvantages = function (int $count) {
        $result = [];
        if ($count <= 0) {
            return $result;
        }

        $currCount = 0;
        while($currCount < $count) {
            $currCount++;
            $result[] = sprintf("Преимущество %d", $currCount);
        }

        return $result;
    };
    $generateFeatures = function (int $count) {
        $result = [];
        if ($count <= 0) {
            return $result;
        }
        $icons = [
            "geo-fill", "tools", "toggles2", "speedometer", "gear-fill", "cpu-fill", "calendar3", "collection", "people-circle", "table", "speedometer2", "home",
        ];
        $currCount = 0;
        while ($currCount < $count) {
            $currCount++;
            $name = sprintf("Функция %d", $currCount);
            $code = sprintf("function-%d", $currCount);
            $result[$code] = [
                'name' => $name,
                'code' => $code,
                'description' => "Paragraph of text beneath the heading to explain the heading.",
                'icon' => $icons[array_rand($icons)],
            ];
        }
        return $result;
    };

    $features = $generateFeatures(rand(6,10));
    $getRandomFeatures = function ($min, $max = null) use ($features) {
        $featuresCount = count($features);
        if(empty($max)) {
            $max = $featuresCount;
        }
        return array_intersect_key(
            $features,
            array_flip(
                (array)array_rand($features, rand($min, min($featuresCount, $max)))
            )
        );
    };
    $subscriptions = [
        [
            'name' => 'Бесплатная',
            'price' => 0,
            'features' => $getRandomFeatures(1, 3),
            'advantages' => $generateAdvantages(rand(1, 3)),
            'link' => [
                'href' => '/login',
                'text' => 'Войти',
                'isOutline' => true,
            ]
        ],
        [
            'name' => 'Платная',
            'price' => 199.99,
            'features' => $getRandomFeatures(4, 6),
            'advantages' => $generateAdvantages(rand(4, 6)),
            'link' => [
                'href' => '/',
                'text' => 'Оформить',
                'isOutline' => false,
            ]
        ],
        [
            'name' => 'Платная (покруче)',
            'price' => 299.99,
            'features' => $features,
            'advantages' => $generateAdvantages(6),
            'link' => [
                'href' => '/',
                'text' => 'Оформить',
                'isOutline' => false,
            ]
        ],
    ];

    return view('pages.home', compact(
        'subscriptions',
        'features'
    ));
});
Route::view('/about', 'pages.about');
Route::get('/profile', function () {
    return view('pages.profile');
});
Route::match(['get', 'post'],'/login', function () {
    return view('pages.login');
});
Route::match(['get', 'post'], '/register', function () {
    return view('pages.register');
});

Route::get('user/{user}', function (\App\Models\User $user) {

    return [
        'roles' => $user->roles()
    ];
});
