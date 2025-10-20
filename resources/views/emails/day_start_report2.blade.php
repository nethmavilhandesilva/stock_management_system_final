<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combined Daily Report</title>
    <style>
        /* Base Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
            margin: 0;
            padding: 0;
            font-size: 14px;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 1100px;
            margin: 20px auto;
            padding: 0 15px;
        }

        /* Section Cards & Headers */
        .report-section {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .report-header {
            background-color: #004d00;
            color: white;
            padding: 20px 25px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .report-header .title {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .report-header h2, .report-header h4 {
            margin: 0;
            line-height: 1.2;
            color: white;
        }
        .report-header h2 { font-size: 22px; }
        .report-header h4 { font-size: 16px; font-weight: normal; }
        .report-header .date-info {
            font-size: 13px;
            color: #ccc;
            text-align: right;
        }
        .print-btn {
            background-color: #003300;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .print-btn:hover { background-color: #001a00; }

        /* Tables */
        .table-container { padding: 20px; overflow-x: auto; }
        .report-table, .compact-table, .bill-summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            color: #333;
            margin-top: 15px;
        }
        .report-table th, .report-table td,
        .compact-table th, .compact-table td,
        .bill-summary-table th, .bill-summary-table td {
            padding: 10px;
            border: 1px solid #e0e0e0;
            text-align: center;
            white-space: nowrap;
        }
        .report-table thead th, .report-table tfoot td {
            background-color: #003300;
            color: white;
        }
        .report-table tbody tr:nth-of-type(odd) { background-color: #f9f9f9; }
        .report-table tbody tr:hover { background-color: #e6f7ff; }

        .item-summary-row td { font-weight: bold; background-color: #e0e0e0 !important; }
        .total-row td { font-weight: bold; background-color: #008000 !important; color: white !important; }
        .total-row td:first-child { text-align: right; }

        .bill-details { padding: 20px; }
        .bill-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #004d00;
            margin-bottom: 15px;
        }
        .bill-header h5 {
            margin: 0;
            color: #004d00;
            font-size: 16px;
        }
        .bill-header .info {
            font-size: 12px;
            color: #666;
            text-align: right;
        }
        .bill-total-row th {
            text-align: right !important;
            font-weight: bold;
        }
        .bill-total-row th:last-child {
            background-color: #e0e0e0;
            color: #333;
        }
        .grand-total {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            padding: 15px;
            border-top: 3px solid #008000;
            color: #004d00;
            background-color: #f0f0f0;
        }

        /* Sales Adjustments Table */
        .sales-adjustments-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .sales-adjustments-table th, .sales-adjustments-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .sales-adjustments-table thead th {
            background-color: #333;
            color: white;
        }
        .table-success { background-color: #d4edda; } /* Original */
        .table-warning { background-color: #fff3cd; } /* Updated */
        .table-danger { background-color: #f8d7da; } /* Deleted */
        .changed { background-color: #ffc107; font-weight: bold; color: #333; }

        /* Financial Report */
        .financial-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .financial-table th, .financial-table td {
            border: 1px solid #e0e0e0;
            padding: 10px;
        }
        .financial-table thead th {
            background-color: #004d00;
            color: white;
            font-weight: bold;
        }
        .financial-total-row { background-color: #f0f0f0; font-weight: bold; }
        .financial-net-balance-row, .financial-profit-row, .financial-damages-row {
            background-color: #eaf8ff;
            font-weight: bold;
        }
        .financial-net-balance-row td:last-child,
        .financial-profit-row td:last-child,
        .financial-damages-row td:last-child {
            font-size: 1.1em;
        }

        /* Loan Report */
        .loan-table th, .loan-table td {
            padding: 8px;
            text-align: left;
        }
        .loan-table th:last-child, .loan-table td:last-child { text-align: right; }
        .loan-totals-row {
            background-color: #dff0d8;
            font-weight: bold;
            color: black;
        }
        .loan-net-balance-row {
            background-color: #004d00;
            color: white;
            font-weight: bold;
        }

        /* Utility Classes */
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-muted { color: #6c757d; }
        .p-4 { padding: 1.5rem !important; }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .alert-info { background-color: #e2f4ff; color: #004d00; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }
        .mb-4 { margin-bottom: 25px; }
        .mt-4 { margin-top: 25px; }

        /* Print Styles */
        @media print {
            body { background-color: #fff !important; }
            .report-section { box-shadow: none !important; border: 1px solid #eee; }
            .report-header { background-color: #eee !important; color: #000 !important; }
            .report-header h2, .report-header h4, .date-info { color: #000 !important; }
            .print-btn { display: none !important; }
            .report-table th, .report-table td,
            .compact-table th, .compact-table td,
            .bill-summary-table th, .bill-summary-table td,
            .sales-adjustments-table th, .sales-adjustments-table td,
            .financial-table th, .financial-table td,
            .loan-table th, .loan-table td { border-color: #ccc; }
            .report-table thead th, .report-table tfoot td { background-color: #ddd !important; color: #000 !important; }
            .total-row td { background-color: #e0e0e0 !important; color: #333 !important; }
            .changed { background-color: #ffc107 !important; color: #333 !important; }
        }
    </style>
  
</head>
<body>

    <div class="container">
        {{-- Section 5 - Customer-wise Sales Summary --}}
<div class="report-section">
    <div class="report-header">
        <div class="title">
            <h2 class="company-name">Sales Report</h2>
            <h4>Customer-wise Bill Summary</h4>
        </div>
        <div class="date-info">
            <span>{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span>
        </div>
    </div>

    <div class="bill-details">
        @if ($salesByBill->isEmpty())
            <div class="alert alert-info">No sales records found.</div>
        @else
            @php $grandTotal = 0; @endphp

            @foreach ($salesByBill as $customerCode => $sales)
                @php
                    $billNo = $sales->first()->bill_no ?? null;
                    $firstPrinted = $sales->first()->FirstTimeBillPrintedOn ?? null;
                    $reprinted = $sales->first()->BillReprintedOn ?? null;
                    $billTotal = 0;
                @endphp

                {{-- Header for each customer --}}
                <div class="bill-header mt-4">
                    <h5>Customer Code: {{ $customerCode }}</h5>
                    @if($billNo)
                        <p><strong>Bill No:</strong> {{ $billNo }}</p>
                    @endif
                    <div class="info">
                        @if($firstPrinted)
                            <span>First Printed: {{ \Carbon\Carbon::parse($firstPrinted)->format('Y-m-d H:i') }}</span>
                        @endif
                        @if($reprinted)
                            <span>Reprinted: {{ \Carbon\Carbon::parse($reprinted)->format('Y-m-d H:i') }}</span>
                        @endif
                    </div>
                </div>

                {{-- Table for each customer's sales --}}
                <table class="report-table bill-summary-table mb-4">
                    <thead>
                        <tr>
                            <th>කේතය</th>
                            <th>පාරිභෝගික කේතය</th>
                            <th>සැපයුම්කරු කේතය</th>
                            <th>භාණ්ඩ නාමය</th>
                            <th>බර</th>
                            <th>කිලෝවකට මිල</th>
                            <th>එකතුව</th>
                            <th>පැකේජ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            @php $billTotal += $sale->total; @endphp
                            <tr>
                                <td>{{ $sale->code }}</td>
                                <td>{{ $sale->customer_code }}</td>
                                <td>{{ $sale->supplier_code }}</td>
                                <td>{{ $sale->item_name }}</td>
                                <td>{{ number_format($sale->weight, 2) }}</td>
                                <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                                <td>{{ number_format($sale->total, 2) }}</td>
                                <td>{{ $sale->packs }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bill-total-row">
                            <th colspan="6">Customer Total:</th>
                            <th>{{ number_format($billTotal, 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

                @php $grandTotal += $billTotal; @endphp
            @endforeach

            <div class="grand-total mt-4">
                <h3>Grand Total: Rs. {{ number_format($grandTotal, 2) }}</h3>
            </div>
        @endif
    </div>
</div>

        {{-- Section 7 - Financial Report --}}
        <div class="report-section">
            <div class="report-header">
                <div class="title">
                    <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
                    <h4>මුදල් වාර්තාව</h4>
                </div>
                <div class="date-info">
                    <span>{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span>
                   
                </div>
            </div>
            <div class="table-container">
                <div class="alert alert-info fw-bold">
                    Sales Total: {{ number_format($financialTotalCr, 2) }}
                </div>
                <table class="financial-table">
                    <thead>
                        <tr>
                            <th>විස්තරය</th>
                            <th>ලැබීම්</th>
                            <th>ගෙවීම</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($financialReportData as $row)
                            <tr>
                                <td>{{ $row['description'] }}</td>
                               <td>{{ $row['dr'] ? number_format(abs($row['dr']), 2) : '' }}</td>

                               <td>{{ $row['cr'] ? number_format(abs($row['cr']), 2) : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="financial-total-row">
                            <td>Total</td>
                           <td>{{ number_format(abs($financialTotalDr), 2) }}</td>

                         <td>{{ number_format(abs($financialTotalCr), 2) }}</td>

                        </tr>
                        <tr class="financial-net-balance-row">
                            <td>ඇතැති මුදල්</td>
                            <td colspan="2" class="text-center">
                                @php $diff = $financialTotalCr + $financialTotalDr; @endphp
                                @if($diff < 0)
                                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                                @else
                                    <span class="text-success">{{ number_format($diff, 2) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="financial-profit-row">
                            <td>💰 Profit</td>
                           <td colspan="2" class="text-success text-center">{{ number_format(0, 2) }}</td>
                        </tr>
                        <tr class="financial-damages-row">
                            <td>Total Damages</td>
                            <td colspan="2" class="text-danger text-center">{{ number_format($totalDamages, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        {{-- ================= SECTION 7 - Loan Report ================= --}}
<div class="custom-card">
    <div class="report-title-bar">
        <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
        <h4 class="fw-bold text-white">ණය වාර්තාව</h4>
        @php
            $settingDate = \App\Models\Setting::value('value');
        @endphp
        <span class="right-info">
            {{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}
        </span>
        
    </div>

    <div class="card-body p-3">
        @if ($finalLoans->isEmpty())
            <div class="alert alert-info text-center">No loan records found.</div>
        @else
            <table class="table table-bordered table-striped table-hover table-sm mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>පාරිභෝගික නම</th>
                        <th class="text-end">මුදල (Rs)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($finalLoans as $loan)
                        <tr style="background-color: {{ $loop->even ? '#f8f9fa' : '#ffffff' }};">
                            <td class="{{ $loan->highlight_color ?? '' }}" style="font-weight: 500;">
                                {{ $loan->customer_short_name }}
                            </td>
                            <td class="text-end fw-bold">
                                {{ number_format($loan->total_amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <th class="text-end">Grand Total:</th>
                        <th class="text-end">
                            {{ number_format($finalLoans->sum('total_amount'), 2) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
</div>

<style>
    .custom-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        background-color: #ffffff;
        overflow: hidden;
        margin: 20px auto;
        max-width: 800px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .report-title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: linear-gradient(90deg, #4b79a1, #283e51);
        color: #fff;
        border-bottom: 2px solid #ccc;
    }

    .company-name {
        font-size: 1.8rem;
        font-weight: bold;
        margin: 0;
    }

    .fw-bold {
        margin: 0;
        font-size: 1.1rem;
    }

    .right-info {
        font-size: 0.95rem;
        background-color: rgba(255,255,255,0.2);
        padding: 3px 8px;
        border-radius: 5px;
    }

    .print-btn {
        background-color: #ff9800;
        color: #fff;
        border: none;
        padding: 6px 12px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: background 0.3s;
    }

    .print-btn:hover {
        background-color: #e68900;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    thead th {
        font-size: 1rem;
        text-align: left;
        padding: 10px;
    }

    tbody td {
        padding: 10px;
        font-size: 0.95rem;
    }

    tfoot th, tfoot td {
        font-size: 1rem;
        font-weight: 600;
        padding: 10px;
    }

   
    
</style>




      
</body>
</html>

   