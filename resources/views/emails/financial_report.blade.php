<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .report-card {
            background-color: #004d00;
            color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            max-width: 800px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        thead th {
            background-color: #003300;
            color: #fff;
        }
        tfoot td {
            font-weight: bold;
            background-color: #006600;
        }
        .alert-info {
            background-color: #006600;
            color: #ffffff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="report-card">
        <h2 style="margin: 0;">TGK ට්‍රේඩර්ස්</h2>
        <h4 style="margin: 5px 0 20px 0;">දෛනික මූල්‍ය වාර්තාව</h4>

        <div class="alert-info">
            Sales Total: {{ number_format($salesTotal, 2) }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>විස්තරය</th>
                    <th>ලැබීම්</th>
                    <th>ගෙවීම</th>
                </tr>
            </thead>
        <tbody>
    @foreach($reportData as $row)
        @if(is_array($row) && array_key_exists('description', $row))
        <tr>
            <td>{{ $row['description'] }}</td>
            <td>{{ $row['dr'] ? number_format($row['dr'], 2) : '' }}</td>
            <td>{{ $row['cr'] ? number_format($row['cr'], 2) : '' }}</td>
        </tr>
        @endif
    @endforeach
</tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td>{{ number_format($totalDr, 2) }}</td>
                    <td>{{ number_format($totalCr, 2) }}</td>
                </tr>
                <tr>
                    <td>ඇතැති මුදල්</td>
                    <td colspan="2">
                        @php
                            $diff = $totalDr - $totalCr;
                        @endphp
                        {{ number_format($diff, 2) }}
                    </td>
                </tr>
                <tr>
                    <td>💰 Profit</td>
                    <td colspan="2">{{ number_format($profitTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Damages</td>
                    <td colspan="2">{{ number_format($totalDamages, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>