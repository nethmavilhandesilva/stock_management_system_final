<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\GrnEntryController;
use App\Http\Controllers\SalesEntryController;
use App\Http\Controllers\BillController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');




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
Route::post('/grn/store', [GrnEntryController::class, 'store'])->name('grn.store2');
Route::get('/grn/{id}/edit', [GrnEntryController::class, 'edit'])->name('grn.edit');
Route::put('/grn/{id}', [GrnEntryController::class, 'update'])->name('grn.update');
Route::delete('/grn/{id}', [GrnEntryController::class, 'destroy'])->name('grn.destroy');
//Sales
Route::get('/dashboard', [SalesEntryController::class, 'create'])->name('dashboard');
Route::post('/grn-entry', [SalesEntryController::class, 'store'])->name('grn.store');
// Route for the AJAX call to mark sales as printed and processed (triggered by F1 key press)

Route::post('/sales/mark-all-processed', [SalesEntryController::class, 'markAllAsProcessed'])->name('sales.markAllAsProcessed');
//Bill printing
Route::post('/sales/mark-printed', [SalesEntryController::class, 'markAsPrinted'])->name('sales.markAsPrinted');
require __DIR__.'/auth.php';
