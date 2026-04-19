<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\InventoryController;
use App\Http\Controllers\Web\LayerViewController;
use App\Http\Controllers\Web\LayupViewController;
use App\Http\Controllers\Web\SupplierViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/suppliers', [SupplierViewController::class, 'index'])->name('supplier.index');
    Route::get('/inventory/layups/{id}', [InventoryController::class, 'layup'])->name('inventory.layup');
    Route::get('/inventory/layers/{id}', [InventoryController::class, 'layer'])->name('inventory.layer');
});




require __DIR__ . '/auth.php';
