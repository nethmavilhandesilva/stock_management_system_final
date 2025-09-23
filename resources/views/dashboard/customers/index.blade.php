@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99;
    }

    .custom-card {
        background-color:#006400 !important;
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
        <h2 class="mb-4 text-center text-primary">පාරිභෝගික ලැයිස්තුව (Customer List)</h2>

        <div class="text-end mb-3">
            <a href="{{ route('customers.create') }}" class="btn btn-add">
                + නව පාරිභෝගිකයෙකු එකතු කරන්න
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>කෙටි නම</th>
                        <th>සම්පූර්ණ නම</th>
                         <th>ID_NO</th>
                        <th>ලිපිනය</th>
                        <th>දුරකථන අංකය</th>
                        <th>ණය සීමාව (Rs.)</th>
                        <th>මෙහෙයුම්</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td><span style="text-transform: uppercase;">{{ $customer->short_name }}</span></td>
                            <td>{{ $customer->name }}</td>
                             <td>{{ $customer->ID_NO }}</td>
                            <td>{{ $customer->address }}</td>
                            <td>{{ $customer->telephone_no }}</td>
                            <td>Rs. {{ number_format($customer->credit_limit, 2) }}</td>
                            <td>
                                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">යාවත්කාලීන</a>
                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('මෙම පාරිභෝගිකයා මකන්නද?')">මකන්න</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">පාරිභෝගිකයන් නොමැත</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
