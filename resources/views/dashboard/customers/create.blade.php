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
        max-width: 600px;
        margin: 40px auto;
    }

    h2 {
        color: #4f46e5;
        font-weight: 700;
        text-align: center;
        margin-bottom: 30px;
    }

    .form-label {
        font-weight: 700;
        color: #000000;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px;
        border: 1px solid #ced4da;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        outline: none;
    }

    .btn-success {
        background-color: #198754;
        border-color: #198754;
        font-weight: 600;
        padding: 10px 24px;
    }

    .btn-success:hover {
        background-color: #157347;
        border-color: #157347;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        font-weight: 600;
        padding: 10px 24px;
        margin-left: 10px;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }
</style>

<div class="form-card">
    <h2>පාරිභෝගිකයා එක් කරන්න</h2>

    <form action="{{ route('customers.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="short_name_field" class="form-label">කෙටි නම</label>
            <input type="text" name="short_name" id="short_name_field" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="name_field" class="form-label">සම්පූර්ණ නම</label>
            <input type="text" name="name" id="name_field" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="address_field" class="form-label">ලිපිනය</label>
            <input type="text" name="address" id="address_field" class="form-control">
        </div>

        <div class="mb-3">
            <label for="telephone_no_field" class="form-label">දුරකථන අංකය</label>
            <input type="text" name="telephone_no" id="telephone_no_field" class="form-control">
        </div>

        <div class="mb-3">
            <label for="credit_limit_field" class="form-label">ණය සීමාව (Rs.)</label>
            <input type="number" step="0.01" name="credit_limit" id="credit_limit_field" class="form-control" required>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success">
                <i class="material-icons align-middle me-1">add_circle_outline</i> එක් කරන්න
            </button>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                <i class="material-icons align-middle me-1">cancel</i> අවලංගු කරන්න
            </a>
        </div>
    </form>
</div>
@endsection
