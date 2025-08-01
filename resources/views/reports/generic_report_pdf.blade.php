{{-- In resources/views/reports/generic_report_pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle ?? 'Report' }}</title>
    <style>
        body { font-family: 'Sinhala', 'Helvetica Neue', 'Helvetica', Arial, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">TGK ට්‍රේඩර්ස්</h2>
    <h4 style="text-align: center;">{{ $reportTitle ?? 'Report' }}</h4>
    <p>Report Date: {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</p>
    
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
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>