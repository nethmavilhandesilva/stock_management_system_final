<?php

namespace App\Http\Controllers;

use App\Models\GrnEntry;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;

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
            'grn_no' => 'required',
            'warehouse_no' => 'required',
          
        ]);

        // 🔍 Fetch item name (type) using item_code (which is item.no)
        $item = Item::where('no', $request->item_code)->first();
        if (!$item) {
            return back()->withErrors(['item_code' => 'Invalid item selected.']);
        }

        // Auto generate GRN entry code
        $last = GrnEntry::latest()->first();
        $autoNo = $last ? $last->id + 1 : 1;
        $autoPurchaseNo = str_pad($autoNo, 4, '0', STR_PAD_LEFT);

        $code = $request->item_code . '-' . $request->supplier_code . '-' . rand(100, 999);

        // 📝 Store the record
        GrnEntry::create([
            'auto_purchase_no' => $autoPurchaseNo,
            'code' => $code,
            'supplier_code' => $request->supplier_code,
            'item_code' => $request->item_code,
            'item_name' => $item->type, // 👍 this is the item name
            'packs' => $request->packs,
            'weight' => $request->weight,
            'txn_date' => $request->txn_date,
            'grn_no' => $request->grn_no,
            'warehouse_no' => $request->warehouse_no,
            'original_packs' => $request->packs,
            'original_weight'=>$request->weight,
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
        $request->validate([
            'item_code' => 'required',
            'supplier_code' => 'required',
            'packs' => 'required|integer',
            'weight' => 'required|numeric',
            'txn_date' => 'required|date',
            'grn_no' => 'required'
        ]);

        $entry = GrnEntry::findOrFail($id);

        $entry->update([
            'item_code' => $request->item_code,
            'supplier_code' => $request->supplier_code,
            'packs' => $request->packs,
            'weight' => $request->weight,
            'txn_date' => $request->txn_date,
            'grn_no' => $request->grn_no,
            'warehouse_no' => $request->warehouse_no,
        ]);

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

}

