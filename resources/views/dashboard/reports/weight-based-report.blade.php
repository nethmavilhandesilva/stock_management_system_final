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

    /* Print specific styles */
    @media print {
        body {
            background-color: #fff !important;
            color: #000;
        }
        .custom-card {
            background-color: #fff !important;
            color: #000 !important;
            box-shadow: none !important;
            border: none !important;
        }
        .custom-card table {
            border: 1px solid #ccc;
        }
        .custom-card table th,
        .custom-card table td {
            border: 1px solid #ccc;
            color: #000;
        }
        .custom-card table thead,
        .custom-card table tfoot {
            background-color: #eee !important;
            color: #000 !important;
        }
        .custom-card table tbody tr:nth-child(odd) {
            background-color: #f9f9f9 !important;
        }
        .report-title-bar h2, .report-title-bar h4, .right-info {
            color: #000 !important;
        }
        .print-btn {
            display: none !important;
        }
    }
</style>

<div class="container mt-4">
    <div class="card shadow border-0 rounded-3 p-4 custom-card">
        <div class="report-title-bar">
            <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
            <h4 class="fw-bold text-white">📦 සැපයුම්කරු අනුව GRN වාර්තාව</h4>
            <span class="right-info">{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span>
            <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
        </div>

        {{-- Display selected supplier and date range --}}
        <div class="mb-3 text-white">
            <strong>සැපයුම්කරු:</strong> {{ $selectedSupplierCode === 'all' ? 'සියලු සැපයුම්කරුවන්' : $selectedSupplierCode }}
            @if($startDate && $endDate)
                <span class="ms-3"><strong>දිනයන්:</strong> {{ $startDate }} සිට {{ $endDate }} දක්වා</span>
            @elseif($startDate)
                <span class="ms-3"><strong>ආරම්භ දිනය:</strong> {{ $startDate }}</span>
            @elseif($endDate)
                <span class="ms-3"><strong>අවසන් දිනය:</strong> {{ $endDate }}</span>
            @endif
        </div>

       <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>අයිතම කේතය</th>
            <th>අයිතමයේ නම</th>
            <th>පැක්</th>
            <th>බර (කිලෝග්‍රෑම්)</th>
            <th>මුළු මුදල</th>
        </tr>
    </thead>
    <tbody>
        @php
            // Initialize total variables for calculations
            $total_packs = 0;
            $total_weight = 0;
            $total_amount = 0;
        @endphp

        {{-- Loop through each GRN entry --}}
        @forelse(  $salesRecords as $entry)
            <tr>
                <td>{{ $entry->item_code }}</td>
                <td>{{ $entry->item_name }}</td>
                <td>{{ $entry->packs }}</td>
                <td>{{ number_format($entry->weight, 2) }}</td>
                <td>{{ number_format($entry->total, 2) }}</td>
            </tr>

            @php
                // Accumulate totals
                $total_packs += $entry->packs;
                $total_weight += $entry->weight;
                $total_amount += $entry->total;
            @endphp
        @empty
            {{-- Message to display if no records are found --}}
            <tr>
                <td colspan="5" class="text-center text-white">වාර්තා නැත</td>
            </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr class="table-secondary fw-bold">
            {{-- "මුළු එකතුව" spans the first two columns (Item Code, Item Name) --}}
            <td class="text-end" colspan="2">මුළු එකතුව:</td>
            {{-- Display totals under their respective columns --}}
            <td>{{ $total_packs }}</td>
            <td>{{ number_format($total_weight, 2) }}</td>
            <td>{{ number_format($total_amount, 2) }}</td>
        </tr>
    </tfoot>
</table>
    </div>
</div>
@endsection