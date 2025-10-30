<?php

namespace App\Http\Controllers;

use App\Models\Supplier2;
use App\Models\Supplier;
use App\Models\GrnEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierController2 extends Controller
{
    protected function getSupplierBalances()
    {
        // 1. Calculate the current balance (sum of all total_amount) for each supplier_code
        $balances = Supplier2::select('supplier_code', DB::raw('SUM(total_amount) as total_balance'))
            ->groupBy('supplier_code')
            ->pluck('total_balance', 'supplier_code');

        // 2. Get the list of existing suppliers (Code/Name) from the Supplier model
        $existingSuppliers = Supplier::select('id', 'code', 'name')->get();

        // 3. Merge the balance into the existing suppliers data
        $suppliersWithBalance = $existingSuppliers->map(function ($supplier) use ($balances) {
            $supplier->balance = $balances->get($supplier->code, 0); // Get balance, default to 0
            return $supplier;
        });

        return $suppliersWithBalance;
    }

    public function index()
    {
        // Get all transactions for the records table
        $suppliers = Supplier2::with('grn')->orderBy('id', 'desc')->get(); // Added eager loading and ordering

        // Get GRN options
        $grnOptions = GrnEntry::select('id', DB::raw("CONCAT(code,' - ',item_code,' - ',item_name) as display_name"))
            ->pluck('display_name', 'id');

        // Get suppliers with their current balance for the dropdowns
        $existingSuppliersWithBalance = $this->getSupplierBalances();

        return view('dashboard.suppliers2.index', compact('suppliers', 'grnOptions', 'existingSuppliersWithBalance'));
    }

    /**
     * UPDATED: Fetches transactions, supplier details, and GRN payment summary for the modal.
     */
    public function getSupplierTransactions(Request $request)
    {
        $request->validate(['supplier_code' => 'required|string']);
        $supplierCode = $request->input('supplier_code');

        // Fetch supplier info
        $supplier = Supplier::where('code', $supplierCode)->first();

        // Fetch all transactions for that supplier
        $transactions = Supplier2::with('grn')
            ->where('supplier_code', $supplierCode)
            ->orderBy('date', 'asc')
            ->get();

        // Calculate totals
        $totalPurchases = $transactions->where('total_amount', '>', 0)->sum('total_amount');
        $totalPayments  = abs($transactions->where('total_amount', '<', 0)->sum('total_amount'));
        $remainingBalance = $totalPurchases - $totalPayments;

        // Running balance calculation for history table
        $runningBalance = 0;

        $history = $transactions->map(function ($txn) use (&$runningBalance) {
            $type = $txn->total_amount >= 0 ? 'Purchase' : 'Payment';
            $amountClass = $txn->total_amount >= 0 ? 'text-success text-end' : 'text-danger text-end';

            // Update running balance
            $runningBalance += $txn->total_amount;

            // Default GRN info
            $grnNo = $txn->grn->code ?? '-';
            $relatedTotal = null;
            if (stripos($txn->description, 'Payment to Supplier') !== false && $txn->grn_id) {
                $grn = GrnEntry::find($txn->grn_id);
                if ($grn) {
                    $relatedTotal = number_format($grn->total_grn, 2);
                    $grnNo = $grn->code ;
                }
            }

            return [
                'date'          => $txn->date,
                'type'          => $type,
                'description'   => $txn->description ?? '-',
                'grn_no'        => $grnNo,
                'amount'        => number_format(abs($txn->total_amount), 2),
                'balance'       => number_format($runningBalance, 2),
                'class'         => $amountClass,
            ];
        });

        // 🆕 NEW LOGIC: Calculate GRN Payment Breakdown Summary
        // 1. Get all GRN entries for this supplier (Purchases)
        $grnEntries = Supplier2::with('grn')
            ->where('supplier_code', $supplierCode)
            ->where('total_amount', '>', 0) // Only look at purchase transactions
            ->whereNotNull('grn_id')
            ->get()
            ->keyBy('grn_id'); // Group by grn_id

        $grnSummary = [];
        
        foreach ($grnEntries as $grnId => $purchaseTxn) {
            // Find the full GRN details
            $grn = $purchaseTxn->grn;

            if ($grn) {
                // 2. Calculate total payments against this specific GRN
                $totalPaid = Supplier2::where('supplier_code', $supplierCode)
                    ->where('grn_id', $grnId)
                    ->where('total_amount', '<', 0) // Only payments (negative amounts)
                    ->where('description', 'LIKE', '%Payment to Supplier%') // Optional: Filter for payment descriptions
                    ->sum('total_amount');
                
                // 3. Find the date of the last payment against this GRN
                $lastPaymentDate = Supplier2::where('supplier_code', $supplierCode)
                    ->where('grn_id', $grnId)
                    ->where('total_amount', '<', 0)
                    ->where('description', 'LIKE', '%Payment to Supplier%')
                    ->latest('date')
                    ->value('date');

                $grnTotal = $purchaseTxn->total_amount;
                $totalPaidAbs = abs($totalPaid); // Total paid is stored as negative
                $remaining = $grnTotal - $totalPaidAbs;
                
                // Add to summary array
                $grnSummary[] = [
                    'grn_code'          => $grn->code,
                    'grn_total'         => number_format($grnTotal, 2),
                    'total_paid'        => number_format($totalPaidAbs, 2),
                    'remaining'         => number_format($remaining, 2),
                    'last_payment_date' => $lastPaymentDate ?? 'N/A',
                ];
            }
        }
        // -------------------------------------------------------------

        return response()->json([
            'supplier_code'       => $supplierCode,
            'supplier_name'       => $supplier->name ?? 'Unknown',
            'supplier_email'      => $supplier->email ?? null,
            'supplier_phone'      => $supplier->phone ?? null,
            'supplier_address'    => $supplier->address ?? null,
            'total_purchases'     => number_format($totalPurchases, 2),
            'total_payments'      => number_format($totalPayments, 2),
            'remaining_balance'   => number_format($remainingBalance, 2),
            'history'             => $history,
            'grn_payment_summary' => $grnSummary, // 🆕 NEW DATA FOR THE BREAKDOWN TABLE
        ]);
    }


    public function store(Request $request)
    {
        // ... (Keep existing store method logic) ...
        $request->validate([
            'supplier_code' => 'required|string',
            'supplier_name' => 'nullable|string',
            'grn_id' => 'nullable|exists:grn_entries,id',
            'total_amount' => 'required|numeric',
            'description' => 'nullable|string|max:500',
            'transaction_id' => 'nullable|exists:supplier2s,id', // for edit
        ]);

        try {
            // ✅ Get current date from Setting
            $setting = \App\Models\Setting::first();
            $currentDate = $setting ? $setting->value : now();

            if ($request->transaction_id) {
                // --- Editing an existing transaction by transaction_id ---
                $transaction = Supplier2::find($request->transaction_id);
                $transaction->update([
                    'supplier_code' => $request->supplier_code,
                    'supplier_name' => $request->supplier_name,
                    'grn_id' => $request->grn_id,
                    'total_amount' => $request->total_amount, // replace old amount
                    'description' => $request->description ?? ('Updated Purchase / GRN ' . $request->grn_id),
                    'date' => $currentDate,
                ]);

                return redirect()
                    ->route('suppliers2.index')
                    ->with('success', 'Transaction updated successfully.');
            } else {
                // --- Check if a record exists with SAME supplier_code and SAME grn_id ---
                $existing = Supplier2::where('supplier_code', $request->supplier_code)
                    ->where('grn_id', $request->grn_id)
                    ->where('total_amount', '>', 0) // Only match against a previous purchase record
                    ->first();

                if ($existing) {
                    // ✅ Update only that matching record
                    $existing->update([
                        'supplier_name' => $request->supplier_name,
                        'total_amount' => $existing->total_amount + $request->total_amount,
                        'description' => $request->description ?? ('Updated Purchase / GRN ' . $request->grn_id),
                        'date' => $currentDate,
                    ]);

                    return redirect()
                        ->route('suppliers2.index')
                        ->with('success', 'Existing record (same supplier & GRN) updated successfully.');
                } else {
                    // 🆕 Create new record if no match for same supplier_code + grn_id
                    Supplier2::create([
                        'supplier_code' => $request->supplier_code,
                        'supplier_name' => $request->supplier_name,
                        'grn_id' => $request->grn_id,
                        'total_amount' => $request->total_amount,
                        'description' => $request->description ?? ('Purchase / GRN ' . $request->grn_id),
                        'date' => $currentDate,
                    ]);

                    return redirect()
                        ->route('suppliers2.index')
                        ->with('success', 'New supplier purchase recorded successfully.');
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to store supplier purchase transaction', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to record purchase transaction. Check logs for details.');
        }
    }


    /**
     * UPDATED: Handles the payment/settlement, including the new description field.
     */
    public function payment(Request $request)
    {
        // Validate supplier_code and include the new description field
        $request->validate([
            'supplier_code' => 'required|string',
            'payment_amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500', 
            'grn_id' => 'nullable|integer', // NEW VALIDATION: GRN is optional
        ]);

        try {
            // Find supplier details from the main Supplier model (or Supplier2 as fallback)
            $supplier = Supplier::where('code', $request->supplier_code)->first();
            
            if (!$supplier) {
                // Fallback to Supplier2 if not found in Supplier (for supplier_name)
                $sampleSupplier2 = Supplier2::where('supplier_code', $request->supplier_code)->first();
                if (!$sampleSupplier2) {
                    return redirect()->back()->with('error', 'Supplier not found for payment processing.');
                }
                $supplierName = $sampleSupplier2->supplier_name;
            } else {
                $supplierName = $supplier->name;
            }
            
            // Use the GRN ID from the request, default to null if it's not present (empty string)
            $grnId = $request->input('grn_id');
            if (empty($grnId)) {
                $grnId = null;
            }

            // ✅ Get the current value from Setting model's 'value' column
            $setting = \App\Models\Setting::first();
            $currentValue = $setting ? $setting->value : null;

            // ✅ Create a new record to log the payment
            Supplier2::create([
                'supplier_code' => $request->supplier_code,
                'supplier_name' => $supplierName,
                'grn_id' => $grnId, // UPDATED: Pass the optional GRN ID
                'total_amount' => -abs($request->payment_amount), // Always store as NEGATIVE amount
                'description' => $request->description ?? 'Payment to Supplier', // Use the description from the form
                'date' => $currentValue, // ✅ Store the Setting value in 'date' column
            ]);

            return redirect()
                ->route('suppliers2.index')
                ->with('success', 'Payment of ' . number_format($request->payment_amount, 2) . ' recorded successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to submit supplier payment transaction', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to submit payment transaction. Check logs for details.');
        }
    }
    
    // ... rest of the original methods
    public function edit($id)
    {
        $supplier = Supplier2::findOrFail($id);
        $grnOptions = GrnEntry::select(
            'id',
            DB::raw("CONCAT(code, ' - ', item_code, ' - ', item_name) as display_name")
        )->pluck('display_name', 'id');

        return view('dashboard.suppliers2.edit', compact('supplier', 'grnOptions'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier2::findOrFail($id);

        $request->validate([
            'supplier_code' => 'required',
            'supplier_name' => 'nullable',
            'grn_id' => 'required',
            'total_amount' => 'required|numeric',
        ]);

        $supplier->update($request->only(['supplier_code', 'supplier_name', 'grn_id', 'total_amount', 'description']));
        return redirect()->route('suppliers2.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy($id)
    {
        Supplier2::destroy($id);
        return redirect()->route('suppliers2.index')->with('success', 'Supplier deleted.');
    }
}