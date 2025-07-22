<?php
namespace App\Http\Controllers;

use App\Models\GrnEntry;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;

class SalesEntryController extends Controller
{
    public function create()
    {
        $suppliers = Supplier::all();
        $items = GrnEntry::select('item_name', 'item_code', 'code')->distinct()->get();
        $entries = GrnEntry::latest()->take(10)->get(); // Show recent 10 entries
        $sales = Sale::latest()->get(); 
        $customers = Customer::all();
        
        return view('dashboard.sales.form', compact('suppliers', 'items', 'entries','sales','customers'));
    }

    public function store(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'supplier_code' => 'required',
        'code' => 'required',
        'item_code' => 'required',
        'item_name' => 'required',
        'weight' => 'required|numeric',
        'price_per_kg' => 'required|numeric',
        'total' => 'required|numeric',
        'packs' => 'required|integer',
       
    ]);

    // Create the sale entry
    Sale::create($validated);

    return redirect()->back()->with('success', 'GRN Entry successfully added to Sales!');
}
}
