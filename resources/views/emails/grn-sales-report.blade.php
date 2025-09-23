<!DOCTYPE html>
<html>
<head>
    <title>GRN කේතය අනුව විකුණුම් වාර්තාව</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
        }
        .report-info {
            margin-top: 20px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .report-info strong {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-size: 13px;
        }
        thead {
            background-color: #343a40;
            color: white;
        }
        tfoot {
            background-color: #343a40;
            color: white;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>TGK ට්‍රේඩර්ස්</h2>
        <h4>📄 GRN කේතය අනුව විකුණුම් වාර්තාව</h4>
        <p>{{ \Carbon\Carbon::now()->format('Y-m-d') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>🗓️ දිනය</th>
                <th>බිල් අංකය</th>
                <th>ගෙණුම්කරු කේතය</th>
                <th>පැක්</th>
                <th>මිල (1kg)</th>
                <th>බර (kg)</th>
                <th>මුළු මුදල (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr>
                      <td>{{ $sale->Date}}</td>
                    <td>{{ $sale->bill_no }}</td>
                    <td>{{ $sale->customer_code }}</td>
                    <td class="text-end">{{ $sale->packs }}</td>
                    <td class="text-end">{{ number_format($sale->price_per_kg, 2) }}</td>
                    <td class="text-end">{{ number_format($sale->weight, 2) }}</td>
                    <td class="text-end">{{ number_format($sale->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">🚫 වාර්තා නැත</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end">මුළු එකතුව:</td>
                <td class="text-end">{{ $total_packs }}</td>
                <td class="text-center">-</td>
                <td class="text-end">{{ number_format($total_weight, 2) }}</td>
                <td class="text-end">{{ number_format($total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

</div>

</body>
</html>