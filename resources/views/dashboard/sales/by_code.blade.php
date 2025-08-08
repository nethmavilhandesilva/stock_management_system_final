@extends('layouts.app')

@section('content')
<style>
    /* Full page background */
    body {
        background-color: #99ff99 !important;
    }

    /* Card background */
    .card {
        background-color: #006400 !important;
        color: white; /* Make text inside card white for better contrast */
    }

    /* Optional: make table text readable */
    .table {
        background-color: white;
        color: black;
    }
</style>

<div class="card shadow-sm border-0 rounded-3 p-3 mt-3">
    <h6 class="mb-3 text-center text-white">Sales for Code: {{ $code }}</h6>

    <table class="table table-sm table-bordered">
        <thead class="table-light">
            <tr>
                <th>Customer</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Weight</th>
                <th>Price/Kg</th>
                <th>Total</th>
                <th>Packs</th>
                <th>Bill No</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td>{{ $sale->customer_code }}</td>
                    <td>{{ $sale->item_code }}</td>
                    <td>{{ $sale->item_name }}</td>
                    <td>{{ $sale->weight }}</td>
                    <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                    <td>{{ number_format($sale->total, 2) }}</td>
                    <td>{{ $sale->packs }}</td>
                    <td>{{ $sale->bill_no }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
