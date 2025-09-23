<!DOCTYPE html>
<html>
<head>
    <title>Sales Report - Bill Summary</title>
    <style>
        body { font-family: 'notosanssinhala', sans-serif; }
        h1, h5 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .bill-total-row th { background-color: #d3d3d3; }
        .grand-total { text-align: right; margin-top: 20px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Sales Report - Bill Summary</h1>
    @php $grandTotal = 0; @endphp
    @forelse ($salesByBill as $billNo => $sales)
        @php
            $firstPrinted = $sales->first()->FirstTimeBillPrintedOn ?? null;
            $reprinted = $sales->first()->BillReprintAfterchanges ?? null;
            $billTotal = 0;
        @endphp
        <div style="margin-top: 20px;">
            <h5 style="text-align: left; margin-bottom: 5px;">Bill No: {{ $billNo }}</h5>
            <p style="text-align: right; margin-top: 0; margin-bottom: 5px;">
                @if($firstPrinted)
                    First Printed: {{ \Carbon\Carbon::parse($firstPrinted)->format('Y-m-d') }}
                @endif
                @if($reprinted)
                    <br>Reprinted: {{ \Carbon\Carbon::parse($reprinted)->format('Y-m-d') }}
                @endif
            </p>
            <table>
                <thead>
                    <tr>
                        <th>කේතය</th>
                        <th>පාරිභෝගික කේතය</th>
                        <th>සැපයුම්කරු කේතය</th>
                        <th>භාණ්ඩ නාමය</th>
                        <th>බර</th>
                        <th>කිලෝවකට මිල</th>
                        <th>එකතුව</th>
                        <th>පැකේජ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                        @php $billTotal += $sale->total; @endphp
                        <tr>
                            <td>{{ $sale->code }}</td>
                            <td>{{ $sale->customer_code }}</td>
                            <td>{{ $sale->supplier_code }}</td>
                            <td>{{ $sale->item_name }}</td>
                            <td>{{ $sale->weight }}</td>
                            <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                            <td>{{ number_format($sale->total, 2) }}</td>
                            <td>{{ $sale->packs }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bill-total-row">
                        <th colspan="6" style="text-align: right;">Bill Total:</th>
                        <th colspan="2">{{ number_format($billTotal, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @php $grandTotal += $billTotal; @endphp
    @empty
        <p style="text-align: center; color: #888;">No sales records found.</p>
    @endforelse
    @if ($salesByBill->isNotEmpty())
        <div class="grand-total">
            Grand Total: {{ number_format($grandTotal, 2) }}
        </div>
    @endif
</body>
</html>