@extends('layouts.app')

@section('content')
    {{-- Include Material Icons if you plan to use them, similar to your example --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    {{-- Select2 Styles --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- jQuery and Select2 - Ensure these are in the

    <head> or loaded before scripts that use them --}}
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
                                    <option value="{{ $entry->code }}" {{-- Using code as the value, as it's passed to hidden
                                        input --}} data-supplier-code="{{ $entry->supplier_code }}"
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


                        <hr class="my-5">

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
                                                data-customer-name="{{ $customer->name }}">
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
                                <input type="hidden" name="customer_code" id="customer_code_hidden"
                                    value="{{ old('customer_code') }}">
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
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                    <i class="material-icons me-2">add_circle_outline</i>Add Sales Entry
                                </button>
                            </div>
                        </form>

                        <hr class="my-5">

                        @if($sales->count())
                            <div class="mt-5">
                                <h3 class="mb-4 text-center">Recent Sales Records</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover shadow-sm rounded-3 overflow-hidden">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col">Customer</th> {{-- Added Customer column --}}
                                                <th scope="col">Supplier</th>
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
                                                    <td>{{ $sale->customer_name }} ({{ $sale->customer_code }})</td> {{-- Display
                                                    Customer --}}
                                                    <td>{{ $sale->supplier_code }}</td>
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
            // Get references to elements (using vanilla JS for direct access where possible)
            const itemSelect = document.getElementById('item_select');
            const codeField = document.getElementById('code');
            const itemCodeField = document.getElementById('item_code');
            const itemNameField = document.getElementById('item_name');
            const supplierSelect = document.getElementById('supplier_code');
            const weightField = document.getElementById('weight');
            const pricePerKgField = document.getElementById('price_per_kg');
            const totalField = document.getElementById('total');
            const packsField = document.getElementById('packs');
            const grnNoField = document.getElementById('grn_no'); // This one isn't on the form, but if you add it later, it's here
            const txnDateField = document.getElementById('txn_date'); // This one isn't on the form, but if you add it later, it's here
            const grnDisplay = document.getElementById('grn_display'); // visible text box

            // New customer fields
            const customerDisplay = document.getElementById('customer_display'); // Renamed to customer_display to avoid ID collision
            const customerSelect = document.getElementById('customer_code'); // This is your visible select, keep this ID for Select2 init
            const customerCodeField = document.getElementById('customer_code_hidden'); // Renamed hidden customer code field
            const customerNameField = document.getElementById('customer_name_hidden'); // Renamed hidden customer name field


            // Function to calculate and update the total
            function calculateTotal() {
                const weight = parseFloat(weightField.value) || 0;
                const price = parseFloat(pricePerKgField.value) || 0;
                totalField.value = (weight * price).toFixed(2);
            }

            // Event listener for when an item is manually selected from the 'Select Item' dropdown
            // This listener is important because the item selection can happen independently of GRN selection.
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

            // Event listeners for total calculation
            weightField.addEventListener('input', calculateTotal);
            pricePerKgField.addEventListener('input', calculateTotal);

            // Initial total calculation (if any values are pre-filled, e.g., on edit form or with old data)
            calculateTotal();

            // jQuery block for Select2 and GRN handling
            $(document).ready(function () {
                // Initialize Select2 for GRN
                $('#grn_select').select2({
                    dropdownParent: $('#grn_select').parent(), // Ensures dropdown is correctly positioned
                    placeholder: "-- Select GRN Entry --",
                    width: '100%',
                    allowClear: true,
                    // Custom templating for options to show all data attributes
                    templateResult: function (data) {
                        if (data.loading) return data.text;
                        if (!data.id) return data.text; // "Select GRN Entry" option
                        const option = $(data.element);
                        return option.text();
                    },
                    templateSelection: function (data) {
                        if (!data.id) {
                            // This is the placeholder text
                            return data.text;
                        }
                        const option = $(data.element);
                        // Return the full text for the display box
                        return option.text();
                    }
                });

                // Initialize Select2 for Customer - **Note: You had `customer_select` here, but your HTML has `customer_code`. I've adjusted to `customer_code`**
                $('#customer_code').select2({
                    dropdownParent: $('#customer_code').parent(), // Adjusted parent to match actual ID
                    placeholder: "-- Select Customer --",
                    width: '100%',
                    allowClear: true,
                    templateResult: function (data) {
                        if (data.loading) return data.text;
                        if (!data.id) return data.text;
                        const option = $(data.element);
                        return option.text();
                    },
                    templateSelection: function (data) {
                        if (!data.id) {
                            return data.text;
                        }
                        const option = $(data.element);
                        return option.text();
                    }
                });

                // When user clicks the visible GRN input, open the Select2 dropdown
                $('#grn_display').on('click', function () {
                    $('#grn_select').select2('open');
                });

                // When user clicks the visible Customer input, open the Select2 dropdown
                // **Note: You don't have a `customer_display` input in your provided HTML. If you add one, uncomment and use this.**
                // $('#customer_display').on('click', function () {
                //     $('#customer_code').select2('open');
                // });


                // When a selection is made from GRN Select2
                $('#grn_select').on('select2:select', function (e) {
                    const selectedOption = $(e.currentTarget).find('option:selected');
                    const selectedText = selectedOption.text();
                    $('#grn_display').val(selectedText);

                    // Fill other fields based on selected data attributes
                    const data = selectedOption.data();

                    // Note: grn_no and txn_date are not directly used in the form's post,
                    // but if you had fields for them, this is where you'd populate them.
                    // $('#grn_no').val(data.grnNo || '');
                    // $('#txn_date').val(data.txnDate || '');

                    // Set the supplier dropdown value
                    supplierSelect.value = data.supplierCode || '';
                    // If you're using Select2 for supplier_code, you'd do:
                    // $('#supplier_code').val(data.supplierCode || '').trigger('change');

                    // Set the item dropdown value AND trigger its change event
                    // This ensures the vanilla JS itemSelect listener updates the hidden item fields
                    itemSelect.value = data.itemCode || '';
                    const itemChangeEvent = new Event('change');
                    itemSelect.dispatchEvent(itemChangeEvent);

                    // Clear weight and price and packs as per requirement, and recalculate total
                    weightField.value = '';
                    pricePerKgField.value = '';
                    packsField.value = '';
                    calculateTotal(); // Update total after clearing dependent fields

                    // *** ADDED: Focus on the weight field after GRN selection ***
                    weightField.focus();
                });

                // When a selection is made from Customer Select2
                // **Note: ID is `customer_code` for the select element**
                $('#customer_code').on('select2:select', function (e) {
                    const selectedOption = $(e.currentTarget).find('option:selected');
                    const selectedText = selectedOption.text();
                    // If you had a customer_display input, you'd update it here:
                    // $('#customer_display').val(selectedText); // Update visible display

                    // Your current hidden fields are already named `customer_code` and `customer_name`
                    // in the form, and you've explicitly added `_hidden` to the JS variables.
                    // Let's ensure these are correctly updated from the selected option.
                    // The `value` of the option is `customer->short_name`, which goes into `customer_code`.
                    customerCodeField.value = selectedOption.val() || '';
                    // To get the full name for `customer_name`, you'd need a data attribute or parse the text.
                    // Based on your current options, the full text includes the name.
                    const customerFullNameMatch = selectedText.match(/(.*) \((.*)\)/);
                    customerNameField.value = customerFullNameMatch ? customerFullNameMatch[1].trim() : selectedText;

                    // *** ADDED: Focus on the weight field after Customer selection ***
                    weightField.focus();
                });

                // Clear GRN textbox if cleared from Select2
                $('#grn_select').on('select2:clear', function () {
                    $('#grn_display').val('');
                    // Clear any related fields that were populated by GRN selection
                    // $('#grn_no').val('');
                    // $('#txn_date').val('');
                    supplierSelect.value = ''; // Clear supplier select
                    itemSelect.value = ''; // Clear item select
                    const itemChangeEvent = new Event('change'); // Trigger item select change to clear hidden fields
                    itemSelect.dispatchEvent(itemChangeEvent);
                    weightField.value = '';
                    pricePerKgField.value = '';
                    packsField.value = '';
                    calculateTotal(); // Update total after clearing dependent fields
                });

                // Clear Customer textbox if cleared from Select2
                // **Note: ID is `customer_code` for the select element**
                $('#customer_code').on('select2:clear', function () {
                    // If you had a customer_display input, you'd clear it here:
                    // $('#customer_display').val('');
                    customerCodeField.value = '';
                    customerNameField.value = '';
                });

                // Populate form fields with old input on validation error or previous submission attempt
                // This is crucial for user experience.
                @if(old('grn_no') || old('customer_code'))
                    // Handle GRN old input
                    const oldGrnCode = "{{ old('code') }}"; // Use 'code' as the value for GRN
                    const oldSupplierCode = "{{ old('supplier_code') }}";
                    const oldItemCode = "{{ old('item_code') }}";
                    const oldWeight = "{{ old('weight') }}";
                    const oldPricePerKg = "{{ old('price_per_kg') }}";
                    const oldPacks = "{{ old('packs') }}";

                    const oldGrnOption = $('#grn_select option').filter(function () {
                        // Adjust this filter if your 'code' for GRN is not unique,
                        // or if you need to match more data attributes to uniquely identify the old GRN.
                        return $(this).val() === oldGrnCode &&
                            $(this).data('supplierCode') === oldSupplierCode &&
                            $(this).data('itemCode') === oldItemCode;
                        // && $(this).data('grnNo') === oldGrnNo; // Uncomment if grn_no is also part of unique identification
                        // && $(this).data('txnDate') === oldTxnDate; // Uncomment if txn_date is also part of unique identification
                    });

                    if (oldGrnOption.length) {
                        $('#grn_select').val(oldGrnOption.val()).trigger('change');
                        $('#grn_display').val(oldGrnOption.text());

                        // Manually set other fields which are cleared by select2:select, but need old values
                        $('#weight').val(oldWeight);
                        $('#price_per_kg').val(oldPricePerKg);
                        $('#packs').val(oldPacks);
                        calculateTotal(); // Recalculate total with old weight/price
                    }

                    // Handle Customer old input
                    const oldCustomerCodeValue = "{{ old('customer_code') }}";
                    const oldCustomerNameValue = "{{ old('customer_name') }}"; // Assuming you're passing customer_name back as old()

                    const oldCustomerOption = $('#customer_code option').filter(function () {
                        return $(this).val() === oldCustomerCodeValue;
                    });

                    if (oldCustomerOption.length) {
                        $('#customer_code').val(oldCustomerOption.val()).trigger('change');
                        // If you had a customer_display input, you'd update it here:
                        // $('#customer_display').val(oldCustomerOption.text());

                        // The hidden customer fields are already populated by old() in the HTML,
                        // but if Select2's change event overwrites them, this ensures they're correct.
                        customerCodeField.value = oldCustomerCodeValue;
                        customerNameField.value = oldCustomerNameValue;
                    }
                @endif
            });
        </script>


@endsection