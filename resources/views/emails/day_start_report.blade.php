<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day Start Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #004d00;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
        }
        .header h4 {
            margin: 5px 0 0;
            font-size: 18px;
        }
        .header .date-info {
            font-size: 14px;
            color: #ccc;
        }
        .table-container {
            margin-top: 20px;
            overflow-x: auto;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            color: #333;
        }
        .report-table thead th, .report-table tfoot td {
            background-color: #003300;
            color: white;
            padding: 10px;
            border: 1px solid #006600;
            text-align: center;
        }
        .report-table tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
        .report-table tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }
        .report-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .item-summary-row td {
            font-weight: bold;
            background-color: #e0e0e0;
        }
        .total-row td {
            font-weight: bold;
            background-color: #008000;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
            <h4 class="fw-bold">📦විකුණුම්/බර මත්තෙහි ඉතිරි වාර්තාව</h4>
            <span class="date-info">{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span>
        </div>

        <div class="table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th rowspan="2">වර්ගය</th>
                        <th colspan="2">මිලදී ගැනීම</th>
                        <th colspan="2">විකුණුම්</th>
                        <th rowspan="2">එකතුව</th>
                        <th colspan="2">ඉතිරි</th>
                    </tr>
                    <tr>
                        <th>මලු</th>
                        <th>බර</th>
                        <th>මලු</th>
                        <th>බර</th>
                        <th>මලු</th>
                        <th>බර</th>
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

                    @forelse($reportData as $item)
                        @php
                            $grandTotalOriginalPacks += $item['original_packs'];
                            $grandTotalOriginalWeight += $item['original_weight'];
                            $grandTotalSoldPacks += $item['sold_packs'];
                            $grandTotalSoldWeight += $item['sold_weight'];
                            $grandTotalSalesValue += $item['total_sales_value'];
                            $grandTotalRemainingPacks += $item['remaining_packs'];
                            $grandTotalRemainingWeight += $item['remaining_weight'];
                        @endphp
                        <tr class="item-summary-row">
                            <td>{{ $item['item_name'] }}</td>
                            <td>{{ number_format($item['original_packs']) }}</td>
                            <td>{{ number_format($item['original_weight'], 2) }}</td>
                            <td>{{ number_format($item['sold_packs']) }}</td>
                            <td>{{ number_format($item['sold_weight'], 2) }}</td>
                            <td>Rs. {{ number_format($item['total_sales_value'], 2) }}</td>
                            <td>{{ number_format($item['remaining_packs']) }}</td>
                            <td>{{ number_format($item['remaining_weight'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">දත්ත නොමැත.</td>
                        </tr>
                    @endforelse

                    <tr class="total-row">
                        <td colspan="1">සමස්ත එකතුව:</td>
                        <td>{{ number_format($grandTotalOriginalPacks) }}</td>
                        <td>{{ number_format($grandTotalOriginalWeight, 2) }}</td>
                        <td>{{ number_format($grandTotalSoldPacks) }}</td>
                        <td>{{ number_format($grandTotalSoldWeight, 2) }}</td>
                        <td>Rs. {{ number_format($grandTotalSalesValue, 2) }}</td>
                        <td>{{ number_format($grandTotalRemainingPacks) }}</td>
                        <td>{{ number_format($grandTotalRemainingWeight, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
