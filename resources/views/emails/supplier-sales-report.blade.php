<!DOCTYPE html>
<html>
<head>
    <title>විකුණුම්/බර මත්තෙහි ඉතිරි වාර්තාව</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; color: #333; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; background-color: #ffffff; padding: 20px; border: 1px solid #ddd; }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #333; }
        .header h2 { margin: 0; font-size: 24px; }
        .header h4 { margin: 5px 0 0; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; font-size: 13px; }
        thead th { background-color: #343a40; color: white; }
        tfoot td { background-color: #343a40; color: white; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #008000 !important; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>TGK ට්‍රේඩර්ස්</h2>
        <h4>📦 විකිුණුම්/බර මත්තෙහි ඉතිරි වාර්තාව</h4>
        <p>{{ \Carbon\Carbon::now()->format('Y-m-d') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">වර්ගය</th>
                <th colspan="2">මිලදී ගැනීම</th>
                <th colspan="2">විකුණුම්</th>
                <th rowspan="2">එකතුව</th>
                <th colspan="2">ඉතිරි</th>
            </tr>
            <tr>
                <th>බර</th>
                <th>මලු</th>
                <th>බර</th>
                <th>මලු</th>
                <th>බර</th>
                <th>මලු</th>
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

            @forelse(collect($reportData)->groupBy('grn_code') as $grnCode => $items)
                @foreach($items->groupBy('item_name') as $itemName => $itemRecords)
                    @php
                        $subTotalOriginalPacks = $itemRecords->sum('original_packs');
                        $subTotalOriginalWeight = $itemRecords->sum('original_weight');
                        $subTotalSoldPacks = $itemRecords->sum('sold_packs');
                        $subTotalSoldWeight = $itemRecords->sum('sold_weight');
                        $subTotalSalesValue = $itemRecords->sum('total_sales_value');
                        $subTotalRemainingPacks = $itemRecords->sum('remaining_packs');
                        $subTotalRemainingWeight = $itemRecords->sum('remaining_weight');

                        $grandTotalOriginalPacks += $subTotalOriginalPacks;
                        $grandTotalOriginalWeight += $subTotalOriginalWeight;
                        $grandTotalSoldPacks += $subTotalSoldPacks;
                        $grandTotalSoldWeight += $subTotalSoldWeight;
                        $grandTotalSalesValue += $subTotalSalesValue;
                        $grandTotalRemainingPacks += $subTotalRemainingPacks;
                        $grandTotalRemainingWeight += $subTotalRemainingWeight;
                    @endphp
                    <tr>
                        <td><strong>{{ $itemName }} ({{ $grnCode }})</strong></td>
                        <td>{{ number_format($subTotalOriginalWeight, 2) }}</td>
                        <td>{{ number_format($subTotalOriginalPacks) }}</td>
                        <td>{{ number_format($subTotalSoldWeight, 2) }}</td>
                        <td>{{ number_format($subTotalSoldPacks) }}</td>
                        <td>Rs. {{ number_format($subTotalSalesValue, 2) }}</td>
                        <td>{{ number_format($subTotalRemainingWeight, 2) }}</td>
                        <td>{{ number_format($subTotalRemainingPacks) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="8" class="text-center">දත්ත නොමැත.</td>
                </tr>
            @endforelse

            <tr class="total-row">
                <td class="text-end"><strong>සමස්ත එකතුව:</strong></td>
                <td><strong>{{ number_format($grandTotalOriginalWeight, 2) }}</strong></td>
                <td><strong>{{ number_format($grandTotalOriginalPacks) }}</strong></td>
                <td><strong>{{ number_format($grandTotalSoldWeight, 2) }}</strong></td>
                <td><strong>{{ number_format($grandTotalSoldPacks) }}</strong></td>
                <td><strong>Rs. {{ number_format($grandTotalSalesValue, 2) }}</strong></td>
                <td><strong>{{ number_format($grandTotalRemainingWeight, 2) }}</strong></td>
                <td><strong>{{ number_format($grandTotalRemainingPacks) }}</strong></td>
            </tr>
        </tbody>
    </table>

</div>

</body>
</html>