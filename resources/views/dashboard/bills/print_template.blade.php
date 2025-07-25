<!DOCTYPE html>
<html>
<head>
    <title>Bill #{{ $bill->bill_number }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 12px; margin: 0; padding: 0; }
        /* Adjusted for a common thermal printer width (e.g., 80mm paper) */
        .container { width: 78mm; /* Approximately 300px at 96dpi */ margin: 0 auto; padding: 5mm; }
        .header, .footer { text-align: center; margin-bottom: 5mm; }
        .header h3 { margin: 0; font-size: 1.2em; }
        .header p { margin: 1mm 0; font-size: 0.9em; }
        .details, .summary { margin-bottom: 5mm; }
        .details p, .summary p { margin: 1mm 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 5mm; }
        th, td { padding: 2mm 0; text-align: left; }
        th { border-bottom: 1px dashed #000; }
        td { border-bottom: none; } /* Remove row borders */
        .text-right { text-align: right; }
        .no-border th, .no-border td { border: none; padding: 1mm 0; } /* For summary table */
        .divider { border-bottom: 1px dashed #000; margin: 3mm 0; }
        .disclaimer { font-size: 0.8em; text-align: center; line-height: 1.2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h3>C11 TGK ට්‍රේඩර්ස්</h3> [cite: 1]
            <p>අල, ඹී ළූනු, කුළුබඩු තොග ගෙන්වන්නෝ / බෙදාහරින්නෝ</p> [cite: 2]
            <p>වි.ආ.ම. වේයන්ගොඩ</p> [cite: 3]
            <p>: 0767485961</p> [cite: 6]
        </div>

        <div class="details">
            <p>දිනය : {{ \Carbon\Carbon::parse($bill->date)->format('n/j/Y') }}</p> [cite: 4]
            <p>වේලාව : {{ \Carbon\Carbon::parse($bill->date)->format('g:i:s A') }}</p> [cite: 5]
            <p>බිල් අංකය : {{ $bill->bill_number }}</p> [cite: 7]
            <p>{{ $bill->customer_name }}</p> [cite: 8]
        </div>

        <table>
            <thead>
                <tr>
                    <th>වර්ගය</th> [cite: 9]
                    <th class="text-right">කිලො</th> [cite: 9]
                    <th class="text-right">Be</th> {{-- This might be 'මිල' (Price) based on the image --}} [cite: 9]
                    <th class="text-right">අගය</th> [cite: 9]
                </tr>
            </thead>
            <tbody>
                @foreach ($bill->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td class="text-right">{{ number_format($item->kilograms, 2) }}</td>
                    <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                    <td class="text-right">{{ number_format($item->value, 2) }}</td>
                </tr>
                {{-- Optional: Additional details like (Onions 789/1) --}}
                @if(isset($item->additional_note))
                <tr>
                    <td colspan="4">{{ $item->additional_note }}</td> [cite: 10]
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        <div class="divider"></div>

        <div class="summary">
            <table class="no-border">
                <tr>
                    <td>ප්‍රවාහන ගාස්තු :</td> [cite: 9]
                    <td class="text-right">{{ number_format($bill->transport_cost, 2) }}</td>
                </tr>
                <tr>
                    <td>කුලිය :</td> [cite: 9]
                    <td class="text-right">{{ number_format($bill->labor_cost, 2) }}</td>
                </tr>
                <tr>
                    <td>අගය :</td> [cite: 9]
                    <td class="text-right">{{ number_format($bill->total_value, 2) }}</td>
                </tr>
            </table>
        </div>
        <div class="divider"></div>

        <div class="footer disclaimer">
            <p>භාණ්ඩ පරීක්ෂා කර බලා රැගෙන යන්න</p> [cite: 11]
            <p>නැවත භාර ගනු නොලැබේ</p> [cite: 12]
        </div>
    </div>

    <script>
        // Automatic print when the page loads
        window.onload = function() {
            window.print();
            // Optional: Close window after printing. This might not work in all browsers/setups.
            // window.onafterprint = function() { window.close(); }
        }
    </script>
</body>
</html>