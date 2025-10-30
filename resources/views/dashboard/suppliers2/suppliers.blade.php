@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99;
        font-family: Arial, sans-serif;
    }
    .report-container {
        background-color: #004d00;
        padding: 20px;
        border-radius: 10px;
        color: white;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        background: white;
        color: black;
        border-radius: 10px;
        overflow: hidden;
    }
    th, td {
        padding: 10px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #008000;
        color: white;
    }
    tr:hover {
        background-color: #f2f2f2;
    }
    .report-title {
        font-size: 1.8rem;
        text-align: center;
        margin-bottom: 15px;
    }
</style>

<div class="container mt-4">
    <div class="report-container">
        <h2 class="report-title">Supplier Report</h2>

        <table>
            <thead>
                <tr>
                    <th>Supplier Code</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Total Purchases (Rs.)</th>
                    <th>Total Payments (Rs.)</th>
                    <th>Remaining Balance (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier['code'] }}</td>
                        <td>{{ $supplier['name'] }}</td>
                        <td>{{ $supplier['phone'] }}</td>
                        <td>{{ $supplier['email'] }}</td>
                         <td>{{ $supplier['address'] }}</td>
                        <td>{{ number_format($supplier['total_purchases'], 2) }}</td>
                        <td>{{ number_format($supplier['total_payments'], 2) }}</td>
                        <td><strong>{{ number_format($supplier['balance'], 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
