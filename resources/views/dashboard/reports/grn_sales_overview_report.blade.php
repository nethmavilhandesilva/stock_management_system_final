
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
                            <th rowspan="2">වර්ගය</th>
                            <th colspan="2">මිලදී ගැනීම</th> {{-- Main header for Purchase --}}
                            <th colspan="2">විකුණුම්</th> {{-- Main header for Sold --}}
                            <th rowspan="2">එකතුව</th> {{-- Main header for Total Sales Value --}}
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
                            // Initialize arrays for grand totals
                            $grandTotalOriginalPacks = 0;
                            $grandTotalOriginalWeight = 0;
                            $grandTotalSoldPacks = 0;
                            $grandTotalSoldWeight = 0;
                            $grandTotalSalesValue = 0;
                            $grandTotalRemainingPacks = 0;
                            $grandTotalRemainingWeight = 0;

                            // Group data by item_name
                            $groupedData = collect($reportData)->groupBy('item_name');
                        @endphp

                        @forelse($groupedData as $itemName => $items)
                            @php
                                // Initialize and calculate sub-totals for each group
                                $subTotalOriginalPacks = $items->sum('original_packs');
                                $subTotalOriginalWeight = $items->sum('original_weight');
                                $subTotalSoldPacks = $items->sum('sold_packs');
                                $subTotalSoldWeight = $items->sum('sold_weight');
                                $subTotalSalesValue = $items->sum('total_sales_value');
                                $subTotalRemainingPacks = $items->sum('remaining_packs');
                                $subTotalRemainingWeight = $items->sum(function($item) {
                                    return floatval(str_replace(',', '', $item['remaining_weight']));
                                });

                                // Add sub-totals to grand totals
                                $grandTotalOriginalPacks += $subTotalOriginalPacks;
                                $grandTotalOriginalWeight += $subTotalOriginalWeight;
                                $grandTotalSoldPacks += $subTotalSoldPacks;
                                $grandTotalSoldWeight += $subTotalSoldWeight;
                                $grandTotalSalesValue += $subTotalSalesValue;
                                $grandTotalRemainingPacks += $subTotalRemainingPacks;
                                $grandTotalRemainingWeight += $subTotalRemainingWeight;
                            @endphp
                            <tr class="item-summary-row">
                                <td><strong>{{ $itemName }}</strong></td>
                                <td><strong>{{ number_format($subTotalOriginalPacks) }}</strong></td>
                                <td><strong>{{ number_format($subTotalOriginalWeight, 2) }}</strong></td>
                                <td><strong>{{ number_format($subTotalSoldPacks) }}</strong></td>
                                <td><strong>{{ number_format($subTotalSoldWeight, 2) }}</strong></td>
                                <td><strong>Rs. {{ number_format($subTotalSalesValue, 2) }}</strong></td>
                                <td><strong>{{ number_format($subTotalRemainingPacks) }}</strong></td>
                                <td><strong>{{ number_format($subTotalRemainingWeight, 2) }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">දත්ත නොමැත.</td>
                            </tr>
                        @endforelse
                        {{-- Grand Totals Row --}}
                        <tr class="total-row">
                            <td class="text-end"><strong>සමස්ත එකතුව:</strong></td>
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
    <div>
        <a href="{{ route('report.download', ['reportType' => 'supplier-sales', 'format' => 'excel']) }}" class="btn btn-success me-2">Download Excel</a>
        <a href="{{ route('report.download', ['reportType' => 'supplier-sales', 'format' => 'pdf']) }}" class="btn btn-danger">Download PDF</a>
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
    .item-summary-row {
        background-color: #005a00 !important;
        font-weight: bold;
    }
    .total-row { /* Specific styling for total rows */
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
        .total-row, .item-summary-row {
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
        .total-row, .item-summary-row {
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
