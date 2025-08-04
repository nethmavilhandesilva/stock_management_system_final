@extends('layouts.app')

@section('content')
<style>
    /* Styling for the overall page background */
    body {
        background-color: #99ff99; /* Page background color (as requested, not changed) */
    }

    /* Styling for the overall container to center content if needed */
    .container.mt-4 {
        /* No specific changes to layout here, but ensures Bootstrap container styling */
    }

    /* Styling for the main content card */
    .card.shadow.p-4 {
        background-color: #006400 !important; /* Card background color (as requested, not changed) */
        padding: 1.5rem; /* Slightly increased padding for a more professional feel */
        color: #333; /* Default text color for the card and its children (dark gray/black) */
        border-radius: 0.5rem; /* Slightly more rounded corners for professionalism */
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Enhanced shadow for depth */
        margin-top: 1.5rem; /* Add some margin from the top page elements */
    }

    /* Styling for the header bar above the card (containing page no and print button) */
    .page-utility-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem; /* Space between utility bar and card */
        color: #333; /* Dark text for page number */
        font-size: 0.9rem;
    }

    .print-button {
        background-color: #339933; /* A nice green for the button */
        color: white; /* White text on button */
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.3rem;
        cursor: pointer;
        font-size: 0.9rem;
        transition: background-color 0.3s ease;
    }
    .print-button:hover {
        background-color: #2b802b; /* Darker green on hover */
    }

    /* Styling for the main report title area inside the card */
    .report-title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem; /* Space below this bar */
        border-bottom: 1px solid rgba(0, 0, 0, 0.1); /* Subtle separator line */
        padding-bottom: 0.75rem; /* Padding below separator */
    }

    /* Styles for 'TGK ට්‍රේඩර්ස්' heading */
    .report-title-bar h2.company-name {
        font-size: 1.8rem; /* Make it significantly bigger */
        font-weight: bold; /* Make it bold */
        color: #000 !important; /* Force black color */
        margin: 0; /* Remove default margins to align in flexbox */
        white-space: nowrap; /* Prevent wrapping for the company name */
    }

    .report-title-bar .right-info {
        font-size: 0.95rem; /* Font size for Date/Time */
        color: #333; /* Dark text */
        white-space: nowrap; /* Prevent wrapping */
    }

    .report-title-bar h4 {
        font-size: 1.5rem; /* Heading size for 'Supplier-wise Report' */
        color: #333; /* Dark text */
        margin: 0 1rem; /* Add some horizontal margin to separate from other elements */
        text-align: center; /* Center this specific heading */
        flex-grow: 1; /* Allow it to take available space */
    }


    /* --- Summary Line Display (The block for supplier/item/ratio) --- */
    .summary-line-display {
        display: flex;
        align-items: center;
        gap: 15px; /* Adjusted gap between items for better spacing */
        flex-wrap: nowrap; /* Prevents items from wrapping to a new line */
        background-color: #66cc66; /* A lighter green for contrast with black text */
        border: 1px solid #339933; /* Darker border for distinction */
        border-radius: 0.4rem; /* Consistent rounded corners */
        padding: 0.75rem 1.25rem; /* Adjusted padding for a cleaner look */
        margin-top: 1.5rem; /* Increased top margin */
        margin-bottom: 1.5rem; /* Increased bottom margin */
        font-size: 0.9rem; /* Slightly larger overall font size for readability */
        color: #333; /* Text color for the summary line (dark) */
        box-shadow: 0 0.2rem 0.4rem rgba(0, 0, 0, 0.08); /* Subtle shadow for this block */
    }

    /* Target specific elements within the summary block for font size */
    .summary-line-display h5 {
        font-size: 1.1rem; /* Slightly larger for supplier name */
        margin-bottom: 0;
        white-space: nowrap;
        color: #333; /* Ensure text is dark */
    }

    .summary-line-display p {
        font-size: 0.9rem; /* Matches the block's overall font size */
        margin-bottom: 0;
        white-space: nowrap;
        color: #333; /* Ensure text is dark */
    }

    /* Ensure strong tags within p and h5 also respect the smaller font size */
    .summary-line-display h5 strong,
    .summary-line-display p strong {
        font-size: inherit; /* Inherit font size from parent p or h5 */
        color: #000; /* Explicitly black for strong text */
    }

    /* --- Table Styling --- */
    .table {
        font-size: 0.8rem; /* MODIFIED: Smaller font size for table content */
        color: #333; /* Set default table text color to dark */
        border-collapse: collapse; /* Ensure borders are single lines */
        width: 100%; /* Ensure table takes full width */
    }

    .table th,
    .table td {
        padding: 0.5rem; /* MODIFIED: Smaller padding for table cells for a more compact look */
        border: 1px solid #000; /* Explicit black border for clear separation */
        text-align: left; /* Align text to the left */
    }

    .table-secondary {
        background-color: #339933 !important; /* A distinct green for table header */
        color: #333; /* Text color for table header (dark) */
    }

    .table-secondary th {
        font-size: 0.85rem; /* MODIFIED: Slightly smaller font for table headers */
        color: #333; /* Ensure header text is dark */
        font-weight: bold; /* Make headers bold */
    }

    /* Stripe colors for table rows */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #e0ffe0; /* Very light green for odd rows */
    }

    .table-striped tbody tr:nth-of-type(even) {
        background-color: #f0fff0; /* Even lighter green for even rows */
    }

    /* Hover effect for table rows */
    .table-striped tbody tr:hover {
        background-color: #c0ffc0; /* Light green on hover */
        cursor: pointer; /* Indicate interactivity */
    }

    /* Styles for the supplier-wise total row */
    .supplier-total-row {
        background-color: #339933; /* Darker green for supplier total row */
        font-weight: bold;
        color: #000; /* Black text for visibility */
    }
    .supplier-total-row td {
        border-top: 2px solid #000 !important; /* Thicker border on top to separate from details */
    }

    /* Styles for the grand total summary */
    .grand-total-summary {
        background-color: #004d00; /* Even darker green, close to card bg */
        color: white; /* White text for contrast */
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        margin-top: 2rem; /* More space above grand total */
        font-size: 1.1rem;
        font-weight: bold;
        display: flex;
        justify-content: space-around; /* Distribute items evenly */
        align-items: center;
        box-shadow: 0 0.2rem 0.4rem rgba(0, 0, 0, 0.1);
    }
    .grand-total-summary strong {
        color: white; /* Ensure strong text is white */
        margin-left: 0.5rem;
    }

    /* Media query for print styles */
    @media print {
        body {
            background-color: #fff !important; /* White background for print */
        }
        .container.mt-4 {
            margin-top: 0 !important;
        }
        .card.shadow.p-4 {
            box-shadow: none !important; /* Remove shadow in print */
            border: 1px solid #ccc !important; /* Light border in print */
            background-color: #fff !important; /* White background for print */
            color: #000 !important; /* Black text for print */
            padding: 1rem !important; /* Adjust padding for print */
        }
        .card.shadow.p-4 h4 {
            color: #000 !important;
        }
        .summary-line-display,
        .table,
        .table th, .table td,
        .table-secondary {
            background-color: #fff !important; /* White background for all elements in print */
            color: #000 !important; /* Black text for print */
            border-color: #999 !important; /* Lighter borders for print */
        }
        .summary-line-display {
            box-shadow: none !important;
        }
        .summary-line-display h5 strong,
        .summary-line-display p strong {
            color: #000 !important;
        }
        .table-striped tbody tr:nth-of-type(odd),
        .table-striped tbody tr:nth-of-type(even) {
            background-color: #fff !important; /* No stripes in print, just white */
        }
        .table-striped tbody tr:hover {
            background-color: #fff !important; /* No hover effect in print */
        }
        .supplier-total-row, .grand-total-summary {
            background-color: #eee !important; /* Light grey for totals in print */
            color: #000 !important;
            border-color: #999 !important;
            page-break-inside: avoid; /* Prevent breaking totals across pages */
        }
        .supplier-total-row td {
            border-top: 1px solid #999 !important;
        }
        .grand-total-summary {
            box-shadow: none !important;
            border: 1px solid #999 !important;
        }
        .grand-total-summary strong {
            color: #000 !important;
        }
        .page-utility-bar, .print-button {
            display: none !important; /* Hide page number and print button in print output */
        }
        .report-title-bar h2.company-name,
        .report-title-bar .right-info,
        .report-title-bar h4 {
            color: #000 !important; /* Ensure these are black in print */
        }
        /* Page breaks for printing large tables */
        .table {
            page-break-after: auto;
        }
        .table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .table thead {
            display: table-header-group; /* Repeat table headers on new pages */
        }
        .table tfoot {
            display: table-footer-group;
        }
    }
</style>

    <div class="container mt-4">
        {{-- Page Number and Print Button at the top of the page --}}
        <div class="page-utility-bar">
            <span class="page-number">Page 1</span> {{-- Assuming a single page report for now --}}
            <button class="print-button" onclick="window.print()">🖨️ Print Report</button>
        </div>

        <div class="card shadow p-4">
            {{-- Report Title Bar with TGK ට්‍රේඩර්ස් and Date/Time --}}
            <div class="report-title-bar">
                <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
                <h4 class="fw-bold text-dark">🧾 සපයුම්කරුව මත වැඩළදම</h4>
                <span class="right-info">{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span> {{-- Current Date and Time --}}
            </div>

            @php
                // Initialize grand totals before the main loop
                $grandTotalPacks = 0;
                $grandTotalWeight = 0.0;
                $grandTotalAmount = 0.0;

                // Group records by supplier_code
                // Assuming $records still contains Sale data for the main table
                $grouped = $records->groupBy('supplier_code');
            @endphp

            @foreach($grouped as $supplierCode => $supplierRecords)
                @php
                    // Initialize supplier-specific totals for each loop iteration (for the sales table)
                    $supplierTotalPacks = 0;
                    $supplierTotalWeight = 0.0;
                    $supplierTotalAmount = 0.0;

                    $firstRecord = $supplierRecords->first();
                    $itemCode = $firstRecord ? $firstRecord->item_code : null;

                    // Fetch current packs for the ratio from GrnEntry
                    $current_grn_packs = 0;
                    if ($itemCode) {
                        $current_grn_packs = \App\Models\GrnEntry::where('supplier_code', $supplierCode)
                            ->where('item_code', $itemCode)
                            ->sum('packs'); // Sum 'packs' from GrnEntry
                    }

                    // Fetch original_packs for the ratio from GrnEntry
                    $original_packs_grn = 0;
                    if ($itemCode) {
                        $original_packs_grn = \App\Models\GrnEntry::where('supplier_code', $supplierCode)
                            ->where('item_code', $itemCode)
                            ->sum('original_packs');
                    }

                    // Calculate the packs ratio for display in the summary line using GRN packs
                    $packs_ratio_display = $current_grn_packs . ' / ' . $original_packs_grn;

                    // Fetch current weight for the ratio from GrnEntry
                    $current_grn_weight = 0.0;
                    if ($itemCode) {
                        $current_grn_weight = \App\Models\GrnEntry::where('supplier_code', $supplierCode)
                            ->where('item_code', $itemCode)
                            ->sum('weight'); // Sum 'weight' from GrnEntry
                    }

                    // Fetch original_weight for the ratio from GrnEntry
                    $original_weight_grn = 0;
                    if ($itemCode) {
                        $original_weight_grn = \App\Models\GrnEntry::where('supplier_code', $supplierCode)
                            ->where('item_code', $itemCode)
                            ->sum('original_weight');
                    }

                    // Calculate the weight ratio for display in the summary line using GRN weight
                    $weight_ratio_display = number_format($current_grn_weight, 2) . ' / ' . number_format($original_weight_grn, 2);

                @endphp

                <div class="bg-light border rounded-3 p-3 my-4 summary-line-display">
                    <h5>සැපයුම්කරු: <strong>{{ $firstRecord->code ?? 'N/A' }}</strong></h5>
                    <p>අයිතම කේතය: <strong>{{ $itemCode ?? 'N/A' }}</strong></p>
                    {{-- Display the ratios based on GRN data --}}
                    <p>ඉතිරිය: <strong>{{ $packs_ratio_display }}</strong></p>
                    <p>මිලදීගැනීම: <strong>{{ $weight_ratio_display }}</strong></p>
                </div>

                <table class="table table-bordered table-striped">
    <thead class="table-secondary">
        <tr>
            <th>අංකය</th>
            <th>මලු</th>
            <th>බර</th>
            <th>මිල</th>
            <th>එකතුව</th>
            <th>ගෙණුම්කරු</th> 
            <th>දිනය</th>
            <th>වැඩකලා</th>
        </tr>
    </thead>
    <tbody>
        @php
            // Initialize totals before loop
            $supplierTotalPacks = 0;
            $supplierTotalWeight = 0;
            $supplierTotalAmount = 0;
        @endphp

        @foreach($supplierRecords as $row)
            <tr>
                <td>{{ $row->bill_no }}</td>
                <td>{{ $row->packs }}</td>
                <td>{{ $row->weight }}</td>
                <td>{{ $row->price_per_kg }}</td>
                <td>{{ $row->total }}</td>
                <td>{{ $row->customer_code }}</td>
                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i') }}</td>
                <td>{{ $shop_no ?? 'N/A' }}</td>
            </tr>
            @php
                $supplierTotalPacks += $row->packs;
                $supplierTotalWeight += $row->weight;
                $supplierTotalAmount += $row->total;
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr class="supplier-total-row">
            <td colspan="1" class="text-end"><strong>Supplier Totals:</strong></td>
            <td><strong>{{ $supplierTotalPacks }}</strong></td>
            <td><strong>{{ number_format($supplierTotalWeight, 2) }}</strong></td>
            <td></td> {{-- Price per Kg - no total --}}
            <td><strong>{{ number_format($supplierTotalAmount, 2) }}</strong></td>
            <td colspan="3"></td> {{-- Remaining columns --}}
        </tr>
    </tfoot>
</table>

                @php
                    // Add supplier totals to grand totals after each supplier's table
                    $grandTotalPacks += $supplierTotalPacks;
                    $grandTotalWeight += $supplierTotalWeight;
                    $grandTotalAmount += $supplierTotalAmount;
                @endphp
            @endforeach

            {{-- Grand Total Summary at the very end of the report --}}
            <div class="grand-total-summary">
                <span>Total Packs: <strong>{{ $grandTotalPacks }}</strong></span>
                <span>Total Weight (kg): <strong>{{ number_format($grandTotalWeight, 2) }}</strong></span>
                <span>Total Amount: <strong>{{ number_format($grandTotalAmount, 2) }}</strong></span>
            </div>
        </div>
    </div>
   <div class="page-utility-bar">
    <span class="page-number">Page 1</span>
    <div>
        <form action="{{ route('report.download', ['reportType' => 'supplier-sales', 'format' => 'excel']) }}" method="POST" class="d-inline">
    @csrf
    {{-- Add the hidden inputs here --}}
    @foreach ($filters as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
    <button type="submit" class="btn btn-success me-2">Download Excel</button>
</form>

<form action="{{ route('report.download', ['reportType' => 'supplier-sales', 'format' => 'pdf']) }}" method="POST" class="d-inline">
    @csrf
    {{-- Add the hidden inputs here --}}
    @foreach ($filters as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
    <button type="submit" class="btn btn-danger">Download PDF</button>
</form>
    </div>
</div>

@endsection