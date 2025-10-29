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
    public function getSupplierTransactions(Request $request)
    {
        $request->validate(['supplier_code' => 'required|string']);

        $supplierCode = $request->input('supplier_code');

        // Fetch all transactions for the given supplier code
        $transactions = Supplier2::where('supplier_code', $supplierCode)
            ->with('grn')
            ->orderBy('created_at', 'asc') // Order by time for history
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json([
                'supplier_code' => $supplierCode,
                'supplier_name' => 'N/A',
                'total_purchases' => number_format(0, 2),
                'total_payments' => number_format(0, 2),
                'remaining_balance' => number_format(0, 2),
                'history' => [],
            ]);
        }

        // Calculate the running balance and separate purchases/payments
        $runningBalance = 0;
        $totalPurchases = 0;
        $totalPayments = 0;
        $history = [];

        foreach ($transactions as $transaction) {
            $amount = $transaction->total_amount;
            $isPayment = $transaction->description === 'Payment';

            if ($isPayment) {
                // Payment transactions are stored as negative in total_amount
                $totalPayments += abs($amount);
            } else {
                // Purchase/GRN transactions are stored as positive
                $totalPurchases += $amount;
            }

            $runningBalance += $amount;

            $history[] = [
                'date' => $transaction->created_at->format('Y-m-d H:i'),
                'type' => $isPayment ? 'Payment' : 'Purchase',
                'description' => $transaction->description,
                'grn_no' => $transaction->grn->code ?? '-',
                'amount' => number_format($amount, 2),
                'balance' => number_format($runningBalance, 2),
                'class' => $isPayment ? 'text-danger' : 'text-success',
            ];
        }

        return response()->json([
            'supplier_code' => $supplierCode,
            'supplier_name' => $transactions->first()->supplier_name ?? 'N/A',
            'total_purchases' => number_format($totalPurchases, 2),
            'total_payments' => number_format($totalPayments, 2),
            'remaining_balance' => number_format($runningBalance, 2),
            'history' => $history,
        ]);
    }

    // ... other methods (index, create, etc.)

    public function store(Request $request)
    {
        $request->validate([
            'supplier_code' => 'required|string',
            'supplier_name' => 'nullable|string',
            'grn_id' => 'nullable|exists:grn_entries,id',
            'total_amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            // ✅ Get date from Setting
            $setting = \App\Models\Setting::first();
            $currentValue = $setting ? $setting->value : null;

            // ✅ Check if a record already exists for this supplier_code
            $existingSupplier = Supplier2::where('supplier_code', $request->supplier_code)->first();

            if ($existingSupplier) {
                // ✅ Update existing record: add new total_amount
                $existingSupplier->update([
                    'total_amount' => $existingSupplier->total_amount + $request->total_amount,
                    'description' => $request->description ?? ('Updated Purchase / GRN ' . $request->grn_id),
                    'grn_id' => $request->grn_id,
                    'date' => $currentValue, // update the date too
                ]);

                return redirect()
                    ->route('suppliers2.index')
                    ->with('success', 'Existing supplier record updated successfully.');
            } else {
                // ✅ Otherwise create a new record
                Supplier2::create([
                    'supplier_code' => $request->supplier_code,
                    'supplier_name' => $request->supplier_name,
                    'grn_id' => $request->grn_id,
                    'total_amount' => $request->total_amount,
                    'description' => $request->description ?? ('Purchase / GRN ' . $request->grn_id),
                    'date' => $currentValue,
                ]);

                return redirect()
                    ->route('suppliers2.index')
                    ->with('success', 'New supplier purchase recorded successfully.');
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

    public function payment(Request $request)
    {
        // Validate supplier_code instead of supplier_id
        $request->validate([
            'supplier_code' => 'required|string',
            'payment_amount' => 'required|numeric|min:0.01',
        ]);

        try {
            // Find a record with this supplier_code to get the supplier name for logging
            $sampleSupplier = Supplier2::where('supplier_code', $request->supplier_code)->first();

            if (!$sampleSupplier) {
                return redirect()->back()->with('error', 'Supplier not found for payment processing.');
            }

            // ✅ Get the current value from Setting model's 'value' column
            $setting = \App\Models\Setting::first();
            $currentValue = $setting ? $setting->value : null;

            // ✅ Create a new record to log the payment
            Supplier2::create([
                'supplier_code' => $sampleSupplier->supplier_code,
                'supplier_name' => $sampleSupplier->supplier_name,
                'grn_id' => null, // Payments aren't linked to GRNs
                'total_amount' => -abs($request->payment_amount), // Always store as NEGATIVE amount
                'description' => 'Payment',
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
            'total_amount' => 'required|numeric|min:0',
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
