<!DOCTYPE html>
<html>
<head>
    <title>{{ $reportTitle }}</title>
    <style>
        body { font-family: 'notosanssinhala', sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #d3d3d3; font-weight: bold; }
        .item-summary-row { background-color: #f2f2f2; font-weight: bold; }
        .total-row { background-color: #cccccc; font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ $reportTitle }}</h1>

    <table>
        <thead>
            <tr>
               <th rowspan="2" style="width: 180px;">වර්ගය</th>
                <th rowspan="2">price</th>
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
            $grandTotalPrice = 0;
        @endphp

        @forelse(collect($reportData)->groupBy('grn_code') as $grnCode => $items)
            @foreach($items->groupBy('item_name')->sortKeys() as $itemName => $itemRecords)
                @php
                    $subTotalOriginalPacks = $itemRecords->sum('original_packs');
                    $subTotalOriginalWeight = $itemRecords->sum('original_weight');
                    $subTotalSoldPacks = $itemRecords->sum('sold_packs');
                    $subTotalSoldWeight = $itemRecords->sum('sold_weight');
                    $subTotalSalesValue = $itemRecords->sum('total_sales_value');
                    $subTotalRemainingPacks = $itemRecords->sum('remaining_packs');
                    $subTotalRemainingWeight = $itemRecords->sum('remaining_weight');
                    $subTotalPrice = $itemRecords->avg('price'); // Or sum if needed

                    $grandTotalOriginalPacks += $subTotalOriginalPacks;
                    $grandTotalOriginalWeight += $subTotalOriginalWeight;
                    $grandTotalSoldPacks += $subTotalSoldPacks;
                    $grandTotalSoldWeight += $subTotalSoldWeight;
                    $grandTotalSalesValue += $subTotalSalesValue;
                    $grandTotalRemainingPacks += $subTotalRemainingPacks;
                    $grandTotalRemainingWeight += $subTotalRemainingWeight;
                    $grandTotalPrice += $subTotalPrice;
                @endphp
                <tr class="item-summary-row">
                    <td><strong>{{ $itemName }} ({{ $grnCode }})</strong></td>
                    <td><strong>{{ number_format($subTotalPrice, 2) }}</strong></td>
                    <td><strong>{{ number_format($subTotalOriginalWeight, 2) }}</strong></td>
                    <td><strong>{{ number_format($subTotalOriginalPacks) }}</strong></td>
                    <td><strong>{{ number_format($subTotalSoldWeight, 2) }}</strong></td>
                    <td><strong>{{ number_format($subTotalSoldPacks) }}</strong></td>
                    <td><strong>Rs. {{ number_format($subTotalSalesValue, 2) }}</strong></td>
                    <td><strong>{{ number_format($subTotalRemainingWeight, 2) }}</strong></td>
                    <td><strong>{{ number_format($subTotalRemainingPacks) }}</strong></td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-4">දත්ත නොමැත.</td>
            </tr>
        @endforelse

        <tr class="total-row">
            <td class="text-end"><strong>සමස්ත එකතුව:</strong></td>
            <td><strong>{{ number_format($grandTotalPrice, 2) }}</strong></td>
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
</body>
</html>
