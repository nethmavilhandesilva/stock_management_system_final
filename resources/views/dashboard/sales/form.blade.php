@extends('layouts.app')

@section('content')
    {{-- Include Material Icons if you plan to use them, similar to your example --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    {{-- Select2 Styles --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- jQuery and Select2 - Ensure these are in the <head> or loaded before scripts that use them --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card shadow-sm border-0 rounded-3 p-4">
                    <h2 class="mb-4 text-center">Add New Sales Entry</h2>

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

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label for="grn_display" class="form-label font-semibold">Select Previous GRN Record</label>
                        {{-- Visible text box to show the selected GRN entry --}}
                        <input type="text" id="grn_display" class="form-control mb-2" placeholder="Select GRN Entry..."
                            readonly>

                        {{-- Hidden select box (searchable via Select2) --}}
                        <select id="grn_select" class="form-select select2 d-none">
                            <option value="">-- Select GRN Entry --</option>
                            @foreach($entries as $entry)
                                <option value="{{ $entry->code }}" {{-- Using code as the value, as it's passed to hidden input --}}
                                    data-supplier-code="{{ $entry->supplier_code }}"
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


                    <hr class="my-2">

                    <form method="POST" action="{{ route('grn.store') }}">
                        @csrf
                        <div class="row g-4">

                            <div class="col-md-6 col-lg-4">
                                <label for="customer_code" class="form-label">Select Customer</label>
                                <select name="customer_code" id="customer_code"
                                    class="form-select select2 @error('customer_code') is-invalid @enderror" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->short_name }}"
                                            data-customer-code="{{ $customer->short_name }}"
                                            data-customer-name="{{ $customer->name }}"
                                            {{ old('customer_code') == $customer->short_name ? 'selected' : '' }}> {{-- Added 'selected' attribute here --}}
                                            {{ $customer->name }} ({{ $customer->short_name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_code')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>


                            {{-- Hidden fields for customer details --}}
                            {{-- IMPORTANT: Changed name for the first hidden input to avoid conflict --}}
                            <input type="hidden" name="customer_code_hidden" id="customer_code_hidden"
                                value="{{ old('customer_code_hidden') }}"> {{-- Updated old() value to match new name --}}
                            <input type="hidden" name="customer_name" id="customer_name_hidden"
                                value="{{ old('customer_name') }}">


                            <div class="col-md-6 col-lg-4">
                                <label for="supplier_code" class="form-label">Supplier</label>
                                <select name="supplier_code" id="supplier_code"
                                    class="form-select @error('supplier_code') is-invalid @enderror" required>
                                    <option value="">Select a Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->code }}" {{ old('supplier_code') == $supplier->code ? 'selected' : '' }}>{{ $supplier->name }} ({{ $supplier->code }})</option>
                                    @endforeach
                                </select>
                                @error('supplier_code')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="item_select" class="form-label">Select Item</label>
                                <select id="item_select" class="form-select @error('item_code') is-invalid @enderror">
                                    <option value="">Select an Item</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->item_code }}" data-code="{{ $item->code }}"
                                            data-item-code="{{ $item->item_code }}" data-item-name="{{ $item->item_name }}"
                                            {{ old('item_code') == $item->item_code ? 'selected' : '' }}>
                                            {{ $item->item_name }} ({{ $item->item_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('item_code')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Hidden fields for item details --}}
                            <input type="hidden" name="code" id="code" value="{{ old('code') }}">
                            <input type="hidden" name="item_code" id="item_code" value="{{ old('item_code') }}">
                            <input type="hidden" name="item_name" id="item_name" value="{{ old('item_name') }}">

                            <div class="col-md-6 col-lg-4">
                                <label for="weight" class="form-label">Weight (kg)</label>
                                <input type="number" name="weight" id="weight" step="0.01"
                                    class="form-control @error('weight') is-invalid @enderror"
                                    value="{{ old('weight') }}" required>
                                @error('weight')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="price_per_kg" class="form-label">Price per Kg</label>
                                <input type="number" name="price_per_kg" id="price_per_kg" step="0.01"
                                    class="form-control @error('price_per_kg') is-invalid @enderror"
                                    value="{{ old('price_per_kg') }}" required>
                                @error('price_per_kg')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="total" class="form-label">Total</label>
                                <input type="number" name="total" id="total"
                                    class="form-control bg-light @error('total') is-invalid @enderror"
                                    value="{{ old('total') }}" readonly>
                                @error('total')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="packs" class="form-label">Packs</label>
                                <input type="number" name="packs" id="packs"
                                    class="form-control @error('packs') is-invalid @enderror" value="{{ old('packs') }}"
                                    required>
                                @error('packs')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div> {{-- End row g-4 --}}

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-5">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm d-none">
                                <i class="material-icons me-2">add_circle_outline</i>Add Sales Entry
                            </button>
                        </div>

                    </form>

                    <hr class="my-2"> @if($sales->count())
                        <div class="mt-5">
                            <h3 class="mb-4 text-center">Recent Sales Records</h3>
                            <h5 class="text-end mb-3"><strong>Total Sales Value:</strong> Rs. {{ number_format($totalSum, 2) }}</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover shadow-sm rounded-3 overflow-hidden">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Code</th> {{-- Added Customer column --}}
                                            <th scope="col">Item Code</th>
                                            <th scope="col">Item</th>
                                            <th scope="col">Weight (kg)</th>
                                            <th scope="col">Price/Kg</th>
                                            <th scope="col">Total</th>
                                            <th scope="col">Packs</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sales as $sale)
                                            <tr>
                                                <td>{{ $sale->code }}</td> {{-- Display Customer --}}
                                                <td>{{ $sale->item_code }}</td>
                                                <td>{{ $sale->item_name }}</td>
                                                <td>{{ number_format($sale->weight, 2) }}</td>
                                                <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                                                <td>{{ number_format($sale->total, 2) }}</td>
                                                <td>{{ $sale->packs }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Get references to elements
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

        // const customerDisplay = document.getElementById('customer_display'); // This element doesn't exist in your HTML
        const customerSelect = document.getElementById('customer_code');
        const customerCodeField = document.getElementById('customer_code_hidden'); // Changed ID to match new name
        const customerNameField = document.getElementById('customer_name_hidden');

        function calculateTotal() {
            const weight = parseFloat(weightField.value) || 0;
            const price = parseFloat(pricePerKgField.value) || 0;
            totalField.value = (weight * price).toFixed(2);
        }

        itemSelect.addEventListener('change', function () {
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
        calculateTotal();

        $(document).ready(function () {
            $('#grn_select').select2({
                dropdownParent: $('#grn_select').parent(),
                placeholder: "-- Select GRN Entry --",
                width: '100%',
                allowClear: true,
                templateResult: function (data) {
                    if (data.loading || !data.id) return data.text;
                    return $(data.element).text();
                },
                templateSelection: function (data) {
                    return data.text;
                }
            });

            $('#customer_code').select2({
                dropdownParent: $('#customer_code').parent(),
                placeholder: "-- Select Customer --",
                width: '100%',
                allowClear: true,
                templateResult: function (data) {
                    if (data.loading || !data.id) return data.text;
                    return $(data.element).text();
                },
                templateSelection: function (data) {
                    return data.text;
                }
            });

            $('#grn_display').on('click', function () {
                $('#grn_select').select2('open');
            });

            $('#grn_select').on('select2:select', function (e) {
                const selectedOption = $(e.currentTarget).find('option:selected');
                const data = selectedOption.data();

                // ✅ Show only the code in grn_display
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

            $('#customer_code').on('select2:select', function (e) {
                const selectedOption = $(e.currentTarget).find('option:selected');
                const selectedText = selectedOption.text();

                customerCodeField.value = selectedOption.val() || '';
                const customerFullNameMatch = selectedText.match(/(.*) \((.*)\)/);
                customerNameField.value = customerFullNameMatch ? customerFullNameMatch[1].trim() : selectedText;

                weightField.focus();
            });

            $('#grn_select').on('select2:clear', function () {
                $('#grn_display').val('');
                supplierSelect.value = '';
                itemSelect.value = '';
                itemSelect.dispatchEvent(new Event('change'));
                weightField.value = '';
                pricePerKgField.value = '';
                packsField.value = '';
                calculateTotal();
            });

            $('#customer_code').on('select2:clear', function () {
                customerCodeField.value = '';
                customerNameField.value = '';
            });

            @if(old('grn_no') || old('customer_code'))
                const oldGrnCode = "{{ old('code') }}";
                const oldSupplierCode = "{{ old('supplier_code') }}";
                const oldItemCode = "{{ old('item_code') }}";
                const oldWeight = "{{ old('weight') }}";
                const oldPricePerKg = "{{ old('price_per_kg') }}";
                const oldPacks = "{{ old('packs') }}";

                const oldGrnOption = $('#grn_select option').filter(function () {
                    return $(this).val() === oldGrnCode &&
                        $(this).data('supplierCode') === oldSupplierCode &&
                        $(this).data('itemCode') === oldItemCode;
                });

                if (oldGrnOption.length) {
                    $('#grn_select').val(oldGrnOption.val()).trigger('change');
                    $('#grn_display').val(oldGrnOption.data('code')); // ✅ only show code
                    $('#weight').val(oldWeight);
                    $('#price_per_kg').val(oldPricePerKg);
                    $('#packs').val(oldPacks);
                    calculateTotal();
                }

                // Removed the JavaScript logic for old customer_code as it's now handled by Blade's 'selected' attribute
                // Only ensure the hidden customer name is set if the old customer code exists
                const oldCustomerCodeValueForHidden = "{{ old('customer_code') }}";
                const oldCustomerNameValueForHidden = "{{ old('customer_name') }}";

                if (oldCustomerCodeValueForHidden) {
                    customerCodeField.value = oldCustomerCodeValueForHidden;
                    customerNameField.value = oldCustomerNameValueForHidden;
                }
            @endif
        });
    </script>
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === "F1") {
                e.preventDefault(); // prevent default F1 behavior (browser help)

                const salesContent = `
                    <div style="padding: 20px; font-family: Arial;">
                        <h2 style="text-align: center;">Sales Invoice</h2>
                        <table border="1" cellspacing="0" cellpadding="8" width="100%" style="border-collapse: collapse;">
                            <thead style="background-color: #f2f2f2;">
                                <tr>
                                    <th>Code</th>
                                    <th>Item Code</th>
                                    <th>Item</th>
                                    <th>Weight (kg)</th>
                                    <th>Price/Kg</th>
                                    <th>Total</th>
                                    <th>Packs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->code }}</td>
                                        <td>{{ $sale->item_code }}</td>
                                        <td>{{ $sale->item_name }}</td>
                                        <td>{{ number_format($sale->weight, 2) }}</td>
                                        <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                                        <td>{{ number_format($sale->total, 2) }}</td>
                                        <td>{{ $sale->packs }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <h3 style="text-align: right; margin-top: 20px;">
                            Total Sales Value: Rs. {{ number_format($totalSum, 2) }}
                        </h3>
                    </div>
                `;

                const printWindow = window.open('', '_blank', 'width=800,height=600');
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Sales Invoice</title>
                            <style>
                                body { font-family: Arial; margin: 40px; }
                                table { width: 100%; border-collapse: collapse; }
                                th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                                th { background-color: #f2f2f2; }
                            </style>
                        </head>
                        <body>
                            ${salesContent}
                            <script>
                                window.onload = function() {
                                    window.print();
                                };
                                window.onafterprint = function() {
                                    window.close();
                                };
                            <\/script>
                        </body>
                    </html>
                `);
                printWindow.document.close();

                // Watch for when the print window closes
                const checkClosed = setInterval(function() {
                    if (printWindow.closed) {
                        clearInterval(checkClosed);

                        // Send request to move records silently
                        fetch("{{ route('sales.moveToHistory') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({})
                        })
                        .then(() => {
                            location.reload(); // Reload the page to refresh table
                        });
                    }
                }, 500);
            }
        });
    </script>
@endsection