@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f4f6f9;
    }

    .custom-card {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 30px;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 10px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #004085;
        margin-bottom: 1.5rem;
        text-align: center;
    }
</style>

<div class="container mt-5">
    <div class="custom-card mx-auto" style="max-width: 900px;">
        <h2 class="section-title">✏️ GRN-4 ඇතුළත් කිරීම යාවත්කාලීන කරන්න</h2>

        <form method="POST" action="{{ route('grn.update', $entry->id) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">භාණ්ඩය (Item)</label>
                    <select name="item_code" class="form-select" required>
                        @foreach($items as $item)
                            <option value="{{ $item->no }}" {{ $entry->item_code == $item->no ? 'selected' : '' }}>
                                {{ $item->no }} - {{ $item->type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">භාණ්ඩ නාමය</label>
                    <input type="text" name="item_name" class="form-control" value="{{ $entry->item_name }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">සැපයුම්කරු</label>
                    <select name="supplier_code" class="form-select" required>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->code }}" {{ $entry->supplier_code == $supplier->code ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">වාහන අංකය (GRN No)</label>
                    <input type="text" name="grn_no" class="form-control" value="{{ $entry->grn_no }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">ගබඩා අංකය</label>
                    <input type="text" name="warehouse_no" class="form-control" value="{{ $entry->warehouse_no }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">පැක් සංඛ්‍යාව</label>
                    <input type="number" name="packs" class="form-control" value="{{ $entry->packs }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">බර (කෝ.ග්‍රෑ)</label>
                    <input type="number" name="weight" class="form-control" value="{{ $entry->weight }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">ගනුදෙනු දිනය</label>
                    <input type="date" name="txn_date" class="form-control" value="{{ $entry->txn_date }}">
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success px-4">යාවත්කාලීන කරන්න</button>
                <a href="{{ route('grn.index') }}" class="btn btn-secondary ms-2">අවලංගු කරන්න</a>
            </div>
        </form>
    </div>
</div>
@endsection
