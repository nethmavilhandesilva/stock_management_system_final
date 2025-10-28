@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #99ff99;
            /* Full page background */
        }

        /* Optional: keep your forms a little distinct */
        .bg-light {
            background-color: #ffffff !important;
        }
    </style>
    <div class="container mt-4">
        <h3 class="text-success mb-3">Supplier Management</h3>

        {{-- Display Alerts --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- Radio buttons for Add Supplier / Payments --}}
        <div class="mb-3">
            <div class="form-check form-check-inline">
                {{-- FIX: Added 'checked' to display this form by default --}}
                <input class="form-check-input" type="radio" name="action_type" id="addSupplierRadio" checked>
                <label class="form-check-label fw-bold" for="addSupplierRadio">Purchase from Supplier</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="action_type" id="paymentRadio">
                <label class="form-check-label fw-bold" for="paymentRadio">Settle to Supplier</label>
            </div>
        </div>

        {{-- Add Supplier Form --}}
        {{-- FIX: Removed style="display: none;" as it will be shown by default with the 'checked' radio --}}
        <form id="supplierForm" action="{{ route('suppliers2.store') }}" method="POST" class="border p-3 rounded bg-light">
            @csrf

            <div class="row mb-3 align-items-end">
                <div class="col-md-3 position-relative">
                    <label for="supplier_search" class="form-label">Select Supplier</label>
                    <input type="text" id="supplier_search" class="form-control" placeholder="🔍 Type supplier code..."
                        autocomplete="off" required> {{-- Added required for visual cue --}}
                    {{-- This hidden ID is critical for the controller to find an existing supplier --}}
                    <input type="hidden" name="existing_supplier_id" id="supplier_id">

                    <input type="hidden" name="supplier_code" id="supplier_code">
                    <input type="hidden" name="supplier_name" id="supplier_name">

                    <div id="supplierDropdown" class="list-group position-absolute w-100 shadow-sm"
                        style="z-index: 10; display:none; max-height:200px; overflow-y:auto;">
                        @foreach($existingSuppliers as $ex)
                            <button type="button" class="list-group-item list-group-item-action" data-id="{{ $ex->id }}"
                                data-code="{{ $ex->code }}" data-name="{{ $ex->name }}">
                                {{ $ex->code }}{{ $ex->name ? ' — ' . $ex->name : '' }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="1"
                        placeholder="Enter description..."></textarea>
                </div>

                <div class="col-md-3">
                    <label for="total_amount" class="form-label">Total Amount</label>
                    <input type="number" name="total_amount" id="total_amount" class="form-control" step="0.01" required>
                </div>

                <div class="col-md-3 position-relative">
                    <label for="grn_search" class="form-label">Search GRN</label>
                    <input type="text" id="grn_search" class="form-control" placeholder="🔍 Type GRN code or item name..."
                        autocomplete="off" required> {{-- Added required for visual cue --}}
                    <input type="hidden" name="grn_id" id="grn_id" required>

                    <div id="grnDropdown" class="list-group position-absolute w-100 shadow-sm"
                        style="z-index: 10; display:none; max-height:200px; overflow-y:auto;">
                        @foreach($grnOptions as $id => $grnNo)
                            {{-- Added data-name (using the code) for consistency --}}
                            <button type="button" class="list-group-item list-group-item-action"
                                data-id="{{ $id }}" data-name="{{ $grnNo }}">{{ $grnNo }}</button>
                        @endforeach
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Save Purchase</button>
        </form>

        {{-- Payments Form (Hidden by default) --}}
        <form id="paymentForm" action="{{ route('suppliers2.payment') }}" method="POST" class="border p-3 rounded bg-light"
            style="display: none;">
            @csrf
         <div class="col-md-6">
    <label for="payment_supplier_code" class="form-label">Select Supplier</label>
    <select name="supplier_code" id="payment_supplier_code" class="form-select" required>
        <option value="">-- Select Supplier --</option>
        @foreach($existingSuppliers as $supplier)
            <option value="{{ $supplier->code }}">
                {{ $supplier->code }} — {{ $supplier->name }}
            </option>
        @endforeach
    </select>
</div>

                <div class="col-md-6">
                    <label for="payment_amount" class="form-label">Payment Amount</label>
                    <input type="number" name="payment_amount" id="payment_amount" class="form-control" step="0.01"
                        required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit Payment</button>
        </form>

        <hr class="my-4">

        {{-- Supplier Records --}}
        <h5 class="text-success">Supplier Records</h5>
        <table class="table table-bordered mt-3">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Supplier Code</th>
                    <th>Supplier Name</th>
                    <th>Total Balance</th>
                    <th>Description</th>
                    <th>GRN No</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->id }}</td>
                        <td>{{ $supplier->supplier_code }}</td>
                        <td>{{ $supplier->supplier_name }}</td>
                        <td>{{ number_format($supplier->total_amount, 2) }}</td>
                        <td>{{ $supplier->description ?? '-' }}</td>
                        <td>{{ $supplier->grn->code ?? '-' }}</td>
                        <td>
                            <a href="{{ route('suppliers2.edit', $supplier->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('suppliers2.destroy', $supplier->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this supplier?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No suppliers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <script>
        // Toggle forms
        const addRadio = document.getElementById('addSupplierRadio');
        const payRadio = document.getElementById('paymentRadio');
        const supplierForm = document.getElementById('supplierForm');
        const paymentForm = document.getElementById('paymentForm');

        function toggleForms() {
            if (addRadio.checked) {
                supplierForm.style.display = 'block';
                paymentForm.style.display = 'none';
            } else if (payRadio.checked) {
                supplierForm.style.display = 'none';
                paymentForm.style.display = 'block';
            }
        }

        // Run on load in case a radio is pre-checked (e.g. from validation failure)
        toggleForms();

        addRadio.addEventListener('change', toggleForms);
        payRadio.addEventListener('change', toggleForms);

        // Live Supplier Search
        const supplierSearch = document.getElementById('supplier_search');
        const supplierDropdown = document.getElementById('supplierDropdown');
        const supplierHiddenInput = document.getElementById('supplier_id');

        supplierSearch.addEventListener('input', function () {
            const filter = this.value.toUpperCase().trim();
            let visible = 0;
            supplierDropdown.querySelectorAll('button').forEach(btn => {
                const text = btn.textContent.toUpperCase();
                const match = text.includes(filter);
                btn.style.display = match ? 'block' : 'none';
                if (match) visible++;
            });
            supplierDropdown.style.display = visible ? 'block' : 'none';

            // Clear hidden IDs if search box is empty (prevent submitting an ID with empty visible text)
            if (filter === '') {
                document.getElementById('supplier_id').value = '';
                document.getElementById('supplier_code').value = '';
                document.getElementById('supplier_name').value = '';
            }
        });

        // Updated supplier selection to set all fields
        supplierDropdown.addEventListener('click', function (e) {
            if (e.target.matches('button[data-id]')) {
                // Set the visible text
                supplierSearch.value = e.target.textContent.trim();
                
                // Set the hidden fields for controller logic
                supplierHiddenInput.value = e.target.getAttribute('data-id');
                document.getElementById('supplier_code').value = e.target.getAttribute('data-code');
                document.getElementById('supplier_name').value = e.target.getAttribute('data-name') || '';

                supplierDropdown.style.display = 'none';
            }
        });

        // Live GRN Search
        const grnSearch = document.getElementById('grn_search');
        const grnDropdown = document.getElementById('grnDropdown');
        const grnHiddenInput = document.getElementById('grn_id');

        grnSearch.addEventListener('input', function () {
            const filter = this.value.toUpperCase().trim();
            let visible = 0;
            grnDropdown.querySelectorAll('button').forEach(btn => {
                const text = btn.textContent.toUpperCase();
                const match = text.includes(filter);
                btn.style.display = match ? 'block' : 'none';
                if (match) visible++;
            });
            grnDropdown.style.display = visible ? 'block' : 'none';

             // Clear hidden ID if search box is empty
            if (filter === '') {
                grnHiddenInput.value = '';
            }
        });

        grnDropdown.addEventListener('click', function (e) {
            if (e.target.matches('button[data-id]')) {
                grnSearch.value = e.target.textContent.trim();
                grnHiddenInput.value = e.target.getAttribute('data-id');
                grnDropdown.style.display = 'none';
            }
        });

        // Hide dropdowns when clicking outside
        document.addEventListener('click', function (e) {
            if (!supplierDropdown.contains(e.target) && e.target !== supplierSearch) {
                supplierDropdown.style.display = 'none';
            }
            if (!grnDropdown.contains(e.target) && e.target !== grnSearch) {
                grnDropdown.style.display = 'none';
            }
        });
    </script>
@endsection