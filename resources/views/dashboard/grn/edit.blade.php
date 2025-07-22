@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-bold mb-4">Edit GRN-4 Entry</h2>

        <form method="POST" action="{{ route('grn.update', $entry->id) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Item</label>
                    <select name="item_code" class="form-select w-full p-2 border rounded">
                        @foreach($items as $item)
                            <option value="{{ $item->no }}" {{ $entry->item_code == $item->no ? 'selected' : '' }}>
                                {{ $item->no }} - {{ $item->type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Item Name</label>
                    <input type="text" name="item_name" class="w-full border p-2 rounded" value="{{ $entry->item_name }}"
                        required>
                </div>

                <div>
                    <label class="block font-medium">Supplier</label>
                    <select name="supplier_code" class="form-select w-full p-2 border rounded">
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->code }}" {{ $entry->supplier_code == $supplier->code ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Vehicle No (GRN No)</label>
                    <input type="text" name="grn_no" class="w-full border p-2 rounded" value="{{ $entry->grn_no }}"
                        required>
                </div>
                <div>
                    <label class="block font-medium">Warehouse No</label>
                    <input type="text" name="warehouse_no" class="w-full border p-2 rounded"
                        value="{{ $entry->warehouse_no }}" required>
                </div>


                <div>
                    <label class="block font-medium">Packs</label>
                    <input type="number" name="packs" class="w-full border p-2 rounded" value="{{ $entry->packs }}"
                        required>
                </div>

                <div>
                    <label class="block font-medium">Weight (KG)</label>
                    <input type="number" name="weight" class="w-full border p-2 rounded" value="{{ $entry->weight }}"
                        required>
                </div>

                <div>
                    <label class="block font-medium">Transaction Date</label>
                    <input type="date" name="txn_date" value="{{ $entry->txn_date }}" class="w-full border p-2 rounded">
                </div>
            </div>

            <button type="submit" class="mt-4 bg-green-600 text-white px-4 py-2 rounded">Update GRN</button>
        </form>
    </div>
@endsection