{{-- resources/views/reports/grn_sales_overview_report.blade.php --}}

@extends('layouts.app') {{-- Extend your main application layout --}}

@section('content') {{-- Place the report content inside the 'content' section --}}

<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header text-center" style="background-color: #004d00 !important;">
            <div class="report-title-bar">
                <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
                <h4 class="fw-bold text-white">📦විකුණුම්/බර මත්තෙහි ඉතිරි වාර්තාව</h4>
                <span class="right-info">{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span>
                <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">දිනය</th>
                            <th rowspan="2">වර්ගය</th>
                            <th rowspan="2">වර්ගය</th>
                            <th colspan="2">මිලදී ගැනීම</th> {{-- Main header for Original --}}
                            <th colspan="2">විකුණුම්</th> {{-- Main header for Sold (Current Weight) --}}
                            <th rowspan="2">එකතුව</th>
                            <th colspan="2">ඉතිරි</th> {{-- Main header for Remaining --}}
                        </tr>
                        <tr>
                            <th>මලු</th> {{-- Sub-header for Original Packs --}}
                            <th>බර</th> {{-- Sub-header for Original Weight --}}
                            <th>මලු</th> {{-- Sub-header for Sold Packs --}}
                            <th>බර</th> {{-- Sub-header for Sold Weight --}}
                            <th>මලු</th> {{-- Sub-header for Remaining Packs --}}
                            <th>බර</th> {{-- Sub-header for Remaining Weight --}}
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
                                $grandTotalOriginalPacks += $data['original_packs'];
                                $grandTotalOriginalWeight += $data['original_weight'];
                                $grandTotalSoldPacks += $data['sold_packs'];
                                $grandTotalSoldWeight += $data['sold_weight'];
                                $grandTotalSalesValue += $data['total_sales_value'];
                                $grandTotalRemainingPacks += $data['remaining_packs'];
                                $grandTotalRemainingWeight += floatval(str_replace(',', '', $data['remaining_weight']));
                            @endphp
                            <tr>
                             <td>{{ date('Y-m-d', strtotime($data['date'])) }}</td>
                                <td>{{ $data['grn_code'] }}</td>
                                <td>{{ $data['item_name'] }}</td>
                                <td>{{ number_format($data['original_packs']) }}</td>
                                <td>{{ number_format($data['original_weight'], 2) }}</td>
                                <td>{{ number_format($data['sold_packs']) }}</td>
                                <td>{{ number_format($data['sold_weight'], 2) }}</td>
                                <td>Rs. {{ number_format($data['total_sales_value'], 2) }}</td>
                                <td>{{ number_format($data['remaining_packs']) }}</td>
                                <td>{{ $data['remaining_weight'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">දත්ත නොමැත.</td>
                            </tr>
                        @endforelse
                        {{-- Totals Row --}}
                        <tr class="total-row">
                            <td colspan="3" class="text-end"><strong>සමස්ත එකතුව:</strong></td>
                            <td><strong>{{ number_format($grandTotalOriginalPacks) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalOriginalWeight, 2) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalSoldPacks) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalSoldWeight, 2) }}</strong></td>
                            <td><strong>Rs. {{ number_format($grandTotalSalesValue, 2) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalRemainingPacks) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalRemainingWeight, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

           
        </div>
    </div>
</div>

{{-- Custom styles for this report page --}}
<style>
    /* Page background */
    body {
        background-color: #99ff99 !important;
    }

    /* Card background and default text color */
    .card {
        background-color: #004d00 !important;
        color: white !important; /* All text inside card */
    }

    /* Report Title Bar specific styling */
    .report-title-bar {
        text-align: center;
        padding: 15px 0;
        position: relative; /* For absolute positioning of print button/date */
        background-color: #004d00; /* Ensure header background matches card */
        color: white; /* Ensure text is white */
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
        background-color: #4CAF50; /* A pleasant green for print button */
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9em;
    }

    /* Table Styling */
    .table {
        color: white; /* Default text color for table content */
        font-size: 0.85em; /* Make table text smaller */
    }
    .table thead th {
        background-color: #003300 !important; /* Darker green for table headers */
        color: white !important;
        border-color: #004d00 !important; /* Border color for headers */
        padding: 0.4rem; /* Reduce padding for smaller table cells */
    }
    .table-bordered th, .table-bordered td {
        border-color: #006600 !important; /* Ensure borders are visible */
        padding: 0.4rem; /* Reduce padding for smaller table cells */
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #004000 !important; /* Slightly different shade for odd rows */
    }
    .table-striped tbody tr:nth-of-type(even) {
        background-color: #005a00 !important; /* Slightly different shade for even rows */
    }
    .table-hover tbody tr:hover {
        background-color: #007000 !important; /* Hover effect */
    }
    .total-row, .total-row-individual { /* Specific styling for total rows */
        background-color: #008000 !important; /* Even lighter green for total rows */
        color: white !important;
        font-weight: bold;
    }
    .text-muted { /* Override text-muted on dark background */
        color: lightgray !important;
    }

    /* Print specific styles */
    @media print {
        body {
            background-color: white !important;
            color: black !important;
        }
        .container-fluid, .card, .card-header, .card-body,
        .report-title-bar, .filter-summary.alert, .table,
        .table thead th, .table tbody tr, .table tbody td,
        .total-row, .total-row-individual {
            background-color: white !important;
            color: black !important;
            border-color: #dee2e6 !important; /* Restore standard light borders for print */
        }
        .card {
            box-shadow: none !important;
            border: none !important;
        }
        .report-title-bar {
            text-align: center;
            padding: 10px 0;
            position: static; /* Remove absolute positioning for print */
        }
        .report-title-bar .print-btn {
            display: none !important; /* Hide the print button when printing */
        }
        .report-title-bar .right-info {
            position: static; /* Remove absolute positioning for print */
            display: block; /* Make it block to appear on a new line */
            margin-top: 5px;
        }
        .print-button, .btn-secondary { /* Hide other buttons when printing */
            display: none !important;
        }
        .table-striped tbody tr:nth-of-type(odd),
        .table-striped tbody tr:nth-of-type(even),
        .total-row, .total-row-individual {
            background-color: #f8f9fa !important; /* Light stripe for print */
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