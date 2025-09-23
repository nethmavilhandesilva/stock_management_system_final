<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report: Bill Summary</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; color: #333; }
        .email-container { max-width: 800px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); overflow: hidden; }
        .report-header { background-color: #006400; color: white; padding: 20px; text-align: center; }
        .report-header h2 { margin: 0; font-size: 24px; }
        .report-header h4 { margin: 5px 0 0; font-size: 18px; font-weight: bold; }
        .report-header .date-info { font-size: 14px; margin-top: 5px; }
        .content { padding: 20px; }
        .bill-section { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .bill-section h5 { color: #006400; font-size: 16px; margin-bottom: 10px; }
        .bill-meta { font-size: 12px; color: #777; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #004d00; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .table-totals { background-color: #e6e6e6; font-weight: bold; }
        .grand-total { text-align: right; padding: 15px 20px; font-size: 20px; font-weight: bold; background-color: #f0f0f0; border-top: 2px solid #ccc; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="report-header">
            <h2>Sales Report</h2>
            <h4>Bill Summary</h4>
            <div class="date-info">{{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}</div>
        </div>

        <div class="content">
            @if ($salesByBill->isEmpty())
                <p style="text-align: center; color: #777;">No sales records found.</p>
            @else
                @foreach ($salesByBill as $billNo => $sales)
                    @php
                        $firstPrinted = $sales->first()->FirstTimeBillPrintedOn ?? null;
                        $reprinted = $sales->first()->BillReprintedOn ?? null;
                        $billTotal = 0;
                    @endphp

                    <div class="bill-section">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <h5 style="margin: 0;">Bill No: {{ $billNo }}</h5>
                            <div class="bill-meta">
                                @if($firstPrinted)
                                    <span>First Printed: {{ \Carbon\Carbon::parse($firstPrinted)->format('Y-m-d H:i') }}</span>
                                @endif
                                @if($reprinted)
                                    <br><span>Reprinted: {{ \Carbon\Carbon::parse($reprinted)->format('Y-m-d H:i') }}</span>
                                @endif
                            </div>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Customer Code</th>
                                    <th>Supplier Code</th>
                                    <th>Item Name</th>
                                    <th>Weight</th>
                                    <th>Price per Kg</th>
                                    <th>Total</th>
                                    <th>Packs</th>
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
                                <tr class="table-totals">
                                    <td colspan="6" class="text-right">Bill Total:</td>
                                    <td colspan="2">{{ number_format($billTotal, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endforeach

                <div class="grand-total">
                    Grand Total: {{ number_format($grandTotal, 2) }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
