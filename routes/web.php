<?php

use App\Http\Controllers\ModuleFertilizerDistributionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MasterFarmerController;
use App\Http\Controllers\MasterPlantController;

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


    Route::prefix('/master-plant')->group(function () {
        Route::get('/', [MasterPlantController::class, 'index']);
        Route::get('/list-data', [MasterPlantController::class, 'listData']);
        Route::get('{id}/info', [MasterPlantController::class, 'viewForm']);
        Route::get('{id}/edit', [MasterPlantController::class, 'editForm']);
        Route::post('{id}/edit', [MasterPlantController::class, 'updateData']);
        Route::get('/add', [MasterPlantController::class, 'addForm']);
        Route::post('/add', [MasterPlantController::class, 'addData']);
    });
});

Route::group(['prefix' => '/module-management', 'middleware' => ['auth']], function () {
    Route::prefix('/fertilizer-distribution')->group(function () {
        Route::get('/', [ModuleFertilizerDistributionController::class, 'index']);
        Route::get('/list-data', [ModuleFertilizerDistributionController::class, 'listData']);
        Route::get('{id}/info', [ModuleFertilizerDistributionController::class, 'viewForm']);
        Route::get('{id}/edit', [ModuleFertilizerDistributionController::class, 'editForm']);
        Route::post('{id}/edit', [ModuleFertilizerDistributionController::class, 'updateData']);
        Route::get('/add', [ModuleFertilizerDistributionController::class, 'addForm']);
        Route::post('/add', [ModuleFertilizerDistributionController::class, 'addData']);
    });

});


Route::group(['prefix' => '/resources', 'middleware' => ['auth']], function () {
    Route::get('/list-all-plant', [MasterPlantController::class, 'listAllDataPlant']);
    Route::get('/list-lender-candidates',[MasterFarmerController::class, 'listLenderCandidates']);
    Route::get('/list-borrower-candidates',[MasterFarmerController::class, 'listBorrowerCandidates']);

});
