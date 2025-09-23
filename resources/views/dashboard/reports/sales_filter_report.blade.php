{{-- resources/views/reports/sales_filter_report.blade.php --}}

@extends('layouts.app') {{-- Extend main layout --}}

@section('content')
<style>
    /* ================= PRINT STYLES ================= */
    @media print {
        /* Hide everything except the card */
        body * {
            visibility: hidden;
        }

        .custom-card, .custom-card * {
            visibility: visible;
        }

        .custom-card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .print-btn {
            display: none !important;
        }
    }

    /* ================= PAGE STYLING ================= */
    body {
        background-color: #99ff99 !important;
    }

    .card {
        background-color: #004d00 !important;
        color: white !important;
    }

    /* Report title bar */
    .report-title-bar {
        text-align: center;
        padding: 15px 0;
        position: relative;
        background-color: #004d00;
        color: white;
    }

    .report-title-bar .company-name {
        font-size: 1.8em;
        margin-bottom: 5px;
    }

    .report-title-bar h4 {
        margin-bottom: 10px;
    }

    .report-title-bar .right-info {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 0.9em;
    }

    .report-title-bar .print-btn {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9em;
    }

    /* Table styling */
    .table {
        color: white;
        font-size: 0.85em;
    }

    .table thead th {
        background-color: #003300 !important;
        color: white !important;
        border-color: #004d00 !important;
        padding: 0.4rem;
    }

    .table-bordered th,
    .table-bordered td {
        border-color: #006600 !important;
        padding: 0.4rem;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #004000 !important;
    }

    .table-striped tbody tr:nth-of-type(even) {
        background-color: #005a00 !important;
    }

    .table-hover tbody tr:hover {
        background-color: #007000 !important;
    }

    /* Total rows */
    .total-row,
    .total-row-individual {
        background-color: #008000 !important;
        color: white !important;
        font-weight: bold;
    }

    .text-muted {
        color: lightgray !important;
    }

    /* Filter summary */
    .filter-summary.alert {
        background-color: #006600 !important;
        color: white !important;
        border: 1px solid #008000 !important;
    }

    /* ================= PRINT SPECIFIC STYLES ================= */
    @media print {
        body {
            background-color: white !important;
            color: black !important;
        }

        .container-fluid,
        .card,
        .card-header,
        .card-body,
        .report-title-bar,
        .filter-summary.alert,
        .table,
        .table thead th,
        .table tbody tr,
        .table tbody td,
        .total-row,
        .total-row-individual {
            background-color: white !important;
            color: black !important;
            border-color: #dee2e6 !important;
        }

        .card {
            box-shadow: none !important;
            border: none !important;
        }

        .report-title-bar {
            text-align: center;
            padding: 10px 0;
            position: static;
        }

        .report-title-bar .print-btn {
            display: none !important;
        }

        .report-title-bar .right-info {
            position: static;
            display: block;
            margin-top: 5px;
        }

        .print-button,
        .btn-secondary {
            display: none !important;
        }

        .table-striped tbody tr:nth-of-type(odd),
        .table-striped tbody tr:nth-of-type(even),
        .total-row,
        .total-row-individual {
            background-color: #f8f9fa !important;
            color: black !important;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6 !important;
        }

        .text-end strong {
            color: black !important;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header text-center" style="background-color: #004d00 !important;">
            <div class="report-title-bar">
                <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
                <h4 class="fw-bold text-white">📦 මුළු විකුණුම්</h4>

                @php
                    $settingDate = \App\Models\Setting::value('value');
                @endphp

                <span class="right-info">
                    {{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}
                </span>

                <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>සැපයුම්කරු</th>
                            <th>වර්ගය</th>
                            <th>බර</th>
                            <th>මිල</th>
                            <th>මලු</th>
                            <th>මුළු මුදල</th>
                            <th>බිල්පත් අංකය</th>
                            <th>පාරිභෝගික කේතය</th>
                            <th>දිනය සහ වේලාව</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalPacks = 0;
                            $totalWeight = 0;
                            $grandTotalAmount = 0;
                        @endphp

                        @forelse($sales as $sale)
                            @php
                                $totalPacks += $sale->packs;
                                $totalWeight += $sale->weight;
                                $grandTotalAmount += $sale->total;
                            @endphp
                            <tr>
                                <td>{{ $sale->code }}</td>
                                <td>{{ $sale->item_name }}</td>
                                <td>{{ number_format($sale->weight, 2) }}</td>
                                <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                                <td>{{ $sale->packs }}</td>
                                <td>{{ number_format($sale->total, 2) }}</td>
                                <td>{{ $sale->bill_no }}</td>
                                <td>{{ $sale->customer_code }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->created_at)->timezone('Asia/Colombo')->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">පෙරහන් කරන ලද දත්ත නොමැත.</td>
                            </tr>
                        @endforelse

                        {{-- Totals Row --}}
                        <tr class="total-row-individual">
                            <td colspan="1" class="text-end"><strong>සමස්ත එකතුව:</strong></td>
                            <td></td>
                            <td><strong>{{ number_format($totalWeight, 2) }}</strong></td>
                            <td></td>
                            <td><strong>{{ number_format($totalPacks) }}</strong></td>
                            <td><strong>Rs. {{ number_format($grandTotalAmount, 2) }}</strong></td>
                            <td colspan="3"></td>
                        </tr>

                        <tr class="total-row">
                            <td colspan="7" class="text-end"><strong>මුළු විකුණුම් වටිනාකම:</strong></td>
                            <td colspan="2"><strong>Rs. {{ number_format($grandTotal, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        <a href="{{ route('report.download', ['reportType' => 'supplier-sales', 'format' => 'excel']) }}" class="btn btn-success me-2">Download Excel</a>
        <a href="{{ route('report.download', ['reportType' => 'supplier-sales', 'format' => 'pdf']) }}" class="btn btn-danger">Download PDF</a>
    </div>
</div>
@endsection
