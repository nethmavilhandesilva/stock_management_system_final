@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #99ff99 !important;
        }

        .custom-card {
            background-color: #004d00 !important;
            color: #fff;
            padding: 25px;
            border-radius: 10px;
        }

        .form-control,
        .form-select {
            padding: 0.15rem 0.4rem !important;
            font-size: 0.75rem !important;
            border: 1px solid black !important;
            color: black !important;
            font-weight: bold !important;
            background-color: white !important;
        }

        .table td,
        .table th {
            padding: 0.3rem;
            font-size: 0.875rem;
        }

        label {
            font-weight: 500;
            margin-bottom: 0.2rem;
            color: #000;
        }

        .table th {
            background-color: #006600;
            color: white;
        }

        h3,
        h4 {
            color: #ffffff;
        }
    </style>

    <div class="container my-4">
        <div class="custom-card">

            <h3 class="mb-4">Customers Loans</h3>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Loan Entry Form --}}
            <form action="{{ route('customers-loans.store') }}" method="POST" id="loanForm">
                @csrf
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label><strong>Loan Type:</strong></label><br>
                        <label><input type="radio" name="loan_type" value="old" checked>වෙළෙන්දාගේ ලාද පරණ නය</label>
                        <label class="ms-3"><input type="radio" name="loan_type" value="today">වෙළෙන්දාගේ අද දින නය ගැනීම
                        </label>
                    </div>
                    <input type="hidden" name="loan_id" id="loan_id">

                    <div class="col-md-4 mb-2"> <label for="customer_id">ගෙණුම්කරු</label>
                        <select class="form-select" id="customer_id" name="customer_id" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->short_name }} - {{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-2"> <label><strong>Settling Way:</strong></label><br>
                        <label><input type="radio" name="settling_way" value="cash" checked> Cash</label>
                        <label class="ms-3"><input type="radio" name="settling_way" value="cheque"> Cheque</label>
                    </div>

                    {{-- Bill No Section - Initially visible (controlled by JS and d-none) --}}
                    <div class="col-md-4 mb-2" id="billNoSection">
                        <label for="bill_no">Bill No</label>
                        <input type="text" class="form-control" name="bill_no">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="description">විස්තරය</label>
                        <input type="text" class="form-control" name="description" id="description" required>
                        <span id="totalAmountDisplay" style="color: #ff0000; font-weight: bold; font-size: 0.9rem; margin-left: 10px;"></span>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="amount">මුදල</label>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>

                    {{-- Cheque Fields - Initially hidden using Bootstrap's d-none class --}}
                    <div id="chequeFields" class="d-flex flex-wrap gap-3 mb-2 d-none">
                        <div class="col-md-3">
                            <label for="cheque_date">Cheque Date</label>
                            <input type="date" class="form-control" name="cheque_date" value="{{ date('Y-m-d') }}" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="cheque_no">Cheque No</label>
                            <input type="text" class="form-control" name="cheque_no" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="bank">Bank</label>
                            <input type="text" class="form-control" name="bank" id="bank" disabled>
                        </div>
                    </div>

                    <div class="col-md-12 mt-2">
                        <button type="submit" class="btn btn-light text-dark" id="addLoanButton">Add Loan</button>
                        <button type="submit" class="btn btn-success" id="updateLoanButton" style="display:none;">Update Loan</button>
                    </div>
                </div>
            </form>

            <hr class="my-4 bg-light">

            {{-- Filter Form --}}
            <form method="GET" class="mb-3">
                <div class="row align-items-end">
                    <div class="col-md-6 mb-2">
                        <label for="filter_customer">Filter by Customer</label>
                        <select class="form-select" name="filter_customer" id="filter_customer">
                            <option value="">-- Filter by Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('filter_customer') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->short_name }} - {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </div>
                </div>
            </form>

            <h4>Loan Records</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-sm mt-2 bg-white text-dark">
                    <thead>
                        <tr>
                            <th>විස්තරය</th>
                            <th>මුදල</th>
                            <th>විලා</th>
                            <th>Loan Type</th>
                            <th>Bill No</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                            <tr class="loan-row" data-loan='@json($loan)'>
                                <td>{{ $loan->description }}</td>
                                <td>{{ number_format($loan->amount, 2) }}</td>
                                <td>{{ $loan->customer->short_name }}</td>
                                <td>{{ ucfirst($loan->loan_type) }}</td>
                                <td>{{ $loan->bill_no ?? '-' }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning edit-loan-btn">Edit</button>
                                    <form action="{{ route('customers-loans.destroy', $loan->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')"
                                            class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No loan records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Include Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- Include jQuery and Select2 JS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        /**
         * Updates the description field based on selected loan type and settling way.
         * If 'old' loan type, description is "Customers old loans".
         * If 'cheque' settling way, description includes the bank name, otherwise "Cheque payment".
         * For other cases, description is cleared.
         */
        function updateDescription() {
            const loanType = document.querySelector('input[name="loan_type"]:checked').value;
            const settlingWay = document.querySelector('input[name="settling_way"]:checked').value;
            const descriptionField = document.getElementById('description');
            const bankField = document.getElementById('bank');
            const customerId = $('#customer_id').val();
            const totalAmountDisplay = $('#totalAmountDisplay');

            if (loanType === 'old') {
                descriptionField.value = "වෙළෙන්දාගේ ලාද පරණ නය"; // "Customers old loans"
                totalAmountDisplay.text(''); // Clear sum display
            } else if (loanType === 'today') {
                descriptionField.value = "වෙළෙන්දාගේ අද දින නය ගැනීම"; // "Customer loans today"

                // Fetch total amount for this customer
                if (customerId) {
                    $.ajax({
                        url: `/customers/${customerId}/loans-total`,
                        method: 'GET',
                        success: function(response) {
                            const formattedAmount = parseFloat(response.total_amount).toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            totalAmountDisplay.text(`(Total Loans: ${formattedAmount})`);
                        },
                        error: function() {
                            totalAmountDisplay.text('(Could not fetch total loans)');
                        }
                    });
                } else {
                    totalAmountDisplay.text(''); // No customer selected, clear sum
                }
            } else if (settlingWay === 'cheque') {
                const bankName = bankField.value.trim();
                if (bankName) {
                    descriptionField.value = `Cheque payment from ${bankName}`;
                } else {
                    descriptionField.value = "Cheque payment";
                }
                totalAmountDisplay.text('');
            } else {
                descriptionField.value = "";
                totalAmountDisplay.text('');
            }
        }

        $(document).ready(function() {
            // Initialize Select2 on customer dropdown (and potentially #filter_customer if it exists)
            $('#customer_id, #filter_customer').select2({
                placeholder: "-- Select Customer --",
                allowClear: true,
                width: '100%'
            });

            // Event listener: When Select2 dropdown for customer_id opens, focus its search field.
            $('#customer_id').on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-container--open .select2-search__field').focus();
                }, 50); // Small delay to ensure full rendering
            });

            // --- IMPORTANT CHANGE HERE ---
            // Function to handle visibility and disabled state of fields
            function toggleSettlingWayFields() {
                const isCheque = $('input[name="settling_way"]:checked').val() === 'cheque';

                // Toggle visibility using Bootstrap classes
                if (isCheque) {
                    $('#chequeFields').removeClass('d-none'); // Show cheque fields
                    $('#billNoSection').addClass('d-none');    // Hide bill no section
                } else {
                    $('#chequeFields').addClass('d-none');     // Hide cheque fields
                    $('#billNoSection').removeClass('d-none'); // Show bill no section
                }

                // Set disabled state for inputs within chequeFields
                $('#chequeFields input').prop('disabled', !isCheque);

                // Set disabled state for bill_no input
                $('input[name="bill_no"]').prop('disabled', isCheque);

                updateDescription(); // Always update description when settling way changes
            }

            // Event listener for settling_way radio buttons
            $('input[name="settling_way"]').on('change', toggleSettlingWayFields);

            // Event listener: Update description when loan_type changes.
            $('input[name="loan_type"]').on('change', updateDescription);

            // Event listener: Update description in real-time as bank name is typed (if settling way is cheque).
            $('#bank').on('input', function() {
                if ($('input[name="settling_way"]:checked').val() === 'cheque') {
                    updateDescription();
                }
            });

            // Also trigger updateDescription when customer selection changes (important for updating the sum)
            $('#customer_id').on('change', function() {
                updateDescription();
            });

            // Initial form setup on page load
            $('#loanForm')[0].reset();
            $('#addLoanButton').show();
            $('#updateLoanButton').hide();
            $('#totalAmountDisplay').text('');

            // --- CRUCIAL: Trigger the function on page load to set initial state ---
            // This ensures "Cash" is selected and fields are correctly hidden/shown from the start.
            $('input[name="settling_way"][value="cash"]').prop('checked', true); // Ensure cash is checked
            toggleSettlingWayFields(); // Manually call the function to set initial visibility and disabled states
            $('input[name="loan_type"]:checked').trigger('change'); // Ensure loan type description is updated


            // Event listener for Enter key navigation within the form.
            $('#loanForm').on('keydown', 'input, select', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    const $current = $(this);

                    if ($current.attr('name') === 'loan_type') {
                        $('#customer_id').select2('open');
                        return;
                    }

                    const $inputs = $('#loanForm').find('input:visible:enabled:not(:button), select:visible:enabled').filter(
                        function() {
                            return !$(this).hasClass('select2-search__field');
                        });
                    const idx = $inputs.index(this);

                    if (idx > -1 && idx + 1 < $inputs.length) {
                        const nextInput = $inputs.eq(idx + 1);
                        if (nextInput.hasClass('select2-hidden-accessible')) {
                            nextInput.select2('open');
                        } else {
                            nextInput.focus();
                        }
                    } else {
                        const form = $(this).closest('form')[0];
                        if (form.checkValidity()) {
                            form.submit();
                        }
                    }
                }
            });

            // Edit Loan Button Handler
            $('.edit-loan-btn').on('click', function() {
                const loan = $(this).closest('tr').data('loan');

                $('#loan_id').val(loan.id);
                $('#customer_id').val(loan.customer_id).trigger('change');
                $('input[name="loan_type"][value="' + loan.loan_type + '"]').prop('checked', true);
                $('input[name="amount"]').val(loan.amount);
                $('input[name="description"]').val(loan.description);

                // Set settling way radio button and trigger the new dedicated function
                $('input[name="settling_way"][value="' + loan.settling_way + '"]').prop('checked', true);
                toggleSettlingWayFields(); // Call the dedicated function

                // Populate fields based on settling_way
                if (loan.settling_way === 'cash') {
                    $('input[name="bill_no"]').val(loan.bill_no ?? '');
                    // Clear cheque fields to ensure no stale data if the form was previously in cheque mode
                    $('input[name="cheque_date"]').val('');
                    $('input[name="cheque_no"]').val('');
                    $('input[name="bank"]').val('');
                } else { // loan.settling_way === 'cheque'
                    $('input[name="cheque_date"]').val(loan.cheque_date ?? '');
                    $('input[name="cheque_no"]').val(loan.cheque_no ?? '');
                    $('input[name="bank"]').val(loan.bank ?? '');
                    // Clear bill_no to ensure no stale data
                    $('input[name="bill_no"]').val('');
                }

                updateDescription(); // Final description update

               
                $('#updateLoanButton').show();
            });
        });
    </script>

@endsection