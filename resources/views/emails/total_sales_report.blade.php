<!DOCTYPE html>
<html>
<head>
    <title>Total Sales Report</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f0f0; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .report-header { text-align: center; margin-bottom: 20px; }
        .report-header h2 { font-size: 24px; font-weight: bold; color: #004d00; margin: 0; }
        .report-header h4 { font-size: 18px; color: #004d00; margin: 5px 0 0; }
        .report-header span { font-size: 14px; color: #777777; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; font-size: 13px; }
        thead tr { background-color: #004d00; color: white; }
        tbody tr:nth-child(odd) { background-color: #f9f9f9; }
        tfoot tr { background-color: #e0e0e0; font-weight: bold; }
        tfoot td { color: #333; }
        .text-end { text-align: right; }
    </style>
</head>
<body>

<div class="container">
    <div class="report-header">
        <h2>TGK ට්‍රේඩර්ස්</h2>
        <h4>📦 මුළු විකුණුම්</h4>
        <span>{{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>සැපයුම්කරු</th>
                <th>මලු</th>
                <th>වර්ගය</th>
                <th>බර</th>
                <th>මිල</th>
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
                    <td>{{ $sale->packs }}</td>
                    <td>{{ $sale->item_name }}</td>
                    <td>{{ number_format($sale->weight, 2) }}</td>
                    <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                    <td>{{ number_format($sale->total, 2) }}</td>
                    <td>{{ $sale->bill_no }}</td>
                    <td>{{ $sale->customer_code }}</td>
                     <td>{{ $sale->Date}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #777;">පෙරහන් කරන ලද දත්ත නොමැත.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row-individual">
                <td colspan="1" class="text-end"><strong>සමස්ත එකතුව:</strong></td>
                <td><strong>{{ number_format($totalPacks) }}</strong></td>
                <td></td>
                <td><strong>{{ number_format($totalWeight, 2) }}</strong></td>
                <td></td>
                <td><strong>Rs. {{ number_format($grandTotalAmount, 2) }}</strong></td>
                <td colspan="3"></td>
            </tr>
            <tr class="total-row">
                <td colspan="7" class="text-end"><strong>මුළු විකුණුම් වටිනාකම:</strong></td>
                <td colspan="2"><strong>Rs. {{ number_format($grandTotal, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>


</body>
</html>