<!DOCTYPE html>
<html>
<head>
    <title>{{ $reportTitle ?? 'Sales Adjustment Report' }}</title>
    <style>
        body { font-family: 'notosanssinhala', sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 14px; }
        thead th { background-color: #f2f2f2; text-align: center; }
        .original { background-color: #d4edda; }
        .updated { background-color: #fff3cd; }
        .deleted { background-color: #f8d7da; }
    </style>
</head>
<body>
    @php
    $companyName = \App\Models\Setting::value('CompanyName');
    @endphp

    <h1 class="company-name">{{ $companyName ?? 'Default Company' }}</h1>
    <h2 style="text-align: center;">{{ $reportTitle ?? 'Sales Adjustment Report' }}</h2>

    <table>
        <thead>
            <tr>
                <th>විකුණුම්කරු</th>
                <th>වර්ගය</th>
                <th>බර</th>
                <th>මිල</th>
                <th>මලු</th>
                <th>මුළු මුදල</th>
                <th>බිල්පත් අංකය</th>
                <th>පාරිභෝගික කේතය</th>
                <th>වර්ගය (type)</th>
                <th>දිනය සහ වේලාව</th>
            </tr>
        </thead>
        <tbody>
            {{-- Loop directly through the flat 'entries' collection --}}
            @forelse ($entries as $entry)
                <tr class="@if($entry->type == 'original') original
                           @elseif($entry->type == 'updated') updated
                           @elseif($entry->type == 'deleted') deleted
                           @endif">
                    <td>{{ $entry->code }}</td>
                    <td>{{ $entry->item_name }}</td>
                    <td>{{ $entry->weight }}</td>
                    <td>{{ number_format($entry->price_per_kg, 2) }}</td>
                    <td>{{ $entry->packs }}</td>
                    <td>{{ number_format($entry->total, 2) }}</td>
                    <td>{{ $entry->bill_no }}</td>
                    <td>{{ strtoupper($entry->customer_code) }}</td>
                    <td>{{ $entry->type }}</td>
                    <td>
                        {{-- The date logic from your original HTML table --}}
                        @if($entry->type == 'original')
                            {{ \Carbon\Carbon::parse($entry->original_created_at)
                                ->timezone('Asia/Colombo')
                                ->format('Y-m-d H:i:s') }}
                        @else
                            {{ $entry->Date ?? \Carbon\Carbon::parse($entry->created_at)->timezone('Asia/Colombo')->format('Y-m-d') }}
                            {{ \Carbon\Carbon::parse($entry->created_at)->setTimezone('Asia/Colombo')->format('H:i:s') }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">සටහන් කිසිවක් සොයාගෙන නොමැත</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
