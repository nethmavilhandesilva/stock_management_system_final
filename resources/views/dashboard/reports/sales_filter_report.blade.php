{{-- resources/views/reports/sales_filter_report.blade.php --}}

@extends('layouts.app') {{-- Extend your main application layout --}}

@section('content') {{-- Place the report content inside the 'content' section --}}

<div class="container-fluid py-4"> {{-- Added a container with padding for better spacing --}}
    <div class="card shadow-sm mb-4"> {{-- Optional: Wrap in a card for better presentation --}}
        <div class="card-header bg-primary text-white text-center">
            <h1 class="h3 mb-0">විකුණුම් වාර්තාව</h1>
            <p class="mb-0">වාර්තාව ජනනය කළ වේලාව: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>
        </div>

        <div class="card-body">
            <div class="filter-summary alert alert-info p-3 mb-4">
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

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>කේතය</th>
                            <th>පාරිභෝගික කේතය</th>
                            <th>අයිතමයේ නම</th>
                            <th>බර (kg)</th>
                            <th>මිල/කිලෝග්‍රෑමය</th>
                            <th>ඇසුරුම්</th>
                            <th>බිල්පත් අංකය</th>
                            <th>මුළු මුදල</th>
                            <th>දිනය</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->code }}</td>
                                <td>{{ $sale->customer_code }}</td>
                                <td>{{ $sale->item_name }}</td>
                                <td>{{ number_format($sale->weight, 2) }}</td>
                                <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                                <td>{{ $sale->packs }}</td>
                                <td>{{ $sale->bill_no }}</td>
                                <td>{{ number_format($sale->total, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">පෙරහන් කරන ලද දත්ත නොමැත.</td>
                            </tr>
                        @endforelse
                        <tr class="total-row table-info">
                            <td colspan="7" class="text-end"><strong>මුළු විකුණුම් වටිනාකම:</strong></td>
                            <td colspan="2"><strong>Rs. {{ number_format($grandTotal, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                <button onclick="window.print()" class="btn btn-success print-button me-2">
                    <i class="material-icons me-1">print</i> වාර්තාව මුද්‍රණය කරන්න
                </button>
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="material-icons me-1">arrow_back</i> ආපසු යන්න
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Basic print-specific styling (if not already in layouts.app) --}}
<style>
    @media print {
        body {
            margin: 0;
            font-size: 0.85em; /* Adjust font size for print */
        }
        .container-fluid {
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .card-header, .card-body {
            padding: 10px 15px !important;
        }
        .report-header, .filter-summary {
            margin-bottom: 10px !important;
            padding: 0 !important;
            border: none !important;
            background-color: transparent !important;
        }
        .table {
            font-size: 0.85em;
        }
        .table th, .table td {
            padding: 5px !important;
        }
        .print-button, .btn-secondary { /* Hide buttons when printing */
            display: none !important;
        }
    }
</style>

@endsection