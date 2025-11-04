<?php

use Illuminate\Support\Facades\Route;


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Route::domain('admin.localhost')->group(function () {
//    Route::get("{all}", function () {
//        return view('home');
//    })->where('all', '^(?!admin|!storage).*$');
//});

