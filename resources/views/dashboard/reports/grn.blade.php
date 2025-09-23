@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99 !important;
        font-size: 0.9rem;
    }
    .card {
        background: linear-gradient(135deg, #004d26, #006400);
        color: white;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    h2 { font-size: 1.3rem; margin-bottom: 12px; }
    h4 { font-size: 1rem; margin-top: 15px; margin-bottom: 8px; }
    .small-text { font-size: 0.85rem; color: #ffd700; }
    .table { background: rgba(255, 255, 255, 0.05); border-radius: 8px; overflow: hidden; width: 100%; margin-bottom: 10px; }
    .table th { background: rgba(255, 255, 255, 0.1); font-weight: bold; }
    .profit-positive { color: #00ff00; font-weight: bold; font-size: 1rem; }
    .profit-negative { color: #ff6347; font-weight: bold; font-size: 1rem; }
    .move-up { margin-top: -30px; }

    @media print {
        body * { visibility: hidden; }
        #print-area, #print-area * { visibility: visible; }
        #print-area { position: absolute; top: 0; left: 0; width: 100%; }
        .no-print { display: none !important; }
        table { font-size: 0.7rem; }
        .card { padding: 10px; }
    }
</style>

<div style="min-height: 100vh; padding: 20px;">
    <button onclick="window.print()" class="btn btn-primary mb-3 no-print">Print</button>

    <div id="print-area">
      @foreach($groupedData as $code => $data)
<div class="card">

    {{-- Date --}}
    <div style="text-align: right; font-weight: bold;">
        {{ \Carbon\Carbon::parse(\App\Models\Setting::value('value'))->format('Y-m-d') }}
    </div>

    {{-- Code & Item Info --}}
    <h2 class="move-up">
        Code: {{ $code }}
        <span class="small-text d-block">
            Item: {{ $data['all_rows']->first()->item_name ?? 'N/A' }} |
            Purchase Price: {{ number_format($data['purchase_price'], 2) }} |
            Original Weight: {{ number_format($data['totalOriginalWeight'], 2) }} |
            Original Packs: {{ number_format($data['totalOriginalPacks'], 2) }} |
            BW: {{ $data['remaining_weight'] }} |
            BP: {{ $data['remaining_packs'] }}
        </span>
    </h2>

    {{-- Sales + GRN Table --}}
    <h4>Transactions</h4>
    <table class="table table-bordered table-sm text-white mb-2">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Bill No</th>
                <th>Customer</th>
                <th>Weight</th>
                <th>Price/Unit</th>
                <th>Packs</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalWeight = 0;
                $totalPacks = 0;
                $totalAmount = 0;
            @endphp
            @foreach($data['all_rows'] as $row)
                @php
                    $totalWeight += is_numeric($row->weight) ? $row->weight : 0;
                    $totalPacks += is_numeric($row->packs) ? $row->packs : 0;
                    $totalAmount += is_numeric($row->total) ? $row->total : 0;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->Date)->format('Y-m-d') }}</td>
                    <td>{{ $row->type }}</td>
                    <td>{{ $row->bill_no }}</td>
                    <td>{{ $row->customer_code }}</td>
                    <td>{{ $row->weight }}</td>
                    <td>{{ $row->price_per_kg }}</td>
                    <td>{{ $row->packs }}</td>
                    <td>{{ $row->total }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="4" class="text-center">Total</td>
                <td>{{ number_format($totalWeight, 2) }}</td>
                <td>-</td>
                <td>{{ number_format($totalPacks, 2) }}</td>
                <td>{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Damage --}}
    <h4>Damage Section</h4>
    <table class="table table-bordered table-sm text-white mb-2">
        <thead>
            <tr>
                <th>Date</th>
                <th>Wasted Weight</th>
                <th>Wasted Packs</th>
                <th>Damage Value</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($data['damage']))
                <tr>
                    <td>{{ \Carbon\Carbon::parse($data['updated_at'])->format('Y-m-d') }}</td>
                    <td>{{ $data['damage']['wasted_weight'] }}</td>
                    <td>{{ $data['damage']['wasted_packs'] }}</td>
                    <td>{{ number_format($data['damage']['damage_value'], 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Profit --}}
    <p class="{{ $data['profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
        Profit: {{ number_format($data['profit'], 2) }}
    </p>

</div>
@endforeach

    </div>
</div>
@endsection



