<?php
// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale; // Replace with your actual model name
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

        if ($request->supplier_code && $request->supplier_code != 'all') {
            $query->where('supplier_code', $request->supplier_code);
        }

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
}
