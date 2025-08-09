@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
    body {
        background-color: #99ff99;
    }

    .form-card {
        background-color: #006400 !important;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 30px 24px;
        max-width: 900px;
        margin: 40px auto;
    }

    .form-label {
        font-weight: 700;
        color: #000000;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 10px;
        border: 1px solid #ced4da;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        outline: none;
    }

    .btn-success {
        background-color: #198754;
        border-color: #198754;
        font-weight: 600;
    }

    .btn-success:hover {
        background-color: #157347;
        border-color: #157347;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        font-weight: 600;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    .text-end {
        text-align: right;
    }

    /* Styles for the password-protected field */
    .password-protected {
        display: none;
    }
</style>

<div class="form-card">
    <h2 class="mb-4 text-center text-primary">✏️ GRN-4 ඇතුළත් කිරීම යාවත්කාලීන කරන්න</h2>

    <form method="POST" action="{{ route('grn.update', $entry->id) }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label for="item_code" class="form-label">භාණ්ඩය (Item)</label>
                <select name="item_code" id="item_code" class="form-select" required>
                    @foreach($items as $item)
                        <option value="{{ $item->no }}" {{ $entry->item_code == $item->no ? 'selected' : '' }}>
                            {{ $item->no }} - {{ $item->type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="item_name" class="form-label">භාණ්ඩ නාමය</label>
                <input type="text" id="item_name" name="item_name" class="form-control" value="{{ $entry->item_name }}" required>
            </div>

            <div class="col-md-6">
                <label for="supplier_code" class="form-label">සැපයුම්කරු</label>
                <select name="supplier_code" id="supplier_code" class="form-select" required>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->code }}" {{ $entry->supplier_code == $supplier->code ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="grn_no" class="form-label">වාහන අංකය (GRN No)</label>
                <input type="text" id="grn_no" name="grn_no" class="form-control" value="{{ $entry->grn_no }}" required>
            </div>

            <div class="col-md-6">
                <label for="warehouse_no" class="form-label">ගබඩා අංකය</label>
                <input type="text" id="warehouse_no" name="warehouse_no" class="form-control" value="{{ $entry->warehouse_no }}" required>
            </div>

            <div class="col-md-6">
                <label for="packs" class="form-label">පැක් සංඛ්‍යාව</label>
                <input type="number" id="packs" name="packs" class="form-control" value="{{ $entry->packs }}" required>
            </div>

            <div class="col-md-6">
                <label for="weight" class="form-label">බර (කෝ.ග්‍රෑ)</label>
                <input type="number" id="weight" name="weight" class="form-control" value="{{ $entry->weight }}" required>
            </div>

            <div class="col-md-6">
                <label for="txn_date" class="form-label">ගනුදෙනු දිනය</label>
                <input type="date" id="txn_date" name="txn_date" class="form-control" value="{{ $entry->txn_date }}">
            </div>
            
            <hr class="mt-4">
            
            <div class="col-md-6">
                <label for="edit_password" class="form-label">මුරපදය (Password)</label>
                <input type="password" id="edit_password" class="form-control" placeholder="Enter password to edit total">
            </div>

            <div class="col-md-6 password-protected" id="total_grn_field">
                <label for="total_grn" class="form-label">GRN සඳහා මුළු එකතුව (Total for GRN)</label>
                <input type="number" id="total_grn" name="total_grn" class="form-control" value="{{ $entry->total_grn ?? '' }}">
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success px-4">
                <i class="material-icons align-middle me-1">update</i> යාවත්කාලීන කරන්න
            </button>
            <a href="{{ route('grn.index') }}" class="btn btn-secondary ms-2">
                <i class="material-icons align-middle me-1">cancel</i> අවලංගු කරන්න
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordField = document.getElementById('edit_password');
        const totalGrnField = document.getElementById('total_grn_field');
        const correctPassword = 'nethma123';

        passwordField.addEventListener('input', function () {
            if (passwordField.value === correctPassword) {
                totalGrnField.style.display = 'block';
                passwordField.style.backgroundColor = '#d4edda';
                passwordField.style.borderColor = '#28a745';
            } else {
                totalGrnField.style.display = 'none';
                passwordField.style.backgroundColor = '';
                passwordField.style.borderColor = '';
            }
        });
    });
</script>
@endpush