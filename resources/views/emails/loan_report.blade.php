<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ණය වාර්තාව</title>
</head>
<body style="background-color: #f4f4f4; margin: 0; padding: 20px; font-family: Arial, sans-serif;">

    <div style="max-width: 800px; margin: 0 auto; padding: 20px; background-color: #006400; color: white; border-radius: 8px;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
            <h2 style="font-weight: 700; font-size: 1.5rem; color: white; margin: 0;">TGK ට්‍රේඩර්ස්</h2>
            <h4 style="margin: 0; color: white; font-weight: 700; white-space: nowrap;">ණය වාර්තාව</h4>
               @php
    $settingDate = \App\Models\Setting::value('value');
@endphp

<span class="right-info">
    {{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}
</span>
        </div>

        @if ($loans->isEmpty())
            <div style="background-color: #a4e4a4; color: #333; padding: 15px; border-radius: 5px; text-align: center;">
                අද දින ණය වාර්තා නොමැත.
            </div>
        @else
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px; color: white;">
                <thead>
                    <tr style="background-color: #004d00; color: white;">
                        <th style="padding: 10px; text-align: left; border: 1px solid #003300;">පාරිභෝගික නම</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #003300;">බිල් අංකය</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #003300;">දිනය</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #003300;">විස්තරය</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #003300;">චෙක්පත්</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #003300;">බැංකුව</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #003300;">ලබීම්</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #003300;">දීම්</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $receivedTotal = 0;
                        $paidTotal = 0;
                    @endphp
                    @foreach ($loans as $loan)
                        @php
                            $receivedAmount = '';
                            $paidAmount = '';
                            if ($loan->description === 'වෙළෙන්දාගේ ලාද පරණ නය') {
                                $receivedTotal += $loan->amount;
                                $receivedAmount = number_format($loan->amount, 2);
                            } elseif ($loan->description === 'වෙළෙන්දාගේ අද දින නය ගැනීම') {
                                $paidTotal += $loan->amount;
                                $paidAmount = number_format($loan->amount, 2);
                            }
                        @endphp
                        <tr style="background-color: {{ $loop->odd ? '#00550088' : 'transparent' }};">
                            <td style="padding: 10px; border: 1px solid #003300;">{{ $loan->customer_short_name }}</td>
                            <td style="padding: 10px; border: 1px solid #003300;">{{ $loan->bill_no }}</td>
                            <td style="padding: 10px; border: 1px solid #003300;">{{ $loan->created_at->format('Y-m-d') }}</td>
                            <td style="padding: 10px; border: 1px solid #003300;">{{ $loan->description }}</td>
                            <td style="padding: 10px; border: 1px solid #003300;">{{ $loan->cheque_no }}</td>
                            <td style="padding: 10px; border: 1px solid #003300;">{{ $loan->bank }}</td>
                            <td style="padding: 10px; border: 1px solid #003300;">{{ $receivedAmount }}</td>
                            <td style="padding: 10px; border: 1px solid #003300;">{{ $paidAmount }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background-color: #004d00; color: white;">
                        <td colspan="6" style="text-align: right; padding: 10px; border: 1px solid #003300;">එකතුව:</td>
                        <td style="padding: 10px; border: 1px solid #003300;">{{ number_format($receivedTotal, 2) }}</td>
                        <td style="padding: 10px; border: 1px solid #003300;">{{ number_format($paidTotal, 2) }}</td>
                    </tr>
                    <tr style="font-weight: bold; background-color: #004d00; color: white;">
                        @php
                            $netBalance = $paidTotal - $receivedTotal;
                        @endphp
                        <td colspan="7" style="text-align: right; padding: 10px; border: 1px solid #003300;">ශුද්ධ ශේෂය:</td>
                        <td style="padding: 10px; border: 1px solid #003300;">{{ number_format($netBalance, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

</body>
</html>