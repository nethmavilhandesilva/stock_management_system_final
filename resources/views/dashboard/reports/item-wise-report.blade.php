@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99;
    }
    .custom-card {
        background-color: #006400 !important;
        color: white; /* for text readability */
    }
    .custom-card table thead, 
    .custom-card table tfoot {
        background-color: #004d00 !important;
        color: white;
    }
    /* Optional: style table rows for better contrast */
    .custom-card table tbody tr:nth-child(odd) {
        background-color: #00800033; /* translucent green */
    }

    /* Title bar - flex container for inline layout */
    .report-title-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .company-name {
        font-weight: 700;
        font-size: 1.5rem;
        color: white;
        margin: 0;
    }

    .report-title-bar h4 {
        margin: 0;
        color: white;
        font-weight: 700;
        white-space: nowrap;
    }

    .right-info {
        color: white;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Print button style */
    .print-btn {
        background-color: #004d00;
        color: white;
        border: none;
        padding: 0.4rem 1rem;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        white-space: nowrap;
        transition: background-color 0.3s ease;
    }
    .print-btn:hover {
        background-color: #003300;
    }
</style>

<div class="container mt-4">
    <div class="card shadow border-0 rounded-3 p-4 custom-card">
        <div class="report-title-bar">
            <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
            <h4 class="fw-bold text-white">📦 අයිතමය අනුව වාර්තාව</h4>
            <span class="right-info">{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span>
            <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>අයිතම කේතය</th>
                    <th>පැක්</th>
                    <th>බර</th>
                    <th>කිලෝ ග්‍රෑම් එකක මිල</th>
                    <th>මුළු මුදල</th>
                    <th>පාරිභෝගික කේතය</th>
                    <th>සැපයුම්කරු කේතය</th>
                    <th>බිල් අංකය</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_packs = 0;
                    $total_weight = 0;
                    $total_amount = 0;
                @endphp

                @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->item_code }}</td>
                        <td>{{ $sale->packs }}</td>
                        <td>{{ $sale->weight }}</td>
                        <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                        <td>{{ number_format($sale->total, 2) }}</td>
                        <td>{{ $sale->customer_code }}</td>
                        <td>{{ $sale->supplier_code }}</td>
                        <td>{{ $sale->bill_no }}</td>
                    </tr>

                    @php
                        $total_packs += $sale->packs;
                        $total_weight += $sale->weight;
                        $total_amount += $sale->total;
                    @endphp
                @endforeach
            </tbody>

            <tfoot>
                <tr class="table-secondary fw-bold">
                    <td class="text-end" colspan="1">මුළු එකතුව:</td>
                    <td>{{ $total_packs }}</td>
                    <td>{{ $total_weight }}</td>
                    <td></td>
                    <td>{{ number_format($total_amount, 2) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
