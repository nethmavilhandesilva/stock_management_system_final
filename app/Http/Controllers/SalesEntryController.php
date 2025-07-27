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
        $unprocessedSales = Sale::whereIn('Processed', ['Y', 'N']) // Include both processed and unprocessed
    ->where(function ($query) {
        $query->where('bill_printed', 'N')
              ->orWhereNull('bill_printed');
    })
    ->get();

        $salesPrinted = Sale::where('bill_printed', 'Y')
            ->orderBy('customer_name')
            ->orderBy('bill_no') // Or ->orderBy('created_at') for chronological order
            ->get()
            ->groupBy('customer_code');
        $totalUnprocessedSum = $unprocessedSales->sum('total');
        $salesNotPrinted = Sale::where('bill_printed', 'N')
            ->orderBy('customer_code')
            ->get()
            ->groupBy('customer_code');
           


        // Calculate total for unprocessed sales
        $totalUnprintedSum = Sale::where('bill_printed', 'N')->sum('total');

        return view('dashboard', compact('suppliers', 'items', 'entries', 'sales', 'customers', 'totalSum', 'unprocessedSales', 'salesPrinted', 'totalUnprocessedSum', 'salesNotPrinted', 'totalUnprintedSum'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_code' => 'required',
            'customer_code' => 'required|string|max:255',
            'customer_name' => 'nullable',
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
                ->withInput($request->only(['customer_code', 'customer_name']));
        } catch (\Exception | \Illuminate\Database\QueryException $e) {
            Log::error('Failed to add sales entry: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Failed to add sales entry: ' . $e->getMessage()])
                ->withInput($request->only(['customer_code', 'customer_name']));
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
    public function markAsPrinted(Request $request)
    {
        // Debugging step: Log the incoming request data
        \Log::info('markAsPrinted Request Data:', $request->all());

        $salesIds = $request->input('sales_ids');
        $billNo = $request->input('bill_no');

        if (empty($salesIds)) {
            return response()->json(['status' => 'error', 'message' => 'No sales IDs provided.'], 400);
        }

        try {
            // This is the critical part to check for errors
            Sale::whereIn('id', $salesIds)->update([
                'bill_printed' => 'Y', // Ensure this column name is correct in your DB
                'processed' => 'Y', // Ensure this column name is correct in your DB
                'bill_no' => $billNo // Ensure this column name is correct and can accept the value
            ]);

            // Debugging step: Log success
            \Log::info('Sales records updated successfully for IDs:', $salesIds);

            return response()->json(['status' => 'success', 'message' => 'Sales marked as printed and processed successfully!']);

        } catch (\Exception $e) {
            // Debugging step: Log the exception details
            \Log::error('Error updating sales records:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(), // This is very detailed, useful for debugging
                'sales_ids' => $salesIds,
                'bill_no' => $billNo
            ]);
            return response()->json(['status' => 'error', 'message' => 'Failed to update sales records.'], 500);
        }
    }
    public function update(Request $request, Sale $sale)
    {
        $validatedData = $request->validate([
            'customer_code' => 'required|string|max:255',
          'customer_name' => 'nullable|string|max:255',
            'code' => 'required|string|max:255', // This is the GRN Code
            'supplier_code' => 'required|string|max:255',
            'item_code' => 'required|string|max:255',
            'item_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'packs' => 'required|integer|min:0',
            // Add any other fields that can be updated
        ]);

        try {
            $sale->update($validatedData);

            return response()->json(['success' => true, 'message' => 'Sales record updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update sales record: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified sales record from storage.
     *
     * @param  \App\Models\Sale  $sale // Using Route Model Binding
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        try {
            $sale->delete();
            return response()->json(['success' => true, 'message' => 'Sales record deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete sales record: ' . $e->getMessage()], 500);
        }
    }
    

    








}