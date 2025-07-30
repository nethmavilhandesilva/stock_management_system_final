<?php
// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale; 
use App\Models\GrnEntry;// Replace with your actual model name
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $suppliers = Sale::select('supplier_code')->distinct()->pluck('supplier_code');
        return view('dashboard.reports.salesbasedonsuppliers', compact('suppliers'));
    }

    public function fetch(Request $request)
{
    $query = Sale::query();
     

    // Filter by supplier_code (optional)
    if ($request->filled('supplier_code') && $request->supplier_code !== 'all') {
        $query->where('supplier_code', $request->supplier_code);
    }

    // Filter by GRN code if selected
    if ($request->filled('code')) {
        $query->where('code', $request->code);
    }

    // Filter by date range
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('created_at', [
            Carbon::parse($request->start_date)->startOfDay(),
            Carbon::parse($request->end_date)->endOfDay()
        ]);
    }

    $records = $query->get([
        'supplier_code',
        'code',
        'packs',
        'weight',
        'price_per_kg',
        'item_code',
        'total',
        'customer_code',
        'created_at'
    ]);

    return view('dashboard.reports.resultsalesbasedonsuppliers', [
        'records' => $records,
        'shop_no' => 'C11'
    ]);
}

    public function fetchItemReport(Request $request)
{
    $validated = $request->validate([
        'item_code' => 'required',
        'supplier_code' => 'nullable|string',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
    ]);

    $query = \App\Models\Sale::where('item_code', $validated['item_code']);

    if ($request->supplier_code && $request->supplier_code !== 'all') {
        $query->where('supplier_code', $request->supplier_code);
    }

    if ($request->start_date) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->end_date) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    $sales = $query->get(['item_code', 'packs', 'weight', 'price_per_kg', 'total', 'customer_code', 'supplier_code', 'bill_no']);

    return view('dashboard.reports.item-wise-report', compact('sales'));
}
   public function getweight(Request $request)
    {
        $supplierCode = $request->input('supplier_code');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // <--- CHANGED: Query the Sale model instead of GrnEntry
        $query = Sale::query();

        // Filter by supplier code if not 'all'
        if ($supplierCode && $supplierCode !== 'all') {
            $query->where('supplier_code', $supplierCode);
        }

        // Filter by date range (assuming 'created_at' is the relevant date column in Sale model)
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate); // <--- CHANGED: Using 'created_at'
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate); // <--- CHANGED: Using 'created_at'
        }

        // Order the results (you might want to order by 'created_at' or 'supplier_code'
        // depending on how you want the data presented in the report)
        $salesRecords = $query->orderBy('created_at', 'asc')->get(); // <--- CHANGED: Variable name to reflect Sale data

        return view('dashboard.reports.weight-based-report', [
            'salesRecords' => $salesRecords, // <--- CHANGED: Pass 'salesRecords' to the view
            'selectedSupplierCode' => $supplierCode,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
      public function getGrnSalecodereport(Request $request)
    {
        $grnCode = $request->input('grn_code'); // This is the $entry->code from the GRN dropdown
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Start building the query on the Sale model
        $query = Sale::query();

        // Filter by the selected GRN code (from GrnEntry::code which matches Sale::code)
        if ($grnCode) {
            $query->where('code', $grnCode); // Assuming 'code' in the Sale model stores the GRN code
        } else {
             // If no GRN code is selected (though modal makes it required),
             // you might want to return an empty set or an error.
             // For now, let's just make sure the query doesn't proceed without a required GRN.
             return redirect()->back()->withErrors('Please select a GRN code.');
        }


        // Filter by date range using 'created_at' or 'sale_date' (adjust if your Sale model uses a different date column)
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $sales = $query->orderBy('created_at', 'asc')->get();

        // Get the full GRN entry details for display in the report header
        $selectedGrnEntry = GrnEntry::where('code', $grnCode)->first();

        return view('dashboard.reports.grn_sale_code_report', [ // Make sure this matches your new Blade file name
            'sales' => $sales,
            'selectedGrnCode' => $grnCode,
            'selectedGrnEntry' => $selectedGrnEntry, // Pass the full GRN entry for display
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}




