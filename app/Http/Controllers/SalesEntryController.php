<?php

namespace App\Http\Controllers;

use App\Models\GrnEntry; // Make sure this is correctly pointing to your GrnEntry model
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            ->get();

        $salesPrinted = Sale::where('bill_printed', 'Y')
            ->orderBy('created_at', 'desc')
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
    // Add grn_entry_code to validation
    $validated = $request->validate([
        'supplier_code' => 'required',
        'customer_code' => 'required|string|max:255',
        'customer_name' => 'nullable',
        'code' => 'required', // This is the GRN Code (e.g., ALA-SANJ-701)
        'item_code' => 'required',
        'item_name' => 'required',
        'weight' => 'required|numeric|min:0.01', // Ensure weight is positive for sale
        'price_per_kg' => 'required|numeric',
        'total' => 'required|numeric',
        'packs' => 'required|integer|min:1', // Changed min:0 to min:1 if selling at least one pack
        'grn_entry_code' => 'required|string|exists:grn_entries,code',
        'original_weight' => 'nullable',
        'original_packs' => 'nullable',
    ]);

    try {
        DB::beginTransaction(); // Start a database transaction

        // 1. Find the original GRN record using the grn_entry_code
        $grnEntry = GrnEntry::where('code', $validated['grn_entry_code'])->first();

        if (!$grnEntry) {
            throw new \Exception('Selected GRN entry not found for update.');
        }

        // 2. Calculate the new weight and packs for the GRN entry
        $weightToDeduct = $validated['weight'];
        $packsToDeduct = $validated['packs'];

        // Deduct from GRN entry
        $grnEntry->weight = max(0, $grnEntry->weight - $weightToDeduct);
        $grnEntry->packs = max(0, $grnEntry->packs - $packsToDeduct);

        // 3. Update the GRN record in the database
        $grnEntry->save();

        // 4. Create the Sale record
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
            'original_weight' => $validated['original_weight'],
            'original_packs' => $validated['original_packs'],
            'Processed' => 'N',
            'FirstTimeBillPrintedOn' => null,
            'BillChangedOn' => null,
            'CustomerBillEnteredOn' => now(), // Update with the current date and time
        ]);

        DB::commit(); // Commit the transaction

        return redirect()->back()->withInput($request->only(['customer_code', 'customer_name']));

    } catch (\Exception | \Illuminate\Database\QueryException $e) {
        DB::rollBack(); // Rollback on any exception
        Log::error('Failed to add sales entry and update GRN: ' . $e->getMessage());
        return redirect()->back()
            ->withErrors(['error' => 'Failed to add sales entry: ' . $e->getMessage()])
            ->withInput($request->all());
    }
}
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
                'bill_no' => $billNo,
                 'FirstTimeBillPrintedOn' => now() // Ensure this column name is correct and can accept the value
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
            'customer_name' => 'nullable|string|max:255', // <-- Make sure this is nullable
            'code' => 'required|string|max:255',
            'supplier_code' => 'required|string|max:255',
            'item_code' => 'required|string|max:255',
            'item_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'packs' => 'required|integer|min:0',
        ]);

        try {
            // Update the sale record with the validated data
            $sale->update([
                'customer_code' => $validatedData['customer_code'],
                'customer_name' => $validatedData['customer_name'] ?? $sale->customer_name, // <-- Use the new name, or the old one if it's not provided
                'code' => $validatedData['code'],
                'supplier_code' => $validatedData['supplier_code'],
                'item_code' => $validatedData['item_code'],
                'item_name' => $validatedData['item_name'],
                'weight' => $validatedData['weight'],
                'price_per_kg' => $validatedData['price_per_kg'],
                'total' => $validatedData['total'],
                'packs' => $validatedData['packs'],
                'updated' => 'Y',
                'BillChangedOn' => now(),
            ]);

            // Get the freshly updated model instance
            $updatedSale = $sale->fresh();

            // Return the complete, updated sale object
            return response()->json([
                'success' => true,
                'message' => 'Sales record updated successfully!',
                'sale' => $updatedSale
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sales record: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy(Sale $sale)
    {
        try {
            $sale->delete();
            return response()->json(['success' => true, 'message' => 'Sales record deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete sales record: ' . $e->getMessage()], 500);
        }
    }
    public function saveAsUnprinted(Request $request)
    {
        // Validate the incoming request to ensure it's an array of IDs
        $validated = $request->validate([
            'sale_ids' => 'required|array',
            'sale_ids.*' => 'integer|exists:sales,id', // Check that each ID exists
        ]);

        if (!empty($validated['sale_ids'])) {
            // Update the records that match the IDs from the table
            // We set `is_printed` to `0` to mark them as unprinted
            // You might need to adjust the column name based on your database schema
            Sale::whereIn('id', $validated['sale_ids'])->update(['is_printed' => 0]);
        }

        return response()->json(['success' => true]);
    }
    
    public function clearAll(Request $request)
{
    Sale::truncate();    // deletes all records from sales table
    GrnEntry::truncate(); // deletes all records from grn_entries table

    return back()->with('success', 'All data cleared from Sales and GRN Entries.');
}
 public function getUnprintedSales($customer_code)
    {
        // Find all sales records for the given customer_code
        // where the bill_printed column has the value 'N'
        $sales = Sale::where('customer_code', $customer_code)
                      ->where('bill_printed', 'N')
                      ->get();

        // Return the sales records as a JSON response
        return response()->json($sales);
    }
     public function getAllSalesData()
    {
        try {
            // Fetch all sales records from the database
            $allSales = Sale::all();

            // Return the sales records as a JSON response
            return response()->json($allSales);

        } catch (\Exception $e) {
            // Log the full error for server-side debugging
            Log::error('Failed to retrieve sales data: ' . $e->getMessage(), [
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'exception_trace' => $e->getTraceAsString(),
            ]);

            // Return a detailed error response to the client
            return response()->json([
                'error' => 'Failed to retrieve sales data.',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
    public function getAllSales()
{
    $sales = Sale::all(); // or your logic
    return response()->json(['sales' => $sales]);
}
       
 
}