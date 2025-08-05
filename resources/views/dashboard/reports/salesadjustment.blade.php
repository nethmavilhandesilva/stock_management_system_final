@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #99ff99 !important;
        }

        .report-title-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            padding: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
        }

        .print-btn {
            background-color: white;
            color: #004d00;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }

        .print-btn:hover {
            background-color: #e6e6e6;
        }

        .card-header {
            background-color: #004d00 !important;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        h4.fw-bold {
            margin: 0;
        }

        table th, table td {
            text-align: center;
            vertical-align: middle;
        }
    </style>

    <div class="container mt-4">
        <div class="card-header text-center">
            <div class="report-title-bar">
                <div>
                    <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
                    <h4 class="fw-bold text-white">📦 වෙනස් කිරීම</h4>
                </div>
                <div>
                    <span class="right-info">{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span><br>
                    <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover table-sm align-middle text-center" style="font-size: 14px;">
    <thead class="table-dark">
        <tr>
            <th>විකුණුම්කරු</th>
            <th>මලු</th>
            <th>වර්ගය</th>
            <th>බර</th>
            <th>මිල</th>
            <th>මුළු මුදල</th>
            <th>බිල්පත් අංකය</th>
            <th>පාරිභෝගික කේතය</th>
            <th>වර්ගය (type)</th>
            <th>දිනය සහ වේලාව</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($entries as $entry)
            <tr>
                <td>{{ $entry->code }}</td>
                <td>{{ $entry->packs }}</td>
                <td>{{ $entry->item_name }}</td>
                <td>{{ $entry->weight }}</td>
                <td>{{ number_format($entry->price_per_kg, 2) }}</td>
                <td>{{ number_format($entry->total, 2) }}</td>
                <td>{{ $entry->bill_no }}</td>
                <td>{{ $entry->customer_code }}</td>
                <td>{{ $entry->type }}</td>
                <td>{{ $entry->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">සටහන් කිසිවක් සොයාගෙන නොමැත</td>
            </tr>
        @endforelse
    </tbody>
</table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $entries->links() }}
        </div>
    </div>
@endsection
