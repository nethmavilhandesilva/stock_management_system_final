@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header text-center" style="background-color: #004d00 !important;">
            <div class="report-title-bar">
                <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
                <h4 class="fw-bold text-white">📦 ඉතිරි වාර්තාව</h4>
                <span class="right-info">{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span>
                <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">වර්ගය</th>
                            <th colspan="2">මිලදී ගැනීම</th>
                            <th colspan="2">විකුණුම්</th>
                            <th colspan="2">ඉතිරි</th>
                        </tr>
                        <tr>
                            <th>මලු</th>
                            <th>බර</th>
                            <th>මලු</th>
                            <th>බර</th>
                            <th>මලු</th>
                            <th>බර</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotalOriginalPacks = 0;
                            $grandTotalOriginalWeight = 0;
                            $grandTotalSoldPacks = 0;
                            $grandTotalSoldWeight = 0;
                            $grandTotalSalesValue = 0;
                            $grandTotalRemainingPacks = 0;
                            $grandTotalRemainingWeight = 0;
                        @endphp

                        @forelse($reportData as $data)
                            @php
                                $originalPacks = floatval($data['original_packs'] ?? 0);
                                $originalWeight = floatval($data['original_weight'] ?? 0);
                                $soldPacks = floatval($data['sold_packs'] ?? 0);
                                $soldWeight = floatval($data['sold_weight'] ?? 0);
                                $remainingPacks = floatval($data['remaining_packs'] ?? 0);
                                $remainingWeight = floatval(str_replace(',', '', $data['remaining_weight'] ?? 0));

                                $grandTotalOriginalPacks += $originalPacks;
                                $grandTotalOriginalWeight += $originalWeight;
                                $grandTotalSoldPacks += $soldPacks;
                                $grandTotalSoldWeight += $soldWeight;
                                $grandTotalRemainingPacks += $remainingPacks;
                                $grandTotalRemainingWeight += $remainingWeight;
                            @endphp
                            <tr>
                                <td>{{ $data['item_name'] }}</td>
                                <td>{{ number_format($originalPacks) }}</td>
                                <td>{{ number_format($originalWeight, 2) }}</td>
                                <td>{{ number_format($soldPacks) }}</td>
                                <td>{{ number_format($soldWeight, 2) }}</td>
                                <td>{{ number_format($remainingPacks) }}</td>
                                <td>{{ number_format($remainingWeight, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">දත්ත නොමැත.</td>
                            </tr>
                        @endforelse

                        {{-- Totals Row --}}
                        <tr class="total-row">
                            <td class="text-end"><strong>සමස්ත එකතුව:</strong></td>
                            <td><strong>{{ number_format($grandTotalOriginalPacks) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalOriginalWeight, 2) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalSoldPacks) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalSoldWeight, 2) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalRemainingPacks) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalRemainingWeight, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Custom Styles --}}
<style>
    body {
        background-color: #99ff99 !important;
    }

    .card {
        background-color: #004d00 !important;
        color: white !important;
    }

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

    .table-bordered th, .table-bordered td {
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

    .total-row {
        background-color: #008000 !important;
        color: white !important;
        font-weight: bold;
    }

    .text-muted {
        color: lightgray !important;
    }

    @media print {
        body {
            background-color: white !important;
            color: black !important;
        }

        .container-fluid, .card, .card-header, .card-body,
        .report-title-bar, .filter-summary.alert, .table,
        .table thead th, .table tbody tr, .table tbody td,
        .total-row {
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

        .print-button, .btn-secondary {
            display: none !important;
        }

        .table-striped tbody tr:nth-of-type(odd),
        .table-striped tbody tr:nth-of-type(even),
        .total-row {
            background-color: #f8f9fa !important;
            color: black !important;
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6 !important;
        }

        .text-end strong {
            color: black !important;
        }
    }
</style>
@endsection
