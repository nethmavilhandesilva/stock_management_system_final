@extends('layouts.app')

@section('horizontal_sidebar')
    {{-- This section will contain the content that was originally in the vertical sidebar --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm rounded-bottom px-3 py-2">
        <div class="container-fluid">
            {{-- Optional: Add a brand/logo if needed --}}
            {{-- <a class="navbar-brand" href="#">Menu</a> --}}

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavHorizontal"
                aria-controls="navbarNavHorizontal" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavHorizontal">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link d-flex align-items-center">
                            <span class="material-icons me-2 text-primary">dashboard</span> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('items.index') }}"
                            class="nav-link d-flex align-items-center {{ Request::routeIs('items.index') ? 'active' : '' }}"
                            aria-current="{{ Request::routeIs('items.index') ? 'page' : '' }}">
                            <span class="material-icons me-2 text-success">inventory_2</span> භාණ්ඩ (Items)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('customers.index') }}" class="nav-link d-flex align-items-center">
                            <span class="material-icons me-2 text-primary">people</span> Customers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('suppliers.index') }}" class="nav-link d-flex align-items-center">
                            <span class="material-icons me-2 text-blue-600">local_shipping</span> සැපයුම්කරුවන් (Suppliers)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('grn.index') }}" class="nav-link d-flex align-items-center">
                            <span class="material-icons me-2 text-blue-600">assignment_turned_in</span> GRN-4
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
@endsection

@section('content')
    {{-- CSS Includes --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- REQUIRED: Minimal inline styling for the new section's appearance and collapse functionality --}}
    <style>
        .printed-sales-list ul,
        .unprinted-sales-list ul {
            list-style: none;
            padding-left: 0;
        }

        .printed-sales-list li,
        .unprinted-sales-list li {
            border: 1px solid #e0e0e0;
            margin-bottom: 8px;
            border-radius: 5px;
            overflow: hidden;
        }

        .customer-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .customer-header:hover {
            background-color: #e9ecef;
        }

        .customer-details {
            padding: 10px 15px;
            background-color: #fff;
            border-top: 1px solid #e0e0e0;
            display: none;
            /* Bootstrap's .collapse class handles visibility */
        }

        .customer-details.show {
            /* Bootstrap adds .show when expanded */
            display: block;
        }

        .customer-details table {
            width: 100%;
            margin-top: 10px;
            font-size: 0.85em;
        }

        .customer-details table th,
        .customer-details table td {
            padding: 4px 8px;
            text-align: left;
            border-bottom: 1px dashed #eee;
        }

        .customer-details table th {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .customer-details .sale-item-row:last-child td {
            border-bottom: none;
        }

        .total-for-customer {
            font-weight: bold;
            text-align: right;
            padding: 5px 0;
            border-top: 1px solid #ddd;
            margin-top: 5px;
        }

        .arrow-icon {
            transition: transform 0.3s ease;
        }

        .arrow-icon.rotated {
            transform: rotate(90deg);
        }
    </style>

    <style>
        /* Page background green */
        body,
        html {
            background-color: #e6ffe6;
            /* Light green background */
        }

        /* Bold black labels */
        label.form-label {
            font-weight: 700;
            color: #000000;
        }

        /* Smaller input fields and selects, with borders */
        input.form-control-sm,
        select.form-select-sm {
            border: 1.5px solid #000000 !important;
            /* stronger black border */
            font-weight: 600;
            font-size: 0.875rem;
            /* smaller font */
        }

        /* Align certain form groups horizontally with smaller width */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .form-row>div {
            flex: 1 1 150px;
            /* allow shrink/grow, min width 150px */
        }

        /* Adjust card background to white for contrast */
        .card {
            background-color: #ffffff !important;
        }

        /* Select2 specific styling adjustments for smaller size */
        .select2-container--bootstrap-5 .select2-selection--single {
            min-height: calc(1.5em + 0.5rem + 2px);
            /* Matches form-control-sm height */
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            /* Matches form-control-sm font-size */
            border: 1.5px solid #000000 !important;
            /* Apply border to select2 */
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: calc(1.5em + 0.5rem + 2px);
            padding-left: 0;
            /* Remove default padding as it's set on the selection */
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + 0.5rem + 2px);
            top: 50%;
            transform: translateY(-50%);
        }
    </style>


    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            {{-- NEW SECTION: Printed Sales Records (bill_printed = 'Y') - Left Column --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-3 p-4">
                    <h3 class="mb-4 text-center">මුද්‍රිත විකුණුම් වාර්තා</h3>

                    @if ($salesPrinted->count())
                        <div class="printed-sales-list">
                            <ul>
                                {{-- Outer loop: Loop over each CUSTOMER GROUP for printed sales --}}
                                @foreach ($salesPrinted as $customerCode => $salesForCustomer)
                                    @php
                                        $customerName = $salesForCustomer->first()->customer_name ?? 'N/A';
                                    @endphp
                                    <li>
                                        {{-- Display the Customer Name/Code header --}}
                                        <div class="customer-group-header">
                                            <strong>{{ $customerName }} ({{ $customerCode }})</strong>
                                        </div>

                                        <ul>
                                            {{-- Inner loop: Group sales by bill_no within this customer and loop over each BILL GROUP --}}
                                            @foreach ($salesForCustomer->groupBy('bill_no') as $billNo => $salesForBill)
                                                @php
                                                    $totalBillAmount = $salesForBill->sum('total');
                                                @endphp
                                                <li>
                                                    <div class="customer-header bill-clickable"
                                                        data-customer-code="{{ $customerCode }}"
                                                        data-customer-name="{{ $customerName }}"
                                                        data-bill-no="{{ $billNo }}" {{-- Pass the bill number --}}
                                                        data-bill-type="printed">
                                                        <span>
                                                            Bill No: {{ $billNo ?? 'N/A' }} - Rs.
                                                            {{ number_format($totalBillAmount, 2) }}
                                                        </span>
                                                        <i class="material-icons arrow-icon">keyboard_arrow_right</i>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-info text-center">No printed sales records found.</div>
                    @endif
                </div>
            </div>

            {{-- EXISTING CONTENT: Main Sales Entry and All Sales Table --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-3 p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Whoops!</strong> There were some problems with your input.
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('grn.store') }}" id="salesEntryForm">
                        @csrf

                        {{-- NEW TOP ROW: Select Customer Dropdown --}}
                        <div class="row justify-content-end" style="margin-top: -10px;">
                            <div class="col-md-4"> {{-- Reduced width from 6 to 4 for compact size --}}
                                <select name="customer_code_select" id="customer_code_select"
                                    class="form-select form-select-sm select2 @error('customer_code') is-invalid @enderror"
                                    style="height: 26px; font-size: 12px; padding-top: 2px; padding-bottom: 2px;">
                                    <option value="" disabled selected style="color: #999;">-- Select Customer --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->short_name }}"
                                            data-customer-code="{{ $customer->short_name }}"
                                            data-customer-name="{{ $customer->name }}"
                                            {{ old('customer_code_select') == $customer->short_name ? 'selected' : '' }}>
                                            {{ $customer->name }} ({{ $customer->short_name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        {{-- Second Row: Customer Code Input and GRN Entry --}}
                        <div class="row g-3 form-row align-items-start mt-2">
                            {{-- Added mt-2 for spacing from the select above --}}
                            {{-- Customer Code Input (col-md-6, adjusted width) --}}
                            <div class="col-md-6" style="margin-bottom: 4px;">
                                <label for="new_customer_code" class="form-label small">පාරිභෝගික කේතය</label>
                                <input type="text" name="customer_code" id="new_customer_code"
                                    class="form-control form-control-sm @error('customer_code') is-invalid @enderror"
                                    value="{{ old('customer_code') }}" placeholder="Enter or select customer code"
                                    style="height: 28px; font-size: 12px;" required>
                                @error('customer_code')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- GRN Entry Field (col-md-6, adjusted width) --}}
                            <div class="col-md-6" style="margin-bottom: 4px;">
                                <label for="grn_display" class="form-label small">GRN Entry</label>
                                <input type="text" id="grn_display" class="form-control form-control-sm"
                                    placeholder="Select GRN Entry..." readonly style="height: 28px; font-size: 12px;">
                                <select id="grn_select" class="form-select form-select-sm select2 d-none">
                                    <option value="">-- Select GRN Entry --</option>
                                    @foreach ($entries as $entry)
                                        <option value="{{ $entry->code }}" data-supplier-code="{{ $entry->supplier_code }}"
                                            data-code="{{ $entry->code }}" data-item-code="{{ $entry->item_code }}"
                                            data-item-name="{{ $entry->item_name }}" data-weight="{{ $entry->weight }}"
                                            data-price="{{ $entry->price_per_kg }}" data-total="{{ $entry->total }}"
                                            data-packs="{{ $entry->packs }}" data-grn-no="{{ $entry->grn_no }}"
                                            data-txn-date="{{ $entry->txn_date }}">
                                            {{ $entry->code }} | {{ $entry->supplier_code }} | {{ $entry->item_code }} |
                                            {{ $entry->item_name }} | {{ $entry->packs }} | {{ $entry->grn_no }} |
                                            {{ $entry->txn_date }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Hidden Customer Name (kept for form submission) --}}
                            <input type="hidden" name="customer_name" id="customer_name_hidden"
                                value="{{ old('customer_name') }}">
                        </div>

                        <hr style="margin: 0.3rem 0; height: 1px;">

                        <div class="row g-3 form-row">
                            <div class="col-md-6">
                                <label for="supplier_code" class="form-label" style="font-size: 0.9rem;">සැපයුම්කරු</label>
                                <select name="supplier_code" id="supplier_code"
                                    class="form-select form-select-sm @error('supplier_code') is-invalid @enderror" required>
                                    <option value="">Select a Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->code }}"
                                            {{ old('supplier_code') == $supplier->code ? 'selected' : '' }}>
                                            {{ $supplier->name }} ({{ $supplier->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_code')
                                    <div class="invalid-feedback" style="font-size: 0.8rem;">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="item_select" class="form-label" style="font-size: 0.9rem;">අයිතමය තෝරන්න</label>
                                <select id="item_select"
                                    class="form-select form-select-sm @error('item_code') is-invalid @enderror">
                                    <option value="">Select an Item</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->item_code }}" data-code="{{ $item->code }}"
                                            data-item-code="{{ $item->item_code }}"
                                            data-item-name="{{ $item->item_name }}"
                                            {{ old('item_code') == $item->item_code ? 'selected' : '' }}>
                                            {{ $item->item_name }} ({{ $item->item_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('item_code')
                                    <div class="invalid-feedback" style="font-size: 0.8rem;">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <input type="hidden" name="code" id="code" value="{{ old('code') }}">
                            <input type="hidden" name="item_code" id="item_code" value="{{ old('item_code') }}">
                            <input type="hidden" name="item_name" id="item_name" value="{{ old('item_name') }}">

                            <div class="col-md-3">
                                <label for="weight" class="form-label" style="font-size: 0.9rem;">බර (kg)</label>
                                <input type="number" name="weight" id="weight" step="0.01"
                                    class="form-control form-control-sm @error('weight') is-invalid @enderror"
                                    value="{{ old('weight') }}" required>
                                @error('weight')
                                    <div class="invalid-feedback" style="font-size: 0.8rem;">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="price_per_kg" class="form-label" style="font-size: 0.9rem;">කිලෝග්‍රෑමයකට
                                    මිල</label>
                                <input type="number" name="price_per_kg" id="price_per_kg" step="0.01"
                                    class="form-control form-control-sm @error('price_per_kg') is-invalid @enderror"
                                    value="{{ old('price_per_kg') }}" required>
                                @error('price_per_kg')
                                    <div class="invalid-feedback" style="font-size: 0.8rem;">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="total" class="form-label" style="font-size: 0.9rem;">සමස්ත</label>
                                <input type="number" name="total" id="total"
                                    class="form-control form-control-sm bg-light @error('total') is-invalid @enderror"
                                    value="{{ old('total') }}" readonly>
                                @error('total')
                                    <div class="invalid-feedback" style="font-size: 0.8rem;">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="packs" class="form-label">ඇසුරුම්</label>
                                <input type="number" name="packs" id="packs"
                                    class="form-control form-control-sm @error('packs') is-invalid @enderror"
                                    value="{{ old('packs') }}" required>
                                @error('packs')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-center mt-4">
                            <input type="hidden" name="sale_id" id="sale_id">
                            <button type="submit" class="btn btn-primary btn-sm shadow-sm" id="addSalesEntryBtn">
                                <i class="material-icons me-2">add_circle_outline</i>Add Sales Entry
                            </button>
                            <button type="button" class="btn btn-success btn-sm shadow-sm" id="updateSalesEntryBtn" style="display:none;">
                                <i class="material-icons me-2">edit</i>Update Sales Entry
                            </button>
                            <button type="button" class="btn btn-danger btn-sm shadow-sm" id="deleteSalesEntryBtn" style="display:none;">
                                <i class="material-icons me-2">delete</i>Delete Sales Entry
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm shadow-sm" id="cancelEntryBtn" style="display:none;">
                                <i class="material-icons me-2">cancel</i>Cancel / New Entry
                            </button>
                        </div>
                    </form>

                    <hr class="my-3" style="margin-top: 0.5rem; margin-bottom: 0.5rem; height: 1px;">

                    {{-- Main Sales Table - ALWAYS RENDERED --}}
                    <div class="mt-2">
                        <h5 class="text-end mb-3" style="font-size: 0.85rem;">
                            <strong>Total Sales Value:</strong> Rs. <span
                                id="mainTotalSalesValue">{{ number_format($totalSum, 2) }}</span>
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover shadow-sm rounded-3 overflow-hidden"
                                style="font-size: 0.85rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">කේතය</th>
                                        <th scope="col">අයිතම කේතය</th>
                                        <th scope="col">අයිතමය</th>
                                        <th scope="col">බර (kg)</th>
                                        <th scope="col">මිල/කිලෝග්‍රෑමය</th>
                                        <th scope="col">සමස්ත</th>
                                        <th scope="col">ඇසුරුම්</th>
                                        <th scope="col">Bill Status</th>
                                    </tr>
                                </thead>
                                <tbody id="mainSalesTableBody">
                                    {{-- This tbody will be dynamically populated by JavaScript. --}}
                                    {{-- An initial message can be added here if needed, like: --}}
                                    {{-- <tr><td colspan="8" class="text-center text-muted">Loading sales data...</td></tr> --}}
                                    {{-- But the JS already handles the "No sales records found" so it's not strictly necessary. --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NEW SECTION: Unprinted Sales Records (bill_printed = 'N') - Right Column --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-3 p-4">
                    <h3 class="mb-4 text-center">මුද්‍රණය නොකළ විකුණුම් වාර්තා</h3>

                    @if ($salesNotPrinted->count())
                        <div class="unprinted-sales-list">
                            <ul>
                                {{-- Loop over each CUSTOMER GROUP for unprinted sales --}}
                                @foreach ($salesNotPrinted as $customerCode => $salesForCustomer)
                                    @php
                                        // $salesForCustomer is a collection of Sale models for a specific customer (unprinted)
                                        $firstSaleForCustomer = $salesForCustomer->first();
                                        $customerName = $firstSaleForCustomer->customer_name;
                                        $totalCustomerSalesAmount = $salesForCustomer->sum('total');
                                    @endphp
                                    <li>
                                        <div class="customer-header bill-clickable"
                                            data-customer-code="{{ $customerCode }}"
                                            data-customer-name="{{ $customerName }}"
                                            data-bill-no="" {{-- No bill_no for unprinted yet --}}
                                            data-bill-type="unprinted">
                                            <span>
                                                ({{ $customerCode }}) - Rs.
                                                {{ number_format($totalCustomerSalesAmount, 2) }}
                                            </span>
                                            <i class="material-icons arrow-icon">keyboard_arrow_right</i>
                                        </div>
                                        <div class="mt-2 text-center">
                                            <button class="btn btn-sm btn-outline-primary print-bill-btn"
                                                data-customer-code="{{ $customerCode }}">
                                                Print Bill
                                            </button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-info text-center">No unprinted sales records found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript Includes (jQuery and Select2 should always be loaded before your custom script that uses them) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Ensure Bootstrap JS is loaded for collapse --}}


    {{-- ALL Custom JavaScript Consolidated Here --}}
    <script>
        // --- Form Calculations & Select2 Interactions ---
        const itemSelect = document.getElementById('item_select');
        const codeField = document.getElementById('code');
        const itemCodeField = document.getElementById('item_code');
        const itemNameField = document.getElementById('item_name');
        const supplierSelect = document.getElementById('supplier_code');
        const weightField = document.getElementById('weight');
        const pricePerKgField = document.getElementById('price_per_kg');
        const totalField = document.getElementById('total');
        const packsField = document.getElementById('packs');
        const grnDisplay = document.getElementById('grn_display');

        const customerSelect = document.getElementById('customer_code_select');
        const newCustomerCodeField = document.getElementById('new_customer_code');
        const customerNameField = document.getElementById('customer_name_hidden');
        newCustomerCodeField.focus();

        const salesEntryForm = document.getElementById('salesEntryForm');
        const saleIdField = document.getElementById('sale_id');
        const addSalesEntryBtn = document.getElementById('addSalesEntryBtn');
        const updateSalesEntryBtn = document.getElementById('updateSalesEntryBtn');
        const deleteSalesEntryBtn = document.getElementById('deleteSalesEntryBtn');
        const cancelEntryBtn = document.getElementById('cancelEntryBtn');


        function calculateTotal() {
            const weight = parseFloat(weightField.value) || 0;
            const price = parseFloat(pricePerKgField.value) || 0;
            totalField.value = (weight * price).toFixed(2);
        }

        itemSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected && selected.dataset) {
                codeField.value = selected.dataset.code || '';
                itemCodeField.value = selected.dataset.itemCode || '';
                itemNameField.value = selected.dataset.itemName || '';
            } else {
                codeField.value = '';
                itemCodeField.value = '';
                itemNameField.value = '';
            }
        });

        weightField.addEventListener('input', calculateTotal);
        pricePerKgField.addEventListener('input', calculateTotal);
        calculateTotal(); // Initial calculation on page load

        $(document).ready(function() {
            // Initialize Select2 for GRN and Customer select fields
            $('#grn_select').select2({
                dropdownParent: $('#grn_select').parent(),
                placeholder: "-- Select GRN Entry --",
                width: '100%',
                allowClear: true,
                templateResult: function(data) {
                    if (data.loading || !data.id) return data.text;
                    return $(data.element).text();
                },
                templateSelection: function(data) {
                    return data.text;
                }
            });

            $('#customer_code_select').select2({
                dropdownParent: $('#customer_code_select').parent(),
                placeholder: "-- Select Customer --",
                width: '100%',
                allowClear: true,
                templateResult: function(data) {
                    if (data.loading) return data.text;
                    if (!data.id) return data.text;
                    return $(`<span>${$(data.element).data('customer-name')} (${$(data.element).data('customer-code')})</span>`);
                },
                templateSelection: function(data) {
                    if (!data.id) return data.id ? $(`<span>${$(data.element).data('customer-name')} (${$(data.element).data('customer-code')})</span>`) : data.text;
                }
            });


            $('#grn_display').on('click', function() {
                $('#grn_select').select2('open');
            });

            $('#grn_select').on('select2:select', function(e) {
                const selectedOption = $(e.currentTarget).find('option:selected');
                const data = selectedOption.data();
                $('#grn_display').val(data.code || '');
                supplierSelect.value = data.supplierCode || '';
                itemSelect.value = data.itemCode || '';
                itemSelect.dispatchEvent(new Event('change'));
                weightField.value = '';
                pricePerKgField.value = '';
                packsField.value = '';
                calculateTotal();
                weightField.focus();
            });

            $('#customer_code_select').on('select2:select', function(e) {
                const selectedOption = $(e.currentTarget).find('option:selected');
                const selectedCustomerCode = selectedOption.val();
                const selectedCustomerName = selectedOption.data('customer-name');

                newCustomerCodeField.value = selectedCustomerCode || '';
                newCustomerCodeField.readOnly = true;
                customerNameField.value = selectedCustomerName || '';

                $('#grn_select').select2('open');
            });

            newCustomerCodeField.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    $('#grn_select').select2('open');
                }
            });

            $('#grn_select').on('select2:clear', function() {
                $('#grn_display').val('');
                supplierSelect.value = '';
                itemSelect.value = '';
                itemSelect.dispatchEvent(new Event('change'));
                weightField.value = '';
                pricePerKgField.value = '';
                packsField.value = '';
                calculateTotal();
            });

            $('#customer_code_select').on('select2:clear', function() {
                newCustomerCodeField.value = '';
                newCustomerCodeField.readOnly = false;
                customerNameField.value = '';
            });

            $('#new_customer_code').on('input', function() {
                if ($(this).val() !== '') {
                    $('#customer_code_select').val(null).trigger('change');
                    customerNameField.value = '';
                }
            });

            // Handle old input values on page load
            $(document).ready(function() {
                $('#grn_select').select2({
                    placeholder: "-- Select GRN Entry --",
                    allowClear: true
                });

                $(document).on('select2:open', function() {
                    document.querySelector('.select2-search__field').focus();
                });

                @if (old('customer_code_select') || old('customer_code'))
                    const oldGrnCode = "{{ old('code') }}";
                    const oldSupplierCode = "{{ old('supplier_code') }}";
                    const oldItemCode = "{{ old('item_code') }}";
                    const oldWeight = "{{ old('weight') }}";
                    const oldPricePerKg = "{{ old('price_per_kg') }}";
                    const oldPacks = "{{ old('packs') }}";
                    const oldGrnOption = $('#grn_select option').filter(function() {
                        return $(this).val() === oldGrnCode && $(this).data('supplierCode') ===
                            oldSupplierCode && $(this).data('itemCode') === oldItemCode;
                    });

                    if (oldGrnOption.length) {
                        $('#grn_select').val(oldGrnOption.val()).trigger('change');
                        $('#grn_display').val(oldGrnOption.data('code'));
                        $('#weight').val(oldWeight);
                        $('#price_per_kg').val(oldPricePerKg);
                        $('#packs').val(oldPacks);
                        calculateTotal();
                    }

                    const newCustomerCodeField = document.getElementById('new_customer_code');
                    const customerNameField = document.getElementById('customer_name_hidden');

                    const oldSelectedCustomerCode = "{{ old('customer_code_select') }}";
                    const oldEnteredCustomerCode = "{{ old('customer_code') }}";
                    const oldCustomerNameValue = "{{ old('customer_name') }}";

                    if (oldSelectedCustomerCode) {
                        $('#customer_code_select').val(oldSelectedCustomerCode).trigger('change');
                        if (newCustomerCodeField) {
                            newCustomerCodeField.value = oldSelectedCustomerCode;
                            newCustomerCodeField.readOnly = true;
                        }
                        if (customerNameField) {
                            customerNameField.value = oldCustomerNameValue;
                        }
                    } else if (oldEnteredCustomerCode) {
                        if (newCustomerCodeField) {
                            newCustomerCodeField.value = oldEnteredCustomerCode;
                            newCustomerCodeField.readOnly = false;
                        }
                        if (customerNameField) {
                            customerNameField.value = oldCustomerNameValue;
                        }
                    }

                    $('#grn_select').select2('open');
                @endif
            });

            $('#grn_select').on('select2:open', function() {
                $('.select2-container--open .select2-search__field').focus();
            });

            // --- JavaScript for F1 and F5 Key Presses ---
          document.addEventListener('keydown', function(e) {
        console.log('Key pressed:', e.key);

        if (e.key === "F1") {
            e.preventDefault();
            console.log('F1 key pressed - attempting to print and mark sales...');

            const salesDataForReceipt = @json($unprocessedSales);

            if (salesDataForReceipt.length === 0) {
                alert('No unprocessed sales records to print!');
                return;
            }

            // ADDED: Confirmation dialog before printing
            if (!confirm('Do you want to print the current unprocessed sales?')) {
                console.log('Print action cancelled by user.');
                return; // Stop execution if the user cancels
            }

            const salesIdsToMarkPrintedAndProcessed = salesDataForReceipt.map(sale => sale.id);

            const now = new Date();
            const date = now.toLocaleDateString();
            const time = now.toLocaleTimeString();
            const customerCode = document.getElementById('new_customer_code').value || 'N/A';
            const customerName = document.getElementById('customer_name_hidden').value || 'N/A';
            const mobile = '0702758908'; // Hardcoded phone number

            const random4Digit = Math.floor(1000 + Math.random() * 9000);
            const billNo = `BILL-${random4Digit}`;

            let itemsHtml = '';
            let totalItemsCount = 0;
            let totalAmountSum = 0;
            salesDataForReceipt.forEach(sale => {
                itemsHtml += `
                    <tr>
                        <td>${sale.item_name} (${sale.item_code})</td>
                        <td class="align-right">${sale.weight.toFixed(2)} kg x ${sale.packs} packs</td>
                        <td class="align-right">${sale.price_per_kg.toFixed(2)}</td>
                        <td class="align-right">${sale.total.toFixed(2)}</td>
                    </tr>
                `;
                totalItemsCount++;
                totalAmountSum += parseFloat(sale.total);
            });

            const salesContent = `
                <div class="receipt-container">
                    <div class="header-section">
                        <h2>ග්‍රාමී</h2>
                        <p>දිනය: ${date}</p>
                        <p>වෙලාව: ${time}</p>
                        <p>බිල් අංකය: ${billNo}</p>
                    </div>

                    <div class="divider"></div>

                    <div class="customer-info">
                        <p>පාරිභෝගික කේතය: ${customerCode}</p>
                        <p>නම: ${customerName}</p>
                        <p>දුරකථන: ${mobile}</p>
                    </div>

                    <div class="divider"></div>

                    <div class="items-section">
                        <table>
                            <thead>
                                <tr>
                                    <th class="item-name-col">අයිතමය</th>
                                    <th class="qty-col">ප්‍රමාණය</th>
                                    <th class="price-col">ඒකක මිල</th>
                                    <th class="total-col">මුළු මුදල</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>
                    </div>

                    <div class="divider"></div>

                    <div class="totals-section">
                        <p>මුළු අයිතම ගණන: ${totalItemsCount}</p>
                        <p>මුළු මුදල: Rs. ${totalAmountSum.toFixed(2)}</p>
                        <p class="grand-total">ගෙවිය යුතු මුළු මුදල: <strong>Rs. ${(totalAmountSum).toFixed(2)}</strong></p>
                    </div>

                    <div class="footer-section">
                        <p>ගෙවීමට ස්තුතියි!</p>
                        <p>නැවත පැමිණෙන්න!</p>
                    </div>
                </div>
            `;

            const printWindow = window.open('', '_blank', 'width=400,height=600');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>විකුණුම් කුපිත්තුව</title>
                        <style>
                            @font-face {
                                font-family: 'NotoSansSinhala';
                                src: url('https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;700&display=swap');
                            }
                            body {
                                font-family: 'Noto Sans Sinhala', sans-serif;
                                margin: 0;
                                padding: 20px;
                                font-size: 12px;
                            }
                            .receipt-container {
                                width: 100%;
                                max-width: 380px;
                                margin: auto;
                                border: 1px dashed #000;
                                padding: 15px;
                            }
                            .header-section, .footer-section, .customer-info {
                                text-align: center;
                                margin-bottom: 10px;
                            }
                            .divider {
                                border-top: 1px dashed #000;
                                margin: 10px 0;
                            }
                            .items-section table {
                                width: 100%;
                                border-collapse: collapse;
                            }
                            .items-section th, .items-section td {
                                padding: 3px;
                                text-align: right;
                            }
                            .item-name-col {
                                text-align: left;
                                width: 40%;
                            }
                            .qty-col {
                                width: 15%;
                            }
                            .price-col {
                                width: 20%;
                            }
                            .total-col {
                                width: 25%;
                            }
                            .totals-section {
                                text-align: right;
                            }
                            .grand-total {
                                font-weight: bold;
                                font-size: 1.1em;
                            }
                        </style>
                    </head>
                    <body>${salesContent}</body>
                </html>
            `);
            printWindow.document.close();
            // ADDED: Explicitly call print()
            printWindow.focus(); // Try to bring the new window to focus
            printWindow.print(); // This explicitly opens the print dialog

            const checkClosed = setInterval(function() {
                // This checks if the print window is closed, meaning the user either printed or cancelled.
                if (printWindow.closed) {
                    clearInterval(checkClosed);
                    console.log('F1: Print window closed. Sending request to mark sales as printed and processed.');

                    fetch("{{ route('sales.markAsPrinted') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                sales_ids: salesIdsToMarkPrintedAndProcessed,
                                bill_no: billNo
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! status: ${response.status}, message: ${text}`)
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('F1: Sales marked as printed and processed response:', data);
                            sessionStorage.setItem('focusOnCustomerSelect', 'true');
                            window.location.reload();
                        })
                        .catch(error => {
                            console.error('F1: Error marking sales as printed and processed:', error);
                            alert('Failed to mark sales as printed. Please check console for details.');
                        });
                }
            }, 500);
        } else if (e.key === "F5") {
            e.preventDefault();
            console.log('F5 key pressed - attempting to mark all displayed sales as processed...');

            fetch("{{ route('sales.markAllAsProcessed') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP error! status: ${response.status}, message: ${text}`)
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response from sales.markAllAsProcessed (F5):', data);
                    if (data.success) {
                        console.log(data.message);
                        sessionStorage.setItem('focusOnCustomerSelect', 'true');
                        window.location.reload();
                    } else {
                        console.error('Server reported an error:', data.message);
                        alert('Operation failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error marking sales as processed by F5:', error);
                    alert('Failed to process sales on F5. Check console for details.');
                });
        }
    });

            // Store the PHP data in JavaScript variables for easier access
            const printedSalesData = @json($salesPrinted->toArray());
            const unprintedSalesData = @json($salesNotPrinted->toArray());
            // allSalesData is the initial data loaded for the main table
            const allSalesData = @json($sales->toArray());

            // NEW: Variable to hold the currently displayed sales data in the main table
            let currentDisplayedSalesData = [];


            console.log("Initial printedSalesData:", printedSalesData);
            console.log("Initial unprintedSalesData:", unprintedSalesData);
            console.log("Initial allSalesData (for default table view):", allSalesData);


            // Function to populate the main sales table
            function populateMainSalesTable(salesArray) {
                console.log("Entering populateMainSalesTable. Sales array received:", salesArray);
                // Update the currentDisplayedSalesData
                currentDisplayedSalesData = salesArray;
                console.log("currentDisplayedSalesData updated to:", currentDisplayedSalesData);

                const mainSalesTableBodyElement = document.getElementById('mainSalesTableBody');

                if (!mainSalesTableBodyElement) {
                    console.error("Error: tbody with ID 'mainSalesTableBody' not found!");
                    return; // Exit if element is not found
                }

                let rowsHtml = '';
                let totalSalesValue = 0;

                if (salesArray.length === 0) {
                    console.log("Sales array is empty. Displaying 'No sales records found.'");
                    rowsHtml = '<tr><td colspan="8" class="text-center">No sales records found for this selection.</td></tr>';
                    totalSalesValue = 0;
                } else {
                    salesArray.forEach(sale => {
                        // Construct the row HTML string
                        rowsHtml += `
                            <tr data-sale-id="${sale.id}">
                                <td>${sale.code || 'N/A'}</td>
                                <td>${sale.item_code || 'N/A'}</td>
                                <td>${sale.item_name || 'N/A'}</td>
                                <td>${(parseFloat(sale.weight) || 0).toFixed(2)}</td>
                                <td>${(parseFloat(sale.price_per_kg) || 0).toFixed(2)}</td>
                                <td>${(parseFloat(sale.total) || 0).toFixed(2)}</td>
                                <td>${(parseFloat(sale.packs) || 0).toFixed(0)}</td>
                                <td>${sale.bill_printed === 'Y' ? 'Printed' : 'Unprinted'}</td>
                            </tr>
                        `;
                        totalSalesValue += parseFloat(sale.total || 0);
                    });
                }

                // Log the HTML string *before* setting it
                console.log("Attempting to set tbody HTML with:", rowsHtml);

                // Set the innerHTML of the tbody using native JavaScript
                mainSalesTableBodyElement.innerHTML = rowsHtml;

                // Update total sales value display
                $('#mainTotalSalesValue').text(totalSalesValue.toFixed(2));
                console.log("populateMainSalesTable finished. Total sales value:", totalSalesValue.toFixed(2));
                // Log the actual HTML that is *now* inside the tbody
                console.log("Current tbody HTML after setting:", mainSalesTableBodyElement.innerHTML);
            }

            // Initial population of the main sales table with all sales data
            populateMainSalesTable(allSalesData);

            // Function to populate the form fields for editing
            function populateFormForEdit(sale) {
                console.log("Populating form for sale:", sale);
                saleIdField.value = sale.id;
                newCustomerCodeField.value = sale.customer_code || '';
                customerNameField.value = sale.customer_name || '';
                newCustomerCodeField.readOnly = true;

                // Set selected customer in Select2, if applicable
                if (sale.customer_code) {
                    $('#customer_code_select').val(sale.customer_code).trigger('change.select2');
                    console.log("Setting customer_code_select to:", sale.customer_code);
                } else {
                    $('#customer_code_select').val(null).trigger('change.select2');
                    console.log("Clearing customer_code_select.");
                }

                // Populate GRN related fields
                $('#grn_display').val(sale.code || ''); // Assuming 'code' is the GRN code
                // Try to select the GRN in the hidden select2, then trigger change
                const grnOption = $('#grn_select option').filter(function() {
                    return $(this).val() === sale.code && $(this).data('supplierCode') === sale.supplier_code && $(this).data('itemCode') === sale.item_code;
                });
                if (grnOption.length) {
                    $('#grn_select').val(grnOption.val()).trigger('change.select2'); // Use change.select2 to trigger Select2's internal change handler
                    console.log("Setting grn_select to:", grnOption.val());
                } else {
                    $('#grn_select').val(null).trigger('change.select2');
                    console.log("Clearing grn_select.");
                }

                supplierSelect.value = sale.supplier_code || '';
                itemSelect.value = sale.item_code || '';
                itemSelect.dispatchEvent(new Event('change')); // Trigger change to update hidden item fields
                console.log("Setting supplier_code to:", sale.supplier_code, "and item_select to:", sale.item_code);


                weightField.value = parseFloat(sale.weight || 0).toFixed(2);
                pricePerKgField.value = parseFloat(sale.price_per_kg || 0).toFixed(2);
                packsField.value = parseInt(sale.packs || 0);
                calculateTotal(); // Recalculate total based on loaded weight/price
                console.log("Weight:", weightField.value, "Price:", pricePerKgField.value, "Packs:", packsField.value);


                // Change form action to update
                salesEntryForm.action = `/sales/update/${sale.id}`;
                console.log("Form action set to:", salesEntryForm.action);

                // Show/hide buttons
                addSalesEntryBtn.style.display = 'none';
                updateSalesEntryBtn.style.display = 'inline-block';
                deleteSalesEntryBtn.style.display = 'inline-block';
                cancelEntryBtn.style.display = 'inline-block';
                console.log("Buttons updated for edit mode.");
            }

            // Function to reset the form to "Add New Entry" mode
            function resetForm() {
                console.log("Resetting form...");
                salesEntryForm.reset(); // Resets all form fields
                saleIdField.value = ''; // Clear hidden ID
                newCustomerCodeField.readOnly = false; // Make customer code editable again
                $('#customer_code_select').val(null).trigger('change.select2'); // Clear customer select2
                $('#grn_select').val(null).trigger('change.select2'); // Clear GRN select2
                $('#grn_display').val(''); // Clear GRN display field
                supplierSelect.value = '';
                itemSelect.value = '';
                itemSelect.dispatchEvent(new Event('change'));
                calculateTotal(); // Recalculate total for empty fields

                salesEntryForm.action = "{{ route('grn.store') }}"; // Revert form action to store

                // Show/hide buttons
                addSalesEntryBtn.style.display = 'inline-block';
                updateSalesEntryBtn.style.display = 'none';
                deleteSalesEntryBtn.style.display = 'none';
                cancelEntryBtn.style.display = 'none';

                newCustomerCodeField.focus(); // Focus on the first input
                console.log("Form reset complete.");
            }

            // Event listener for clicking on table rows to populate form
            document.getElementById('mainSalesTableBody').addEventListener('click', function(event) {
                const clickedRow = event.target.closest('tr[data-sale-id]');
                if (clickedRow) {
                    const saleId = clickedRow.dataset.saleId;
                    console.log("Row clicked, sale ID:", saleId);
                    // IMPORTANT CHANGE: Search within currentDisplayedSalesData
                    const saleToEdit = currentDisplayedSalesData.find(sale => String(sale.id) === String(saleId));
                    if (saleToEdit) {
                        console.log("Sale found in currentDisplayedSalesData for ID:", saleId, saleToEdit);
                        populateFormForEdit(saleToEdit);
                    } else {
                        console.warn("Sale NOT found in currentDisplayedSalesData for ID:", saleId);
                        alert("Could not find this record for editing. It might not be in the currently displayed sales list. Please try reloading the page if this persists.");
                    }
                }
            });

            // Event listener for Update button
            updateSalesEntryBtn.addEventListener('click', function() {
                const saleId = saleIdField.value;
                if (!saleId) {
                    alert('No record selected for update.');
                    return;
                }

                const formData = new FormData(salesEntryForm);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });
                data['_method'] = 'PUT'; // Laravel expects PUT/PATCH for updates
                data['_token'] = '{{ csrf_token() }}';

                fetch(`/sales/update/${saleId}`, {
                        method: 'POST', // Use POST for Laravel PUT/PATCH spoofing
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => Promise.reject(errorData));
                        }
                        return response.json();
                    })
                    .then(result => {
                        if (result.success) {
                            alert(result.message);
                            sessionStorage.setItem('focusOnCustomerSelect', 'true');
                            window.location.reload();
                        } else {
                            alert('Update failed: ' + result.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating sales entry:', error);
                        let errorMessage = 'An error occurred during update.';
                        if (error && error.message) {
                            errorMessage += '\n' + error.message;
                        }
                        if (error && error.errors) {
                            for (const key in error.errors) {
                                errorMessage += `\n${key}: ${error.errors[key].join(', ')}`;
                            }
                        }
                        alert(errorMessage);
                    });
            });

            // Event listener for Delete button
            deleteSalesEntryBtn.addEventListener('click', function() {
                const saleId = saleIdField.value;
                if (!saleId) {
                    alert('No record selected for deletion.');
                    return;
                }

                if (!confirm('Are you sure you want to delete this sales record?')) {
                    return;
                }

                fetch(`/sales/delete/${saleId}`, {
                        method: 'POST', // Use POST for Laravel DELETE spoofing
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE', // Laravel expects DELETE
                            _token: '{{ csrf_token() }}'
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => Promise.reject(errorData));
                        }
                        return response.json();
                    })
                    .then(result => {
                        if (result.success) {
                            alert(result.message);
                            sessionStorage.setItem('focusOnCustomerSelect', 'true');
                            window.location.reload();
                        } else {
                            alert('Delete failed: ' + result.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting sales entry:', error);
                        let errorMessage = 'An error occurred during deletion.';
                        if (error && error.message) {
                            errorMessage += '\n' + error.message;
                        }
                        alert(errorMessage);
                    });
            });


            // Event listener for Cancel button
            cancelEntryBtn.addEventListener('click', resetForm);

            // Initial form state (hide update/delete, show add)
            resetForm();

            // Handle click on customer headers in Printed and Unprinted sections
            $('.customer-header').on('click', function() {
                console.log("Customer header clicked!");

                const customerCode = $(this).data('customer-code');
                const billType = $(this).data('bill-type');
                const billNo = $(this).data('bill-no');

                console.log("Clicked Customer Code:", customerCode);
                console.log("Clicked Bill Type:", billType);
                console.log("Clicked Bill No:", billNo);

                let salesToDisplay = [];

                if (billType === 'printed') {
                    console.log("Attempting to filter PRINTED sales...");
                    // Ensure printedSalesData[customerCode] exists and is an array before filtering
                    if (printedSalesData[customerCode] && Array.isArray(printedSalesData[customerCode])) {
                        salesToDisplay = printedSalesData[customerCode].filter(sale => {
                            return String(sale.bill_no) === String(billNo);
                        });
                        console.log("Printed sales data for customerCode:", printedSalesData[customerCode]);
                    } else {
                        console.log("No printed sales data found or not an array for customerCode:", customerCode);
                    }
                } else if (billType === 'unprinted') {
                    console.log("Attempting to filter UNPRINTED sales...");
                    // Ensure unprintedSalesData[customerCode] exists and is an array
                    if (unprintedSalesData[customerCode] && Array.isArray(unprintedSalesData[customerCode])) {
                        salesToDisplay = unprintedSalesData[customerCode];
                        console.log("Unprinted sales data for customerCode:", unprintedSalesData[customerCode]);
                    } else {
                        console.log("No unprinted sales data found or not an array for customerCode:", customerCode);
                    }
                } else {
                    console.log("Unknown billType:", billType);
                }

                console.log("Sales to Display after filter:", salesToDisplay);
                populateMainSalesTable(salesToDisplay); // This will also update currentDisplayedSalesData
            });


            // Handle Print Bill button click for unprinted bills (from list)
            $(document).on('click', '.print-bill-btn', function() {
                var customerCode = $(this).data('customer-code');
                if (confirm('Are you sure you want to print the bill for ' + customerCode +
                        '? This will mark all *unprinted* sales for this customer as printed and processed.')) {
                    $.ajax({
                        url: '/sales/print-bill/' + customerCode,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            customer_code: customerCode
                        },
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                sessionStorage.setItem('focusOnCustomerSelect', 'true');
                                location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error("AJAX error:", xhr.responseText);
                            alert('An error occurred while trying to print the bill.');
                        }
                    });
                }
            });

            // Check sessionStorage on page load for F1/F5 focus
            if (sessionStorage.getItem('focusOnCustomerSelect') === 'true') {
                $(document).on('select2:open', function() {
                    document.querySelector('.select2-search__field').focus();
                });
                $('#new_customer_code').select2('open');
                sessionStorage.removeItem('focusOnCustomerSelect');
            }
        });
    </script>
@endsection