<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MasterPlantController;
use App\Http\Controllers\MasterFarmerController;
use App\Http\Controllers\MasterFertilizerController;
use App\Http\Controllers\ModuleFertilizerDistributionController;
use App\Http\Controllers\ModulePlantingPlanController;

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

        Route::get("/get-fertilizer-qty-owned", [MasterFarmerController::class, 'getFertilizerOwned']);
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

    Route::prefix('/master-fertilizer')->group(function () {
        Route::get('/', [MasterFertilizerController::class, 'index']);
        Route::get('/list-data', [MasterFertilizerController::class, 'listData']);
        Route::get('{id}/info', [MasterFertilizerController::class, 'viewForm']);
        Route::get('{id}/edit', [MasterFertilizerController::class, 'editForm']);
        Route::post('{id}/edit', [MasterFertilizerController::class, 'updateData']);
        Route::get('/add', [MasterFertilizerController::class, 'addForm']);
        Route::post('/add', [MasterFertilizerController::class, 'addData']);
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
        Route::post('/update-loan', [ModuleFertilizerDistributionController::class, 'updateLoan']);

    });

    Route::prefix('/planting-plan')->group(function(){
        Route::get('/', [ModulePlantingPlanController::class, 'index']);
        Route::get('/list-data', [ModulePlantingPlanController::class, 'listData']);
        Route::get('/add', [ModulePlantingPlanController::class, 'addForm']);
        Route::post('/add', [ModulePlantingPlanController::class, 'addData']);
        Route::get('{id}/edit', [ModulePlantingPlanController::class, 'editForm']);
        Route::post('{id}/edit', [ModulePlantingPlanController::class, 'updateData']);
        Route::post('/', [ModulePlantingPlanController::class, 'deleteData']);
    });


    Route::prefix('/fertilizer-distribution-periode')->group(function(){
        Route::get('/', [ModuleFertilizerDistributionController::class, 'indexPeriode']);
        Route::get('/list-data-periode', [ModuleFertilizerDistributionController::class, 'listDataPeriode']);
        Route::get('{id}/print', [ModuleFertilizerDistributionController::class, 'printData']);

    });

});


Route::group(['prefix' => '/resources', 'middleware' => ['auth']], function () {
    Route::get('/list-all-plant', [MasterPlantController::class, 'listAllDataPlant']);
    Route::get('/list-all-fertilizer', [MasterFertilizerController::class, 'listAllDataFertilizer']);
    Route::get('/list-lender-candidates',[MasterFarmerController::class, 'listLenderCandidates']);
    Route::get('/list-borrower-candidates',[MasterFarmerController::class, 'listBorrowerCandidates']);
    Route::post('/list-lender-lended', [ModuleFertilizerDistributionController::class, 'listLenderLended']); //list pemberi peminjaman


    Route::get("/list-planting-plan", [ModulePlantingPlanController::class, 'listPlantingPlan']);
    Route::get("/list-farmer-borrower", [ModuleFertilizerDistributionController::class, 'listFarmerBorrower']);


    Route::get("/list-all-farmer", [MasterFarmerController::class, 'listAllDataFarmer']);
    Route::get("{id}/get-farmer-fertilizer-plant-data", [MasterFarmerController::class, 'getFarmerFertilizerPlantData']);

    Route::get("/list-farmer-fertilizer-needed", [ModuleFertilizerDistributionController::class, 'getFarmerFertilizerNeeded']);

});
