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

        return view('dashboard.reports.weight-based-report', [ // Make sure this matches your new Blade file name
            'sales' => $sales,
            'selectedGrnCode' => $grnCode,
            'selectedGrnEntry' => $selectedGrnEntry, // Pass the full GRN entry for display
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
      public function getSalesFilterReport(Request $request)
    {
        // Start with all sales data
        $query = Sale::query();

        // Apply filters if present
        if ($request->filled('supplier_code')) {
            $query->where('supplier_code', $request->input('supplier_code'));
        }

        if ($request->filled('customer_code')) {
            $query->where('customer_code', $request->input('customer_code'));
        }

        if ($request->filled('item_code')) {
            // Assuming item_code in Sale model refers to the 'no' attribute in Item model
            $query->where('item_code', $request->input('item_code'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        // Apply ordering
        switch ($request->input('order_by', 'id_desc')) { // Default to id_desc
            case 'id_asc':
                $query->orderBy('id', 'asc');
                break;
            case 'customer_code_asc':
                $query->orderBy('customer_code', 'asc');
                break;
            case 'customer_code_desc':
                $query->orderBy('customer_code', 'desc');
                break;
            case 'item_name_asc':
                $query->orderBy('item_name', 'asc');
                break;
            case 'item_name_desc':
                $query->orderBy('item_name', 'desc');
                break;
            case 'total_desc':
                $query->orderBy('total', 'desc');
                break;
            case 'total_asc':
                $query->orderBy('total', 'asc');
                break;
            case 'weight_desc':
                $query->orderBy('weight', 'desc');
                break;
            case 'weight_asc':
                $query->orderBy('weight', 'asc');
                break;
            case 'id_desc':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $sales = $query->get([
            'code', 'packs', 'item_name', 'weight', 'price_per_kg', 'total', 'bill_no', 'customer_code', 'created_at'
        ]);

        // Calculate grand total
        $grandTotal = $sales->sum('total');

        // Pass data to the report view
        return view('dashboard.reports.sales_filter_report', compact('sales', 'grandTotal', 'request'));
    }
        public function getGrnSalesOverviewReport()
    {
        // Fetch all GRN entries
        $grnEntries = GrnEntry::all();

        $reportData = [];

        foreach ($grnEntries as $grnEntry) {
            // Fetch sales related to this GRN entry's code
            // IMPORTANT: Confirm 'code' in GrnEntry matches the foreign key in Sale.
            // If Sale has a 'grn_entry_code' column that stores GrnEntry->code,
            // then use: $relatedSales = Sale::where('grn_entry_code', $grnEntry->code)->get();
            $relatedSales = Sale::where('code', $grnEntry->code)->get();

            $totalSoldPacks = $relatedSales->sum('packs');
            $totalSoldWeight = $relatedSales->sum('weight');
            $totalSalesValueForGrn = $relatedSales->sum('total'); // NEW: Sum of total for related sales

            $remainingPacks = $grnEntry->original_packs - $totalSoldPacks;
            $remainingWeight = $grnEntry->original_weight - $totalSoldWeight;

            $reportData[] = [
                'date'             => Carbon::parse($grnEntry->created_at)->timezone('Asia/Colombo')->format('Y-m-d H:i:s'),
                'grn_code'         => $grnEntry->code,
                'item_name'        => $grnEntry->item_name,
                'original_packs'   => $grnEntry->original_packs,
                'original_weight'  => $grnEntry->original_weight,
                'sold_packs'       => $totalSoldPacks,
                'sold_weight'      => $totalSoldWeight,
                'total_sales_value' => $totalSalesValueForGrn, // NEW: Add to report data
                'remaining_packs'  => $remainingPacks,
                'remaining_weight' => number_format($remainingWeight, 2),
            ];
        }

        // Pass the processed data to the view
        return view('dashboard.reports.grn_sales_overview_report', [
            'reportData' => collect($reportData)
        ]);
    }
}




