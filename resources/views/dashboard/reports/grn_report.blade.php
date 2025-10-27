@extends('layouts.app')

@section('content')
    <style>
        body {
            background: linear-gradient(135deg, #99ff99, #99ff99);
        }

        .report-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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

        th,
        td {
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

        /* Filter form styling */
        form.bg-light {
            background: #e8f5e9 !important;
            border: 1px solid #99ff99;
        }

        /* --- NEW MODAL/CLICKABLE ROW STYLES --- */
        .clickable-row {
            cursor: pointer;
            transition: background-color 0.15s;
        }

        .clickable-row:hover {
            background-color: #f1f8e9;
            /* Light green hover */
        }

        .modal-header {
            background-color: #228B22;
            color: white;
            border-bottom: none;
        }

        .modal-title {
            font-weight: bold;
        }

        .modal-table th {
            background: #e8f5e9;
            color: #333;
            font-size: 13px;
        }

        /* ------------------------------------- */

        /* Print styles */
        @media print {
            body {
                background: white !important;
            }

            /* Hide everything except the report container */
            body * {
                visibility: hidden;
            }

            .report-container,
            .report-container * {
                visibility: visible;
            }

            .report-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            /* Make all text black and bold */
            .report-container,
            .report-container table,
            .report-container th,
            .report-container td {
                color: black !important;
                font-weight: bold !important;
            }

            /* Hide print button */
            .print-btn {
                display: none;
            }
        }
    </style>

    {{-- ✅ FILTER SECTION --}}
    <div class="container mt-4">
        <form method="GET" action="{{ route('grn.report2') }}" class="row g-3 p-3 bg-light rounded shadow-sm">

            {{-- Supplier Code Filter --}}
            <div class="col-md-3">
                <label for="supplier_code" class="form-label fw-bold text-success">Supplier Code</label>
                <select name="supplier_code" id="supplier_code" class="form-select">
                    <option value="">All</option>
                    @foreach ($supplierCodes as $code)
                        <option value="{{ $code }}" {{ request('supplier_code') == $code ? 'selected' : '' }}>
                            {{ $code }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Item Code Filter --}}
            <div class="col-md-3">
                <label for="item_code" class="form-label fw-bold text-success">Item Code</label>
                <select name="item_code" id="item_code" class="form-select">
                    <option value="">All</option>
                    @foreach ($itemCodes as $item)
                        <option value="{{ $item->item_code }}" {{ request('item_code') == $item->item_code ? 'selected' : '' }}>
                            {{ $item->item_code }} - {{ $item->item_name }}
                        </option>
                    @endforeach
                </select>
            </div>


            {{-- Date Range Filters --}}
            <div class="col-md-3">
                <label for="start_date" class="form-label fw-bold text-success">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                    class="form-control">
            </div>

            <div class="col-md-3">
                <label for="end_date" class="form-label fw-bold text-success">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>

            {{-- Buttons --}}
            <div class="col-12 text-center mt-2">
                <button type="submit" class="btn btn-success px-4">Apply Filters</button>
                <a href="{{ route('grn.report2') }}" class="btn btn-secondary px-4">Reset</a>
            </div>
        </form>
    </div>

    {{-- ✅ REPORT SECTION --}}
    <div class="report-container">
        <button class="print-btn" onclick="window.print()">🖨️ Print Report</button>
        <h2 class="report-title">Goods Received Note (GRN) Report</h2>

        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Supplier Code</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Packs</th>
                    <th>Weight</th>
                    <th>Txn Date</th>
                    <th>Original Packs</th>
                    <th>Original Weight</th>
                    <th>GRN No</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($grnEntries as $entry)
                    {{-- Make the row clickable and pass the 'code' to the modal via data attribute --}}
                    <tr class="clickable-row" data-bs-toggle="modal" data-bs-target="#grnDetailsModal"
                        data-grn-code="{{ $entry->code }}">
                        <td>{{ $entry->code }}</td>
                        <td>{{ $entry->supplier_code }}</td>
                        <td>{{ $entry->item_code }}</td>
                        <td>{{ $entry->item_name }}</td>
                        <td>{{ $entry->packs }}</td>
                        <td>{{ $entry->weight }}</td>
                        <td>{{ \Carbon\Carbon::parse($entry->txn_date)->format('Y-m-d') }}</td>
                        <td>{{ $entry->original_packs }}</td>
                        <td>{{ $entry->original_weight }}</td>
                        <td>{{ $entry->grn_no }}</td>
                    </tr>

                    {{-- Original Related records from GrnEntry2 (can be removed if modal replaces this) --}}
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
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">No records found. Please apply filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ GRN Details Modal --}}
    <div class="modal fade" id="grnDetailsModal" tabindex="-1" aria-labelledby="grnDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl"> {{-- Increased size for more content --}}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="grnDetailsModalLabel">GRN Details for Code: <span
                            id="modal-grn-code"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">






                    {{-- Sale Records Section --}}
                    <h4>Related Sale Records</h4>
                    <table class="table table-bordered modal-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Customer Code</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Packs</th>
                                <th>Weight</th>
                                <th>Price/KG</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="sale-details-body">
                            <tr>
                                <td colspan="8" class="text-center text-muted">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    {{-- GrnEntry2 Section --}}
                    <h4>Related Entry Details </h4>
                    <table class="table table-bordered mb-4 modal-table">
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
                        <tbody id="grn-entry2-details-body">
                            <tr>
                                <td colspan="6" class="text-center text-muted">Loading...</td>
                            </tr>
                        </tbody>
                    </table>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Add the script section to the bottom of the page or use @push('scripts') if configured --}}
@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Ensure you have the Bootstrap JS bundle loaded for the modal functionality --}}

    <script>
        $(document).ready(function () {
            // Event listener for clicking on a clickable row
            $('.clickable-row').on('click', function () {
                var grnCode = $(this).data('grn-code');
                console.log("Clicked GRN Code:", grnCode);

                // 1. Display the main GRN details in the modal (from the clicked row)
                var cells = $(this).children('td');
                var mainDetailsHtml = '<tr>' +
                    '<td>' + cells.eq(0).text() + '</td>' + // Code
                    '<td>' + cells.eq(1).text() + '</td>' + // Supplier Code
                    '<td>' + cells.eq(2).text() + '</td>' + // Item Code
                    '<td>' + cells.eq(3).text() + '</td>' + // Item Name
                    '<td>' + cells.eq(4).text() + '</td>' + // Packs
                    '<td>' + cells.eq(5).text() + '</td>' + // Weight
                    '<td>' + cells.eq(6).text() + '</td>' + // Txn Date
                    '<td>' + cells.eq(9).text() + '</td>' + // GRN No
                    '</tr>';

                $('#modal-grn-code').text(grnCode);
                $('#grn-main-details-body').html(mainDetailsHtml);

                // Set loading state for dynamic tables
                $('#grn-entry2-details-body').html('<tr><td colspan="6" class="text-center text-muted">Loading related GRN entries...</td></tr>');
                $('#sale-details-body').html('<tr><td colspan="8" class="text-center text-muted">Loading related Sale records...</td></tr>');

                // 2. AJAX call to fetch GrnEntry2 and Sale data
                $.ajax({
                    url: '{{ route('grn.fetch.details') }}', // Must be defined in web.php
                    method: 'GET',
                    data: {
                        code: grnCode
                    },
                    success: function (response) {
                        // --- Update GrnEntry2 table ---
                        var grnEntry2Html = '';
                        if (response.grnEntry2.length > 0) {
                            $.each(response.grnEntry2, function (i, item) {
                                grnEntry2Html += '<tr>' +
                                    '<td>' + item.item_code + '</td>' +
                                    '<td>' + item.item_name + '</td>' +
                                    '<td>' + item.packs + '</td>' +
                                    '<td>' + item.weight + '</td>' +
                                    '<td>' + item.per_kg_price + '</td>' +
                                    '<td>' + item.type + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            grnEntry2Html = '<tr><td colspan="6" class="text-center text-success">No related GrnEntry2 records found.</td></tr>';
                        }
                        $('#grn-entry2-details-body').html(grnEntry2Html);

                        // --- Update Sale table ---
                        var saleHtml = '';
                        if (response.sales.length > 0) {
                            $.each(response.sales, function (i, sale) {
                                saleHtml += '<tr>' +
                                    '<td>' + sale.Date + '</td>' + // Ensure case matches model column
                                    '<td>' + sale.customer_code + '</td>' +
                                    '<td>' + sale.item_code + '</td>' +
                                    '<td>' + sale.item_name + '</td>' +
                                    '<td>' + sale.packs + '</td>' +
                                    '<td>' + sale.weight + '</td>' +
                                    '<td>' + sale.price_per_kg + '</td>' +
                                    '<td>' + sale.total + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            saleHtml = '<tr><td colspan="8" class="text-center text-success">No related Sale records found.</td></tr>';
                        }
                        $('#sale-details-body').html(saleHtml);

                    },
                    error: function (xhr) {
                        console.error('Error fetching details:', xhr.responseText);
                        $('#grn-entry2-details-body').html('<tr><td colspan="6" class="text-center text-danger">Error loading GrnEntry2 data.</td></tr>');
                        $('#sale-details-body').html('<tr><td colspan="8" class="text-center text-danger">Error loading Sale data.</td></tr>');
                    }
                });
            });
        });
    </script>
@endsection