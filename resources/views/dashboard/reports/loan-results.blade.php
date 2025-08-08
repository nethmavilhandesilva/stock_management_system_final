@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99;
    }
    .custom-card {
        background-color: #006400 !important;
        color: white; /* for text readability */
    }
    .custom-card table {
        background-color: #006400 !important; /* make table background dark green */
        color: white; /* white text inside table */
    }
    .custom-card table thead, 
    .custom-card table tfoot {
        background-color: #004d00 !important;
        color: white;
    }
    /* Optional: style table rows for better contrast */
    .custom-card table tbody tr:nth-child(odd) {
        background-color: #00550088; /* slightly lighter translucent green */
    }
    .custom-card table tbody tr:nth-child(even) {
        background-color: transparent;
    }

    /* Title bar - flex container for inline layout */
    .report-title-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .company-name {
        font-weight: 700;
        font-size: 1.5rem;
        color: white;
        margin: 0;
    }

    .report-title-bar h4 {
        margin: 0;
        color: white;
        font-weight: 700;
        white-space: nowrap;
    }

    .right-info {
        color: white;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Print button style */
    .print-btn {
        background-color: #004d00;
        color: white;
        border: none;
        padding: 0.4rem 1rem;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        white-space: nowrap;
        transition: background-color 0.3s ease;
    }
    .print-btn:hover {
        background-color: #003300;
    }
</style>
<div class="container mt-4" style="background-color: #99ff99; min-height: 100vh; padding: 20px;">

    
    <div class="card custom-card shadow border-0 rounded-3 p-4">
        <div class="report-title-bar">
        <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
        <h4 class="fw-bold text-white">📦 අයිතමය අනුව වාර්තාව</h4>
        <span class="right-info">{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span>
        <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
    </div>

        <div class="card-body p-0">
            @if ($errors->any())
                <div class="alert alert-danger m-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($loans->isEmpty())
                <div class="alert alert-info m-3">
                    No loan records found for the selected filters.
                </div>
            @else
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>බිල් අංකය</th>
                            <th>දිනය</th>
                            <th>විස්තරය</th>
                            <th>එකතුව</th>
                            <th>චෙක්පත්</th>
                            <th>බැංකුව</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalAmount = 0; @endphp
                        @foreach ($loans as $loan)
                            @php $totalAmount += $loan->amount; @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $loan->bill_no }}</td>
                                <td>{{ $loan->created_at->format('Y-m-d') }}</td>
                                <td>{{ $loan->description }}</td>
                                <td>{{ number_format($loan->amount, 2) }}</td>
                                <td>{{ $loan->cheque_no }}</td>
                                <td>{{ $loan->bank }}</td>
                            </tr>
                        @endforeach
                        <tr style="font-weight: bold; background-color: #dff0d8; color: black;">
                            <td colspan="4" class="text-end">එකතුව:</td>
                            <td>{{ number_format($totalAmount, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection
