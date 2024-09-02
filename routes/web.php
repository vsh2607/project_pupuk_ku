<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MasterFarmerController;

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/logout', [LoginController::class, 'logout']);

Route::group(['prefix' => '/', 'middleware' => ['auth']], function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::group(['prefix' => '/master-data', 'middleware' => ['auth']], function () {
    Route::prefix('/master-farmer')->group(function () {
        Route::get('/', [MasterFarmerController::class, 'index']);
        Route::get('/list-data', [MasterFarmerController::class, 'listData']);
        Route::get('{id}/info', [MasterFarmerController::class, 'viewForm']);
        Route::get('{id}/edit', [MasterFarmerController::class, 'editForm']);
        Route::post('{id}/edit', [MasterFarmerController::class, 'updateData']);
        Route::get('/add', [MasterFarmerController::class, 'addForm']);
        Route::post('/add', [MasterFarmerController::class, 'addData']);
    });
});
