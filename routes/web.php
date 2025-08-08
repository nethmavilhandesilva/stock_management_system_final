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

// Middleware for authenticated users
Route::middleware('auth')->group(function () {
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard
    Route::get('/dashboard', [SalesEntryController::class, 'create'])->name('dashboard');

    // Items
    Route::resource('items', ItemController::class)->except(['create', 'show']);

    // Customers
    Route::resource('customers', CustomerController::class)->except(['create', 'show']);

    // Suppliers
    Route::resource('suppliers', SupplierController::class)->except(['create', 'show']);

    // GRN (Goods Received Note)
    Route::resource('grn', GrnEntryController::class)->except(['create', 'show']);
    Route::post('/grn/{id}/hide', [GrnEntryController::class, 'hide'])->name('grn.hide');
    Route::post('/grn/{id}/unhide', [GrnEntryController::class, 'unhide'])->name('grn.unhide');

    // Sales
    Route::post('/grn-entry', [SalesEntryController::class, 'store'])->name('grn.store'); // This is a POST to create a sales record from GRN data
    Route::post('/sales/mark-all-processed', [SalesEntryController::class, 'markAllAsProcessed'])->name('sales.markAllAsProcessed');
    Route::post('/sales/mark-printed', [SalesEntryController::class, 'markAsPrinted'])->name('sales.markAsPrinted');
    Route::post('/sales/save-as-unprinted', [SalesEntryController::class, 'saveAsUnprinted'])->name('sales.save-as-unprinted');
    Route::post('/clear-data', [SalesEntryController::class, 'clearAll'])->name('clear.data');
    Route::post('/sales/day-start', [SalesEntryController::class, 'dayStart'])->name('sales.dayStart');
    Route::get('api/sales/unprinted/{customer_code}', [SalesEntryController::class, 'getUnprintedSales']);
    Route::get('/sales/all-data', [SalesEntryController::class, 'getAllSalesData']);
    Route::get('/sales/all', [SalesEntryController::class, 'getAllSales']);
    Route::put('/sales/{sale}', [SalesEntryController::class, 'update'])->name('sales.update');
    Route::delete('/sales/{sale}', [SalesEntryController::class, 'destroy'])->name('sales.delete');

    // Reports
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::post('/report/fetch', [ReportController::class, 'fetch'])->name('report.fetch');
    Route::post('/report/item', [App\Http\Controllers\ReportController::class, 'fetchItemReport'])->name('report.item.fetch');
    Route::post('/report/weight', [ReportController::class, 'getweight'])->name('report.supplier_grn.fetch');
    Route::post('/report/sale-code', [ReportController::class, 'getGrnSalecodereport'])->name('report.grn_sale.fetch');
    Route::get('/reports/sales/filter', [ReportController::class, 'getSalesFilterReport'])->name('report.sales.filter');
    Route::get('/reports/grn-sales-overview', [ReportController::class, 'getGrnSalesOverviewReport'])->name('report.grn.sales.overview');
    Route::get('/reports/grn-sales-overview2', [ReportController::class, 'getGrnSalesOverviewReport2'])->name('report.grn.sales.overview2');
    Route::post('/reports/salesadjustment/filter', [ReportController::class, 'salesAdjustmentReport'])->name('reports.salesadjustment.filter');
    Route::post('/report/download/{reportType}/{format}', [ReportController::class, 'downloadReport'])->name('report.download');

    // Customers Loans
    Route::resource('customers-loans', CustomersLoanController::class);
    Route::get('/customers/{id}/loans-total', [CustomersLoanController::class, 'getTotalLoanAmount']);
    Route::post('/get-loan-amount', [SalesEntryController::class, 'getLoanAmount'])->name('get.loan.amount');
});

// Guest-only routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
});

// API-like routes (no authentication needed if used by public)
Route::get('api/grn-entry/{code}', [GrnEntryController::class, 'getGrnEntryByCode']);
Route::get('/grn-used-data/{code}', [GrnEntryController::class, 'getUsedData']);
Route::get('/fetch-customer/{customer_code?}', [SalesEntryController::class, 'fetchCustomer'])->name('fetch.customer');

require __DIR__.'/auth.php';