<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\GrnEntry;
use App\Models\Sale;
use App\Models\Item;

class DashboardController extends Controller
{
   public function index()
{
    // Customers list
    $customers = Customer::select('short_name', 'name')->get();

    // GRN entries (latest first)
    $entries = GrnEntry::orderBy('txn_date', 'desc')->get();

    // Fetch items for pack_due values
    $items = Item::select('no', 'pack_due')->get(); // Add this line

    // 1. Fetch all sales with basic validation (must have ID and weight)
    $allSales = Sale::whereNotNull('id')
        ->whereNotNull('weight')
        ->get();

    // 2. Split into groups

    // "New" sales: not processed and not bill_printed
    $sales = $allSales->filter(function ($sale) {
        return ($sale->processed === 'N' || is_null($sale->processed))
            && is_null($sale->bill_printed);
    })->values();

    // Printed & unprinted groups
    $printedSales   = $allSales->where('bill_printed', 'Y')->values();
    $unprintedSales = $allSales->where('bill_printed', 'N')->values();

    // Total sum for "new" sales group
    $totalSum = $sales->sum(function ($s) {
        return $s->weight * $s->price_per_kg;
    });

    // Send data to the view
    return view(
        'reactdashboard.sales.entry',
        compact(
            'customers',
            'entries',
            'sales',
            'totalSum',
            'printedSales',
            'unprintedSales',
            'items' // Add this line
        )
    );
}
}
