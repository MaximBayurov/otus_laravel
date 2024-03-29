<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\Api\V1;
use App\Http\Controllers\Api\V2;
use Illuminate\Routing\Router;

if (!function_exists('getV1Routes')) {
    function getV1Routes(Router $router) {
        $router->apiResource('/languages', V1\LanguageController::class);
        $router->apiResource('/constructions', V1\ConstructionController::class);
        $router->any('/telegram', V1\TelegramWebhookController::class)->name('telegram');
        $router->prefix('/auth')
            ->name('auth.')
            ->group(function ($router) {
                $router->post('login', [V1\JwtAuthController::class, 'login'])->name('login');
                $router->post('logout', [V1\JwtAuthController::class, 'logout'])->name('logout');
                $router->post('refresh', [V1\JwtAuthController::class, 'refresh'])->name('refresh');
                $router->post('me', [V1\JwtAuthController::class, 'me'])->name('me');
            });
    }
}

if (!function_exists('getV2Routes')) {
    function getV2Routes(Router $router)
    {
        getV1Routes($router);
        $router->get('/languages/{language}', V2\ShowLanguageController::class)->name('languages.show');
    }
}

Route::prefix('/v1')
    ->name('v1.')
    ->group(getV1Routes(...));

Route::prefix('/v2')
    ->name('v2.')
    ->group(getV2Routes(...));
