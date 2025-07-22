<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\GrnEntryController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
//Item
Route::resource('items', ItemController::class);
//Customers
Route::resource('customers', CustomerController::class);
//Supliers
Route::resource('suppliers', SupplierController::class);
//GRN
Route::resource('grn', GrnEntryController::class);

Route::get('/grn/{id}/edit', [GrnEntryController::class, 'edit'])->name('grn.edit');
Route::put('/grn/{id}', [GrnEntryController::class, 'update'])->name('grn.update');
Route::delete('/grn/{id}', [GrnEntryController::class, 'destroy'])->name('grn.destroy');



require __DIR__.'/auth.php';
