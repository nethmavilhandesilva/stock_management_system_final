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
        $balances = Supplier2::select('supplier_code', DB::raw('SUM(total_amount) as total_balance'))
            ->groupBy('supplier_code')
            ->pluck('total_balance', 'supplier_code');

        $existingSuppliers = Supplier::select('id', 'code', 'name')->get();

        $suppliersWithBalance = $existingSuppliers->map(function ($supplier) use ($balances) {
            $supplier->balance = $balances->get($supplier->code, 0);
            return $supplier;
        });

        return $suppliersWithBalance;
    }

    public function index(Request $request)
    {
        $dates = \App\Models\Setting::pluck('value')->toArray();

        $query = Supplier2::with('grn')->orderBy('id', 'desc');

        if (!$request->has('search') || empty($request->search)) {
            $query->whereIn('date', $dates);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = strtoupper($request->search);
            $query->where(DB::raw('UPPER(supplier_code)'), 'like', "%{$search}%");
        }

        $suppliers = $query->get();

        $grnOptions = GrnEntry::select('id', DB::raw("CONCAT(code,' - ',item_code,' - ',item_name) as display_name"))
            ->pluck('display_name', 'id');

        $existingSuppliersWithBalance = $this->getSupplierBalances();

        return view('dashboard.suppliers2.index', compact('suppliers', 'grnOptions', 'existingSuppliersWithBalance'));
    }


    public function getSupplierTransactions(Request $request)
    {
        $request->validate(['supplier_code' => 'required|string']);
        $supplierCode = $request->supplier_code;

        $supplier = Supplier::where('code', $supplierCode)->first();

        $transactions = Supplier2::with('grn')
            ->where('supplier_code', $supplierCode)
            ->orderBy('date', 'asc')
            ->get();

        $totalPurchases = $transactions->where('total_amount', '>', 0)->sum('total_amount');
        $totalPayments  = abs($transactions->where('total_amount', '<', 0)->sum('total_amount'));
        $remainingBalance = $totalPurchases - $totalPayments;

        $runningBalance = 0;

        $history = $transactions->map(function ($txn) use (&$runningBalance) {
            $type = $txn->total_amount >= 0 ? 'Purchase' : 'Payment';

            $runningBalance += $txn->total_amount;

            $grnNo = $txn->grn->code ?? '-';

            return [
                'date' => $txn->date,
                'type' => $type,
                'description' => $txn->description ?? '-',
                'grn_no' => $grnNo,
                'amount' => number_format(abs($txn->total_amount), 2),
                'balance' => number_format($runningBalance, 2),
                'class' => $txn->total_amount >= 0 ? 'text-success text-end' : 'text-danger text-end',
            ];
        });

        // GRN breakdown summary
        $purchases = Supplier2::with('grn')
            ->where('supplier_code', $supplierCode)
            ->where('total_amount', '>', 0)
            ->whereNotNull('grn_id')
            ->get()
            ->keyBy('grn_id');

        $grnSummary = [];

        foreach ($purchases as $grnId => $purchaseTxn) {
            $grn = $purchaseTxn->grn;

            $totalPaid = Supplier2::where('supplier_code', $supplierCode)
                ->where('grn_id', $grnId)
                ->where('total_amount', '<', 0)
                ->sum('total_amount');

            $lastPaymentDate = Supplier2::where('supplier_code', $supplierCode)
                ->where('grn_id', $grnId)
                ->where('total_amount', '<', 0)
                ->latest('date')
                ->value('date');

            $remaining = $purchaseTxn->total_amount - abs($totalPaid);

            $grnSummary[] = [
                'grn_code' => $grn->code,
                'grn_total' => number_format($purchaseTxn->total_amount, 2),
                'total_paid' => number_format(abs($totalPaid), 2),
                'remaining' => number_format($remaining, 2),
                'last_payment_date' => $lastPaymentDate ?? 'N/A',
            ];
        }

        return response()->json([
            'supplier_code' => $supplierCode,
            'supplier_name' => $supplier->name ?? 'Unknown',
            'supplier_email' => $supplier->email ?? null, // Added for modal
            'supplier_phone' => $supplier->phone ?? null, // Added for modal
            'supplier_address' => $supplier->address ?? null, // Added for modal
            'total_purchases' => number_format($totalPurchases, 2),
            'total_payments' => number_format($totalPayments, 2),
            'remaining_balance' => number_format($remainingBalance, 2),
            'history' => $history,
            'grn_payment_summary' => $grnSummary,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'supplier_code' => 'required|string',
            'grn_id' => 'nullable|exists:grn_entries,id',
            'total_amount' => 'required|numeric',
            'transaction_id' => 'nullable|exists:supplier2s,id',
        ]);

        try {
            $currentDate = \App\Models\Setting::value('value');

            if ($request->transaction_id) {
                $txn = Supplier2::find($request->transaction_id);
                $txn->update([
                    'supplier_code' => $request->supplier_code,
                    'supplier_name' => $request->supplier_name,
                    'grn_id' => $request->grn_id,
                    'total_amount' => $request->total_amount,
                    'description' => $request->description,
                    'date' => $currentDate,
                ]);

                return back()->with('success', 'Transaction updated.');
            }

            $existing = Supplier2::where('supplier_code', $request->supplier_code)
                ->where('grn_id', $request->grn_id)
                ->where('total_amount', '>', 0)
                ->first();

            if ($existing) {
                $existing->update([
                    'total_amount' => $existing->total_amount + $request->total_amount,
                    'description' => $request->description,
                    'date' => $currentDate,
                ]);

                return back()->with('success', 'Existing GRN purchase updated.');
            }

            Supplier2::create([
                'supplier_code' => $request->supplier_code,
                'supplier_name' => $request->supplier_name,
                'grn_id' => $request->grn_id,
                'total_amount' => $request->total_amount,
                'description' => $request->description ?? ('Purchase / GRN ' . $request->grn_id),
                'date' => $currentDate,
            ]);

            return back()->with('success', 'New purchase recorded.');

        } catch (\Exception $e) {
            Log::error("Store Error", ['e' => $e->getMessage()]);
            return back()->with('error', 'Error saving transaction.');
        }
    }


    public function payment(Request $request)
    {
        $request->validate([
            'supplier_code' => 'required|string',
            'payment_amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'grn_id' => 'nullable|integer',
            // *** NEW Validation ***
            'payment_method' => 'required|in:cash,cheque',
            'payment_cheque_no' => 'required_if:payment_method,cheque|nullable|string',
            'payment_cheque_date' => 'required_if:payment_method,cheque|nullable|date',
            'payment_bank_name' => 'required_if:payment_method,cheque|nullable|string',
        ]);

        try {
            $supplier = Supplier::where('code', $request->supplier_code)->first();
            $supplierName = $supplier->name ?? Supplier2::where('supplier_code', $request->supplier_code)->value('supplier_name');

            $grnId = $request->grn_id ?: null;
            $currentDate = \App\Models\Setting::value('value');

            // *** NEW: Build Description ***
            $description = $request->description ?? 'Payment to Supplier';
            if ($request->payment_method === 'cheque') {
                $description = sprintf(
                    '%s (Cheque No: %s, Date: %s, Bank: %s)',
                    $description,
                    $request->payment_cheque_no,
                    $request->payment_cheque_date,
                    $request->payment_bank_name
                );
            }
            // *** END NEW ***

            Supplier2::create([
                'supplier_code' => $request->supplier_code,
                'supplier_name' => $supplierName,
                'grn_id' => $grnId,
                'total_amount' => -abs($request->payment_amount),
                'description' => $description, // Use the new description
                'date' => $currentDate,
            ]);

            return back()->with('success', 'Payment recorded.');

        } catch (\Exception $e) {
            Log::error("Payment Error", ['e' => $e->getMessage()]);
            return back()->with('error', 'Payment failed.');
        }
    }

    // *** This method is REQUIRED by your blade file ***
    public function getUnpaidGrns($supplier_code)
    {
        try {
            $purchases = Supplier2::where('supplier_code', $supplier_code)
                ->where('total_amount', '>', 0)
                ->whereNotNull('grn_id')
                ->with('grn')
                ->get();

            $unpaidGrns = [];

            foreach ($purchases as $purchase) {
                $totalPaid = Supplier2::where('supplier_code', $supplier_code)
                    ->where('grn_id', $purchase->grn_id)
                    ->where('total_amount', '<', 0)
                    ->sum('total_amount');

                $balance = $purchase->total_amount + $totalPaid;

                if ($balance > 0.01) {
                    $unpaidGrns[] = [
                        'grn_id' => $purchase->grn_id,
                        'grn_code' => $purchase->grn->code ?? 'N/A',
                        'date' => $purchase->date,
                        'total_amount' => (float)$purchase->total_amount,
                        'remaining_balance' => (float)$balance,
                    ];
                }
            }

            return response()->json(['success' => true, 'grns' => $unpaidGrns]);

        } catch (\Exception $e) {
            Log::error("Get Unpaid GRN Error", ['e' => $e->getMessage()]);
            return response()->json(['success' => false], 500);
        }
    }

    // *** THIS IS THE UPDATED METHOD ***
    public function storeManyPayment(Request $request)
    {
        // 1. Validation - Replaced 'grn_ids_to_pay' with 'grn_payments'
        $request->validate([
            'supplier_code' => 'required|string|exists:suppliers,code',
            'description' => 'nullable|string|max:255',
            'many_payment_amount' => 'required|numeric|min:0.01',
            'grn_payments' => 'required|array|min:1', // The array of [grn_id => amount]
            'grn_payments.*' => 'required|numeric|min:0.01', // Validates all the *amounts*
            'many_payment_method' => 'required|in:cash,cheque',
            'many_cheque_no' => 'required_if:many_payment_method,cheque|nullable|string',
            'many_cheque_date' => 'required_if:many_payment_method,cheque|nullable|date',
            'many_bank_name' => 'required_if:many_payment_method,cheque|nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $supplier = Supplier::where('code', $request->supplier_code)->firstOrFail();
            $currentDate = \App\Models\Setting::value('value');

            // Build Base Description - This logic is unchanged
            $baseDescription = $request->description ?? 'Payment to Supplier';
            if ($request->many_payment_method === 'cheque') {
                $baseDescription = sprintf(
                    '%s (Cheque No: %s, Date: %s, Bank: %s)',
                    $baseDescription,
                    $request->many_cheque_no,
                    $request->many_cheque_date,
                    $request->many_bank_name
                );
            }

            $totalAllocated = 0;

            // 2. Logic - Loop through the new 'grn_payments' array
            // The $grn_id is the 'key' and $payment_amount is the 'value'
            foreach ($request->grn_payments as $grn_id => $payment_amount) {
                
                if ($payment_amount <= 0) {
                    continue; // Skip if amount is zero or negative
                }
                
                $totalAllocated += $payment_amount;

                // Find the GRN code for a better description
                $grn = GrnEntry::find($grn_id);
                $grnCode = $grn ? $grn->code : $grn_id;
                
                // Create a separate payment transaction for the *exact* allocated amount
                Supplier2::create([
                    'supplier_code' => $supplier->code,
                    'supplier_name' => $supplier->name,
                    'existing_supplier_id' => $supplier->id,
                    'grn_id' => $grn_id,
                    'date' => $currentDate,
                    'description' => $baseDescription . ' (GRN: ' . $grnCode . ')',
                    'total_amount' => -1 * abs($payment_amount), // Use the specific allocated amount
                ]);
            }

            // 3. Sanity check (Good practice)
            // Check if the total allocated amount matches the total payment amount sent
            if (abs($totalAllocated - $request->many_payment_amount) > 0.01) {
                // This means the user's Javascript calculation was different from the server's.
                // We roll back to prevent a mismatch in payment.
                DB::rollBack();
                return redirect()->route('suppliers2.index')
                    ->with('error', 'Payment allocation mismatch. Please refresh and try again.');
            }

            DB::commit();
            return redirect()->route('suppliers2.index')->with('success', 'Payments allocated and processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Many Payment Error", [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return redirect()->route('suppliers2.index')
                ->with('error', 'Error processing payments: ' . $e->getMessage());
        }
    }
    // *** END OF UPDATED METHOD ***


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