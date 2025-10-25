@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg,#99ff99,#99ff99);
    }

    .report-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 30px;
        margin: 30px auto;
        max-width: 1200px;
    }

    .report-title {
        text-align: center;
        font-size: 26px;
        color: #006400;
        font-weight: bold;
        margin-bottom: 15px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    th, td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: center;
        font-size: 14px;
    }

    th {
        background: #228B22;
        color: white;
    }

    .sub-table {
        margin-top: 10px;
        border: 1px solid #ddd;
    }

    .sub-table th {
        background: #e8f5e9;
        color: #333;
    }

    .print-btn {
        display: block;
        margin: 10px auto 20px auto;
        padding: 10px 20px;
        background-color: #228B22;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
    }

    .print-btn:hover {
        background-color: #1b6e1b;
    }

    /* Print styles */
    @media print {
        body {
            background: white !important;
        }

        /* Hide everything except the report container */
        body * {
            visibility: hidden;
        }

        .report-container, .report-container * {
            visibility: visible;
        }

        .report-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        /* Make all text black and bold */
        .report-container, .report-container table, .report-container th, .report-container td {
            color: black !important;
            font-weight: bold !important;
        }

        /* Hide print button */
        .print-btn {
            display: none;
        }
    }
</style>

<div class="report-container">
    <button class="print-btn" onclick="window.print()">🖨️ Print Report</button>
    <h2 class="report-title">Goods Received Note (GRN) Report</h2>

    <table>
        <thead>
            <tr>
                <th>GRN No</th>
                <th>Code</th>
                <th>Supplier Code</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Packs</th>
                <th>Weight</th>
                <th>Txn Date</th>
                <th>Original Packs</th>
                <th>Original Weight</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grnEntries as $entry)
                <tr>
                    <td>{{ $entry->grn_no }}</td>
                    <td>{{ $entry->code }}</td>
                    <td>{{ $entry->supplier_code }}</td>
                    <td>{{ $entry->item_code }}</td>
                    <td>{{ $entry->item_name }}</td>
                    <td>{{ $entry->packs }}</td>
                    <td>{{ $entry->weight }}</td>
                    <td>{{ \Carbon\Carbon::parse($entry->txn_date)->format('Y-m-d') }}</td>
                    <td>{{ $entry->original_packs }}</td>
                    <td>{{ $entry->original_weight }}</td>
                </tr>

                {{-- Related records from GrnEntry2 --}}
                @if (isset($grnEntry2Data[$entry->code]))
                    <tr>
                        <td colspan="10">
                            <table class="sub-table">
                                <thead>
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Packs</th>
                                        <th>Weight</th>
                                        <th>Per KG Price</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($grnEntry2Data[$entry->code] as $sub)
                                        <tr>
                                            <td>{{ $sub->item_code }}</td>
                                            <td>{{ $sub->item_name }}</td>
                                            <td>{{ $sub->packs }}</td>
                                            <td>{{ $sub->weight }}</td>
                                            <td>{{ $sub->per_kg_price }}</td>
                                            <td>{{ $sub->type }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
@endsection
