<?php
namespace App\Http\Controllers;

use App\Models\GrnEntry;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\SalesHistory;

class SalesEntryController extends Controller
{
    public function create()
    {
        $suppliers = Supplier::all();
        $items = GrnEntry::select('item_name', 'item_code', 'code')->distinct()->get();
        $entries = GrnEntry::latest()->take(10)->get(); // Show recent 10 entries
        $sales = Sale::latest()->get(); 
        $customers = Customer::all();
        $totalSum = $sales->sum('total');
        $customersWithSales = Sale::select('customer_code', 'customer_name')
                                ->distinct()
                                ->orderBy('customer_name')
                                ->get();
        


        
        return view('dashboard.sales.form', compact('suppliers', 'items', 'entries','sales','customers','totalSum','customersWithSales'));
    }

 public function store(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'supplier_code' => 'required',
        'customer_code' => 'required|string|max:255',
        'customer_name' => 'required|string|max:255',
        'code' => 'required',
        'item_code' => 'required',
        'item_name' => 'required',
        'weight' => 'required|numeric',
        'price_per_kg' => 'required|numeric',
        'total' => 'required|numeric',
        'packs' => 'required|integer',
    ]);

    try {
        Sale::create([
            'supplier_code' => $validated['supplier_code'],
            'customer_code' => $validated['customer_code'],
            'customer_name' => $validated['customer_name'],
            'code' => $validated['code'],
            'item_code' => $validated['item_code'],
            'item_name' => $validated['item_name'],
            'weight' => $validated['weight'],
            'price_per_kg' => $validated['price_per_kg'],
            'total' => $validated['total'],
            'packs' => $validated['packs'],
        ]);

        return redirect()->back()
                         ->with('success', 'GRN Entry successfully added to Sales!')
                         ->withInput($request->only(['customer_code', 'customer_name'])); // Only flash these two inputs
    } catch (\Exception $e) {
        return redirect()->back()
                         ->withErrors(['error' => 'Failed to add sales entry: ' . $e->getMessage()])
                         ->withInput($request->only(['customer_code', 'customer_name'])); // Only flash these two inputs for errors
    }
}
public function moveToHistory(Request $request)
{
    // Get all sales records
    $sales = Sale::all();

    // Move each record to sales_history
    foreach ($sales as $sale) {
        SalesHistory::create($sale->toArray());
    }

    // Delete all from sales table
    Sale::truncate();

    return response()->json(['success' => true]);
}
 public function getCustomerSales($customerCode)
    {
        $sales = Sales::where('customer_code', $customerCode)
                      ->orderBy('created_at', 'desc')
                      ->get();

        return response()->json(['sales' => $sales]);
    }


}
