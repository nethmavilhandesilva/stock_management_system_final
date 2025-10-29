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

    .form-control,
    textarea.form-control {
        border-radius: 8px;
        padding: 10px;
        border: 1px solid #ced4da;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        resize: vertical;
    }

    textarea.form-control {
        min-height: 80px;
    }

    .form-control:focus,
    textarea.form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        outline: none;
    }

    .btn-success {
        background-color: #4f46e5;
        border-color: #4f46e5;
        font-weight: 600;
        padding: 10px 24px;
    }

    .btn-success:hover {
        background-color: #4338ca;
        border-color: #4338ca;
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
    <h2>✏️ සැපයුම්කරු සංස්කරණය (Edit Supplier)</h2>

    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="code_field" class="form-label">කේතය (Code)</label>
            <input type="text" id="code_field" name="code" value="{{ $supplier->code }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="name_field" class="form-label">නම (Name)</label>
            <input type="text" id="name_field" name="name" value="{{ $supplier->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="address_field" class="form-label">ලිපිනය (Address)</label>
            <textarea id="address_field" name="address" class="form-control" required>{{ $supplier->address }}</textarea>
        </div>

        <div class="mb-3">
            <label for="phone_field" class="form-label">දුරකථන අංකය (Phone Number)</label>
            <input type="text" id="phone_field" name="phone" value="{{ $supplier->phone }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="email_field" class="form-label">ඊමේල් (Email Address)</label>
            <input type="email" id="email_field" name="email" value="{{ $supplier->email }}" class="form-control">
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success">
                <i class="material-icons align-middle me-1">save</i> යාවත්කාලීන කරන්න
            </button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <i class="material-icons align-middle me-1">cancel</i> අවලංගු කරන්න
            </a>
        </div>
    </form>
</div>
@endsection
