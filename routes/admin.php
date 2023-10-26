<?php

use App\Http\Controllers\ConstructionsController;
use App\Http\Controllers\LanguageController;

Route::get('/', function () {
    return view('pages.admin.index');
});

Route::resource('constructions', ConstructionsController::class);
Route::resource('languages', LanguageController::class);
