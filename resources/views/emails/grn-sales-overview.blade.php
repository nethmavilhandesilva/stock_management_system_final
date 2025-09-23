<!DOCTYPE html>
<html>
<head>
    <title>ඉතිරි වාර්තාව</title>
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
        .total-row { background-color: #008000 !important; font-weight: bold; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>TGK ට්‍රේඩර්ස්</h2>
        <h4>📦 ඉතිරි වාර්තාව</h4>
        <p>{{ \Carbon\Carbon::now()->format('Y-m-d') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">වර්ගය</th>
                <th colspan="2">මිලදී ගැනීම</th>
                <th colspan="2">විකුණුම්</th>
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
                $grandTotalRemainingPacks = 0;
                $grandTotalRemainingWeight = 0;
            @endphp
            @forelse($reportData as $data)
                @php
                    $originalPacks = floatval($data['original_packs'] ?? 0);
                    $originalWeight = floatval($data['original_weight'] ?? 0);
                    $soldPacks = floatval($data['sold_packs'] ?? 0);
                    $soldWeight = floatval($data['sold_weight'] ?? 0);
                    $remainingPacks = floatval($data['remaining_packs'] ?? 0);
                    $remainingWeight = floatval($data['remaining_weight'] ?? 0);

                    $grandTotalOriginalPacks += $originalPacks;
                    $grandTotalOriginalWeight += $originalWeight;
                    $grandTotalSoldPacks += $soldPacks;
                    $grandTotalSoldWeight += $soldWeight;
                    $grandTotalRemainingPacks += $remainingPacks;
                    $grandTotalRemainingWeight += $remainingWeight;
                @endphp
                <tr>
                    <td>{{ $data['item_name'] }}</td>
                    <td>{{ number_format($originalWeight, 2) }}</td>
                    <td>{{ number_format($originalPacks) }}</td>
                    <td>{{ number_format($soldWeight, 2) }}</td>
                    <td>{{ number_format($soldPacks) }}</td>
                    <td>{{ number_format($remainingWeight, 2) }}</td>
                    <td>{{ number_format($remainingPacks) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">දත්ත නොමැත.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="text-end"><strong>සමස්ත එකතුව:</strong></td>
                <td><strong>{{ number_format($grandTotalOriginalWeight, 2) }}</strong></td>
                <td><strong>{{ number_format($grandTotalOriginalPacks) }}</strong></td>
                <td><strong>{{ number_format($grandTotalSoldWeight, 2) }}</strong></td>
                <td><strong>{{ number_format($grandTotalSoldPacks) }}</strong></td>
                <td><strong>{{ number_format($grandTotalRemainingWeight, 2) }}</strong></td>
                <td><strong>{{ number_format($grandTotalRemainingPacks) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>

</body>
</html>