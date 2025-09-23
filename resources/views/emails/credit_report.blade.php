<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Report - TGK Traders</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; color: #333; }
        .email-container { max-width: 800px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); overflow: hidden; }
        .report-header { background-color: #006400; color: white; padding: 20px; text-align: center; }
        .report-header h2 { margin: 0; font-size: 24px; }
        .report-header h4 { margin: 5px 0 0; font-size: 18px; font-weight: bold; }
        .report-header .date-info { font-size: 14px; margin-top: 5px; }
        .content { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #004d00; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .total-row { background-color: #dff0d8; font-weight: bold; color: black; }
        .net-balance-row { background-color: #004d00; font-weight: bold; color: white; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="report-header">
            <h2>TGK ට්‍රේඩර්ස්</h2>
            <h4>ණය වාර්තාව</h4>
            <div class="date-info">{{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}</div>
        </div>

        <div class="content">
            @if ($loans->isEmpty())
                <p style="text-align: center; color: #777;">No loan records found for the selected filters.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>පාරිභෝගික නම (Customer Name)</th>
                            <th>බිල් අංකය (Bill No)</th>
                            <th>දිනය (Date)</th>
                            <th>විස්තරය (Description)</th>
                            <th>චෙක්පත් (Cheque No)</th>
                            <th>බැංකුව (Bank)</th>
                            <th>ලබීම් (Receipts)</th>
                            <th>දීම් (Payments)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            @php
                                $receivedAmount = '';
                                $paidAmount = '';
                                if ($loan->description === 'වෙළෙන්දාගේ ලාද පරණ නය') {
                                    $receivedAmount = number_format($loan->amount, 2);
                                } elseif ($loan->description === 'වෙළෙන්දාගේ අද දින නය ගැනීම') {
                                    $paidAmount = number_format($loan->amount, 2);
                                }
                            @endphp
                            <tr>
                                <td>{{ $loan->customer_short_name }}</td>
                                <td>{{ $loan->bill_no }}</td>
                                <td>{{ $loan->created_at->format('Y-m-d') }}</td>
                                <td>{{ $loan->description }}</td>
                                <td>{{ $loan->cheque_no }}</td>
                                <td>{{ $loan->bank }}</td>
                                <td>{{ $receivedAmount }}</td>
                                <td>{{ $paidAmount }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="6" class="text-end">එකතුව:</td>
                            <td>{{ number_format($receivedTotal, 2) }}</td>
                            <td>{{ number_format($paidTotal, 2) }}</td>
                        </tr>
                        <tr class="net-balance-row">
                            <td colspan="7" class="text-end">ශුද්ධ ශේෂය:</td>
                            <td>{{ number_format($netBalance, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>
</body>
</html>