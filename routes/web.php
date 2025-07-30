<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\GrnEntryController;
use App\Http\Controllers\SalesEntryController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');
// New default route to redirect to login
Route::get('/', function () {
    return redirect('/login');
});



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
Route::put('/sales/update/{sale}', [SalesEntryController::class, 'update'])->name('sales.update');
Route::delete('/sales/delete/{sale}', [SalesEntryController::class, 'destroy'])->name('sales.delete');
// Route for the AJAX call to mark sales as printed and processed (triggered by F1 key press)

Route::post('/sales/mark-all-processed', [SalesEntryController::class, 'markAllAsProcessed'])->name('sales.markAllAsProcessed');
//Bill printing
Route::post('/sales/mark-printed', [SalesEntryController::class, 'markAsPrinted'])->name('sales.markAsPrinted');
//Reports
// routes/web.php
Route::get('/report', [ReportController::class, 'index'])->name('report.index');
Route::post('/report/fetch', [ReportController::class, 'fetch'])->name('report.fetch');
Route::post('/report/item', [App\Http\Controllers\ReportController::class, 'fetchItemReport'])->name('report.item.fetch');


require __DIR__.'/auth.php';
