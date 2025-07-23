@extends('layouts.app')

@section('content')
    {{-- CSS Includes --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> {{ session('error' )}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label for="grn_display" class="form-label font-semibold">Select Previous GRN Record</label>
                        <input type="text" id="grn_display" class="form-control mb-2" placeholder="Select GRN Entry..."
                            readonly>
                        <select id="grn_select" class="form-select select2 d-none">
                            <option value="">-- Select GRN Entry --</option>
                            @foreach($entries as $entry)
                                <option value="{{ $entry->code }}"
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
                                            {{ old('customer_code') == $customer->short_name ? 'selected' : '' }}>
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

                            <input type="hidden" name="customer_code_hidden" id="customer_code_hidden"
                                value="{{ old('customer_code_hidden') }}">
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
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-5">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm d-none">
                                <i class="material-icons me-2">add_circle_outline</i>Add Sales Entry
                            </button>
                        </div>
                    </form>

                    <hr class="my-2">
                    {{-- The table will now always show all sales, regardless of Processed status --}}
                    @if($sales->count())
                        <div class="mt-5">
                            <h3 class="mb-4 text-center">All Sales Records</h3> {{-- Changed heading --}}
                            <h5 class="text-end mb-3"><strong>Total Sales Value:</strong> Rs. {{ number_format($totalSum, 2) }}</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover shadow-sm rounded-3 overflow-hidden">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Code</th>
                                            <th scope="col">Item Code</th>
                                            <th scope="col">Item</th>
                                            <th scope="col">Weight (kg)</th>
                                            <th scope="col">Price/Kg</th>
                                            <th scope="col">Total</th>
                                            <th scope="col">Packs</th>
                                            {{-- Optionally display internal flags for debugging/info --}}
                                           
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
                                                {{-- Displaying the flag --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="mt-5 alert alert-info text-center">No sales records found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Debug URL display (remains as it was) --}}
    <p>Debug F5 Route URL: {{ route('sales.markAllAsProcessed') }}</p>

    {{-- JavaScript Includes (jQuery and Select2 should always be loaded before your custom script that uses them) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
        const customerSelect = document.getElementById('customer_code');
        const customerCodeField = document.getElementById('customer_code_hidden');
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
        calculateTotal(); // Initial calculation on page load

        $(document).ready(function () {
            $('#grn_select').select2({
                dropdownParent: $('#grn_select').parent(),
                placeholder: "-- Select GRN Entry --",
                width: '100%',
                allowClear: true,
                templateResult: function (data) { if (data.loading || !data.id) return data.text; return $(data.element).text(); },
                templateSelection: function (data) { return data.text; }
            });

            $('#customer_code').select2({
                dropdownParent: $('#customer_code').parent(),
                placeholder: "-- Select Customer --",
                width: '100%',
                allowClear: true,
                templateResult: function (data) { if (data.loading || !data.id) return data.text; return $(data.element).text(); },
                templateSelection: function (data) { return data.text; }
            });

            $('#grn_display').on('click', function () { $('#grn_select').select2('open'); });

            $('#grn_select').on('select2:select', function (e) {
                const selectedOption = $(e.currentTarget).find('option:selected');
                const data = selectedOption.data();
                $('#grn_display').val(data.code || '');
                supplierSelect.value = data.supplierCode || '';
                itemSelect.value = data.itemCode || '';
                itemSelect.dispatchEvent(new Event('change')); // Trigger change to update hidden item fields
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
                itemSelect.dispatchEvent(new Event('change')); // Clear hidden item fields
                weightField.value = '';
                pricePerKgField.value = '';
                packsField.value = '';
                calculateTotal();
            });

            $('#customer_code').on('select2:clear', function () {
                customerCodeField.value = '';
                customerNameField.value = '';
            });

            // Handle old input values on page load
            @if(old('grn_no') || old('customer_code'))
                const oldGrnCode = "{{ old('code') }}";
                const oldSupplierCode = "{{ old('supplier_code') }}";
                const oldItemCode = "{{ old('item_code') }}";
                const oldWeight = "{{ old('weight') }}";
                const oldPricePerKg = "{{ old('price_per_kg') }}";
                const oldPacks = "{{ old('packs') }}";
                const oldGrnOption = $('#grn_select option').filter(function () {
                    return $(this).val() === oldGrnCode && $(this).data('supplierCode') === oldSupplierCode && $(this).data('itemCode') === oldItemCode;
                });
                if (oldGrnOption.length) {
                    $('#grn_select').val(oldGrnOption.val()).trigger('change');
                    $('#grn_display').val(oldGrnOption.data('code'));
                    $('#weight').val(oldWeight);
                    $('#price_per_kg').val(oldPricePerKg);
                    $('#packs').val(oldPacks);
                    calculateTotal();
                }
                const oldCustomerCodeValueForHidden = "{{ old('customer_code') }}";
                const oldCustomerNameValueForHidden = "{{ old('customer_name') }}";
                if (oldCustomerCodeValueForHidden) {
                    customerCodeField.value = oldCustomerCodeValueForHidden;
                    customerNameField.value = oldCustomerNameValueForHidden;
                }
            @endif
        });

        // --- JavaScript for F1 and F5 Key Presses (ORIGINAL LOGIC RESTORED) ---
        document.addEventListener('keydown', function(e) {
            console.log('Key pressed:', e.key); // DEBUG: Log any key pressed

            // F1 Key Press Logic (Print Receipt and Mark Sales as Printed and Processed)
            if (e.key === "F1") {
                e.preventDefault(); // Prevent default F1 behavior (browser help)
                console.log('F1 key pressed - attempting to print and mark sales...'); // DEBUG

                const salesDataForReceipt = @json($sales); // Get currently displayed sales

                if (salesDataForReceipt.length === 0) {
                    alert('No sales records to print!');
                    return;
                }

                const salesIdsToMarkPrintedAndProcessed = salesDataForReceipt.map(sale => sale.id);

                // Construct receipt content
                const now = new Date();
                const date = now.toLocaleDateString();
                const time = now.toLocaleTimeString();
                const customerCode = document.getElementById('customer_code_hidden').value || 'N/A';
                const customerName = document.getElementById('customer_name_hidden').value || 'N/A';
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
                        <div class="header-section"><h2>Your Grocery Shop</h2><p>123 Main Street, Gonawala, Sri Lanka</p><p>Phone: +94 11 234 5678</p><p>Date: ${date}</p><p>Time: ${time}</p><p>Customer Code: ${customerCode}</p><p>Customer Name: ${customerName}</p></div>
                        <div class="divider"></div>
                        <div class="items-section"><table><thead><tr><th class="item-name-col">Item</th><th class="qty-col">Qty</th><th class="price-col">Unit Price</th><th class="total-col">Amount</th></tr></thead><tbody>${itemsHtml}</tbody></table></div>
                        <div class="divider"></div>
                        <div class="totals-section"><p>Total Items: ${totalItemsCount}</p><p class="grand-total">Total Amount: <span>Rs. ${totalAmountSum.toFixed(2)}</span></p></div>
                        <div class="footer-section"><p>Thank you for your purchase!</p><p>Please come again.</p></div>
                    </div>
                `;

                const printWindow = window.open('', '_blank', 'width=400,height=600');
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Sales Receipt</title>
                            <style>
                                body { font-family: 'Consolas', 'Courier New', monospace; margin: 0; padding: 20px; box-sizing: border-box; font-size: 12px; }
                                .receipt-container { width: 100%; max-width: 380px; margin: 0 auto; border: 1px dashed #000; padding: 15px; }
                                .header-section, .footer-section { text-align: center; margin-bottom: 10px; }
                                .header-section h2 { margin: 0; font-size: 1.5em; }
                                .header-section p { margin: 2px 0; }
                                .divider { border-bottom: 1px dashed #000; margin: 10px 0; }
                                .items-section table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
                                .items-section th, .items-section td { padding: 4px 0; border: none; }
                                .items-section thead th { border-bottom: 1px solid #000; padding-bottom: 5px; text-align: left; }
                                .item-name-col { width: 40%; text-align: left; }
                                .qty-col { width: 30%; text-align: right; }
                                .price-col { width: 15%; text-align: right; }
                                .total-col { width: 15%; text-align: right; }
                                .align-right { text-align: right; }
                                .totals-section { text-align: right; margin-top: 10px; }
                                .totals-section p { margin: 2px 0; }
                                .grand-total { font-size: 1.2em; font-weight: bold; }
                            </style>
                        </head>
                        <body>
                            ${salesContent}
                            <script>
                                window.onload = function() { window.print(); };
                                window.onafterprint = function() { window.close(); };
                            <\/script>
                        </body>
                    </html>
                `);
                printWindow.document.close();

                // Monitor if the print window is closed by the user
                const checkClosed = setInterval(function() {
                    if (printWindow.closed) {
                        clearInterval(checkClosed); // Stop checking

                        // Send sales IDs to backend to mark as bill_printed = 'Y' AND Processed = 'Y'
                        console.log('F1: Print window closed. Sending request to mark sales as printed and processed.'); // DEBUG
                        fetch("{{ route('sales.markAsPrinted') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ sales_ids: salesIdsToMarkPrintedAndProcessed })
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => { throw new Error(`HTTP error! status: ${response.status}, message: ${text}`) });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('F1: Sales marked as printed and processed response:', data); // DEBUG
                            // NO WINDOW.LOCATION.RELOAD() HERE: Records remain visible as per new requirement
                        })
                        .catch(error => {
                            console.error('F1: Error marking sales as printed and processed:', error); // DEBUG
                            alert('Failed to mark sales as printed. Please check console for details.');
                        });
                    }
                }, 500); // Check every 500ms
            }
            // F5 Key Press Logic (Mark All Displayed Sales as Processed)
            else if (e.key === "F5") {
                e.preventDefault(); // Prevent default F5 behavior (browser refresh)
                console.log('F5 key pressed - attempting to mark all displayed sales as processed...'); // DEBUG

                fetch("{{ route('sales.markAllAsProcessed') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    // Check if response is OK (status 200-299)
                    if (!response.ok) {
                        return response.text().then(text => { throw new Error(`HTTP error! status: ${response.status}, message: ${text}`) });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response from sales.markAllAsProcessed (F5):', data); // DEBUG
                    if (data.success) {
                        console.log(data.message);
                        // Refresh the page to reflect changes
                        window.location.reload();
                    } else {
                        console.error('Server reported an error:', data.message);
                        alert('Operation failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error marking sales as processed by F5:', error); // DEBUG
                    alert('Failed to process sales on F5. Check console for details.');
                });
            }
        });
    </script>
@endsection