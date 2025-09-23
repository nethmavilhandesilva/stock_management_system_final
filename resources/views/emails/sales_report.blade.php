<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-end {
            text-align: right;
        }
        h3 {
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: center;">
            <h2 style="margin: 0;">Sales Report</h2>
            <h4 style="margin: 5px 0 20px 0;">Bill Summary</h4>
        </div>

        @if ($salesByBill->isEmpty())
            <p>No sales records found.</p>
        @else
            @php $grandTotal = 0; @endphp

            @foreach ($salesByBill as $billNo => $sales)
                @php
                    $firstPrinted = $sales->first()->FirstTimeBillPrintedOn ?? null;
                    $reprinted = $sales->first()->BillReprintAfterchanges ?? null;
                    $billTotal = 0;
                @endphp

                <h5 style="margin-top: 20px; margin-bottom: 5px;">Bill No: {{ $billNo }}</h5>
                <small>
                    @if($firstPrinted)
                        First Printed: {{ \Carbon\Carbon::parse($firstPrinted)->format('Y-m-d') }}
                    @endif
                    @if($reprinted)
                        Reprinted: {{ \Carbon\Carbon::parse($reprinted)->format('Y-m-d') }}
                    @endif
                </small>

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
                        <tr>
                            <th colspan="6" class="text-end">Bill Total:</th>
                            <th colspan="2">{{ number_format($billTotal, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>

                @php $grandTotal += $billTotal; @endphp
            @endforeach

            <div class="text-end" style="margin-top: 20px;">
                <h3>Grand Total: {{ number_format($grandTotal, 2) }}</h3>
            </div>
        @endif
    </div>
</body>
</html>