<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('dashboard.items.index', compact('items'));
    }

    public function create()
    {
        return view('dashboard.items.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'no'        => 'required',
        'type'      => 'required',
        'pack_cost' => 'required|numeric',
        'pack_due'  => 'required|numeric',
    ]);

    // Force 'no' to uppercase
    $data = $request->all();
    $data['no'] = strtoupper($data['no']);

    Item::create($data);

    return redirect()->route('items.index')->with('success', 'Item added successfully!');
}

    public function edit(Item $item)
    {
        return view('dashboard.items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'no' => 'required',
            'type' => 'required',
            'pack_cost' => 'required|numeric',
            'pack_due' => 'required|numeric',
        ]);

        $item->update($request->all());

        return redirect()->route('items.index')->with('success', 'Item updated successfully!');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully!');
    }
}
