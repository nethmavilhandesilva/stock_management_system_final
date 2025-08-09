<?php

namespace App\Http\Controllers;

use App\Models\GrnEntry;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrnEntryController extends Controller
{
    public function index()
    {
        $entries = GrnEntry::latest()->get();
        return view('dashboard.grn.index', compact('entries'));
    }

    public function create()
    {
        $items = Item::all();
        $suppliers = Supplier::all();
        return view('dashboard.grn.create', compact('items', 'suppliers'));
    }
 public function store(Request $request)
    {
        $request->validate([
            'item_code' => 'required',
            'supplier_code' => 'required',
            'packs' => 'required|integer',
            'weight' => 'required|numeric',
            'txn_date' => 'required|date',
            'grn_no' => 'nullable',
            'warehouse_no' => 'nullable',
            // --- NEW: Add validation for the separate total_grn field ---
            'total_grn' => 'required|numeric',
        ]);

        // 🔍 Fetch item name (type) and supplier name using their respective codes
        $item = Item::where('no', $request->item_code)->first();
        if (!$item) {
            return back()->withErrors(['item_code' => 'Invalid item selected.']);
        }

        $supplier = Supplier::where('code', $request->supplier_code)->first();
        if (!$supplier) {
            return back()->withErrors(['supplier_code' => 'Invalid supplier selected.']);
        }

        // Auto generate GRN entry code
        $last = GrnEntry::latest()->first();
        $autoNo = $last ? $last->id + 1 : 1;
        $autoPurchaseNo = str_pad($autoNo, 4, '0', STR_PAD_LEFT);

        // --- NEW LOGIC FOR SEQUENTIAL NUMBER ---
        // 1. Get the last GRN Entry record and its sequential number
        $lastGrnEntry = GrnEntry::orderBy('sequence_no', 'desc')->first();

        // 2. Determine the next sequential number
        if ($lastGrnEntry) {
            $nextSequentialNumber = $lastGrnEntry->sequence_no + 1;
        } else {
            // If no records exist, start from 1000
            $nextSequentialNumber = 1000;
        }

        // 3. Construct the 'code' string using the new sequential number, and the first three letters of the item name and supplier name
        $itemTypePrefix = substr($item->no, 0, 3);
        $supplierNamePrefix = substr($supplier->code, 0, 3);

        $code = $itemTypePrefix . '-' . $supplierNamePrefix . '-' . $nextSequentialNumber;
        // --- END NEW LOGIC ---

        // 📝 Store the record
        GrnEntry::create([
            'auto_purchase_no' => $autoPurchaseNo,
            'code' => $code,
            'supplier_code' => $request->supplier_code,
            'item_code' => $request->item_code,
            'item_name' => $item->type,
            'packs' => $request->packs,
            'weight' => $request->weight,
            'txn_date' => $request->txn_date,
            'grn_no' => $request->grn_no,
            'warehouse_no' => $request->warehouse_no,
            'original_packs' => $request->packs,
            'original_weight' => $request->weight,
            'sequence_no' => $nextSequentialNumber, // Save the new sequence number
            'total_grn' => $request->total_grn, // Store the separate total from the request
        ]);

        return redirect()->route('grn.index')->with('success', 'GRN Entry added successfully.');
    }

    public function edit($id)
    {
        $entry = GrnEntry::findOrFail($id);
        $items = Item::all();
        $suppliers = Supplier::all();
        return view('dashboard.grn.edit', compact('entry', 'items', 'suppliers'));
    }

   public function update(Request $request, $id)
    {
        // Define the validation rules for the form fields.
        // `total_grn` is added as `nullable|numeric` because it is an optional field.
        $request->validate([
            'item_code' => 'required',
            'supplier_code' => 'required',
            'packs' => 'required|integer',
            'weight' => 'required|numeric',
            'txn_date' => 'required|date',
            'grn_no' => 'required',
            'warehouse_no' => 'required',
            'total_grn' => 'nullable|numeric' // Added validation for the new field
        ]);

        // Find the GRN entry by its ID.
        $entry = GrnEntry::findOrFail($id);

        // Prepare the data to be updated.
        $updateData = [
            'item_code' => $request->item_code,
            'item_name' => $request->item_name,
            'supplier_code' => $request->supplier_code,
            'packs' => $request->packs,
            'weight' => $request->weight,
            'txn_date' => $request->txn_date,
            'grn_no' => $request->grn_no,
            'warehouse_no' => $request->warehouse_no,
        ];
        
        // Only update `total_grn` if it's present in the request.
        // This prevents overwriting with a null value if the password isn't entered.
        if ($request->has('total_grn')) {
            $updateData['total_grn'] = $request->total_grn;
        }

        // Update the entry in the database.
        $entry->update($updateData);

        // Redirect back to the index page with a success message.
        return redirect()->route('grn.index')->with('success', 'Entry updated successfully.');
    }

    public function destroy($id)
    {
        $entry = GrnEntry::findOrFail($id);
        $entry->delete();

        return redirect()->route('grn.index')->with('success', 'Entry deleted.');
    }
    public function getGrnEntryByCode($code)
    {
        $grnEntry = GrnEntry::where('code', $code)->first();

        if ($grnEntry) {
            return response()->json($grnEntry);
        }

        return response()->json(['error' => 'GRN Entry not found.'], 404);
    }
  public function getUsedData($code)
{
    $usedWeight = DB::table('grn_entries')
                    ->where('code', $code)
                    ->sum('weight');

    $usedPacks = DB::table('grn_entries')
                    ->where('code', $code)
                    ->sum('packs');

    return response()->json([
        'used_weight' => $usedWeight,
        'used_packs' => $usedPacks
    ]);
}
public function hide($id)
{
    $entry = GrnEntry::findOrFail($id);
    $entry->is_hidden = true;
    $entry->save();

    return response()->json(['status' => 'hidden']);
}

public function unhide($id)
{
    $entry = GrnEntry::findOrFail($id);
    $entry->is_hidden = false;
    $entry->save();

    return response()->json(['status' => 'unhidden']);
}


}

