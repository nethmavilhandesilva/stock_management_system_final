
{{-- resources/views/reports/sales_filter_report.blade.php --}}

@extends('layouts.app') {{-- Extend your main application layout --}}

@section('content') {{-- Place the report content inside the 'content' section --}}

<div class="container-fluid py-4"> {{-- Added a container with padding for better spacing --}}
    <div class="card shadow-sm mb-4"> {{-- Optional: Wrap in a card for better presentation --}}
        {{-- Replaced the old card-header content with the new title bar --}}
        <div class="card-header text-center" style="background-color: #004d00 !important;">
            <div class="report-title-bar">
                <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
                <h4 class="fw-bold text-white">📦 මුළු විකුණුම්</h4>
                <span class="right-info">{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span>
                <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
            </div>
        </div>

        <div class="card-body">
            {{-- Filter Summary - Removed as per user's previous request (though not explicitly stated in this turn, it was removed from the provided code) --}}
            {{-- If you still need the filter summary, uncomment the block below and re-add the `filter-summary` div. --}}
            {{--
            <div class="filter-summary alert p-3 mb-4">
                <h5 class="alert-heading mb-2">යොදන ලද පෙරහන්:</h5>
                <ul class="list-unstyled mb-0">
                    <li><strong>සැපයුම්කරු කේතය:</strong> {{ $request->supplier_code ?? 'සියලු' }}</li>
                    <li><strong>පාරිභෝගික කේතය:</strong> {{ $request->customer_code ?? 'සියලු' }}</li>
                    <li><strong>අයිතම කේතය:</strong> {{ $request->item_code ?? 'සියලු' }}</li>
                    <li><strong>ආරම්භක දිනය:</strong> {{ $request->start_date ?? 'සියලු' }}</li>
                    <li><strong>අවසන් දිනය:</strong> {{ $request->end_date ?? 'සියලු' }}</li>
                    <li><strong>අනුපිළිවෙල:</strong> {{ str_replace('_', ' ', $request->order_by ?? 'සාමාන්‍ය (නව සිට පැරණි)') }}</li>
                </ul>
            </div>
            --}}

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>සැපයුම්කරු</th>
                            <th>ඇසුරුම්</th>
                            <th>අයිතමයේ නම</th>
                            <th>බර (kg)</th>
                            <th>මිල/කිලෝග්‍රෑමය</th>
                            <th>මුළු මුදල</th>
                            <th>බිල්පත් අංකය</th>
                            <th>පාරිභෝගික කේතය</th>
                            <th>දිනය සහ වේලාව</th> {{-- Changed header to reflect time --}}
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalPacks = 0;
                            $totalWeight = 0;
                            $grandTotalAmount = 0; // Renamed to avoid conflict with existing $grandTotal
                        @endphp

                        @forelse($sales as $sale)
                            @php
                                $totalPacks += $sale->packs;
                                $totalWeight += $sale->weight;
                                $grandTotalAmount += $sale->total; // Summing up individual sale totals
                            @endphp
                            <tr>
                                <td>{{ $sale->code }}</td>
                                <td>{{ $sale->packs }}</td>
                                <td>{{ $sale->item_name }}</td>
                                <td>{{ number_format($sale->weight, 2) }}</td>
                                <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                                <td>{{ number_format($sale->total, 2) }}</td>
                                <td>{{ $sale->bill_no }}</td>
                                <td>{{ $sale->customer_code }}</td>
                                {{-- Updated to display date and time in Sri Lankan timezone --}}
                                <td>{{ \Carbon\Carbon::parse($sale->created_at)->timezone('Asia/Colombo')->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">පෙරහන් කරන ලද දත්ත නොමැත.</td>
                            </tr>
                        @endforelse
                        {{-- New row for individual column totals --}}
                        <tr class="total-row-individual">
                            <td colspan="1" class="text-end"><strong>සමස්ත එකතුව:</strong></td>
                            <td><strong>{{ number_format($totalPacks) }}</strong></td> {{-- Total Packs --}}
                            <td></td>
                            <td><strong>{{ number_format($totalWeight, 2) }}</strong></td> {{-- Total Weight --}}
                            <td></td>
                            <td><strong>Rs. {{ number_format($grandTotalAmount, 2) }}</strong></td> {{-- Total Amount --}}
                            <td colspan="3"></td> {{-- Span remaining columns --}}
                        </tr>
                        {{-- Existing grand total row (if still needed, can be combined with above) --}}
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

    /* Filter Summary (if re-added) */
    .filter-summary.alert {
        background-color: #006600 !important; /* A slightly lighter green for the filter summary */
        color: white !important;
        border: 1px solid #008000 !important; /* A matching border */
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
