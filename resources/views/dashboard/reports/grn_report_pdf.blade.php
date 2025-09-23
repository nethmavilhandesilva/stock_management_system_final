<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>GRN Report</title>
    <style>
        body {
            font-family: 'notosanssinhala', sans-serif;
            font-size: 0.9rem;
            color: #000;
        }
        .card {
            background: linear-gradient(135deg, #004d26, #006400);
            color: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
        }
        h2 {
            font-size: 1.2rem;
            margin-top: 0;
            margin-bottom: 8px;
        }
        h4 {
            font-size: 1rem;
            margin: 10px 0 5px;
        }
        .small-text {
            font-size: 0.85rem;
            color: #ffd700;
            display: block;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        th, td {
            border: 1px solid rgba(255,255,255,0.3);
            padding: 4px;
            text-align: center;
        }
        th {
            background: rgba(255,255,255,0.1);
        }
        .profit-positive {
            color: #00ff00;
            font-weight: bold;
        }
        .profit-negative {
            color: #ff6347;
            font-weight: bold;
        }
        .date-right {
            text-align: right;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    @foreach($groupedData as $code => $data)
                <div class="card">
                    @php
                        $settingDate = \App\Models\Setting::value('value');
                    @endphp
                    <div style="text-align: right; font-weight: bold;">
                        {{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}
                    </div>

                    <h2 class="move-up">
                        Code: {{ $code }}
                        <span class="small-text d-block">
                            Item: {{ $data['sales']->first()->item_name ?? 'N/A' }} |
                            Purchase Price: {{ number_format($data['purchase_price'], 2) }} |
                            ow: {{ number_format($data['totalOriginalWeight'], 2) }} |
                            op: {{ number_format($data['totalOriginalPacks'], 2) }} |
                            BW: {{ $data['remaining_weight'] }} |
                            BP: {{ $data['remaining_packs'] }}
                        </span>
                    </h2>

                    {{-- Sales Data --}}
                    <h4>Sales Data</h4>
                    <table class="table table-bordered table-sm text-white mb-2">
                        <thead>
                            <tr>
                                <th>දිනය</th>
                                <th>බිල් අංකය</th>
                                <th>ගෙණුම්කරු</th>
                                <th>බර</th>
                                <th>කිලෝමිල</th>
                                <th>මුළු මුදල</th>
                                <th>පැක්</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalWeight = 0;
                                $totalAmount = 0;
                                $totalPacks = 0;
                            @endphp
                            @foreach($data['sales'] as $sale)
                                @php
                                    $totalWeight += $sale->weight;
                                    $totalAmount += $sale->total;
                                    $totalPacks += $sale->packs;
                                @endphp
                                <tr>
                                    <td>{{ $sale->Date }}</td>
                                    <td>{{ $sale->bill_no }}</td>
                                    <td>{{ $sale->customer_code }}</td>
                                    <td>{{ $sale->weight }}</td>
                                    <td>{{ $sale->price_per_kg }}</td>
                                    <td>{{ $sale->total }}</td>
                                    <td>{{ $sale->packs }}</td>
                                </tr>
                            @endforeach
                            <tr style="font-weight: bold;">
                                <td colspan="3" class="text-center">මුළු එකතුව</td>
                                <td>{{ number_format($totalWeight, 2) }}</td>
                                <td>-</td>
                                <td>{{ number_format($totalAmount, 2) }}</td>
                                <td>{{ number_format($totalPacks, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Damage Section --}}
                    <h4>Damage Section</h4>
                    <table class="table table-bordered table-sm text-white mb-2">
                        <thead>
                            <tr>
                                <th>දිනය</th>
                                <th>Wasted Packs</th>
                                <th>Wasted Weight</th>
                                <th>Damage Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalDamagePacks = 0;
                                $totalDamageWeight = 0;
                                $totalDamageValue = 0;
                            @endphp
                            @if(!empty($data['damage']))
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($data['updated_at'])->format('Y-m-d') }}</td>
                                    <td>{{ $data['damage']['wasted_packs'] }}</td>
                                    <td>{{ $data['damage']['wasted_weight'] }}</td>
                                    <td>{{ number_format($data['damage']['damage_value'], 2) }}</td>
                                </tr>
                                @php
                                    $totalDamagePacks += $data['damage']['wasted_packs'];
                                    $totalDamageWeight += $data['damage']['wasted_weight'];
                                    $totalDamageValue += $data['damage']['damage_value'];
                                @endphp
                            @endif
                            <tr style="font-weight: bold;">
                                <td class="text-center">මුළු එකතුව</td>
                                <td>{{ number_format($totalDamagePacks, 2) }}</td>
                                <td>{{ number_format($totalDamageWeight, 2) }}</td>
                                <td>{{ number_format($totalDamageValue, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Profit --}}
                    <p class="{{ $data['profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        Profit: {{ number_format($data['profit'], 2) }}
                    </p>
                </div>
            @endforeach
        </div>

</body>
</html>
