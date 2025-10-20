<div style="font-family: sans-serif; padding: 20px; border: 1px solid #ccc;">
    <h2 style="font-weight: bold;">TGK ට්‍රේඩර්ස්</h2>
    <h4>බර වාර්තාව</h4>

    <span style="float: right;">
        {{ $settingDate ?? now()->format('Y-m-d') }}
    </span>

    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead style="background-color: #4CAF50; color: white;">
            <tr>
                <th style="padding: 8px; border: 1px solid #ddd;">අයිතම කේතය</th>
                <th style="padding: 8px; border: 1px solid #ddd;">වර්ගය</th>
                <th style="padding: 8px; border: 1px solid #ddd;">බර (kg)</th>
                <th style="padding: 8px; border: 1px solid #ddd;">මලු</th>
                <th style="padding: 8px; border: 1px solid #ddd;">මලු ගාස්තුව (Rs)</th>
                <th style="padding: 8px; border: 1px solid #ddd;">එකතුව (Rs)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_packs = 0;
                $total_weight = 0;
                $total_pack_due_cost = 0;
                $total_net_total = 0;
            @endphp

            @foreach ($sales as $sale)
                @php
                    $pack_due = $sale->pack_due ?? 0;
                    $packs = $sale->packs ?? 0;
                    $weight = $sale->weight ?? 0;
                    $item_total = $sale->total ?? 0;

                    $pack_due_cost = $packs * $pack_due;
                    $net_total = $item_total - $pack_due_cost;

                    $total_packs += $packs;
                    $total_weight += $weight;
                    $total_pack_due_cost += $pack_due_cost;
                    $total_net_total += $net_total;
                @endphp
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $sale->item_code }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $sale->item_name }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($weight, 2) }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($packs, 0) }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($pack_due_cost, 2) }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($net_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <td colspan="2" style="padding: 8px; border: 1px solid #ddd; text-align: right;">මුළු එකතුව:</td>
                <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($total_weight, 2) }}</td>
                <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($total_packs, 0) }}</td>
                <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($total_pack_due_cost, 2) }}</td>
                <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($total_net_total, 2) }}</td>
            </tr>
            <tr style="background-color: #ddd; font-weight: bold;">
                <td colspan="5" style="padding: 8px; border: 1px solid #ddd; text-align: right;">අවසන් මුළු එකතුව:</td>
                <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($final_total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
