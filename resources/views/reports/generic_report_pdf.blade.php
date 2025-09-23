<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle ?? 'Report' }}</title>
    <style>
        body, table, th, td, h2, h4, p {
            font-family: 'notosanssinhala', sans-serif;
            font-size: 10px;
            line-height: 1.3;
        }
        h2, h4 { text-align: center; margin: 5px 0; }
        p.report-date { text-align: right; margin: 5px 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; vertical-align: middle; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        tbody tr:nth-child(even) { background-color: #f9f9f9; }
        /* Totals row styling */
        tbody tr.total-row { 
            font-weight: bold; 
            background-color: #f2f2f2; 
        }
        tbody tr.total-row td {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
    </style>
</head>
<body>

    <h2>TGK ට්‍රේඩර්ස්</h2>
    <h4>{{ $reportTitle ?? 'Report' }}</h4>

    @php
        $settingDate = \App\Models\Setting::value('value');
    @endphp

    <p class="report-date">
        Report Date:
        {{ $settingDate ? \Carbon\Carbon::parse($settingDate)->format('Y-m-d H:i') : \Carbon\Carbon::now()->format('Y-m-d H:i') }}
    </p>

    {{-- Extra metadata if available --}}
   @if(!empty($meta))
    @foreach($meta as $label => $value)
        @if($value)
            <p><strong>{{ $label }}:</strong> {{ $value }}</p>
        @endif
    @endforeach
@endif

    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
                <tr @if(isset($row[0]) && $row[0] === 'TOTAL') class="total-row" @endif>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>


