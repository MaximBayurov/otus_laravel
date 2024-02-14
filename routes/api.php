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

use App\Http\Controllers\Api\V1\LanguageController;
use App\Http\Controllers\Auth\JwtAuthController;
use Illuminate\Routing\Router;

Route::prefix('/auth')
    ->name('auth.')
    ->group(function ($router) {
        Route::post('login', [JwtAuthController::class, 'login'])->name('login');
        Route::post('logout', [JwtAuthController::class, 'logout'])->name('logout');
        Route::post('refresh', [JwtAuthController::class, 'refresh'])->name('refresh');
        Route::post('me', [JwtAuthController::class, 'me'])->name('me');
    });


function getV1Routes(Router $router) {
    $router->apiResource('/languages', LanguageController::class);
}

Route::prefix('/v1')
    ->name('v1.')
    ->group(getV1Routes(...));
