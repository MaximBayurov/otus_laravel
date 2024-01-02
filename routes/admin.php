<?php

use App\Http\Controllers\ConstructionsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LanguageController;

Route::get('/', function () {
    if (!Auth::user()?->can('admin.home')) {
        return redirect()->route('home');
    }
    return view('pages.admin.index');
})->name('home');

Route::resource('constructions', ConstructionsController::class);
Route::resource('languages', LanguageController::class);

Route::prefix('/import')->name('import.')->controller(ImportController::class)->group(function () {
    Route::get('/',"index")->name('index');
    Route::post('/fields',"getFields")->name('fields');
    Route::post('/',"start")->name('start');
});

Route::prefix('/export')->name('export.')->controller(ExportController::class)->group(function () {
    Route::get('/',"index")->name('index');
    Route::post('/',"start")->name('start');
});
