<div style="font-family: sans-serif; padding: 20px; background-color: #f9f9f9;">
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="font-weight: bold; color: #004d00;">TGK ට්‍රේඩර්ස්</h2>
        <h4 style="color: #004d00;">📦 වෙනස් කිරීම</h4>
        <span style="font-size: 14px; color: #555;">
            {{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}
        </span>
    </div>

    <table class="table table-bordered table-striped table-hover table-sm align-middle text-center" style="font-size: 14px;">
        <thead class="table-dark">
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
    @forelse ($entries as $entry)
        <tr class="@if($entry->type == 'original') table-success 
                   @elseif($entry->type == 'updated') table-warning 
                   @elseif($entry->type == 'deleted') table-danger 
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
                @if($entry->type == 'original')
                    {{ \Carbon\Carbon::parse($entry->original_created_at)->format('Y-m-d') }}
                @else
                    {{ $entry->Date }}
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
</div>
