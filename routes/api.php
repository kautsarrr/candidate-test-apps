<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\LayerController;
use App\Http\Controllers\LayupController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::apiResource('suppliers', SupplierController::class);
Route::apiResource('layups', LayupController::class);
Route::apiResource('layers', LayerController::class);
Route::get('/suppliers/exportall', [SupplierController::class, 'exportAll']);
Route::get('/suppliers/{id}/export', [SupplierController::class, 'export']);
Route::post('/suppliers/import', [SupplierController::class, 'import']);
