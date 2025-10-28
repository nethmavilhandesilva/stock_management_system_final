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
                            ->pluck('display_name','id');
    
    // For Add Supplier dropdown
    $existingSuppliers = Supplier::select('id','code','name')->get();

    return view('dashboard.suppliers2.index', compact('suppliers','grnOptions','existingSuppliers'));
}

   public function store(Request $request)
{
    $request->validate([
        'supplier_code' => 'required',
        'supplier_name' => 'nullable',
        'grn_id' => 'required',
        'total_amount' => 'required|numeric|min:0',
    ]);

    try {
        // Check if a record with the same supplier_code already exists
        $supplier = Supplier2::where('supplier_code', $request->supplier_code)->first();

        if ($supplier) {
            // If exists, add the new amount to the existing total_amount
            $supplier->total_amount += $request->total_amount;
            // Optionally update supplier_name if provided
            if ($request->supplier_name) {
                $supplier->supplier_name = $request->supplier_name;
            }
            $supplier->save();
        } else {
            // Otherwise, create a new record
            Supplier2::create($request->only(['supplier_code', 'supplier_name', 'grn_id', 'total_amount']));
        }

        return redirect()->route('suppliers2.index')->with('success', 'Supplier added successfully.');

    } catch (\Exception $e) {
        \Log::error('Failed to store supplier', [
            'error' => $e->getMessage(),
            'request_data' => $request->all(),
        ]);

        return redirect()->back()->withInput()->with('error', 'Failed to add supplier. Check logs for details.');
    }
}


    public function edit($id)
    {
        $supplier = Supplier2::findOrFail($id);
        $grnOptions = GrnEntry::select('id',
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

        $supplier->update($request->only(['supplier_code', 'supplier_name', 'grn_id', 'total_amount']));

        return redirect()->route('suppliers2.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy($id)
    {
        Supplier2::destroy($id);
        return redirect()->route('suppliers2.index')->with('success', 'Supplier deleted.');
    }
    public function payment(Request $request)
{
    $request->validate([
        'supplier_id' => 'required|exists:supplier2s,id',
        'payment_amount' => 'required|numeric|min:0.01',
    ]);

    $supplier = Supplier2::findOrFail($request->supplier_id);

    // Deduct payment from total_amount
    $supplier->total_amount -= $request->payment_amount;
    if ($supplier->total_amount < 0) $supplier->total_amount = 0;
    $supplier->save();

    return redirect()->route('suppliers2.index')->with('success', 'Payment applied successfully.');
}

}
