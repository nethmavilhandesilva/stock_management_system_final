@extends('layouts.app')

@section('content')
<style>
    /* Your existing CSS styles for body, custom-card, table, etc. go here */
    body { background-color: #99ff99; }
    .custom-card { background-color: #006400 !important; color: white; }
    .custom-card table thead,
    .custom-card table tfoot { background-color: #004d00 !important; color: white; }
    .custom-card table tbody tr:nth-child(odd) { background-color: #00800033; }
    .report-title-bar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap; }
    .company-name { font-weight: 700; font-size: 1.5rem; color: white; margin: 0; }
    .report-title-bar h4 { margin: 0; color: white; font-weight: 700; white-space: nowrap; }
    .right-info { color: white; font-weight: 600; white-space: nowrap; }
    .print-btn { background-color: #004d00; color: white; border: none; padding: 0.4rem 1rem; border-radius: 5px; cursor: pointer; font-weight: 600; white-space: nowrap; transition: background-color 0.3s ease; }
    .print-btn:hover { background-color: #003300; }

    /* Print specific styles (as provided in your previous request) */
    @media print {
        body { background-color: #fff !important; color: #000; }
        .custom-card { background-color: #fff !important; color: #000 !important; box-shadow: none !important; border: none !important; }
        .custom-card table { border: 1px solid #ccc; }
        .custom-card table th, .custom-card table td { border: 1px solid #ccc; color: #000; }
        .custom-card table thead, .custom-card table tfoot { background-color: #eee !important; color: #000 !important; }
        .custom-card table tbody tr:nth-child(odd) { background-color: #f9f9f9 !important; }
        .report-title-bar h2, .report-title-bar h4, .right-info { color: #000 !important; }
        .print-btn { display: none !important; }
    }
</style>
<style>
    .compact-table th,
    .compact-table td {
        font-size: 13px;
        padding: 4px 8px;
    }
</style>

<div class="container mt-4">
    <div class="card shadow border-0 rounded-3 p-4 custom-card">
        <div class="report-title-bar">
            <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
            <h4 class="fw-bold text-white">📄 GRN කේතය අනුව විකුණුම් වාර්තාව</h4>
            <span class="right-info">{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span>
            <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
        </div>

        {{-- Display selected GRN Code and Date Range --}}
        <div class="mb-3 text-white">
            <strong>තෝරාගත් GRN කේතය:</strong> {{ $selectedGrnCode }}
            

            @if($startDate && $endDate)
                <span class="ms-3"><strong>දිනයන්:</strong> {{ $startDate }} සිට {{ $endDate }} දක්වා</span>
            @elseif($startDate)
                <span class="ms-3"><strong>ආරම්භ දිනය:</strong> {{ $startDate }}</span>
            @elseif($endDate)
                <span class="ms-3"><strong>අවසන් දිනය:</strong> {{ $endDate }}</span>
            @endif
        </div>
<table class="table table-sm table-bordered table-striped compact-table">
    <thead>
        <tr>
            <th>අයිතම කේතය</th>
            <th>වර්ගය</th>
            <th>මලු</th>
            <th>බර</th>
            <th>එකතුව</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_packs = 0;
            $total_weight = 0;
            $total_amount = 0;
        @endphp

        @forelse($sales as $sale)
            <tr>
                <td>{{ $sale->item_code }}</td>
                <td>{{ $sale->item_name }}</td>
                <td>{{ $sale->packs }}</td>
                <td>{{ number_format($sale->weight, 2) }}</td>
                <td>{{ number_format($sale->total, 2) }}</td>
            </tr>

            @php
                $total_packs += $sale->packs;
                $total_weight += $sale->weight;
                $total_amount += $sale->total;
            @endphp
        @empty
            <tr>
                <td colspan="5" class="text-center text-white bg-secondary">වාර්තා නැත</td>
            </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr class="table-secondary fw-bold">
            <td class="text-end" colspan="2">මුළු එකතුව:</td>
            <td>{{ $total_packs }}</td>
            <td>{{ number_format($total_weight, 2) }}</td>
            <td>{{ number_format($total_amount, 2) }}</td>
        </tr>
    </tfoot>
</table>
    </div>
     <div>
       
        <a href="{{ route('report.download', ['reportType' => 'supplier-sales', 'format' => 'excel']) }}" class="btn btn-success me-2">Download Excel</a>
        <a href="{{ route('report.download', ['reportType' => 'supplier-sales', 'format' => 'pdf']) }}" class="btn btn-danger">Download PDF</a>
    </div>
</div>
@endsection