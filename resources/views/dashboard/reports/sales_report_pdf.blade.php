<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <style>
        body { font-family: 'notosanssinhala', sans-serif; }
        h1, h5 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .grand-total { text-align: right; margin-top: 20px; font-size: 12px; font-weight: bold; }
        .summary-info { margin-bottom: 15px; text-align: left; }
        .customer-section { margin-bottom: 30px; page-break-inside: avoid; }
        .customer-header { background-color: #e0e0e0; padding: 8px; margin-bottom: 10px; border-left: 4px solid #333; }
        .customer-total { text-align: right; margin-top: 10px; font-weight: bold; background-color: #f5f5f5; padding: 5px; }
        .bill-info { display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <h1>Sales Report</h1>
    
    <!-- Summary Information -->
    <div class="summary-info">
        @if(request()->filled('start_date') && request()->filled('end_date'))
            <p><strong>Period:</strong> {{ request()->start_date }} to {{ request()->end_date }}</p>
        @elseif(request()->filled('start_date'))
            <p><strong>From:</strong> {{ request()->start_date }}</p>
        @elseif(request()->filled('end_date'))
            <p><strong>Until:</strong> {{ request()->end_date }}</p>
        @endif
        
        @if(request()->filled('customer_code'))
            <p><strong>Customer Code:</strong> {{ request()->customer_code }}</p>
        @endif
        
        @if(request()->filled('supplier_code'))
            <p><strong>Supplier Code:</strong> {{ request()->supplier_code }}</p>
        @endif
        
        @if(request()->filled('item_code'))
            <p><strong>Item Code:</strong> {{ request()->item_code }}</p>
        @endif
        
        @if(request()->filled('bill_no'))
            <p><strong>Bill No:</strong> {{ request()->bill_no }}</p>
        @endif
    </div>

    @php 
        $grandTotal = 0;
        // Group sales data by customer code and bill no
        $groupedData = $salesData->groupBy(function($item) {
            return $item->customer_code . '|' . ($item->bill_no ?: 'NO_BILL');
        });
    @endphp
    
    @if($salesData->isNotEmpty())
        @foreach($groupedData as $groupKey => $customerSales)
            @php
                list($customerCode, $billNo) = explode('|', $groupKey);
                $customerTotal = $customerSales->sum('total');
                $grandTotal += $customerTotal;
                $hasBill = $billNo !== 'NO_BILL';
            @endphp
            
            <div class="customer-section">
                <!-- Customer Header with Bill No -->
                <div class="customer-header">
                    <div class="bill-info">
                        <h3>Customer: {{ $customerCode }}</h3>
                        @if($hasBill)
                            <h3>Bill No: {{ $billNo }}</h3>
                        @else
                           
                        @endif
                    </div>
                </div>

                <!-- Customer Sales Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Supplier Code</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Weight</th>
                            <th>Price per Kg</th>
                            <th>Total</th>
                            <th>Packs</th>
                            <th>First Printed</th>
                            <th>Reprinted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customerSales as $sale)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($sale->Date)->format('Y-m-d') }}</td>
                                <td>{{ $sale->supplier_code }}</td>
                                <td>{{ $sale->item_code }}</td>
                                <td>{{ $sale->item_name }}</td>
                                <td>{{ $sale->weight }}</td>
                                <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                                <td>{{ number_format($sale->total, 2) }}</td>
                                <td>{{ $sale->packs }}</td>
                                <td>
                                    @if($sale->FirstTimeBillPrintedOn)
                                        {{ \Carbon\Carbon::parse($sale->FirstTimeBillPrintedOn)->format('Y-m-d') }}
                                    @endif
                                </td>
                                <td>
                                    @if($sale->BillReprintAfterchanges)
                                        {{ \Carbon\Carbon::parse($sale->BillReprintAfterchanges)->format('Y-m-d') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Customer Total -->
                <div class="customer-total">
                    @if($hasBill)
                        Bill Total: {{ number_format($customerTotal, 2) }} | 
                    @else
                        Customer Total: {{ number_format($customerTotal, 2) }} | 
                    @endif
                    Records: {{ $customerSales->count() }}
                </div>
            </div>
        @endforeach
        
        <!-- Grand Total -->
        <div class="grand-total">
            Grand Total: {{ number_format($grandTotal, 2) }}<br>
            Total Records: {{ $salesData->count() }}<br>
            Total Groups: {{ $groupedData->count() }}
        </div>
    @else
        <p style="text-align: center; color: #888;">No sales records found.</p>
    @endif
</body>
</html>