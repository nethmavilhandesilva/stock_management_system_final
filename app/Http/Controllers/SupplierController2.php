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
    public function index()
    {
        $suppliers = Supplier2::all(); // for records table
        $grnOptions = GrnEntry::select('id', DB::raw("CONCAT(code,' - ',item_code,' - ',item_name) as display_name"))
            ->pluck('display_name', 'id');

        // For Add Supplier dropdown
        $existingSuppliers = Supplier::select('id', 'code', 'name')->get();

        return view('dashboard.suppliers2.index', compact('suppliers', 'grnOptions', 'existingSuppliers'));
    }

    // ... other methods (index, create, etc.)

    public function store(Request $request)
    {
        // This is the Purchase/Add Supplier logic
        $request->validate([
            'supplier_code' => 'required',
            'supplier_name' => 'nullable',
            'grn_id' => 'required',
            'total_amount' => 'required|numeric|min:0.01', // Must be positive purchase amount
            'description' => 'nullable|string|max:500',
        ]);

        try {
            Supplier2::create([
                'supplier_code' => $request->supplier_code,
                'supplier_name' => $request->supplier_name,
                'grn_id' => $request->grn_id,
                'total_amount' => $request->total_amount, // Store as POSITIVE amount
                'description' => $request->description ?? 'Purchase/GRN ' . $request->grn_id,
            ]);

            return redirect()->route('suppliers2.index')->with('success', 'Supplier purchase recorded successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to store supplier purchase transaction', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Failed to record purchase transaction. Check logs for details.');
        }
    }
   public function payment(Request $request)
{
    // Validate supplier_code instead of supplier_id
    $request->validate([
        'supplier_code' => 'required|exists:supplier2s,supplier_code',
        'payment_amount' => 'required|numeric|min:0.01',
    ]);

    try {
        // Find supplier by supplier_code
        $originalSupplier = Supplier2::where('supplier_code', $request->supplier_code)->first();

        if (!$originalSupplier) {
            return redirect()->back()->with('error', 'Supplier not found for payment processing.');
        }

        // Create a new record to log the payment
        Supplier2::create([
            'supplier_code' => $originalSupplier->supplier_code,
            'supplier_name' => $originalSupplier->supplier_name,
            'grn_id' => null, // Payments aren't linked to GRNs
            'total_amount' => -abs($request->payment_amount), // Always store as NEGATIVE amount
            'description' => 'Payment', // Payment record
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
