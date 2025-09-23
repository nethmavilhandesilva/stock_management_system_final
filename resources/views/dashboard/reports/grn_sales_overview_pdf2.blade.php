<!DOCTYPE html>
<html>
<head>
    <title>ඉතිරි වාර්තාව</title>
    <style>
        body { font-family: 'notosanssinhala', sans-serif; margin: 0; padding: 0; }
        h1 { text-align: center; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #d3d3d3; font-weight: bold; }
        .total-row { background-color: #cccccc; font-weight: bold; }

        /* Print settings for A4 */
        @media print {
            body { margin: 0; }
            table { font-size: 10px; page-break-inside: auto; width: 100%; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; } /* Repeat header on each page */
            tfoot { display: table-footer-group; }
            @page {
                size: A4 portrait; /* change to 'landscape' if needed */
                margin: 20mm;
            }
        }
    </style>
</head>
<body>
    <h1>ඉතිරි වාර්තාව</h1>
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
                    $remainingWeight = floatval(str_replace(',', '', $data['remaining_weight'] ?? 0));

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
                    <td colspan="7" class="text-center text-muted py-4">දත්ත නොමැත.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td class="text-end"><strong>සමස්ත එකතුව:</strong></td>
                <td><strong>{{ number_format($grandTotalOriginalWeight, 2) }}</strong></td>
                <td><strong>{{ number_format($grandTotalOriginalPacks) }}</strong></td>
                <td><strong>{{ number_format($grandTotalSoldWeight, 2) }}</strong></td>
                <td><strong>{{ number_format($grandTotalSoldPacks) }}</strong></td>
                <td><strong>{{ number_format($grandTotalRemainingWeight, 2) }}</strong></td>
                <td><strong>{{ number_format($grandTotalRemainingPacks) }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
