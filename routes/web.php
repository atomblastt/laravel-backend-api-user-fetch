<?php

use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)
    ->prefix('/')->group(function () {
        Route::get('/', 'index');
        Route::post('/delete/{id}', 'destroy')->name('delete-user');
    });
