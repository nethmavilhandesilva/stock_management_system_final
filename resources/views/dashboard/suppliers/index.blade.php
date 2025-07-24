@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f0f4f8;
    }

    .custom-card {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 24px;
    }

    .table thead th {
        background-color: #e6f0ff;
        color: #003366;
        text-align: center;
    }

    .table tbody td {
        vertical-align: middle;
        text-align: center;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f5ff;
    }

    .btn-sm {
        font-size: 0.875rem;
        padding: 6px 12px;
    }

    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-add {
        background-color: #198754;
        border-color: #198754;
        color: #fff;
    }

    .btn-add:hover {
        background-color: #157347;
        border-color: #157347;
    }
</style>

<div class="container-fluid mt-5">
    <div class="custom-card">
        <h2 class="mb-4 text-center text-primary">📦 සැපයුම්කරුවන් (Suppliers)</h2>

        <div class="text-end mb-3">
            <a href="{{ route('suppliers.create') }}" class="btn btn-add">+ නව සැපයුම්කරු</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>සංකේතය (Code)</th>
                        <th>නම (Name)</th>
                        <th>ලිපිනය (Address)</th>
                        <th>මෙහෙයුම් (Actions)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->code }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->address }}</td>
                            <td>
                                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-warning btn-sm">යාවත්කාලීන</a>
                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('මෙම සැපයුම්කරු මකන්නද?')" class="btn btn-danger btn-sm">මකන්න</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">සැපයුම්කරුවන් නොමැත</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
