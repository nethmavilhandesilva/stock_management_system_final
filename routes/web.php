<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\GrnEntryController;
use App\Http\Controllers\SalesEntryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CustomersLoanController;

// New default route to redirect to login
Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/register', [RegisteredUserController::class, 'create'])
     ->middleware('guest')
     ->name('register');
//Item
Route::resource('items', ItemController::class);

//Customers
Route::resource('customers', CustomerController::class);

//Supliers
Route::resource('suppliers', SupplierController::class);

//GRN
Route::resource('grn', GrnEntryController::class);
Route::post('/grn/store', [GrnEntryController::class, 'store'])->name('grn.store2');
Route::get('api/grn-entry/{code}', [GrnEntryController::class, 'getGrnEntryByCode']);
Route::get('/grn-used-data/{code}', [GrnEntryController::class, 'getUsedData']);
Route::post('/grn/{id}/hide', [GrnEntryController::class, 'hide'])->name('grn.hide');
Route::post('/grn/{id}/unhide', [GrnEntryController::class, 'unhide'])->name('grn.unhide');


//Sales
Route::get('/dashboard', [SalesEntryController::class, 'create'])->name('dashboard');
Route::post('/grn-entry', [SalesEntryController::class, 'store'])->name('grn.store');
Route::put('/sales/update/{sale}', [SalesEntryController::class, 'update'])->name('sales.update');
Route::delete('/sales/delete/{sale}', [SalesEntryController::class, 'destroy'])->name('sales.delete');

// Route for the AJAX call to mark sales as printed and processed
Route::post('/sales/mark-all-processed', [SalesEntryController::class, 'markAllAsProcessed'])->name('sales.markAllAsProcessed');
Route::get('api/sales/unprinted/{customer_code}', [SalesEntryController::class, 'getUnprintedSales']);

// FIX: Make the customer_code parameter optional to prevent route-helper errors
Route::get('/fetch-customer/{customer_code?}', [SalesEntryController::class, 'fetchCustomer'])->name('fetch.customer');

//Bill printing
Route::post('/sales/mark-printed', [SalesEntryController::class, 'markAsPrinted'])->name('sales.markAsPrinted');
Route::post('/sales/save-as-unprinted', [SalesEntryController::class, 'saveAsUnprinted'])->name('sales.save-as-unprinted');
Route::put('sales/update/{saleId}', 'SalesEntryController@update');
// Clear data route
Route::post('/clear-data', [SalesEntryController::class, 'clearAll'])->name('clear.data');
Route::get('/sales/all-data', [SalesEntryController::class, 'getAllSalesData']);
Route::get('/sales/all', [SalesEntryController::class, 'getAllSales']);
Route::post('/sales/day-start', [SalesEntryController::class, 'dayStart'])->name('sales.dayStart');
//Reports
Route::get('/report', [ReportController::class, 'index'])->name('report.index');
Route::post('/report/fetch', [ReportController::class, 'fetch'])->name('report.fetch');
Route::post('/report/item', [App\Http\Controllers\ReportController::class, 'fetchItemReport'])->name('report.item.fetch');
Route::post('/report/weight', [ReportController::class, 'getweight'])->name('report.supplier_grn.fetch');
Route::post('/report/sale-code', [ReportController::class, 'getGrnSalecodereport'])->name('report.grn_sale.fetch');
Route::get('/reports/sales/filter', [ReportController::class, 'getSalesFilterReport'])->name('report.sales.filter');
Route::get('/reports/grn-sales-overview', [ReportController::class, 'getGrnSalesOverviewReport'])->name('report.grn.sales.overview');
Route::get('/reports/grn-sales-overview2', [ReportController::class, 'getGrnSalesOverviewReport2'])->name('report.grn.sales.overview2');
Route::post('/reports/salesadjustment/filter', [ReportController::class, 'salesAdjustmentReport'])->name('reports.salesadjustment.filter');

//Reports
Route::post('/report/download/{reportType}/{format}', [ReportController::class, 'downloadReport'])->name('report.download');
//customer loans
Route::resource('customers-loans', CustomersLoanController::class);
// Example route in web.php
Route::get('/customers/{id}/loans-total', [CustomersLoanController::class, 'getTotalLoanAmount']);
Route::post('/get-loan-amount', [SalesEntryController::class, 'getLoanAmount'])->name('get.loan.amount');
Route::get('/sales/codes', [SalesEntryController::class, 'listCodes'])->name('sales.codes');
Route::get('/sales/code/{code}', [SalesEntryController::class, 'showByCode'])->name('sales.byCode');

Route::post('/loan-report/results', [CustomersLoanController::class, 'loanReportResults'])->name('loan.report.results');
require __DIR__.'/auth.php';