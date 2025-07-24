<?php

namespace App\Http\Controllers;

use App\Models\GrnEntry;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;   // Still good to keep if you use transactions elsewhere
use Illuminate\Support\Facades\Log;  // Still good to keep for logging errors

class SalesEntryController extends Controller
{
    /**
     * Displays the sales entry form.
     * Now fetches ALL sales records, as none are removed from display.
     * The 'Processed' column is an internal flag, not a display filter.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $items = GrnEntry::select('item_name', 'item_code', 'code')->distinct()->get();
        $entries = GrnEntry::latest()->take(10)->get();

        // Fetch ALL sales records to display
         $sales = Sale::where('Processed', 'N')->get();
        $customers = Customer::all();
        $totalSum = $sales->sum('total'); // Sum will now be for all displayed sales
        $unprocessedSales = Sale::where('Processed', 'N')->get();
          $salesPrinted  = Sale::where('bill_printed', 'Y')
                            ->orderBy('customer_name') // Order for easier viewing
                            ->get()
                            ->groupBy('customer_code');
         $totalUnprocessedSum = $unprocessedSales->sum('total');
          $salesNotPrinted = Sale::where('bill_printed', 'N')
                            ->orderBy('customer_code')
                            ->get()
                            ->groupBy('customer_code'); // Group by customer for the display

        // Calculate total for unprocessed sales
        $totalUnprintedSum = Sale::where('bill_printed', 'N')->sum('total');

        return view('dashboard', compact('suppliers', 'items', 'entries', 'sales', 'customers', 'totalSum','unprocessedSales','salesPrinted','totalUnprocessedSum','salesNotPrinted','totalUnprintedSum'));
    }

    
    public function store(Request $request)
    {
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
                // Newly added sales are never printed initially
                'Processed' => 'N',    // Newly added sales are unprocessed initially
            ]);

            return redirect()->back()
                ->with('success', 'GRN Entry successfully added to Sales!')
                ->withInput($request->only(['customer_code', 'customer_name']));
        } catch (\Exception | \Illuminate\Database\QueryException $e) {
            Log::error('Failed to add sales entry: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Failed to add sales entry: ' . $e->getMessage()])
                ->withInput($request->only(['customer_code', 'customer_name']));
        }
    }

    
    public function markSalesAsPrinted(Request $request)
    {
        $request->validate([
            'sales_ids' => 'required|array',
            'sales_ids.*' => 'exists:sales,id',
        ]);

        try {
            DB::beginTransaction();

            Sale::whereIn('id', $request->input('sales_ids'))
                ->update([
                    'bill_printed' => 'Y', // Mark as printed
                    'Processed' => 'Y'     // Also mark as processed
                ]);

            DB::commit();

            return response()->json(['message' => 'Sales marked as printed and processed successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error marking sales as printed and processed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark sales as printed and processed.'], 500);
        }
    }
    // In your SalesController.php

   public function markAllAsProcessed(Request $request)
{
    try {
        DB::beginTransaction();

        Sale::where('Processed', 'N')->update([
            'Processed' => 'Y',
            'bill_printed' => DB::raw("IFNULL(bill_printed, 'N')") // Set to 'N' only if currently NULL
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'All sales with Processed = N are now marked as processed, and NULL bill_printed values set to N.'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        \Log::error('Error marking all sales as processed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to mark sales as processed: ' . $e->getMessage()
        ], 500);
    }
}




}