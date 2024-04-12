<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => "admin"], function () {
    Route::get('login', [AdminController::class, 'loginPage'])->name('admin.loginPage');
    Route::post('login', [AdminController::class, 'login'])->name('admin.login');
    Route::get('logout', [AdminController::class, 'logout'])->name('admin.logout');

    Route::middleware(['Admin'])->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::group(['prefix' => "ad"], function () {
            Route::get('/', [AdminController::class, 'ad'])->name('admin.ad');
        });
    });
});