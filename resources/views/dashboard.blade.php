@extends('layouts.app')

@section('horizontal_sidebar')
    {{-- This section will contain the content that was originally in the vertical sidebar --}}
    <nav
        class="navbar navbar-expand-lg navbar-light shadow-sm rounded-bottom px-3 py-2 custom-dark-green-bg navbar-compact">
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
    {{-- NEW: Separate Horizontal Navigation for Reports - FIXED AT BOTTOM --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg fixed-bottom custom-bottom-nav">
        <div class="container-fluid">
            <span class="navbar-text text-white me-3 d-none d-lg-block">වාර්තා:</span> {{-- Reports: --}}

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavReports"
                aria-controls="navbarNavReports" aria-expanded="false" aria-label="Toggle report navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="navbarNavReports">
                <ul class="navbar-nav mb-2 mb-lg-0">

                    {{-- Supplier --}}
                    <li class="nav-item mx-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#reportFilterModal"
                            class="nav-link d-flex align-items-center">
                            <span class="material-icons me-2 text-info">person</span>
                            <span class="text-white">සැපයුම්කරු</span>
                        </a>
                    </li>

                    {{-- Vegetables / Item-wise --}}
                    <li class="nav-item mx-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#itemReportModal"
                            class="nav-link d-flex align-items-center">
                            <span class="material-icons me-2 text-warning">category</span>
                            <span class="text-white">එළවළු</span>
                        </a>
                    </li>

                    {{-- By Weight --}}
                    <li class="nav-item mx-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#weight_modal"
                            class="nav-link d-flex align-items-center">
                            <span class="material-icons me-2 text-danger">scale</span>
                            <span class="text-white">බර මත</span>
                        </a>
                    </li>

                    {{-- Price Collection / GRN Sales --}}
                    <li class="nav-item mx-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#grnSaleReportModal"
                            class="nav-link d-flex align-items-center">
                            <span class="material-icons me-2 text-success">receipt_long</span>
                            <span class="text-white">මිල එක්කතුව</span>
                        </a>
                    </li>

                    {{-- Sales --}}
                    <li class="nav-item mx-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#reportFilterModal1"
                            class="nav-link d-flex align-items-center">
                            <span class="material-icons me-2" style="font-size: 18px;">shopping_cart</span>
                            <span class="text-white">විකුණුම්</span>
                        </a>
                    </li>

                    {{-- Remaining Report 1 --}}
                    <li class="nav-item mx-2">
                        <a href="{{ route('report.grn.sales.overview') }}" target="_blank"
                            class="nav-link d-flex align-items-center text-white">
                            <span class="material-icons me-2" style="font-size: 18px;">storage</span>
                            ඉතිරි වාර්තාව 1
                        </a>
                    </li>

                    {{-- Remaining Report 2 --}}
                    <li class="nav-item mx-2">
                        <a href="{{ route('report.grn.sales.overview2') }}" target="_blank"
                            class="nav-link d-flex align-items-center text-white">
                            <span class="material-icons me-2" style="font-size: 18px;">storage</span>
                            ඉතිරි වාර්තාව 2
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>


    <style>
        /* Custom CSS to push content up if fixed-bottom nav bar covers it */
        body {
            padding-bottom: 70px;
            /* Adjust this value based on the actual height of your fixed-bottom navbar */
        }

        .custom-bottom-nav {
            background-color: #004d00 !important;
            /* A slightly darker green for the bottom nav */
        }

        /* Adjustments for the bottom nav links */
        .custom-bottom-nav .nav-link {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
            font-size: 0.95rem !important;
            /* Slightly larger than the top compact nav */
        }

        .custom-bottom-nav .nav-link .material-icons {
            font-size: 20px !important;
            /* Slightly larger icons */
        }

        /* Center the nav items when collapsed (mobile) and on larger screens */
        .custom-bottom-nav .navbar-collapse {
            justify-content: center;
            /* Centers the ul inside the collapsed div */
        }

        .custom-bottom-nav .navbar-nav {
            width: 100%;
            /* Make ul take full width inside collapse for justify-content to work */
            justify-content: space-around;
            /* Distribute items evenly */
        }

        /* Add horizontal margin between nav items for better spacing on larger screens */
        .custom-bottom-nav .navbar-nav .nav-item {
            margin: 0 5px;
            /* Adjust as needed */
        }
    </style>
    <style>
        /* Adjustments for a more compact navbar */
        .navbar.navbar-compact {
            /* Reduce overall vertical padding of the navbar container */
            padding-top: 0.3rem !important;
            /* Adjust this value */
            padding-bottom: 0.3rem !important;
            /* Adjust this value */
        }

        .navbar.navbar-compact .navbar-nav .nav-link {
            /* Reduce vertical padding within each nav link */
            padding-top: 0.2rem !important;
            /* Adjust this value */
            padding-bottom: 0.2rem !important;
            /* Adjust this value */

            /* Make the text slightly smaller */
            font-size: 0.85rem !important;
            /* Adjust this value, e.g., 0.8rem for even smaller */
        }

        .navbar.navbar-compact .navbar-nav .nav-link .material-icons {
            /* Make the Material Icons smaller */
            font-size: 18px !important;
            /* Default is often 24px, 18px is a good reduction */
            margin-right: 0.3rem !important;
            /* Adjust margin next to icon if needed */
        }

        /* If you want to reduce the size of the toggler icon on mobile */
        /* This is less common but can be done */
        /*
                                                                                                                            .navbar.navbar-compact .navbar-toggler {
                                                                                                                                padding: 0.25rem 0.5rem !important;
                                                                                                                            }
                                                                                                                            .navbar.navbar-compact .navbar-toggler-icon {
                                                                                                                                width: 1.2em !important;
                                                                                                                                height: 1.2em !important;
                                                                                                                            }
                                                                                                                            */
    </style>
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
            min-height: calc(1.5em + 1rem + 2px);
            /* Increased from 0.5rem to 1rem */
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
            /* Changed from flex: 4; as flex-basis below will dictate initial size */
            padding: 5px;
            /* Adjusted padding for better fit, corrected typo '25 px' to '25px' previously, but now making it more balanced */
            white-space: nowrap;
            /* Prevent text wrapping unless necessary */
            overflow: hidden;
            text-align: center !important;
            /* IMPORTANT: Center text within each column */
            /* Hide overflow */
            text-overflow: ellipsis;
            /* Add ellipsis for overflow */
            box-sizing: border-box;
            /* Include padding in element's total width */
        }

        /* Specific column widths - adjust these values as needed for your data */
        .grn-code {
            flex-basis: 15%;
            /* Using flex-basis for explicit width distribution */
            max-width: 15%;
        }

        .grn-supplier-code {
            flex-basis: 25%;
            max-width: 25%;
        }

        .grn-item-code {
            flex-basis: 15%;
            max-width: 15%;
        }

        .grn-item-name {
            flex-basis: 25%;
            max-width: 25%;
        }

        /* Wider for names */
        .grn-packs {
            flex-basis: 10%;
            max-width: 10%;
            text-align: right !important;
            /* Keep right alignment for packs if desired */
        }

        .grn-grn-no {
            flex-basis: 15%;
            max-width: 15%;
        }

        .grn-txn-date {
            flex-basis: 15%;
            max-width: 15%;
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
            /* Default text color, overridden by .select2-black-text if present */
        }

        .select2-black-text {
            color: #FF0000 !important;
            text-align: center !important;
            font-weight: bold !important;
            font-size: 14px !important;
            /* REMOVED: position: relative; and transform: translateY(-8px); for vertical centering */
        }

        /* Style for the main Select2 input field to match grn_display */
        .select2-container--default .select2-selection--single {
            height: 34px !important;
            /* Keep the height from previous request */
            /* Removed duplicate text-align, padding, font-size, border, border-radius as they are defined elsewhere or not needed here */
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 32px;
            /* Adjusted to be slightly less than container height for better alignment */
            top: 50%;
            transform: translateY(-50%);
            /* Center vertically */
            right: 4px;
            /* Adjust if needed */
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
            text-align: center;
            /* Grey out the placeholder option */
        }

        /* This targets the actual text display area inside the Select2 box. */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 34px !important;
            /* Match line-height to the height for vertical centering */
            font-size: 16px !important;
            /* Made text "some what big" (increased from 14px to 16px) */
            padding: 0 12px !important;
            /* CRITICAL CHANGE: Set top/bottom padding to 0 for vertical centering */
            text-align: center !important;
            /* Center the text horizontally */
            font-weight: bold !important;
            /* Make the text bold */
        }

        /* Optional: If you also want the options in the dropdown list to have the same font styling */
        .select2-results__option {
            font-size: 16px !important;
            font-weight: bold !important;
            /* text-align: center !important; -- This is now handled by .grn-column where appropriate for individual cells */
        }
    </style>


    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            {{-- Container for the two stacked Printed Sales Records columns --}}
            <div class="col-md-2">
                {{-- ORIGINAL SECTION: Printed Sales Records (bill_printed = 'Y') - Top Left Column --}}
                <div class="card shadow-sm border-0 rounded-3 p-3 mb-4">
                    <h6 class="mb-2 text-center" style="color: white;">මුද්‍රිත විකුණුම් වාර්තා</h6>

                    {{-- 🔍 Search Bar --}}
                    <input type="text" id="searchCustomerCode" class="form-control form-control-sm mb-2"
                        placeholder="Search by Bill No...">

                    @if ($salesPrinted->count())
                        <div class="printed-sales-list">
                            <ul id="printedSalesList">
                                {{-- Outer loop: CUSTOMER GROUP --}}
                                {{-- First, sort the customer groups by the 'created_at' of their latest sale --}}
                                @foreach ($salesPrinted->sortByDesc(fn($sales) => $sales->first()->created_at) as $customerCode => $salesForCustomer)
                                    @php
                                        $customerName = $salesForCustomer->first()->customer_name ?? 'N/A';
                                    @endphp
                                    <li data-customer-code="{{ $customerCode }}">
                                        <div class="customer-group-header">
                                            {{-- Customer header content here (optional) --}}
                                        </div>
                                        <ul>
                                            {{-- Inner loop: BILL GROUP --}}
                                            {{-- Sort the bills within each customer group by the 'created_at' of the latest sale in
                                            that bill --}}
                                            @foreach ($salesForCustomer->groupBy('bill_no')->sortByDesc(fn($sales) => $sales->first()->created_at) as $billNo => $salesForBill)
                                                @php
                                                    $totalBillAmount = $salesForBill->sum('total');
                                                @endphp
                                                <li>
                                                    <div class="customer-header bill-clickable" data-customer-code="{{ $customerCode }}"
                                                        data-customer-name="{{ $customerName }}" data-bill-no="{{ $billNo ?? '' }}"
                                                        data-bill-type="printed"
                                                        style="font-size: 11px; padding: 2px 6px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #ddd; margin-bottom: 3px; border-radius: 4px; background-color: #f9f9f9;">
                                                        <span style="flex: 1;">
                                                            {{ strtoupper($customerCode ?? 'N/A') }} - Rs.
                                                            {{ number_format($totalBillAmount, 2) }}


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
                <form action="{{ route('clear.data') }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete all data?');">
                    @csrf
                    <button type="submit" class="btn btn-danger mt-3">🗑️ Delete All Sales & GRN Entries</button>
                </form>
            </div>



            {{-- EXISTING CONTENT: Main Sales Entry and All Sales Table --}}
            <div class="col-md-8">
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

                        <div class="col-md-2"
                            style="margin-top: -4px; margin-bottom: 2px; max-width: 150px; text-align: center;">
                            {{-- ADD THIS LINE --}}
                            <div id="billNoDisplay"
                                style="color: black; font-weight: bold; margin-bottom: 5px; font-size: 1.1em;">
                                {{-- Bill No will be displayed here --}}
                            </div>
                        </div>
                        <div class="row justify-content-end" style="margin-top: -15px;">
                            <div class="col-md-3">
                                <select name="customer_code_select" id="customer_code_select"
                                    class="form-select form-select-sm select2 @error('customer_code') is-invalid @enderror"
                                    style="height: 34px; font-size: 14px; padding: 6px 12px; line-height: 1.5;">
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
                            <div class="col-md-2" style="margin-top: -4px; margin-bottom: 6px; max-width: 120px;">
                                <input type="text" name="customer_code" id="new_customer_code"
                                    class="form-control text-uppercase @error('customer_code') is-invalid @enderror"
                                    value="{{ old('customer_code') }}" placeholder="පාරිභෝගික කේතය"
                                    style="height: 45px; font-size: 16px; padding: 8px 16px; width: 100%; border: 1px solid black; color: black;"
                                    required>
                                @error('customer_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-1" style="margin-bottom: 2px;">
                                    <input type="text" id="grn_display" class="form-control"
                                        placeholder="Select GRN Entry..." readonly
                                        style="height: 45px; font-size: 16px; padding: 8px 16px; display: none; text-align: center !important; border: 1px solid black; color: black;">
                                    <select id="grn_select" class="form-select select2"
                                        style="height: 45px; font-size: 16px; padding: 8px 16px; border: 1px solid black; color: black;">
                                        <option value="">-- Select GRN Entry --</option>
                                        @foreach ($entries as $entry)
                                            <option value="{{ $entry->code }}" data-supplier-code="{{ $entry->supplier_code }}"
                                                data-code="{{ $entry->code }}" data-item-code="{{ $entry->item_code }}"
                                                data-item-name="{{ $entry->item_name }}" data-weight="{{ $entry->weight }}"
                                                data-price="{{ $entry->price_per_kg }}" data-total="{{ $entry->total }}"
                                                data-packs="{{ $entry->packs }}" data-grn-no="{{ $entry->grn_no }}"
                                                data-txn-date="{{ $entry->txn_date }}"
                                                data-original-weight="{{ $entry->original_weight }}"
                                                data-original-packs="{{ $entry->original_packs }}">
                                                {{ $entry->code }} | {{ $entry->supplier_code }} | {{ $entry->item_code }} |
                                                {{ $entry->item_name }} | {{ $entry->packs }} | {{ $entry->grn_no }} |
                                                {{ $entry->txn_date }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                            </div>

                            <input type="hidden" name="customer_name" id="customer_name_hidden"
                                value="{{ old('customer_name') }}">
                            <input type="hidden" name="grn_entry_code" id="grn_entry_code" value="">
                        </div>

                        <div class="row g-1 form-row">
                            <div class="col-md-3 mb-1 d-none">
                                <select name="supplier_code_display" id="supplier_code_display"
                                    class="form-select @error('supplier_code') is-invalid @enderror" disabled
                                    style="border: 1px solid black; color: black;">
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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-1 d-none">
                                <input type="hidden" name="item_code" value="{{ old('item_code') }}">
                                <select id="item_select" class="form-select @error('item_code') is-invalid @enderror"
                                    disabled style="border: 1px solid black; color: black;">
                                    <option value="" disabled selected>අයිතමය තෝරන්න (Select Item)</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->item_code }}" data-code="{{ $item->code }}"
                                            data-item-code="{{ $item->item_code }}" data-item-name="{{ $item->item_name }}" {{ old('item_code') == $item->item_code ? 'selected' : '' }}>
                                            ({{ $item->item_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('item_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 align-items-start">

                            <!-- Item Name -->
                            <div style="flex: 1 1 100px;">
                                <input type="text" id="item_name_display_from_grn" class="form-control" readonly
                                    placeholder="අයිතමයේ නම (Item Name)"
                                    style="background-color: #e9ecef; color: black; height: 45px; font-size: 14px; padding: 6px 10px; border: 1px solid black;">
                            </div>

                            <!-- Weight -->
                            <div style="flex: 1 1 120px;">
                                <input type="number" name="weight" id="weight" step="0.01"
                                    class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight') }}"
                                    placeholder="බර (kg)" required
                                    style="height: 45px; font-size: 14px; padding: 6px 10px; border: 1px solid black; color: black;">
                                <small id="remaining_weight_display" class="form-text text-danger fw-bold"
                                    style="font-size: 0.85rem;">Remaining: 0.00 kg</small>
                            </div>

                            <!-- Price per KG -->
                            <div style="flex: 1 1 120px;">
                                <input type="number" name="price_per_kg" id="price_per_kg" step="0.01"
                                    class="form-control @error('price_per_kg') is-invalid @enderror"
                                    value="{{ old('price_per_kg') }}" placeholder="මිල (Price/kg)" required
                                    style="height: 45px; font-size: 14px; padding: 6px 10px; border: 1px solid black; color: black;">
                            </div>



                            <!-- Packs -->
                            <div style="flex: 1 1 120px;">
                                <input type="number" name="packs" id="packs"
                                    class="form-control @error('packs') is-invalid @enderror" value="{{ old('packs') }}"
                                    placeholder="ඇසුරුම් (Packs)" required
                                    style="height: 45px; font-size: 14px; padding: 6px 10px; border: 1px solid black; color: black;">
                                <small id="remaining_packs_display" class="form-text text-danger fw-bold"
                                    style="font-size: 0.85rem;">Remaining Packs: 0</small>
                            </div>
                            <!-- Total -->
                            <div style="flex: 1 1 100px;">
                                <input type="number" name="total" id="total" readonly
                                    class="form-control bg-light @error('total') is-invalid @enderror"
                                    value="{{ old('total') }}" placeholder="සමස්ත (Total)"
                                    style="height: 45px; font-size: 14px; padding: 6px 10px; border: 1px solid black; color: black;">
                            </div>
                        </div>

                        <!-- Hidden fields -->
                        <input type="hidden" name="code" id="code" value="{{ old('code') }}">
                        <input type="hidden" name="item_name" id="item_name" value="{{ old('item_name') }}">
                        <input type="hidden" name="original_weight" id="original_weight_input">
                        <input type="hidden" name="original_packs" id="original_packs_input">




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

                            <style>
                                #mainSalesTableBody tr,
                                #mainSalesTableBody td {
                                    background-color: black !important;
                                    color: white !important;
                                }
                            </style>
                            <table class="table table-bordered table-hover shadow-sm rounded-3 overflow-hidden"
                                style="font-size: 0.85rem; margin-top: -10px;">

                                <thead style="background-color: white; color: black;">
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

                            <h5 class="text-end mb-3" style="font-size: 1.5rem; color: red;">
                                <strong>Total Sales Value:</strong> Rs. <span
                                    id="mainTotalSalesValue">{{ number_format($totalSum, 2) }}</span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NEW SECTION: Unprinted Sales Records (bill_printed = 'N') - Right Column --}}
            <div class="col-md-2"> {{-- You can change to col-md-2 if needed for smaller width --}}
                <div class="card shadow-sm border-0 rounded-3 p-3"> {{-- Reduced padding from p-4 to p-3 --}}
                    <h6 class="mb-2 text-center text-white">මුද්‍රණය නොකළ විකුණුම් වාර්තා</h6>
                    {{-- 🔍 Search Bar --}}
                    <input type="text" id="searchUnprintedCustomerCode" class="form-control form-control-sm mb-2"
                        placeholder="Search by customer code...">

                    @if ($salesNotPrinted->count())
                        <div class="unprinted-sales-list">
                            <ul id="unprintedSalesList">
                                {{-- Loop over each CUSTOMER GROUP for unprinted sales --}}
                                @foreach ($salesNotPrinted->reverse() as $customerCode => $salesForCustomer)
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
                                                ({{ strtoupper($customerCode) }}) -
                                                Rs.{{ number_format($totalCustomerSalesAmount, 2) }}
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

                {{-- DUPLICATE SECTION: Unprinted Sales Records --}}
                <div class="card shadow-sm border-0 rounded-3 p-3 mt-3"> {{-- Added margin-top (mt-3) to create space --}}
                    <h6 class="mb-2 text-center text-white">මුද්‍රණය නොකළ විකුණුම් වාර්තා</h6>
                    {{-- Changed heading to
                    distinguish --}}

                    {{-- 🔍 Search Bar --}}
                    <input type="text" id="searchUnprintedCustomerCodeDuplicate" class="form-control form-control-sm mb-2"
                        placeholder="Search by customer code...">

                    @if ($salesNotPrinted->count())
                        @php $sortedSalesNotPrinted = $salesNotPrinted->sortKeys(); @endphp

                        <div class="unprinted-sales-list">
                            <ul id="unprintedSalesListDuplicate">
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
                                                ({{ strtoupper($customerCode) }}) -
                                                Rs.{{ number_format($totalCustomerSalesAmount, 2) }}
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



                {{-- JavaScript Includes (jQuery and Select2 should always be loaded before your custom script that uses
                them)
                --}}
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

                <script>
                    $(document).ready(function () {
                        $('#new_customer_code').on('input', function () {
                            const customerCode = $(this).val().toUpperCase();

                            // Only fetch if the input has a value
                            if (customerCode) {
                                // Construct the URL using the base path and the JavaScript variable
                                const url = `{{ url('/get-unprinted-sales') }}/${customerCode}`;

                                $.ajax({
                                    url: url,
                                    method: 'GET',
                                    success: function (response) {
                                        console.log("Fetched sales data:", response);

                                        // ... (rest of your success function remains the same)
                                    },

                                });
                            } else {
                                // The input field is empty, clear the table and total
                                $('#mainSalesTableBody').empty();
                                $('#mainTotalSalesValue').text('0.00');
                            }
                        });
                    });
                </script>

                <script>

                    document.addEventListener('DOMContentLoaded', function () {

                        function resetFormAndTable() {
                            // Get the list of sales IDs from the table to be marked as unprinted.
                            // This assumes your table rows have a data-sale-id attribute.
                            const saleIds = [];
                            document.querySelectorAll('#mainSalesTableBody tr[data-sale-id]').forEach(row => {
                                saleIds.push(row.dataset.saleId);
                            });

                            // If there are records, send them to the server to be marked as unprinted
                            if (saleIds.length > 0) {
                                console.log('Resetting form - sending unprinted sales to server.');

                                const csrfToken = '{{ csrf_token() }}';
                                const url = '{{ route('sales.save-as-unprinted') }}';

                                const data = new Blob([JSON.stringify({
                                    _token: csrfToken,
                                    sale_ids: saleIds
                                })], {
                                    type: 'application/json'
                                });

                                // Use navigator.sendBeacon for a reliable background request
                                navigator.sendBeacon(url, data);
                            }

                            // After sending the request, proceed with clearing the form and reloading
                            document.getElementById('salesEntryForm').reset();
                            salesEntryForm.action = "{{ route('grn.store') }}";
                            document.getElementById('mainSalesTableBody').innerHTML = '';
                            document.getElementById('new_customer_code').value = '';
                            document.getElementById('customer_name_hidden').value = '';
                            $('#customer_code_select').val(null).trigger('change.select2');
                            document.getElementById('grn_display').style.display = 'none';
                            $('#grn_select').next('.select2-container').show();
                            $('#grn_select').val(null).trigger('change.select2');

                            // Reset the button displays
                            addSalesEntryBtn.style.display = 'inline-block';
                            updateSalesEntryBtn.style.display = 'none';
                            deleteSalesEntryBtn.style.display = 'none';
                            cancelEntryBtn.style.display = 'none';

                            console.log("Form, table, and buttons reset. Reloading page.");
                            location.reload();
                        }

                        // Add an event listener for the F10 key press
                        document.addEventListener('keydown', function (event) {
                            if (event.key === 'F10') {
                                event.preventDefault();
                                resetFormAndTable();
                            }
                        });

                        // Update the click handler for the cancel button to use the new function
                        document.getElementById('cancelEntryBtn').addEventListener('click', function () {
                            resetFormAndTable();
                        });
                    });
                </script>

                {{-- Ensure Bootstrap JS is loaded for collapse --}}
                <script>
                    // ... (existing JavaScript code, including Select2 initializations and other event listeners) ...

                    // NEW: Search functionality for Printed Sales Records
                    document.getElementById('searchCustomerCode').addEventListener('keyup', function () {
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
                <script>
                    // NEW: Search functionality for DUPLICATE Printed Sales Records
                    document.getElementById('searchCustomerCodeDuplicate').addEventListener('keyup', function () {
                        const searchTerm = this.value.toLowerCase();
                        const printedSalesListDuplicate = document.getElementById('printedSalesListDuplicate');
                        const customerGroups = printedSalesListDuplicate.querySelectorAll('li[data-customer-code]');

                        customerGroups.forEach(customerGroup => {
                            let customerGroupHasVisibleBills = false;
                            const billItems = customerGroup.querySelectorAll('li > .customer-header.bill-clickable');

                            billItems.forEach(billItem => {
                                const billNoElement = billItem.querySelector('span');
                                // This includes both customer code and bill number in its text,
                                // allowing search across both.
                                const billNoText = billNoElement ? billNoElement.textContent.toLowerCase() : '';

                                if (billNoText.includes(searchTerm)) {
                                    billItem.style.display = 'flex';
                                    customerGroupHasVisibleBills = true;
                                } else {
                                    billItem.style.display = 'none';
                                }
                            });

                            if (customerGroupHasVisibleBills) {
                                customerGroup.style.display = 'block';
                            } else {
                                customerGroup.style.display = 'none';
                            }
                        });
                    });

                    // For DUPLICATE Printed Sales (this is new, specifically for input event to filter customer groups)
                    // This second listener allows filtering the top-level customer groups directly
                    // if the search term matches the customer code itself.
                    document.getElementById('searchCustomerCodeDuplicate').addEventListener('input', function () {
                        const searchValue = this.value.toLowerCase();
                        document.querySelectorAll('#printedSalesListDuplicate > li').forEach(li => {
                            const customerCode = li.getAttribute('data-customer-code').toLowerCase();
                            // Check if the customer code matches
                            if (customerCode.includes(searchValue)) {
                                li.style.display = ''; // Show the customer group
                                // Also ensure all bills within this group are shown if the customer code matches the search
                                li.querySelectorAll('li > .customer-header.bill-clickable').forEach(billItem => {
                                    billItem.style.display = 'flex';
                                });
                            } else {
                                // If customer code doesn't match, check if any of its bills match
                                let anyBillMatches = false;
                                li.querySelectorAll('li > .customer-header.bill-clickable').forEach(billItem => {
                                    const billNoElement = billItem.querySelector('span');
                                    const billNoText = billNoElement ? billNoElement.textContent.toLowerCase() : '';
                                    if (billNoText.includes(searchValue)) {
                                        anyBillMatches = true;
                                    }
                                });

                                if (anyBillMatches) {
                                    li.style.display = ''; // Show the customer group if any bill matches
                                } else {
                                    li.style.display = 'none'; // Hide if neither customer code nor any bill matches
                                }
                            }
                        });
                    });
                </script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const searchInput = document.getElementById('searchUnprintedCustomerCodeDuplicate');
                        const listItems = document.querySelectorAll('#unprintedSalesListDuplicate li');

                        searchInput.addEventListener('input', function () {
                            const query = this.value.toLowerCase().trim();

                            listItems.forEach(function (li) {
                                const customerCode = li.getAttribute('data-customer-code')?.toLowerCase() || '';
                                li.style.display = customerCode.includes(query) ? 'block' : 'none';
                            });
                        });
                    });
                </script>
                <script>
                    $(document).ready(function () {
                        // Event listener for clicking on a bill in the printed sales list
                        $(document).on('click', '.printed-sales-list .bill-clickable', function () {
                            var billNo = $(this).data('bill-no');
                            var customerCode = $(this).data('customer-code');
                            var customerName = $(this).data('customer-name');

                            // Display the bill number above the customer code input
                            $('#billNoDisplay').text('Bill No: ' + billNo); // THIS LINE IS NEW/MODIFIED

                            // Optionally, set the customer code input and select
                            $('#new_customer_code').val(customerCode);
                            $('#customer_name_hidden').val(customerName);

                            // If you want to update the Select2 dropdown for customer
                            $('#customer_code_select').val(customerCode).trigger('change');
                        });

                        // ... (rest of your existing JavaScript) ...

                    });
                </script>
                {{-- FETCHING THE WEIGHT DETAILS --}}
                <script>
                    let originalGrnWeight = 0; // To store the weight from the selected GRN entry
                    let originalGrnPacks = 0; // To store the packs from the selected GRN entry

                    document.addEventListener('DOMContentLoaded', function () {
                        console.log('DOM Content Loaded. Initializing script.');

                        const grnSelect = $('#grn_select');
                        const weightInput = document.getElementById('weight');
                        const remainingWeightDisplay = document.getElementById('remaining_weight_display');
                        const packsInput = document.getElementById('packs');
                        const remainingPacksDisplay = document.getElementById('remaining_packs_display');

                        // Other elements (keeping them here for completeness)
                        const supplierCodeDisplay = document.getElementById('supplier_code_display');
                        const supplierCodeHidden = document.getElementById('supplier_code');
                        const itemSelect = document.getElementById('item_select');
                        const itemCodeHidden = document.querySelector('input[name="item_code"]');
                        const itemNameDisplay = document.getElementById('item_name_display_from_grn');
                        const pricePerKgInput = document.getElementById('price_per_kg');
                        const totalInput = document.getElementById('total');
                        const grnEntryCodeHidden = document.getElementById('grn_entry_code');

                        // Function to update remaining weight display
                        function updateRemainingWeight() {
                            const currentWeightInput = parseFloat(weightInput.value);

                            console.log('--- Inside updateRemainingWeight ---');
                            console.log('originalGrnWeight:', originalGrnWeight);
                            console.log('currentWeightInput (from input field):', weightInput.value, '(parsed:', currentWeightInput, ')');

                            if (!isNaN(currentWeightInput) && originalGrnWeight > 0) {
                                let remaining = originalGrnWeight - currentWeightInput;
                                if (remaining < 0) {
                                    remaining = 0;
                                }
                                remainingWeightDisplay.textContent = `Remaining: ${remaining.toFixed(2)} kg`;
                                console.log('Calculated Remaining (weight dynamic):', remaining.toFixed(2));
                            } else if (originalGrnWeight > 0) {
                                remainingWeightDisplay.textContent = `Remaining: ${originalGrnWeight.toFixed(2)} kg`;
                                console.log('Displaying Full GRN Weight (weight input not valid):', originalGrnWeight.toFixed(2));
                            } else {
                                remainingWeightDisplay.textContent = `Remaining: 0.00 kg`;
                                console.log('Displaying default 0.00 kg (no GRN weight or zero)');
                            }
                            console.log('Current remaining_weight_display text:', remainingWeightDisplay.textContent);
                            console.log('-----------------------------------');
                        }

                        // Function to update remaining packs display
                        function updateRemainingPacks() {
                            const currentPacksInput = parseInt(packsInput.value);

                            console.log('--- Inside updateRemainingPacks ---');
                            console.log('originalGrnPacks:', originalGrnPacks);
                            console.log('currentPacksInput (from input field):', packsInput.value, '(parsed:', currentPacksInput, ')');

                            if (!isNaN(currentPacksInput) && originalGrnPacks > 0) {
                                let remaining = originalGrnPacks - currentPacksInput;
                                if (remaining < 0) {
                                    remaining = 0;
                                }
                                remainingPacksDisplay.textContent = `Remaining Packs: ${remaining}`;
                                console.log('Calculated Remaining Packs (dynamic):', remaining);
                            } else if (originalGrnPacks > 0) {
                                remainingPacksDisplay.textContent = `Remaining Packs: ${originalGrnPacks}`;
                                console.log('Displaying Full GRN Packs (packs input not valid):', originalGrnPacks);
                            } else {
                                remainingPacksDisplay.textContent = `Remaining Packs: 0`;
                                console.log('Displaying default 0 packs (no GRN packs or zero)');
                            }
                            console.log('Current remaining_packs_display text:', remainingPacksDisplay.textContent);
                            console.log('-----------------------------------');
                        }

                        // Initialize Select2
                        grnSelect.select2();
                        console.log('Select2 initialized for #grn_select');

                        // Event listener for Select2's custom 'select2:select' event
                        grnSelect.on('select2:select', function (e) {
                            console.log('--- GRN Select event fired (select2:select) ---');
                            const selectedOption = e.params.data.element; // Get the raw option element

                            // Log raw dataset values to ensure they are available
                            console.log('Raw data-weight from option:', selectedOption.dataset.weight);
                            console.log('Raw data-packs from option:', selectedOption.dataset.packs);

                            // Set original weight and packs
                            originalGrnWeight = parseFloat(selectedOption.dataset.weight);
                            originalGrnPacks = parseInt(selectedOption.dataset.packs);

                            console.log('originalGrnWeight set to:', originalGrnWeight);
                            console.log('originalGrnPacks set to:', originalGrnPacks);


                            // Populate other fields
                            supplierCodeDisplay.value = selectedOption.dataset.supplierCode;
                            supplierCodeHidden.value = selectedOption.dataset.supplierCode;
                            itemSelect.value = selectedOption.dataset.itemCode;
                            itemCodeHidden.value = selectedOption.dataset.itemCode;
                            itemNameDisplay.value = selectedOption.dataset.itemName;
                            pricePerKgInput.value = selectedOption.dataset.price;
                            totalInput.value = selectedOption.dataset.total;
                            grnEntryCodeHidden.value = selectedOption.dataset.code;

                            // Clear input fields initially, so Remaining displays full GRN value
                            weightInput.value = '';
                            packsInput.value = ''; // Clear packs input

                            // Call update functions for initial display after clearing inputs
                            updateRemainingWeight();
                            updateRemainingPacks();
                            console.log('--- End GRN Select event ---');
                        });

                        // Event listener for weight input changes
                        weightInput.addEventListener('input', function () {
                            console.log('--- Weight Input event fired ---');
                            updateRemainingWeight();
                        });

                        // Event listener for packs input changes
                        packsInput.addEventListener('input', function () {
                            console.log('--- Packs Input event fired ---');
                            updateRemainingPacks();
                        });

                        // Initial update on page load (e.g., if form reloads with old values)
                        console.log('--- Initial Page Load Check ---');
                        if (grnSelect.val()) { // Check if a value is pre-selected in Select2
                            const selectedOption = grnSelect.find(':selected')[0]; // Get the raw option element
                            if (selectedOption && selectedOption.dataset.weight && selectedOption.dataset.packs) {
                                originalGrnWeight = parseFloat(selectedOption.dataset.weight);
                                originalGrnPacks = parseInt(selectedOption.dataset.packs);

                                grnEntryCodeHidden.value = selectedOption.dataset.code;

                                // Value for weightInput and packsInput are already set by Blade's old() helper.
                                // Call update functions to display initial remaining based on old() values.
                                updateRemainingWeight();
                                updateRemainingPacks();
                            } else {
                                console.log('No pre-selected GRN or missing data-attributes on load.');
                                remainingWeightDisplay.textContent = `Remaining: 0.00 kg`;
                                remainingPacksDisplay.textContent = `Remaining Packs: 0`;
                            }
                        } else {
                            console.log('No GRN selected initially on page load.');
                            remainingWeightDisplay.textContent = `Remaining: 0.00 kg`;
                            remainingPacksDisplay.textContent = `Remaining Packs: 0`;
                        }
                        console.log('--- End Initial Page Load Check ---');
                    });
                </script>
                {{-- TYPING THE CUSTOMER_CODE AND FETCHING UNPRINTED SALES --}}
                <!-- First script block: Define globally accessible functions -->
                <script>
                    function populateFormForEdit(sale) {
                        console.log("Populating form for sale:", sale);

                        saleIdField.value = sale.id;
                        newCustomerCodeField.value = sale.customer_code || '';
                        customerNameField.value = sale.customer_name || '';

                        if (sale.customer_code) {
                            $('#customer_code_select').val(sale.customer_code).trigger('change.select2');
                            console.log("Setting customer_code_select to:", sale.customer_code);
                        } else {
                            $('#customer_code_select').val(null).trigger('change.select2');
                            console.log("Clearing customer_code_select.");
                        }

                        // --- NEW LOGIC TO POPULATE GRN FIELDS ---
                        const grnDisplay = document.getElementById('grn_display');
                        grnDisplay.style.display = 'block';
                        grnDisplay.value = sale.code || '';

                        const grnSelect = document.getElementById('grn_select');
                        $(grnSelect).next('.select2-container').hide();

                        console.log("Setting grn_display to:", grnDisplay.value);

                        const grnOption = $('#grn_select option').filter(function () {
                            return $(this).val() === sale.code &&
                                $(this).data('supplierCode') === sale.supplier_code &&
                                $(this).data('itemCode') === sale.item_code;
                        });

                        if (grnOption.length) {
                            $('#grn_select').val(grnOption.val());
                            console.log("Setting grn_select to:", grnOption.val());
                        } else {
                            $('#grn_select').val(null);
                            console.log("Clearing grn_select.");
                        }
                        // --- END OF GRN LOGIC ---

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


                </script>

                <!-- Second script block: Main logic -->
                <script>
                    $(document).ready(function () {
                        // Debounce function to delay execution until the user stops typing
                        function debounce(func, delay) {
                            let timeout;
                            return function (...args) {
                                const context = this;
                                clearTimeout(timeout);
                                timeout = setTimeout(() => func.apply(context, args), delay);
                            };
                        }

                        // Function to fetch and display unprinted sales data
                        function fetchUnprintedSales(customerCode) {
                            let tableBody = $('#mainSalesTableBody');
                            tableBody.empty(); // Clear the table body first
                            $('#customer_name').val('');

                            if (customerCode) {
                                $.ajax({
                                    url: '/api/sales/unprinted/' + customerCode,
                                    method: 'GET',
                                    success: function (response) {
                                        if (response.length > 0) {
                                            response.forEach(function (sale) {
                                                let row = $(`
                                                <tr>
                                                    <td>${sale.code}</td>
                                                    <td>${sale.item_code}</td>
                                                    <td>${sale.item_name}</td>
                                                    <td>${sale.weight}</td>
                                                    <td>${sale.price_per_kg}</td>
                                                    <td>${sale.total}</td>
                                                    <td>${sale.packs}</td>
                                                </tr>
                                            `);

                                                // Click handler to populate form with selected row
                                                row.on('click', function () {
                                                    populateFormForEdit(sale);
                                                });

                                                tableBody.append(row);
                                            });

                                            // Populate table + auto-fill form with first entry
                                            populateMainSalesTable(response);
                                            populateFormForEdit(response[0]);
                                        } else {
                                            tableBody.html('<tr><td colspan="7" class="text-center">No unprinted sales records found for this customer.</td></tr>');
                                        }
                                    },
                                    error: function (xhr) {
                                        console.error("AJAX Error fetching sales records:", xhr.responseText);
                                        tableBody.html('<tr><td colspan="7" class="text-center text-danger">Error fetching sales data. Please try again.</td></tr>');
                                    }
                                });
                            } else {
                                tableBody.html('<tr><td colspan="7" class="text-center">Please enter a customer code to view records.</td></tr>');
                            }
                        }

                        const debouncedFetchUnprintedSales = debounce(fetchUnprintedSales, 300);
                        $('#new_customer_code').on('keyup', function () {
                            let customerCode = $(this).val().trim();
                            debouncedFetchUnprintedSales(customerCode);
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
                                const weight = option.data('weight');
                                const originalWeight = option.data('originalWeight');
                                const originalPacks = option.data('originalPacks');

                                const txnDate = option.data('txnDate');

                                // Construct the HTML for the tabular display for each row (data row only)
                                const $result = $(`
                                                                                                                                                                                                            <div class="grn-option-row">
                                                                                                                                                                                                                <div class="grn-column grn-code"><strong>${code || ''}</strong></div>
                                                                                                                                                                                                                <div class="grn-column grn-supplier-code">${supplierCode || ''}</div>
                                                                                                                                                                                                                 <div class="grn-column grn-supplier-code">${originalWeight || ''}</div>
                                                                                                                                                                                                                  <div class="grn-column grn-supplier-code">${originalPacks || ''}</div>

                                                                                                                                                                                                                <div class="grn-column grn-grn-no">${weight || ''}</div>
                                                                                                                                                                                                                <div class="grn-column grn-packs">${packs || 0}</div>

                                                                                                                                                                                                                <div class="grn-column grn-txn-date">${txnDate || ''}</div>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        `);
                                return $result;
                            },
                            templateSelection: function (data) {
                                if (!data.id) {
                                    return data.text;
                                }
                                const option = $(data.element);
                                const code = option.data('code');
                                const supplierCode = option.data('supplierCode');
                                const itemCode = option.data('itemCode');
                                const itemName = option.data('itemName');
                                const packs = option.data('packs');
                                const grnNo = option.data('grnNo');
                                const weight = option.data('weight');
                                const txnDate = option.data('txnDate');
                                const originalWeight = option.data('originalWeight');
                                const originalPacks = option.data('originalPacks');

                                var $selection = $('<span></span>'); // Create a new jQuery span element
                                // Add a class here, e.g., 'select2-black-text'
                                $selection.addClass('select2-black-text');
                                $selection.css('text-align', 'center'); // Add inline style


                                $selection.html(`${code || ''}(කිලෝ,: ${originalWeight || 0} /මලු: ${originalPacks || ''} /දිනය: ${txnDate || ''})`);

                                return $selection;
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
                                                                                                                                                                                                                     <div class="grn-column grn-supplier-code">Original Weight</div>
                                                                                                                                                                                                                      <div class="grn-column grn-supplier-code">Original Packs</div>
                                                                                                                                                                                                                     <div class="grn-column grn-grn-no">Remaining Weight</div>
                                                                                                                                                                                                                    <div class="grn-column grn-packs"> Remaining Packs</div>

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
                            const selectedOption = $(e.params.data.element); // Get the raw <option> element
                            const data = selectedOption.data();
                            // Access its data attributes

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
                            itemNameField.value = data.itemName || '';
                            // Also set the hidden item_name field

                            weightField.value = '';
                            pricePerKgField.value = '';
                            packsField.value = '';

                            // ADDED: Populate hidden fields for original_weight and original_packs
                            $('#original_weight_input').val(data.originalWeight); // Access using camelCase
                            $('#original_packs_input').val(data.originalPacks);   // Access using camelCase

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


                        document.addEventListener('keydown', function (e) {
                            if (e.key === "F1") {
                                e.preventDefault();

                                const tableRows = document.querySelectorAll('#mainSalesTableBody tr');
                                if (!tableRows.length || (tableRows.length === 1 && tableRows[0].querySelector('td[colspan="7"]'))) {
                                    alert('No sales records in the table to print!');
                                    return;
                                }

                                const salesData = [];
                                tableRows.forEach(row => {
                                    if (row.hasAttribute('data-sale-id')) {
                                        const cells = row.querySelectorAll('td');
                                        salesData.push({
                                            id: row.getAttribute('data-sale-id'),
                                            customer_code: row.getAttribute('data-customer-code'),
                                            customer_name: row.getAttribute('data-customer-name'),
                                            mobile: row.getAttribute('data-customer-mobile') || '',
                                            code: cells[0]?.textContent.trim() || '',
                                            item_code: cells[1]?.textContent.trim() || '',
                                            item_name: cells[2]?.textContent.trim() || '',
                                            weight: parseFloat(cells[3]?.textContent) || 0,
                                            price_per_kg: parseFloat(cells[4]?.textContent) || 0,
                                            total: parseFloat(cells[5]?.textContent) || 0,
                                            packs: parseInt(cells[6]?.textContent) || 0
                                        });
                                    }
                                });

                                if (!salesData.length) {
                                    alert('No printable sales records found!');
                                    return;
                                }

                                const salesByCustomer = salesData.reduce((acc, sale) => {
                                    (acc[sale.customer_code] ||= []).push(sale);
                                    return acc;
                                }, {});
                                const customerCode = Object.keys(salesByCustomer)[0];
                                const customerSales = salesByCustomer[customerCode];
                                const customerName = customerSales[0].customer_code || 'N/A';
                                const mobile = customerSales[0]?.mobile || '-';
                                const billNo = Math.floor(1000 + Math.random() * 9000).toString();
                                const date = new Date().toLocaleDateString();
                                const time = new Date().toLocaleTimeString();

                                let totalAmountSum = 0;
                                const salesIds = [];

                                const itemsHtml = customerSales.map(sale => {
                                    totalAmountSum += sale.total;
                                    salesIds.push(sale.id);
                                    return `
                                <tr>
                                    <td style="text-align: left; padding: 2px 0;">${sale.item_name} <br>${sale.packs}</td>
                                    <td style="text-align: right; padding: 2px 0;">${sale.weight.toFixed(2)}</td>
                                    <td style="text-align: right; padding: 2px 0;">${sale.price_per_kg.toFixed(2)}</td>
                                    <td style="text-align: right; padding: 2px 0;">${sale.total.toFixed(2)}</td>
                                </tr>
                            `;
                                }).join('');

                                const receiptHtml = `
                            <div class="receipt-container" style="width: 70mm; margin: 0 auto; padding: 0;">
                                <div class="company-info" style="text-align: center; margin-bottom: 5px;">
                                    <h3 style="font-size: 1.2em; margin-bottom: 2px; font-weight: bold;">
                                        <span style="font-weight: bold;">C11</span> TGK ට්‍රේඩර්ස්
                                    </h3>
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
                                            <td colspan="2" style="text-align: right; padding: 0;">
                                                <span style="font-weight: bold; font-size: 1.1rem; text-transform: uppercase;">${customerName}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <hr style="border: none; height: 4px; background-color: black; margin: 5px 0; width: 100%;">

                                <div class="items-section">
                                    <table style="width: 100%; font-size: 10px; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th style="text-align: left; padding: 2px 0;">වර්ගය<br>මලු</th>
                                                <th style="text-align: right; padding: 2px 0;">කිලෝ</th>
                                                <th style="text-align: right; padding: 2px 0;">මිල</th>
                                                <th style="text-align: right; padding: 2px 0;">අගය</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td colspan="4"><div style="height: 4px; background-color: black; margin: 5px 0;"></div></td></tr>
                                            ${itemsHtml}
                                        </tbody>
                                    </table>
                                </div>

                                <hr style="border: none; height: 4px; background-color: black; margin: 5px 0; width: 100%;">

                                <div class="summary-section" style="text-align: left; margin-bottom: 5px;">
                                    <table style="width: 100%; font-size: 10px; border-collapse: collapse;">
                                        <tr>
                                            <td colspan="3" style="text-align: left; padding: 0;">අගය :</td>
                                            <td style="text-align: right; font-weight: bold; font-size: 12px; padding: 0;">
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

                                printReceipt(receiptHtml, customerName, () => {
                                    if (salesIds.length) {
                                        fetch("{{ route('sales.markAsPrinted') }}", {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({ sales_ids: salesIds, bill_no: billNo })
                                        })
                                            .then(res => res.json())
                                            .then(data => {
                                                console.log("Marked printed:", data);
                                                sessionStorage.setItem('focusOnCustomerSelect', 'true');
                                                window.location.reload();
                                            })
                                            .catch(err => {
                                                console.error("Print marking failed:", err);
                                                alert('Failed to mark sales as printed.');
                                            });
                                    }
                                });
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
                        function printReceipt(salesContent, customerName, onCompleteCallback) {
                            const printWindow = window.open('', '', 'width=300,height=600');

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
                                        overflow: hidden;
                                    }
                                    .receipt-container {
                                        width: 100%;
                                        max-width: 70mm;
                                        margin-left: 0;
                                        margin-right: auto;
                                        border: none;
                                        padding: 0;
                                        text-align: left;
                                    }
                                    .company-info {
                                        text-align: left;
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
                                        text-align: left;
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
                                    }
                                    .footer-section {
                                        text-align: left;
                                        margin-top: 10px;
                                    }
                                    .footer-section p {
                                        margin: 0;
                                        line-height: 1.2;
                                    }
                                    hr {
                                        display: block;
                                        height: 1px;
                                        background: transparent;
                                        width: 100%;
                                        border: none;
                                        border-top: solid 2px #000 !important;
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

                            // Give it time to render styles, then print
                            setTimeout(() => {
                                printWindow.print();

                                // Auto-close after 10 seconds
                                setTimeout(() => {
                                    if (!printWindow.closed) {
                                        printWindow.close();
                                    }
                                    if (typeof onCompleteCallback === 'function') {
                                        onCompleteCallback();
                                    }
                                }, 00000);
                            }, 500);
                        }




                        // New event listener for page refresh or window close
                        document.addEventListener('DOMContentLoaded', function () {
                            let isRefresh = false;

                            // We'll set a flag if the user presses F5 or Ctrl+R (common refresh shortcuts)
                            window.addEventListener('keydown', function (e) {
                                if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                                    isRefresh = true;
                                }
                            });

                            // The 'beforeunload' event fires for both refresh and closing.

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

    currentDisplayedSalesData = salesArray;
    console.log("currentDisplayedSalesData updated to:", currentDisplayedSalesData);

    const mainSalesTableBodyElement = document.getElementById('mainSalesTableBody');

    if (!mainSalesTableBodyElement) {
        console.error("Error: tbody with ID 'mainSalesTableBody' not found!");
        return;
    }

    // Clear existing rows safely
    while (mainSalesTableBodyElement.firstChild) {
        mainSalesTableBodyElement.removeChild(mainSalesTableBodyElement.firstChild);
    }

    let totalSalesValue = 0;

    if (salesArray.length === 0) {
        console.log("Sales array is empty. Displaying 'No sales records found.'");
        const noRecordsRow = document.createElement('tr');
        noRecordsRow.innerHTML = '<td colspan="8" class="text-center">No sales records found for this selection.</td>';
        mainSalesTableBodyElement.appendChild(noRecordsRow);
        totalSalesValue = 0;
    } else {
        salesArray.forEach(sale => {
            const newRow = document.createElement('tr');
            newRow.dataset.saleId = sale.id;
            newRow.dataset.id = sale.id;
            newRow.dataset.customerCode = sale.customer_code;
            newRow.dataset.customerName = sale.customer_name;

            // Ensure all values are handled gracefully, with a fallback to 'N/A' or 0
            const code = sale.code || 'N/A';
            const itemCode = sale.item_code || 'N/A';
            const itemName = sale.item_name || 'N/A';
            const weight = (parseFloat(sale.weight) || 0).toFixed(2);
            const pricePerKg = (parseFloat(sale.price_per_kg) || 0).toFixed(2);
            const total = (parseFloat(sale.total) || 0).toFixed(2);
            const packs = (parseInt(sale.packs) || 0);

            newRow.innerHTML = `
                <td data-field="code">${code}</td>
                <td data-field="item_code">${itemCode}</td>
                <td data-field="item_name">${itemName}</td>
                <td data-field="weight">${weight}</td>
                <td data-field="price_per_kg">${pricePerKg}</td>
                <td data-field="total">${total}</td>
                <td data-field="packs">${packs}</td>
            `;

            mainSalesTableBodyElement.appendChild(newRow);
            totalSalesValue += parseFloat(total);
        });
    }

    $('#mainTotalSalesValue').text(totalSalesValue.toFixed(2));
    console.log("populateMainSalesTable finished. Total sales value:", totalSalesValue.toFixed(2));
}


// Call the function initially to populate the table
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

    // --- NEW LOGIC TO POPULATE GRN FIELDS ---

    // 1. Show the `grn_display` input field
    const grnDisplay = document.getElementById('grn_display');
    grnDisplay.style.display = 'block';
    grnDisplay.value = sale.code || '';

    // 2. Hide the `grn_select` dropdown
    const grnSelect = document.getElementById('grn_select');
    $(grnSelect).next('.select2-container').hide();
    console.log("Setting grn_display to:", grnDisplay.value);

    // Set value of hidden select if option matches
    const grnOption = $('#grn_select option').filter(function () {
        return $(this).val() === sale.code && $(this).data('supplierCode') === sale.supplier_code &&
            $(this).data('itemCode') === sale.item_code;
    });
    if (grnOption.length) {
        $('#grn_select').val(grnOption.val());
        console.log("Setting grn_select to:", grnOption.val());
    } else {
        $('#grn_select').val(null);
        console.log("Clearing grn_select.");
    }

    // --- END OF NEW LOGIC ---

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

    // ✅ Focus the cursor on the weight field
    weightField.focus();
}

// When Enter pressed in #weight input, move focus to #price_per_kg
document.getElementById('weight').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault(); // prevent form submission or other defaults
        document.getElementById('price_per_kg').focus();
    }
});

document.getElementById('price_per_kg').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault(); // prevent form submission or other defaults
        document.getElementById('packs').focus();
    }
});


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

// Get references
const salesEntryForm = document.getElementById('salesEntryForm');
const updateSalesEntryBtn = document.getElementById('updateSalesEntryBtn');
const saleIdField = document.getElementById('sale_id');

let originalFormData = {}; // To store the original values for comparison

// Helper function to get current form data as an object
function getCurrentFormData(form) {
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    return data;
}

// Store original form data when a record is selected (you must call this manually when loading data)
function storeOriginalFormData() {
    if (salesEntryForm) {
        originalFormData = getCurrentFormData(salesEntryForm);
    }
}

// Compare current data with original to see if any changes were made
function isFormDataChanged(currentData) {
    for (let key in currentData) {
        if (currentData[key] !== originalFormData[key]) {
            return true; // At least one field has changed
        }
    }
    return false;
}

if (salesEntryForm && updateSalesEntryBtn && saleIdField) {

    // Enter keypress triggers update only if update button is visible
    salesEntryForm.addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            const style = window.getComputedStyle(updateSalesEntryBtn);
            const visible = style.display !== 'none' && style.visibility !== 'hidden';
            const rendered = updateSalesEntryBtn.offsetWidth > 0 || updateSalesEntryBtn.offsetHeight > 0;

            if (visible && rendered) {
                event.preventDefault();
                updateSalesEntryBtn.click();
            }
        }
    });

    // Click event for update button
    updateSalesEntryBtn.addEventListener('click', function () {
        const saleId = saleIdField.value;
        if (!saleId) {
            alert('No record selected for update.');
            return;
        }

        const currentFormData = getCurrentFormData(salesEntryForm);
        if (!isFormDataChanged(currentFormData)) {
            alert('No changes detected. Update not required.');
            return;
        }

        // Add method and token
        currentFormData['_method'] = 'PUT';
        currentFormData['_token'] = '{{ csrf_token() }}';

        fetch(`sales/update/${saleId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(currentFormData)
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => Promise.reject(errorData));
                }
                return response.json();
            })
            .then(result => {
                console.log("Server response:", result);

                // Check the structure of the returned `sale` object here
                console.log("Updated sale record received from server:", result.sale); // THIS IS THE NEW LOG TO HELP YOU DEBUG

                if (result.success && result.sale) {
                    alert(result.message);

                    const updatedIndex = currentDisplayedSalesData.findIndex(sale => String(sale.id) === String(saleId));
                    if (updatedIndex !== -1) {
                        currentDisplayedSalesData[updatedIndex] = result.sale;
                        populateMainSalesTable(currentDisplayedSalesData);
                    } else {
                        alert("Record updated on server but not found in local data. Reloading.");
                        window.location.reload();
                    }

                    resetForm(); // Reset form
                } else {
                    alert('Update failed: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error updating sales entry:', error);
                let errorMessage = 'An error occurred during update.';
                if (error?.message) errorMessage += '\n' + error.message;
                if (error?.errors) {
                    for (const key in error.errors) {
                        errorMessage += `\n${key}: ${error.errors[key].join(', ')}`;
                    }
                }
                alert(errorMessage);
            });
    });
}else {
                            console.error("Form, update button, or sale ID field not found. Please check their IDs.");
                        }
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
                {{-- typing customer code and fetching data from unprinted sales records them) --}}



@endsection