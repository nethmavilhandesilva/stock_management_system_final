@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
    body {
        background-color: #99ff99;
    }
    

    .custom-card {
        background-color: #006400 !important;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 24px;
    }

    .form-label {
        font-weight: 500;
        color: #003366;
    }

    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    input[readonly] {
        background-color: #e9ecef;
    }
</style>

<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="custom-card">
                <h2 class="text-primary mb-4">📝 නව GRN ඇතුළත් කිරීම</h2>

                <form method="POST" action="{{ route('grn.store2') }}">
                    @csrf

                    {{-- Item Selection --}}
                    <div class="mb-3">
                        <label for="item_code" class="form-label">අයිතම <span class="text-danger">*</span></label>
                        <select id="item_code" name="item_code" class="form-control @error('item_code') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Select an Item --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->no }}" data-type="{{ $item->type }}" {{ old('item_code') == $item->no ? 'selected' : '' }}>
                                    {{ $item->no }} - {{ $item->type }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Supplier Selection --}}
                    <div class="mb-3">
                        <label for="supplier_code" class="form-label">සැපයුම්කරු <span class="text-danger">*</span></label>
                        <select id="supplier_code" name="supplier_code" class="form-control @error('supplier_code') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Select a Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->code }}" data-name="{{ $supplier->name }}" {{ old('supplier_code') == $supplier->code ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- GRN No (Auto-generated) --}}
                    <div class="mb-3">
                        <label for="grn_no" class="form-label">GRN අංකය <span class="text-danger">*</span></label>
                        <input type="text" id="grn_no" name="grn_no" readonly
                               value="{{ old('grn_no') }}"
                               class="form-control @error('grn_no') is-invalid @enderror"
                               placeholder="Will be auto-generated">
                        @error('grn_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Warehouse No --}}
                    <div class="mb-3">
                        <label for="warehouse_no" class="form-label">ගබඩා අංකය <span class="text-danger">*</span></label>
                        <input type="text" id="warehouse_no" name="warehouse_no" placeholder="e.g., WH-01 or Aisle-C"
                               value="{{ old('warehouse_no') }}"
                               class="form-control @error('warehouse_no') is-invalid @enderror" required>
                        @error('warehouse_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Packs --}}
                    <div class="mb-3">
                        <label for="packs" class="form-label">පැක්‌ <span class="text-danger">*</span></label>
                        <input type="number" id="packs" name="packs" placeholder="e.g., 10" value="{{ old('packs') }}" min="1"
                               class="form-control @error('packs') is-invalid @enderror" required>
                        @error('packs')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Weight --}}
                    <div class="mb-3">
                        <label for="weight" class="form-label">බර (kg) <span class="text-danger">*</span></label>
                        <input type="number" id="weight" name="weight" step="0.01" placeholder="e.g., 250.75" value="{{ old('weight') }}" min="0.01"
                               class="form-control @error('weight') is-invalid @enderror" required>
                        @error('weight')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Transaction Date --}}
                    <div class="mb-3">
                        <label for="txn_date" class="form-label">ගනුදෙනු දිනය <span class="text-danger">*</span></label>
                        <input type="date" id="txn_date" name="txn_date" value="{{ old('txn_date', date('Y-m-d')) }}"
                               class="form-control @error('txn_date') is-invalid @enderror">
                        @error('txn_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="material-icons align-middle me-1">check_circle</i>GRN ඇතුළත් කරන්න
                        </button>
                        <a href="{{ route('grn.index') }}" class="btn btn-secondary">
                            <i class="material-icons align-middle me-1">cancel</i>අවලංගු කරන්න
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemSelect = document.getElementById('item_code');
        const supplierSelect = document.getElementById('supplier_code');
        const grnInput = document.getElementById('grn_no');

        function generateGRN() {
            const itemOption = itemSelect.options[itemSelect.selectedIndex];
            const supplierOption = supplierSelect.options[supplierSelect.selectedIndex];

            const itemType = itemOption?.getAttribute('data-type') || '';
            const supplierName = supplierOption?.getAttribute('data-name') || '';

            if (itemType && supplierName) {
                const itemPart = itemType.trim().substring(0, 3).toUpperCase();
                const supplierPart = supplierName.trim().substring(0, 3).toUpperCase();
                const timestamp = Date.now().toString().slice(-4);
                const generatedGRN = `${itemPart}${supplierPart}-${timestamp}`;
                grnInput.value = generatedGRN;
            }
        }

        itemSelect.addEventListener('change', generateGRN);
        supplierSelect.addEventListener('change', generateGRN);
    });
</script>
@endsection
