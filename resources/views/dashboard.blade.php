@extends('layouts.app')

@section('horizontal_sidebar')
    {{-- This section will contain the content that was originally in the vertical sidebar --}}
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm rounded-bottom px-3 py-2 custom-dark-green-bg">
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
                            <span class="material-icons me-2 text-primary">dashboard</span> <span
                                class="text-white">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('items.index') }}"
                            class="nav-link d-flex align-items-center {{ Request::routeIs('items.index') ? 'active' : '' }}"
                            aria-current="{{ Request::routeIs('items.index') ? 'page' : '' }}">
                            <span class="material-icons me-2 text-success">inventory_2</span> <span
                                class="text-white">භාණ්ඩ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('customers.index') }}" class="nav-link d-flex align-items-center">
                            <span class="material-icons me-2 text-primary">people</span> <span
                                class="text-white">ගනුදෙනුකරුවන්</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('suppliers.index') }}" class="nav-link d-flex align-items-center">
                            <span class="material-icons me-2 text-blue-600">local_shipping</span> <span
                                class="text-white">සැපයුම්කරුවන්</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('grn.index') }}" class="nav-link d-flex align-items-center">
                            <span class="material-icons me-2 text-blue-600">assignment_turned_in</span> <span
                                class="text-white">GRN</span>
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

        .custom-dark-green-bg {
            background-color: #006400 !important;
            /* A common dark green hex code */
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
            background-color: #99ff99;
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
            background-color: #87b387ff !important;
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

        /* Custom CSS for the tabular Select2 dropdown */

        /* Remove default padding from Select2 options to control inner spacing */
        .select2-container--default .select2-results__option {
            padding: 0 !important;
        }

        /* Flexbox for the row layout inside the dropdown */
        .grn-option-row {
            display: flex;
            justify-content: space-between;
            /* Distribute space between columns */
            align-items: center;
            /* Vertically center content */
            padding: 5px 8px;
            /* Padding for the entire row */
            border-bottom: 1px solid #eee;
            /* Separator between rows */
            font-family: Arial, sans-serif;
            /* Adjust font as needed */
            font-size: 10px;
            /* Adjust font size as needed */
            line-height: 1.2;
            /* Adjust line height for multi-line content */
            color: #333;
            /* Default text color */
        }

        /* Style for the single header row within the dropdown */
        /* REMOVED: .grn-header-row position sticky moved to .grn-header-row-wrapper */
        .grn-header-row {
            font-weight: bold;
            background-color: #f0f0f0;
            /* Light grey background for header */
            border-bottom: 1px solid #ccc;
            /* Stronger border below header */
            padding-top: 2px;
            padding-bottom: 2px;
            /* Ensure the header row is not affected by select2's default option styling */
            margin-top: -1px;
            /* Adjust to sit flush with the top of the dropdown */
        }

        /* NEW: Styles for the GRN Header Wrapper */
        .grn-header-row-wrapper {
            position: sticky;
            /* Make the wrapper sticky */
            top: 0;
            z-index: 10;
            background-color: #fff;
            /* Ensure background is white to hide content underneath when sticky */
            border-bottom: 1px solid #ccc;
            /* Optional: a border below the header */
        }

        /* NEW: Add padding to the results list to prevent options from being hidden by the sticky header */
        .select2-results__options.has-custom-header {
            padding-top: 30px;
            /* Adjust this value based on the actual height of your header row */
        }


        .grn-option-row:last-child {
            border-bottom: none;
            /* No border for the last row */
        }

        /* Individual column styling and width distribution */
        .grn-column {
            flex: 1;
            /* Distribute space evenly initially */
            padding: 0 4px;
            /* Padding within columns */
            white-space: nowrap;
            /* Prevent text wrapping unless necessary */
            overflow: hidden;
            /* Hide overflow */
            text-overflow: ellipsis;
            /* Add ellipsis for overflow */
            box-sizing: border-box;
            /* Include padding in element's total width */
        }

        /* Specific column widths - adjust these values as needed for your data */
        .grn-code {
            flex: 2;
            max-width: 120px;
        }

        /* Example: Wider for codes */
        .grn-supplier-code {
            flex: 1;
            max-width: 70px;
        }

        .grn-item-code {
            flex: 1.5;
            max-width: 90px;
        }

        .grn-item-name {
            flex: 2;
            max-width: 130px;
        }

        /* Wider for names */
        .grn-packs {
            flex: 0.8;
            max-width: 50px;
            text-align: right;
        }

        .grn-grn-no {
            flex: 1.2;
            max-width: 80px;
        }

        .grn-txn-date {
            flex: 1.5;
            max-width: 100px;
        }

        /* Highlighted (hovered) option in Select2 dropdown */
        .select2-container--default .select2-results__option--highlighted {
            background-color: #007bff !important;
            /* Blue highlight */
            color: white !important;
        }

        /* Text color for selected option displayed in the Select2 input */
        .select2-selection__rendered {
            color: #333;
        }

        /* Style for the main Select2 input field to match grn_display */
        .select2-container--default .select2-selection--single {
            height: 24px;
            /* Match height of grn_display */
            padding: 2px 6px;
            /* Match padding of grn_display */
            font-size: 11px;
            /* Match font size of grn_display */
            border: 1px solid #ced4da;
            /* Default Bootstrap form control border */
            border-radius: 0.25rem;
            /* Default Bootstrap border radius */
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 22px;
            /* Adjust arrow height */
            top: 0px;
            /* Position arrow correctly */
        }

        /* Ensure search input inside dropdown (if visible) matches size */
        .select2-search__field {
            height: 24px !important;
            font-size: 11px !important;
            padding: 2px 6px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
        }

        /* Style the placeholder option in the dropdown */
        .select2-results__option[role=option][aria-disabled=true] {
            color: #999;
            /* Grey out the placeholder option */
        }
    </style>


    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            {{-- NEW SECTION: Printed Sales Records (bill_printed = 'Y') - Left Column --}}
            <div class="col-md-3"> {{-- You can reduce this to col-md-2 or add custom width for smaller size --}}
                <div class="card shadow-sm border-0 rounded-3 p-3"> {{-- Reduced padding from p-4 to p-3 --}}
                    <h6 class="mb-2 text-center">මුද්‍රිත විකුණුම් වාර්තා</h6>

                    {{-- 🔍 Search Bar --}}
                    <input type="text" id="searchCustomerCode" class="form-control form-control-sm mb-2"
                        placeholder="Search by Bill No...">

                    @if ($salesPrinted->count())
                        <div class="printed-sales-list">
                            <ul id="printedSalesList">
                                {{-- Outer loop: CUSTOMER GROUP --}}
                                @foreach ($salesPrinted as $customerCode => $salesForCustomer)
                                    @php
                                        $customerName = $salesForCustomer->first()->customer_name ?? 'N/A';
                                    @endphp
                                    <li data-customer-code="{{ $customerCode }}">
                                        <div class="customer-group-header">

                                        </div>

                                        <ul>
                                            {{-- Inner loop: BILL GROUP --}}
                                            @foreach ($salesForCustomer->groupBy('bill_no') as $billNo => $salesForBill)
                                                @php
                                                    $totalBillAmount = $salesForBill->sum('total');
                                                @endphp
                                                <li>
                                                    <div class="customer-header bill-clickable" data-customer-code="{{ $customerCode }}"
                                                        data-customer-name="{{ $customerName }}" data-bill-no="{{ $billNo ?? '' }}"
                                                        data-bill-type="printed"
                                                        style="font-size: 11px; padding: 2px 6px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #ddd; margin-bottom: 3px; border-radius: 4px; background-color: #f9f9f9;">
                                                         <span style="flex: 1;">
                                                         (  Bill No: {{ $billNo ?? 'N/A' }} - Rs.)     {{ number_format($totalBillAmount, 2) }}
                                                        </span>

                                                       



                                                        <i class="material-icons arrow-icon"
                                                            style="font-size: 14px;">keyboard_arrow_right</i>
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

                        <div class="row justify-content-end" style="margin-top: -15px;">
                            <div class="col-md-3">
                                <select name="customer_code_select" id="customer_code_select"
                                    class="form-select form-select-sm select2 @error('customer_code') is-invalid @enderror"
                                    style="height: 24px; font-size: 11px; padding-top: 1px; padding-bottom: 1px; padding-left: 6px; line-height: 1.2;">
                                    <option value="" disabled selected style="color: #999;">-- පාරිභෝගිකයා තෝරන්න --
                                    </option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->short_name }}"
                                            data-customer-code="{{ $customer->short_name }}"
                                            data-customer-name="{{ $customer->name }}" {{ old('customer_code_select') == $customer->short_name ? 'selected' : '' }}>
                                            {{ $customer->name }} ({{ $customer->short_name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_code')
                                    <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-1 form-row align-items-start mt-1">
                            <div class="col-md-3" style="margin-bottom: 2px; max-width: 135px;">
                                {{-- Adjusted for ~8 characters --}}
                                <input type="text" name="customer_code" id="new_customer_code"
                                    class="form-control form-control-sm text-uppercase @error('customer_code') is-invalid @enderror"
                                    value="{{ old('customer_code') }}" placeholder="පාරිභෝගික කේතය"
                                    style="height: 24px; font-size: 11px; padding: 2px 6px;" required>
                                @error('customer_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>





                            <div class="col-md-3" style="margin-bottom: 2px;">

                                <input type="text" id="grn_display" class="form-control form-control-sm"
                                    placeholder="Select GRN Entry..." readonly
                                    style="height: 24px; font-size: 11px; padding: 2px 6px; display: none;">
                                <select id="grn_select" class="form-select form-select-sm select2">
                                    <option value="">-- Select GRN Entry --</option>
                                    @foreach ($entries as $entry)
                                        <option value="{{ $entry->code }}" data-supplier-code="{{ $entry->supplier_code }}"
                                            data-code="{{ $entry->code }}" data-item-code="{{ $entry->item_code }}"
                                            data-item-name="{{ $entry->item_name }}" data-weight="{{ $entry->weight }}"
                                            data-price="{{ $entry->price_per_kg }}" data-total="{{ $entry->total }}"
                                            data-packs="{{ $entry->packs }}" data-grn-no="{{ $entry->grn_no }}"
                                            data-txn-date="{{ $entry->txn_date }}">
                                            {{-- The text inside the option tag is less important for templateResult,
                                            but good for fallback/accessibility --}}
                                            {{ $entry->code }} | {{ $entry->supplier_code }} | {{ $entry->item_code }} |
                                            {{ $entry->item_name }} | {{ $entry->packs }} | {{ $entry->grn_no }} |
                                            {{ $entry->txn_date }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="customer_name" id="customer_name_hidden"
                                value="{{ old('customer_name') }}">
                        </div>

                        <hr style="margin: 0.1rem 0; height: 1px;">

                        <div class="row g-1 form-row">
                            <div class="col-md-3 mb-1">
                                <select name="supplier_code_display" id="supplier_code_display"
                                    class="form-select form-select-sm @error('supplier_code') is-invalid @enderror" disabled
                                    style="height: 24px; font-size: 11px; color: #888; padding: 2px 6px;">
                                    <option value="" disabled selected>සැපයුම්කරු (Supplier)</option>
                                    @php $currentSupplierCode = old('supplier_code', $sale->supplier_code ?? ''); @endphp
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->code }}" {{ $currentSupplierCode == $supplier->code ? 'selected' : '' }}>
                                            {{ $supplier->name }} ({{ $supplier->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="supplier_code" id="supplier_code"
                                    value="{{ $currentSupplierCode }}">
                                @error('supplier_code')
                                    <div class="invalid-feedback" style="font-size: 0.8rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-1">
                                <input type="hidden" name="item_code" value="{{ old('item_code') }}">
                                <select id="item_select"
                                    class="form-select form-select-sm @error('item_code') is-invalid @enderror" disabled
                                    style="height: 24px; font-size: 11px; color: #888; padding: 2px 6px;">
                                    <option value="" disabled selected>අයිතමය තෝරන්න (Select Item)</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->item_code }}" data-code="{{ $item->code }}"
                                            data-item-code="{{ $item->item_code }}" data-item-name="{{ $item->item_name }}" {{ old('item_code') == $item->item_code ? 'selected' : '' }}>
                                            ({{ $item->item_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('item_code')
                                    <div class="invalid-feedback" style="font-size: 0.8rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-1">
                                <label for="item_name_display_from_grn" class="form-label visually-hidden">Item Name</label>
                                <input type="text" id="item_name_display_from_grn" class="form-control form-control-sm"
                                    readonly placeholder="අයිතමයේ නම (Item Name)"
                                    style="height: 24px; font-size: 11px; background-color: #e9ecef; color: #888; padding: 2px 6px;">
                            </div>

                            <input type="hidden" name="code" id="code" value="{{ old('code') }}">
                            <input type="hidden" name="item_name" id="item_name" value="{{ old('item_name') }}">

                            <div class="d-flex justify-content-between align-items-center"
                                style="gap: 10px; margin-top: -10px;">
                                <div class="col-auto" style="max-width: 110px;">
                                    <input type="number" name="weight" id="weight" step="0.01"
                                        class="form-control form-control-sm @error('weight') is-invalid @enderror"
                                        value="{{ old('weight') }}" placeholder="බර (kg)"
                                        style="height: 24px; font-size: 11px; padding: 2px 6px; color: #888;" required>
                                    @error('weight')
                                        <div class="invalid-feedback" style="font-size: 0.8rem;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-auto" style="max-width: 115px;">
                                    <input type="number" name="price_per_kg" id="price_per_kg" step="0.01"
                                        class="form-control form-control-sm @error('price_per_kg') is-invalid @enderror"
                                        value="{{ old('price_per_kg') }}" placeholder="මිල (Price/kg)"
                                        style="height: 24px; font-size: 11px; padding: 2px 6px; color: black;" required>
                                    @error('price_per_per_kg')
                                        <div class="invalid-feedback" style="font-size: 0.8rem;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-auto" style="max-width: 115px;">
                                    <input type="number" name="total" id="total"
                                        class="form-control form-control-sm bg-light @error('total') is-invalid @enderror"
                                        value="{{ old('total') }}" placeholder="සමස්ත (Total)" readonly
                                        style="height: 24px; font-size: 11px; padding: 2px 6px; color: black;">
                                    @error('total')
                                        <div class="invalid-feedback" style="font-size: 0.8rem;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-auto" style="max-width: 95px;">
                                    <input type="number" name="packs" id="packs"
                                        class="form-control form-control-sm @error('packs') is-invalid @enderror"
                                        value="{{ old('packs') }}" placeholder="ඇසුරුම් (Packs)"
                                        style="height: 24px; font-size: 11px; padding: 2px 6px; color: black;" required>
                                    @error('packs')
                                        <div class="invalid-feedback" style="font-size: 0.8rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-center mt-4">
                            <input type="hidden" name="sale_id" id="sale_id">
                            <button type="submit" class="btn btn-primary btn-sm shadow-sm d-none" id="addSalesEntryBtn">
                                <i class="material-icons me-2">add_circle_outline</i>Add Sales Entry
                            </button>
                            <button type="button" class="btn btn-success btn-sm shadow-sm" id="updateSalesEntryBtn"
                                style="display:none;">
                                <i class="material-icons me-2">edit</i>Update Sales Entry
                            </button>
                            <button type="button" class="btn btn-danger btn-sm shadow-sm" id="deleteSalesEntryBtn"
                                style="display:none;">
                                <i class="material-icons me-2">delete</i>Delete Sales Entry
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm shadow-sm" id="cancelEntryBtn"
                                style="display:none;">
                                <i class="material-icons me-2">cancel</i>Cancel / New Entry
                            </button>
                        </div>
                    </form>



                    {{-- Main Sales Table - ALWAYS RENDERED --}}
                    <div class="mt-0">


                        <div class="table-responsive">
                            <table class="table table-bordered table-hover shadow-sm rounded-3 overflow-hidden"
                                style="font-size: 0.85rem; margin-top: -10px;">

                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">කේතය</th>
                                        <th scope="col">අයිතම කේතය</th>
                                        <th scope="col">අයිතමය</th>
                                        <th scope="col">බර (kg)</th>
                                        <th scope="col">මිල/කිලෝග්‍රෑමය</th>
                                        <th scope="col">සමස්ත</th>
                                        <th scope="col">මලු</th>
                                    </tr>
                                </thead>
                                <tbody id="mainSalesTableBody">
                                    {{-- This tbody will be dynamically populated by JavaScript. --}}
                                    {{-- IMPORTANT: Ensure your JavaScript populating this table adds 'data-sale-id',
                                    'data-customer-code', and 'data-customer-name' attributes to each <tr> --}}
                                        {{-- Example:
                                    <tr data-sale-id="123" data-customer-code="CUST001" data-customer-name="John Doe">...
                                    </tr> --}}
                                </tbody>
                            </table>
                            <h5 class="text-end mb-3" style="font-size: 0.85rem;">
                                <strong>Total Sales Value:</strong> Rs. <span
                                    id="mainTotalSalesValue">{{ number_format($totalSum, 2) }}</span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NEW SECTION: Unprinted Sales Records (bill_printed = 'N') - Right Column --}}
            <div class="col-md-3"> {{-- You can change to col-md-2 if needed for smaller width --}}
                <div class="card shadow-sm border-0 rounded-3 p-3"> {{-- Reduced padding from p-4 to p-3 --}}
                    <h6 class="mb-2 text-center">මුද්‍රණය නොකළ විකුණුම් වාර්තා</h6>

                    {{-- 🔍 Search Bar --}}
                    <input type="text" id="searchUnprintedCustomerCode" class="form-control form-control-sm mb-2"
                        placeholder="Search by customer code...">

                    @if ($salesNotPrinted->count())
                        <div class="unprinted-sales-list">
                            <ul id="unprintedSalesList">
                                {{-- Loop over each CUSTOMER GROUP for unprinted sales --}}
                                @foreach ($salesNotPrinted as $customerCode => $salesForCustomer)
                                    @php
                                        $firstSaleForCustomer = $salesForCustomer->first();
                                        $customerName = $firstSaleForCustomer->customer_name;
                                        $totalCustomerSalesAmount = $salesForCustomer->sum('total');
                                    @endphp
                                    <li data-customer-code="{{ $customerCode }}">
                                        <div class="customer-header bill-clickable" data-customer-code="{{ $customerCode }}"
                                            data-customer-name="{{ $customerName }}" data-bill-no="" data-bill-type="unprinted"
                                            style="font-size: 11px; padding: 2px 6px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #ddd; margin-bottom: 3px; border-radius: 4px; background-color: #f9f9f9;">

                                            <span style="flex: 1;">
                                                ({{ $customerCode }}) - Rs. {{ number_format($totalCustomerSalesAmount, 2) }}
                                            </span>

                                            <i class="material-icons arrow-icon" style="font-size: 14px;">keyboard_arrow_right</i>
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


            {{-- JavaScript Includes (jQuery and Select2 should always be loaded before your custom script that uses them)
            --}}
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            {{-- Ensure Bootstrap JS is loaded for collapse --}}
             <script>
                // ... (existing JavaScript code, including Select2 initializations and other event listeners) ...

                // NEW: Search functionality for Printed Sales Records
                document.getElementById('searchCustomerCode').addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const printedSalesList = document.getElementById('printedSalesList');
                    const customerGroups = printedSalesList.querySelectorAll('li[data-customer-code]');

                    customerGroups.forEach(customerGroup => {
                        let customerGroupHasVisibleBills = false;
                        const billItems = customerGroup.querySelectorAll('li > .customer-header.bill-clickable');

                        billItems.forEach(billItem => {
                            const billNoElement = billItem.querySelector('span'); // The span containing "Bill No: ..."
                            const billNoText = billNoElement ? billNoElement.textContent.toLowerCase() : '';

                            if (billNoText.includes(searchTerm)) {
                                billItem.style.display = 'flex'; // Show the bill
                                customerGroupHasVisibleBills = true;
                            } else {
                                billItem.style.display = 'none'; // Hide the bill
                            }
                        });

                        // Show/hide the customer group header based on whether any bills within it are visible
                        // You need a way to target the customer group header explicitly.
                        // For now, if no bills are visible, hide the whole customer group li
                        if (customerGroupHasVisibleBills) {
                            customerGroup.style.display = 'block'; // Or 'list-item'
                        } else {
                            customerGroup.style.display = 'none';
                        }
                    });
                });


                // ... (rest of your existing JavaScript code) ...
            </script>

            <script>
                // For Printed Sales (already added earlier)
                document.getElementById('searchCustomerCode').addEventListener('input', function () {
                    const searchValue = this.value.toLowerCase();
                    document.querySelectorAll('#printedSalesList > li').forEach(li => {
                        const code = li.getAttribute('data-customer-code').toLowerCase();
                        li.style.display = code.includes(searchValue) ? '' : 'none';
                    });
                });

                // For Unprinted Sales (this is new)
                document.getElementById('searchUnprintedCustomerCode').addEventListener('input', function () {
                    const searchValue = this.value.toLowerCase();
                    document.querySelectorAll('#unprintedSalesList > li').forEach(li => {
                        const code = li.getAttribute('data-customer-code').toLowerCase();
                        li.style.display = code.includes(searchValue) ? '' : 'none';
                    });
                });
            </script>



            {{-- ALL Custom JavaScript Consolidated Here --}}
            <script>
                // --- Form Calculations & Select2 Interactions ---
                const itemSelect = document.getElementById('item_select');
                const codeField = document.getElementById('code');
                // const itemCodeField = document.getElementById('item_code'); // This is no longer strictly needed if the hidden input inside item_select div is used.
                const itemNameField = document.getElementById('item_name'); // This is the hidden field
                const supplierSelect = document.getElementById('supplier_code');
                const supplierDisplaySelect = document.getElementById('supplier_code_display'); // Add this line
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

                // NEW: Get reference to the new item name display field
                const itemNameDisplayFromGrn = document.getElementById('item_name_display_from_grn');


                function calculateTotal() {
                    const weight = parseFloat(weightField.value) || 0;
                    const price = parseFloat(pricePerKgField.value) || 0;
                    totalField.value = (weight * price).toFixed(2);
                }

                // This listener is mostly for internal consistency if itemSelect.value is set programmatically.
                // The main item_name population will now come from grn_select.
                itemSelect.addEventListener('change', function () {
                    const selected = this.options[this.selectedIndex];
                    if (selected && selected.dataset) {
                        codeField.value = selected.dataset.code || '';
                        // itemNameField.value = selected.dataset.itemName || ''; // We will now get item_name from GRN select
                        // Ensure the hidden item_code is updated when item_select value changes programmatically
                        document.querySelector('input[name="item_code"]').value = selected.dataset.itemCode || '';
                    } else {
                        codeField.value = '';
                        // itemNameField.value = ''; // We will now get item_name from GRN select
                        document.querySelector('input[name="item_code"]').value = '';
                    }
                });


                weightField.addEventListener('input', calculateTotal);
                pricePerKgField.addEventListener('input', calculateTotal);
                calculateTotal(); // Initial calculation on page load

                $(document).ready(function () {
                    // Initialize Select2 for GRN with custom templateResult and templateSelection
                    $('#grn_select').select2({
                        dropdownParent: $('#grn_select').parent(),
                        placeholder: "-- Select GRN Entry --",
                        width: '100%',
                        allowClear: true,
                        minimumResultsForSearch: 0, // Set to 0 to enable search but still use templateResult
                        templateResult: function (data, container) {
                            // If it's the placeholder, loading message, or has no ID, just return the text
                            if (data.loading || !data.id) {
                                return data.text;
                            }

                            // Get the raw option element to access data-attributes
                            const option = $(data.element);

                            // Extract data from data-attributes
                            const code = option.data('code');
                            const supplierCode = option.data('supplierCode');
                            const itemCode = option.data('itemCode');
                            const itemName = option.data('itemName');
                            const packs = option.data('packs');
                            const grnNo = option.data('grnNo');
                            const weight =option.data('weight');

                            const txnDate = option.data('txnDate');

                            // Construct the HTML for the tabular display for each row (data row only)
                            const $result = $(`
                                                                        <div class="grn-option-row">
                                                                            <div class="grn-column grn-code"><strong>${code || ''}</strong></div>
                                                                            <div class="grn-column grn-supplier-code">${supplierCode || ''}</div>

                                                                            <div class="grn-column grn-packs">${packs || 0}</div>
                                                                            <div class="grn-column grn-grn-no">${weight || ''}</div>
                                                                            <div class="grn-column grn-txn-date">${txnDate || ''}</div>
                                                                        </div>
                                                                    `);
                            return $result;
                        },
                        templateSelection: function (data) {
                            // This defines how the selected item looks in the main Select2 input field
                            if (!data.id) {
                                return data.text; // For the initial placeholder
                            }
                            const option = $(data.element);
                            const code = option.data('code');
                            const supplierCode = option.data('supplierCode');
                            const itemCode = option.data('itemCode');
                            const itemName = option.data('itemName');
                            const packs = option.data('packs');
                            const grnNo = option.data('grnNo');
                            const weight=option.data('weight');
                            const txnDate = option.data('txnDate');
                            // Format the selected value as a single line
                           return `${code || ''}         (KG: ${weight || 0} / MALU: ${packs || ''} / Dinaya: ${txnDate || ''})`;


                        }
                    });

                    // Add an event listener for when the Select2 dropdown opens
                    $('#grn_select').on('select2:open', function () {
                        console.log("Select2 dropdown opened. Attempting to add header..."); // Debugging log

                        // Find the main dropdown container
                        const $dropdown = $('.select2-dropdown');

                        // Find the results container where options are listed
                        const $resultsContainer = $dropdown.find('.select2-results__options');

                        // Check if the header wrapper already exists within this dropdown instance
                        if ($dropdown.find('.grn-header-row-wrapper').length === 0) {
                            console.log("Header not found, creating and prepending."); // Debugging log

                            const $headerWrapper = $(`
                                                                        <div class="grn-header-row-wrapper">
                                                                            <div class="grn-option-row grn-header-row">
                                                                                <div class="grn-column grn-code">Code</div>
                                                                                <div class="grn-column grn-supplier-code">Sup...</div>

                                                                                <div class="grn-column grn-packs">Packs</div>
                                                                                <div class="grn-column grn-grn-no">Weight</div>
                                                                                <div class="grn-column grn-txn-date">Date</div>
                                                                            </div>
                                                                        </div>
                                                                    `);

                            // Prepend the header wrapper to the .select2-results element
                            // This puts it before the <ul> which contains the actual options
                            $dropdown.find('.select2-results').prepend($headerWrapper);

                            // Add a class to the results options UL to give it padding at the top,
                            // so options don't hide behind the sticky header.


                        } else {
                            console.log("Header already exists, not re-adding."); // Debugging log
                        }
                    });


                    $('#customer_code_select').select2({
                        dropdownParent: $('#customer_code_select').parent(),
                        placeholder: "-- Select Customer --",
                        width: '100%',
                        allowClear: true,
                        templateResult: function (data) {
                            if (data.loading) return data.text;
                            if (!data.id) return data.text;
                            return $(
                                `<span>${$(data.element).data('customer-name')} (${$(data.element).data('customer-code')})</span>`
                            );
                        },
                        templateSelection: function (data) {
                            if (!data.id) return data.text; // Return placeholder text if nothing is selected
                            return $(
                                `<span>${$(data.element).data('customer-name')} (${$(data.element).data('customer-code')})</span>`
                            );
                        }
                    });


                    // Handle click on grn_display to open Select2 dropdown
                    $('#grn_display').on('click', function () {
                        $('#grn_select').select2('open');
                    });

                    // Event listener for when a Select2 option is selected for GRN
                    $('#grn_select').on('select2:select', function (e) {
                        const selectedOption = $(e.params.data
                            .element); // Get the raw <option> element
                        const data = selectedOption.data(); // Access its data attributes

                        // Update the read-only grn_display field with the formatted string
                        const grnCodeForDisplay = data.code || '';
                        const supplierCodeForDisplay = data.supplierCode || '';
                        const itemCodeForDisplay = data.itemCode || '';
                        const itemNameForDisplay = data.itemName || '';
                        const packsForDisplay = data.packs || '';
                        const grnNoForDisplay = data.grnNo || '';
                        const txnDateForDisplay = data.txnDate || '';
                        grnDisplay.value =
                            `${grnCodeForDisplay}| ${supplierCodeForDisplay}  | ${packsForDisplay} | ${grnNoForDisplay}`;

                        // Populate other form fields using the data attributes
                        supplierSelect.value = data.supplierCode || ''; // Hidden input for supplier_code
                        supplierDisplaySelect.value = data.supplierCode || ''; // Display select for supplier_code

                        itemSelect.value = data.itemCode || ''; // Set item code in disabled select
                        itemSelect.dispatchEvent(new Event('change')); // Trigger change to update hidden item_code

                        itemNameDisplayFromGrn.value = data.itemName || ''; // Populate the dedicated item name display field
                        itemNameField.value = data.itemName || ''; // Also set the hidden item_name field

                        weightField.value = '';
                        pricePerKgField.value = '';
                        packsField.value = '';
                        calculateTotal();
                        weightField.focus();
                    });

                    $('#customer_code_select').on('select2:select', function (e) {
                        const selectedOption = $(e.currentTarget).find('option:selected');
                        const selectedCustomerCode = selectedOption.val();
                        const selectedCustomerName = selectedOption.data('customer-name');

                        newCustomerCodeField.value = selectedCustomerCode || '';
                        newCustomerCodeField.readOnly = true;
                        customerNameField.value = selectedCustomerName || '';

                        $('#grn_select').select2('open');
                    });

                    newCustomerCodeField.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            $('#grn_select').select2('open');
                        }
                    });

                    // Clear GRN selection and related fields
                    $('#grn_select').on('select2:clear', function () {
                        grnDisplay.value = 'Select GRN Entry...'; // Reset display field placeholder
                        supplierSelect.value = '';
                        supplierDisplaySelect.value = ''; // Clear display select
                        itemSelect.value = '';
                        itemSelect.dispatchEvent(new Event('change')); // Clear item related hidden fields
                        itemNameDisplayFromGrn.value = ''; // NEW: Clear the item name display field
                        itemNameField.value = ''; // NEW: Clear hidden item_name field
                        weightField.value = '';
                        pricePerKgField.value = '';
                        packsField.value = '';
                        calculateTotal();
                    });

                    $('#customer_code_select').on('select2:clear', function () {
                        newCustomerCodeField.value = '';
                        newCustomerCodeField.readOnly = false;
                        customerNameField.value = '';
                    });

                    $('#new_customer_code').on('input', function () {
                        if ($(this).val() !== '') {
                            $('#customer_code_select').val(null).trigger('change');
                            customerNameField.value = '';
                        }
                    });

                    // Handle old input values on page load
                    $(document).ready(function () {
                        $(document).on('select2:open', function () {
                            const searchField = document.querySelector('.select2-search__field');
                            if (searchField) {
                                searchField.focus();
                            }
                        });

                        @if (old('customer_code_select') || old('customer_code'))
                            const oldGrnCode = "{{ old('code') }}";
                            const oldSupplierCode = "{{ old('supplier_code') }}";
                            const oldItemCode = "{{ old('item_code') }}";
                            const oldItemName = "{{ old('item_name') }}";
                            const oldWeight = "{{ old('weight') }}";
                            const oldPricePerKg = "{{ old('price_per_kg') }}";
                            const oldPacks = "{{ old('packs') }}";
                            const oldGrnOption = $('#grn_select option').filter(function () {
                                return $(this).val() === oldGrnCode &&
                                    $(this).data('supplierCode') === oldSupplierCode &&
                                    $(this).data('itemCode') === oldItemCode;
                            });

                            if (oldGrnOption.length) {
                                $('#grn_select').val(oldGrnOption.val()).trigger('change.select2');
                                grnDisplay.value = oldGrnOption.data('code') || '';
                                itemNameDisplayFromGrn.value = oldGrnOption.data('itemName') || '';
                                itemNameField.value = oldGrnOption.data('itemName') || '';
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
                    function populateSalesTable(salesArray) {
                        const tableBody = document.getElementById('mainSalesTableBody');
                        tableBody.innerHTML = ''; // Clear existing rows

                        if (!salesArray || salesArray.length === 0) {
                            tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No sales records found.</td></tr>';
                            return;
                        }

                        salesArray.forEach(sale => {
                            const row = document.createElement('tr');
                            // --- CRITICAL: Add data- attributes to the row for easy retrieval by F1 function ---
                            row.setAttribute('data-sale-id', sale.id);
                            row.setAttribute('data-customer-code', sale.customer_code);
                            row.setAttribute('data-customer-name', sale.customer_name || 'N/A'); // Ensure customer_name exists

                            row.innerHTML = `
                                                                <td>${sale.code}</td>
                                                                <td>${sale.item_code}</td>
                                                                <td>${sale.item_name}</td>
                                                                <td>${(parseFloat(sale.weight) || 0).toFixed(2)}</td>
                                                                <td>${(parseFloat(sale.price_per_kg) || 0).toFixed(2)}</td>
                                                                <td>${(parseFloat(sale.total) || 0).toFixed(2)}</td>
                                                                <td>${sale.packs}</td>
                                                            `;
                            tableBody.appendChild(row);
                        });
                    }


                    // --- JavaScript for F1 and F5 Key Presses ---
                    document.addEventListener('keydown', function (e) {
                        console.log('Key pressed:', e.key);

                        if (e.key === "F1") {
                            e.preventDefault();
                            console.log('F1 key pressed - attempting to print and mark sales by customer...');

                            // --- START OF MODIFIED SECTION: Read data directly from the table ---
                            const salesDataForReceipt = [];
                            const tableRows = document.querySelectorAll('#mainSalesTableBody tr');

                            // Check if the table is empty or only contains the "No sales records found" message
                            if (tableRows.length === 0 || (tableRows.length === 1 && tableRows[0].querySelector('td[colspan="7"]'))) {
                                alert('No sales records in the table to print!');
                                return;
                            }

                            tableRows.forEach(row => {
                                // Ensure it's a data row, not a placeholder message row
                                if (row.hasAttribute('data-sale-id')) {
                                    const customerCode = row.getAttribute('data-customer-code');
                                    const customerName = row.getAttribute('data-customer-name');
                                    const saleId = row.getAttribute('data-sale-id');

                                    // Extract data from cells - adjust indices if your column order changes
                                    const cells = row.querySelectorAll('td');
                                    const code = cells[0] ? cells[0].textContent : ''; // GRN Code
                                    const itemCode = cells[1] ? cells[1].textContent : '';
                                    const itemName = cells[2] ? cells[2].textContent : '';
                                    const weight = cells[3] ? parseFloat(cells[3].textContent) : 0;
                                    const pricePerKg = cells[4] ? parseFloat(cells[4].textContent) : 0;
                                    const total = cells[5] ? parseFloat(cells[5].textContent) : 0;
                                    const packs = cells[6] ? parseInt(cells[6].textContent, 10) : 0;

                                    salesDataForReceipt.push({
                                        id: saleId,
                                        customer_code: customerCode,
                                        customer_name: customerName,
                                        code: code,
                                        item_code: itemCode,
                                        item_name: itemName,
                                        weight: weight,
                                        price_per_kg: pricePerKg,
                                        total: total,
                                        packs: packs
                                    });
                                }
                            });
                            // --- END OF MODIFIED SECTION ---

                            if (salesDataForReceipt.length === 0) {
                                alert('No printable sales records found in the table!');
                                return;
                            }

                            if (!confirm('Do you want to print the current sales (only one customer group from table)?')) {
                                console.log('Print action cancelled by user.');
                                return;
                            }

                            // Group sales by customer_code (This part remains unchanged)
                            const salesByCustomer = salesDataForReceipt.reduce((acc, sale) => {
                                const customerCode = sale.customer_code;
                                if (!acc[customerCode]) {
                                    acc[customerCode] = [];
                                }
                                acc[customerCode].push(sale);
                                return acc;
                            }, {});

                            // Get the first customer group only (assuming F1 prints the first group of unprinted sales)
                            const firstCustomerCode = Object.keys(salesByCustomer)[0];

                            if (!firstCustomerCode) {
                                alert("No customer group found in the table data.");
                                return;
                            }

                            const customerSales = salesByCustomer[firstCustomerCode];
                            const customerName = customerSales[0].customer_code || 'N/A';
                            const mobile = '0702758908'; // This should ideally be dynamic or from config

                            const now = new Date();
                            const date = now.toLocaleDateString();
                            const time = now.toLocaleTimeString();
                            const random4Digit = Math.floor(1000 + Math.random() * 9000);
                            const billNo = `${random4Digit}`; // <--- This billNo needs to be sent to backend

                            let itemsHtml = '';
                            let totalItemsCount = 0;
                            let totalAmountSum = 0;
                            const allSalesIdsToMarkPrintedAndProcessed = [];

                            // Get the first item's name and packs for the footer note
                            const firstItemName = customerSales.length > 0 ? customerSales[0].item_name : '';
                            const firstItemPacks = customerSales.length > 0 ? customerSales[0].packs : 0;


                            customerSales.forEach(sale => {
                                itemsHtml += `
                                                                    <tr>
                                                                        <td style="text-align: left;">
                                                                          ${sale.item_name} <br>  ${sale.packs}
                                                                          </td>

                                                                        <td style="text-align: right;">${(parseFloat(sale.weight) || 0).toFixed(2)}</td>

                                                                        <td style="text-align: right;">${(parseFloat(sale.price_per_kg) || 0).toFixed(2)}</td>
                                                                        <td style="text-align: right;">${(parseFloat(sale.total) || 0).toFixed(2)}</td>
                                                                    </tr>
                                                                `;
                                totalItemsCount++;
                                totalAmountSum += parseFloat(sale.total);
                                // Collect the sale IDs for marking as processed
                                allSalesIdsToMarkPrintedAndProcessed.push(sale.id);
                            });

                            const salesContent = `
                                                                <div class="receipt-container" style="width: 70mm; margin: 0 auto; padding: 0;">
                                                                    <div class="company-info" style="text-align: center; margin-bottom: 5px;">
                                                                        <h3 style="font-size: 1.2em; margin-bottom: 2px; font-weight: bold;"><span style="font-weight: bold;">C11</span> TGK ට්‍රේඩර්ස්</h3>
                                                                        <p style="white-space: nowrap; margin: 0; line-height: 1.2;">අල, ෆී ළූනු, කුළුබඩු තොග ගෙන්වන්නෝ / බෙදාහරින්නෝ</p>
                                                                        <p style="margin: 0; line-height: 1.2;">වි.ආ.ම. වේයන්ගොඩ</p>
                                                                    </div>





                                                                    <div class="bill-details" style="text-align: left; margin-bottom: 5px;">
                                                                        <table style="width: 100%; font-size: 10px; border-collapse: collapse;">
                                                                            <tr>
                                                                                <td colspan="2" style="text-align: left; padding: 0;">දිනය : ${date}</td>
                                                                                <td colspan="2" style="text-align: right; padding: 0;">${time}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="4" style="text-align: left; padding: 0;">දුර : ${mobile}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" style="text-align: left; padding: 0;">බිල් අංකය : <span style="font-weight: bold;">${billNo}</span></td>
                                                                                <td colspan="2" style="text-align: right; padding: 0;"><span style="font-weight: bold;">${customerName}</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>

                                                                  <hr style="border: none; height: 4px; background-color: black; margin: 5px 0; width: 100%;">


                                                                    <div class="items-section">
                                                                        <table style="width: 100%; font-size: 10px; border-collapse: collapse;">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="text-align: left; padding: 2px 0;">
                                                                                     වර්ගය<br>මලු
                                                                                          </th>
                                                                                           <th style="text-align: right; padding: 2px 0;">කිලෝ</th>


                                                                                    <th style="text-align: right; padding: 2px 0;">මිල</th>
                                                                                    <th style="text-align: right; padding: 2px 0;">අගය</th>
                                                                                </tr>
                                                                            </thead>

                                                                             <tbody>
                        <!-- Simulated <hr> using a full-width table row -->
                         <tr>
                <td colspan="4">
                    <div style="height: 4px; background-color: black; margin: 5px 0; width: 100%;"></div>
                </td>
            </tr>

                        ${itemsHtml}
                    </tbody>
                                                                        </table>
                                                                    </div>

                                                                    <hr style="border: none; height: 4px; background-color: black; margin: 5px 0; width: 100%;">


                                                                    <div class="summary-section" style="text-align: left; margin-bottom: 5px;">
                                                                        <table style="width: 100%; font-size: 10px; border-collapse: collapse;">

                                                                            <tr>
                                                                                <td colspan="3" style="text-align: left; padding: 0;">අගය :</td>
                                                                                <td style="text-align: right; font-weight: bold; font-size: 12px; border-top: 1px solid black; border-bottom: 3px double black; padding: 0;">
                                                                                    ${totalAmountSum.toFixed(2)}
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>

                                                                   <hr style="border: none; height: 4px; background-color: black; margin: 5px 0; width: 100%;">


                                                                    <div class="footer-section" style="text-align: center; margin-top: 10px;">

                                                                        <p style="margin: 0; line-height: 1.2;">භාණ්ඩ පරීක්ෂාකර බලා රැගෙන යන්න</p>
                                                                        <p style="margin: 0; line-height: 1.2;">නැවත භාර ගනු නොලැබේ</p>
                                                                    </div>
                                                                </div>
                                                            `;

                            const printWindow = window.open('', '_blank', 'width=400,height=600');
                            printWindow.document.write(`
                                                              <html>
                                                <head>
                                                    <title>විකුණුම් කුපිත්තුව - ${customerName}</title>
                                                    <style>
                                                        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;700&display=swap');
                                                        body {
                                                            font-family: 'Noto Sans Sinhala', sans-serif;
                                                            margin: 0;
                                                            padding: 5mm;
                                                            font-size: 10px;
                                                            line-height: 1.2;
                                                            overflow: hidden; /* Prevent scrollbars in print preview */
                                                        }
                                                        .receipt-container {
                                                            width: 100%;
                                                            max-width: 70mm;
                                                            margin-left: 0;
                                                            margin-right: auto;
                                                            border: none;
                                                            padding: 0;
                                                            text-align: left; /* Changed from default to left */
                                                        }
                                                        .company-info {
                                                            text-align: left; /* Changed from center to left */
                                                            margin-bottom: 5px;
                                                        }
                                                        .company-info h3 {
                                                            font-size: 1.2em;
                                                            margin-bottom: 2px;
                                                            font-weight: bold;
                                                        }
                                                        .company-info p {
                                                            margin: 0;
                                                            line-height: 1.2;
                                                        }
                                                        .bill-details, .summary-section, .footer-section {
                                                            text-align: left;
                                                            margin-bottom: 5px;
                                                        }
                                                        .bill-details p, .summary-section p {
                                                            margin: 0;
                                                            line-height: 1.2;
                                                            display: flex;
                                                            justify-content: space-between;
                                                        }
                                                        .bill-details p span:first-child, .summary-section p span:first-child {
                                                            text-align: left;
                                                            font-weight: normal;
                                                        }
                                                        .bill-details p span:last-child, .summary-section p span:last-child {
                                                            text-align: right;
                                                            font-weight: bold;
                                                        }
                                                        .customer-name-on-bill {
                                                            text-align: left; /* Changed from center to left */
                                                            font-weight: bold;
                                                            margin-top: 5px;
                                                        }
                                                        .divider {
                                                            border-top: 1px dashed #000;
                                                            margin: 8px 0;
                                                        }
                                                        .items-section table {
                                                            width: 100%;
                                                             border-bottom: none;
                                                            font-size: 10px;
                                                        }
                                                        .items-section th, .items-section td {
                                                            padding: 2px 0;
                                                            text-align: right;
                                                            border-bottom: none;
                                                        }
                                                        .items-section th {
                                                            font-weight: bold;
                                                            text-align: center;

                                                        }
                                                        .col-item {
                                                            text-align: left;
                                                            width: 40%;
                                                        }
                                                        .col-qty {
                                                            width: 20%;
                                                        }
                                                        .col-rate {
                                                            width: 20%;
                                                        }
                                                        .col-value {
                                                            width: 20%;
                                                        }
                                                        .grand-total {
                                                            font-size: 1.1em;
                                                            font-weight: bold;
                                                            border-top: 1px dashed #000;
                                                            padding-top: 5px;
                                                            margin-top: 5px;
                                                        }
                                                        .footer-section {
                                                            text-align: left; /* Changed from center to left */
                                                            margin-top: 10px;
                                                        }
                                                        .footer-section p {
                                                            margin: 0;
                                                            line-height: 1.2;
                                                        }
                                                    </style>
                                                </head>
                                                <body>
                                                    <div class="receipt-container">
                                                        ${salesContent}
                                                    </div>
                                                </body>
                                                </html>

                                                            `);
                            printWindow.document.close();
                            printWindow.focus();
                            printWindow.print();

                            const checkClosed = setInterval(function () {
                                if (printWindow.closed) {
                                    clearInterval(checkClosed);
                                    console.log(`Print window closed. Sending request to mark sales as printed and processed.`);

                                    // Only send the fetch request if there are IDs to process
                                    if (allSalesIdsToMarkPrintedAndProcessed.length > 0) {
                                        fetch("{{ route('sales.markAsPrinted') }}", {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({
                                                sales_ids: allSalesIdsToMarkPrintedAndProcessed,
                                                bill_no: billNo // <--- IMPORTANT: Sending the generated billNo to backend
                                            })
                                        })
                                            .then(response => {
                                                if (!response.ok) {
                                                    return response.text().then(text => {
                                                        throw new Error(`HTTP error! status: ${response.status}, message: ${text}`);
                                                    });
                                                }
                                                return response.json();
                                            })
                                            .then(data => {
                                                console.log('Sales marked as printed and processed:', data);
                                                sessionStorage.setItem('focusOnCustomerSelect', 'true');
                                                window.location.reload();
                                            })
                                            .catch(error => {
                                                console.error('Error marking sales as printed and processed:', error);
                                                alert('Failed to mark sales as printed. Please check console for details.');
                                            });
                                    } else {
                                        console.log("No sales IDs were collected for marking as processed (likely no unprocessed sales for the customer). Reloading page.");
                                        sessionStorage.setItem('focusOnCustomerSelect', 'true'); // Still reload to ensure fresh state
                                        window.location.reload();
                                    }
                                }
                            }, 500);
                        }



                        else if (e.key === "F5") {
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
                                            throw new Error(
                                                `HTTP error! status: ${response.status}, message: ${text}`)
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
                                // Construct the row HTML string, ensuring data-id, data-customer-code, data-customer-name are present
                                rowsHtml += `
                                                                        <tr data-sale-id="${sale.id}" data-id="${sale.id}" data-customer-code="${sale.customer_code}" data-customer-name="${sale.customer_name}">
                                                                            <td>${sale.code || 'N/A'}</td>
                                                                            <td>${sale.item_code || 'N/A'}</td>
                                                                            <td>${sale.item_name || 'N/A'}</td>
                                                                            <td>${(parseFloat(sale.weight) || 0).toFixed(2)}</td>
                                                                            <td>${(parseFloat(sale.price_per_kg) || 0).toFixed(2)}</td>
                                                                            <td>${(parseFloat(sale.total) || 0).toFixed(2)}</td>
                                                                            <td>${(parseFloat(sale.packs) || 0).toFixed(0)}</td>

                                                                        </tr>
                                                                    `;
                                totalSalesValue += parseFloat(sale.total || 0);
                            });
                        }

                        console.log("Attempting to set tbody HTML with:", rowsHtml);

                        mainSalesTableBodyElement.innerHTML = rowsHtml;

                        $('#mainTotalSalesValue').text(totalSalesValue.toFixed(2));
                        console.log("populateMainSalesTable finished. Total sales value:", totalSalesValue.toFixed(2));
                        console.log("Current tbody HTML after setting:", mainSalesTableBodyElement.innerHTML);
                    }

                    populateMainSalesTable(allSalesData);

                    function populateFormForEdit(sale) {
                        console.log("Populating form for sale:", sale);
                        saleIdField.value = sale.id;
                        newCustomerCodeField.value = sale.customer_code || '';
                        customerNameField.value = sale.customer_name || '';
                        newCustomerCodeField.readOnly = true;

                        if (sale.customer_code) {
                            $('#customer_code_select').val(sale.customer_code).trigger('change.select2');
                            console.log("Setting customer_code_select to:", sale.customer_code);
                        } else {
                            $('#customer_code_select').val(null).trigger('change.select2');
                            console.log("Clearing customer_code_select.");
                        }

                        grnDisplay.value = sale.code || '';
                        const grnOption = $('#grn_select option').filter(function () {
                            return $(this).val() === sale.code && $(this).data('supplierCode') === sale.supplier_code &&
                                $(this).data('itemCode') === sale.item_code;
                        });
                        if (grnOption.length) {
                            $('#grn_select').val(grnOption.val()).trigger('change.select2');
                            console.log("Setting grn_select to:", grnOption.val());
                        } else {
                            $('#grn_select').val(null).trigger('change.select2');
                            console.log("Clearing grn_select.");
                        }

                        supplierSelect.value = sale.supplier_code || '';
                        supplierDisplaySelect.value = sale.supplier_code || '';
                        itemSelect.value = sale.item_code || '';
                        itemSelect.dispatchEvent(new Event('change'));
                        console.log("Setting supplier_code to:", sale.supplier_code, "and item_select to:", sale.item_code);

                        itemNameDisplayFromGrn.value = sale.item_name || '';
                        itemNameField.value = sale.item_name || '';
                        console.log("Setting item name display to:", itemNameDisplayFromGrn.value);


                        weightField.value = parseFloat(sale.weight || 0).toFixed(2);
                        pricePerKgField.value = parseFloat(sale.price_per_kg || 0).toFixed(2);
                        packsField.value = parseInt(sale.packs || 0);
                        calculateTotal();
                        console.log("Weight:", weightField.value, "Price:", pricePerKgField.value, "Packs:", packsField.value);


                        salesEntryForm.action = `sales/update/${sale.id}`;
                        console.log("Form action set to:", salesEntryForm.action);

                        addSalesEntryBtn.style.display = 'none';
                        updateSalesEntryBtn.style.display = 'inline-block';
                        deleteSalesEntryBtn.style.display = 'inline-block';
                        cancelEntryBtn.style.display = 'inline-block';
                        console.log("Buttons updated for edit mode.");
                    }

                    function resetForm() {
                        console.log("Resetting form...");
                        salesEntryForm.reset();
                        saleIdField.value = '';
                        newCustomerCodeField.readOnly = false;
                        $('#customer_code_select').val(null).trigger('change.select2');
                        $('#grn_select').val(null).trigger('change.select2');
                        grnDisplay.value = 'Select GRN Entry...';
                        supplierSelect.value = '';
                        supplierDisplaySelect.value = '';
                        itemSelect.value = '';
                        itemSelect.dispatchEvent(new Event('change'));
                        itemNameDisplayFromGrn.value = '';
                        itemNameField.value = '';
                        calculateTotal();

                        salesEntryForm.action = "{{ route('grn.store') }}";

                        addSalesEntryBtn.style.display = 'inline-block';
                        updateSalesEntryBtn.style.display = 'none';
                        deleteSalesEntryBtn.style.display = 'none';
                        cancelEntryBtn.style.display = 'none';

                        newCustomerCodeField.focus();
                        console.log("Form reset complete.");
                    }

                    document.getElementById('mainSalesTableBody').addEventListener('click', function (event) {
                        const clickedRow = event.target.closest('tr[data-sale-id]');
                        if (clickedRow) {
                            const saleId = clickedRow.dataset.saleId;
                            console.log("Row clicked, sale ID:", saleId);
                            const saleToEdit = currentDisplayedSalesData.find(sale => String(sale.id) === String(saleId));
                            if (saleToEdit) {
                                console.log("Sale found in currentDisplayedSalesData for ID:", saleId, saleToEdit);
                                populateFormForEdit(saleToEdit);
                            } else {
                                console.warn("Sale NOT found in currentDisplayedSalesData for ID:", saleId);
                                alert(
                                    "Could not find this record for editing. It might not be in the currently displayed sales list. Please try reloading the page if this persists."
                                );
                            }
                        }
                    });

                    updateSalesEntryBtn.addEventListener('click', function () {
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
                        data['_method'] = 'PUT';
                        data['_token'] = '{{ csrf_token() }}';

                        fetch(`sales/update/${saleId}`, {
                            method: 'POST',
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

                    deleteSalesEntryBtn.addEventListener('click', function () {
                        const saleId = saleIdField.value;
                        if (!saleId) {
                            alert('No record selected for deletion.');
                            return;
                        }

                        if (!confirm('Are you sure you want to delete this sales record?')) {
                            return;
                        }

                        fetch(`sales/delete/${saleId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                _method: 'DELETE',
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


                    cancelEntryBtn.addEventListener('click', resetForm);

                    resetForm();

                    $('.customer-header').on('click', function () {
                        console.log("Customer header clicked!");

                        const customerCode = $(this).data('customer-code');
                        const billType = $(this).data('bill-type');
                        const billNo = $(this).data('bill-no'); // This will now correctly have a value or ''

                        console.log("Clicked Customer Code:", customerCode);
                        console.log("Clicked Bill Type:", billType);
                        console.log("Clicked Bill No:", billNo);
                        newCustomerCodeField.value = customerCode;

                        let salesToDisplay = [];

                        if (billType === 'printed') {
                            console.log("Attempting to filter PRINTED sales...");
                            if (printedSalesData[customerCode] && Array.isArray(printedSalesData[customerCode])) {
                                salesToDisplay = printedSalesData[customerCode].filter(sale => {
                                    // Ensure both are treated as strings for comparison
                                    return String(sale.bill_no) === String(billNo);
                                });
                                console.log("Printed sales data for customerCode:", printedSalesData[customerCode]);
                            } else {
                                console.log("No printed sales data found or not an array for customerCode:",
                                    customerCode);
                            }
                        } else if (billType === 'unprinted') {
                            console.log("Attempting to filter UNPRINTED sales...");
                            if (unprintedSalesData[customerCode] && Array.isArray(unprintedSalesData[customerCode])) {
                                salesToDisplay = unprintedSalesData[customerCode];
                                console.log("Unprinted sales data for customerCode:", unprintedSalesData[
                                    customerCode]);
                            } else {
                                console.log("No unprinted sales data found or not an array for customerCode:",
                                    customerCode);
                            }
                        } else {
                            console.log("Unknown billType:", billType);
                        }

                        console.log("Sales to Display after filter:", salesToDisplay);
                        populateMainSalesTable(salesToDisplay);
                    });


                    $(document).on('click', '.print-bill-btn', function () {
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
                                success: function (response) {
                                    if (response.success) {
                                        alert(response.message);
                                        sessionStorage.setItem('focusOnCustomerSelect', 'true');
                                        location.reload();
                                    } else {
                                        alert('Error: ' + response.message);
                                    }
                                },
                                error: function (xhr) {
                                    console.error("AJAX error:", xhr.responseText);
                                    alert('An error occurred while trying to print the bill.');
                                }
                            });
                        }
                    });

                    if (sessionStorage.getItem('focusOnCustomerSelect') === 'true') {
                        $(document).on('select2:open', function () {
                            document.querySelector('.select2-search__field').focus();
                        });
                        // Check if the element actually exists and is a select2 element
                        if ($('#new_customer_code').data('select2')) {
                            $('#new_customer_code').select2('open');
                        } else {
                            // Fallback to focus the customer code text input if select2 not applied or is hidden
                            newCustomerCodeField.focus();
                        }
                        sessionStorage.removeItem('focusOnCustomerSelect');
                    }
                });
            </script>
@endsection