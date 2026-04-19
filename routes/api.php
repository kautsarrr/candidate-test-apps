<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\LayerController;
use App\Http\Controllers\LayupController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::apiResource('suppliers', SupplierController::class);
Route::apiResource('layups', LayupController::class);
Route::apiResource('layers', LayerController::class);
