<?php

use App\Http\Controllers\ConstructionsController;
use App\Http\Controllers\LanguageController;

Route::get('/', function () {
    if (!Auth::user()?->can('admin.home')) {
        return redirect()->route('home');
    }
    return view('pages.admin.index');
})->name('home');

Route::resource('constructions', ConstructionsController::class);
Route::resource('languages', LanguageController::class);
