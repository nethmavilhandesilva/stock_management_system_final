@extends('layouts.app')

@section('content')
{{-- This link is typically in layouts.app, but added here for self-containment if needed --}}
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="container-fluid mt-4">
    <div class="row justify-content-center"> {{-- Added justify-content-center for horizontal centering --}}
        <div class="col-md-8"> {{-- Adjusted column size for better form width --}}
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <h2 class="mb-4">Register New GRN Entry</h2> {{-- Simplified header to match target style --}}
                {{-- The descriptive paragraph is removed to match the simpler header style of the target --}}

                <form method="POST" action="{{ route('grn.store') }}">
                    @csrf

                    {{-- Item Selection --}}
                    <div class="mb-3">
                        <label for="item_code" class="form-label">Item <span class="text-danger">*</span></label> {{-- Used text-danger for required indicator --}}
                        <select id="item_code" name="item_code" class="form-control @error('item_code') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Select an Item --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->no }}" {{ old('item_code') == $item->no ? 'selected' : '' }}>{{ $item->no }} - {{ $item->type }}</option>
                            @endforeach
                        </select>
                        @error('item_code')
                            <div class="invalid-feedback">{{ $message }}</div> {{-- Bootstrap's invalid-feedback for errors --}}
                        @enderror
                    </div>

                    {{-- Supplier Selection --}}
                    <div class="mb-3">
                        <label for="supplier_code" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select id="supplier_code" name="supplier_code" class="form-control @error('supplier_code') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Select a Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->code }}" {{ old('supplier_code') == $supplier->code ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Vehicle No (GRN No) --}}
                    <div class="mb-3">
                        <label for="grn_no" class="form-label">Vehicle No (GRN No) <span class="text-danger">*</span></label>
                        <input type="text" id="grn_no" name="grn_no" placeholder="e.g., ABC-1234 or GRN-2023-001" value="{{ old('grn_no') }}"
                               class="form-control @error('grn_no') is-invalid @enderror" required>
                        @error('grn_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Warehouse No --}}
                    <div class="mb-3">
                        <label for="warehouse_no" class="form-label">Warehouse No <span class="text-danger">*</span></label>
                        <input type="text" id="warehouse_no" name="warehouse_no" placeholder="e.g., WH-01 or Aisle-C" value="{{ old('warehouse_no') }}"
                               class="form-control @error('warehouse_no') is-invalid @enderror" required>
                        @error('warehouse_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Packs --}}
                    <div class="mb-3">
                        <label for="packs" class="form-label">Number of Packs <span class="text-danger">*</span></label>
                        <input type="number" id="packs" name="packs" placeholder="e.g., 10" value="{{ old('packs') }}" min="1"
                               class="form-control @error('packs') is-invalid @enderror" required>
                        @error('packs')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Weight --}}
                    <div class="mb-3">
                        <label for="weight" class="form-label">Weight (KG) <span class="text-danger">*</span></label>
                        <input type="number" id="weight" name="weight" step="0.01" placeholder="e.g., 250.75" value="{{ old('weight') }}" min="0.01"
                               class="form-control @error('weight') is-invalid @enderror" required>
                        @error('weight')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Transaction Date --}}
                    <div class="mb-3">
                        <label for="txn_date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                        <input type="date" id="txn_date" name="txn_date" value="{{ old('txn_date', date('Y-m-d')) }}"
                               class="form-control @error('txn_date') is-invalid @enderror">
                        @error('txn_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-center mt-4"> {{-- Using Bootstrap flex for button alignment --}}
                        <button type="submit" class="btn btn-success me-2">Submit GRN Entry</button>
                        <a href="{{ route('grn.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
