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
                        <label><input type="radio" name="loan_type" value="old" checked> Customers Old Loans</label>
                        <label class="ms-3"><input type="radio" name="loan_type" value="today"> Customers Taking Loans
                            Today</label>
                    </div>
                    <input type="hidden" name="loan_id" id="loan_id">

                    <div class="col-md-4 mb-2"> <label for="customer_id">Customer</label>
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

                    <div class="col-md-4 mb-2" id="billNoSection"> <label for="bill_no">Bill No</label>
                        <input type="text" class="form-control" name="bill_no">
                    </div>

                    <div class="col-md-4 mb-2"> <label for="description">Description</label>
                        <input type="text" class="form-control" name="description" id="description" required>
                    </div>

                    <div class="col-md-4 mb-2"> <label for="amount">Amount</label>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>

                    <div id="chequeFields" class="d-flex flex-wrap gap-3 mb-2" style="display: none;">
                        <div class="col-md-3"> <label for="cheque_date">Cheque Date</label>
                            <input type="date" class="form-control" name="cheque_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3"> <label for="cheque_no">Cheque No</label>
                            <input type="text" class="form-control" name="cheque_no">
                        </div>
                        <div class="col-md-3"> <label for="bank">Bank</label>
                            <input type="text" class="form-control" name="bank" id="bank">
                        </div>
                    </div>


                    <div class="col-md-12 mt-2">
                        <button type="submit" class="btn btn-light text-dark" id="addLoanButton" style="display: none;">Add
                            Loan</button>
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
                            <th>Customer</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Loan Type</th>
                            <th>Settling Way</th>
                            <th>Bill No</th>
                            <th>Date</th>
                            <th>Actions</th> <!-- New column -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                            <tr class="loan-row" data-loan='@json($loan)'>
                                <td>{{ $loan->customer->short_name }} - {{ $loan->customer->name }}</td>
                                <td>{{ $loan->description }}</td>
                                <td>{{ number_format($loan->amount, 2) }}</td>
                                <td>{{ ucfirst($loan->loan_type) }}</td>
                                <td>{{ ucfirst($loan->settling_way) }}</td>
                                <td>{{ $loan->bill_no ?? '-' }}</td>
                                <td>{{ $loan->created_at->format('Y-m-d') }}</td>
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

            if (loanType === 'old') {
                descriptionField.value = "Customers old loans";
            } else if (settlingWay === 'cheque') {
                const bankName = bankField.value.trim();
                if (bankName) {
                    descriptionField.value = `Cheque payment from ${bankName}`;
                } else {
                    descriptionField.value = "Cheque payment";
                }
            } else {
                descriptionField.value = "";
            }
        }

        $(document).ready(function () {
            // Initialize Select2 on customer dropdown (and potentially #filter_customer if it exists)
            $('#customer_id, #filter_customer').select2({
                placeholder: "-- Select Customer --",
                allowClear: true,
                width: '100%'
            });

            // Event listener: When Select2 dropdown for customer_id opens, focus its search field.
            $('#customer_id').on('select2:open', function () {
                setTimeout(function () {
                    // Target the specific search input created by Select2 within the open container.
                    $('.select2-container--open .select2-search__field').focus();
                }, 50); // Small delay to ensure full rendering
            });

            // Trigger Select2 to open on page load to automatically focus the search bar.
            $('#customer_id').select2('open');

            // Event listener: Show/hide cheque fields and manage description based on settling_way selection.
            $('input[name="settling_way"]').on('change', function () {
                const isCheque = $(this).val() === 'cheque';
                $('#chequeFields').toggle(isCheque);
                $('#billNoSection').toggle(!isCheque);

                // Enable/disable fields to control which data is sent with the form.
                $('#chequeFields input').prop('disabled', !isCheque);
                $('input[name="bill_no"]').prop('disabled', isCheque);

                updateDescription(); // Update description after settling way changes.
            });

            // Event listener: Update description when loan_type changes.
            $('input[name="loan_type"]').on('change', updateDescription);

            // Event listener: Update description in real-time as bank name is typed (if settling way is cheque).
            $('#bank').on('input', function () {
                if ($('input[name="settling_way"]:checked').val() === 'cheque') {
                    updateDescription();
                }
            });

            // Initial setup on page load:
            // 1. Set the initial description.
            // 2. Adjust visibility and enabled state of cheque/bill_no fields.
            updateDescription();
            const initialSettlingWay = $('input[name="settling_way"]:checked').val();
            $('#chequeFields').toggle(initialSettlingWay === 'cheque');
            $('#billNoSection').toggle(initialSettlingWay !== 'cheque');
            $('#chequeFields input').prop('disabled', initialSettlingWay !== 'cheque');
            $('input[name="bill_no"]').prop('disabled', initialSettlingWay === 'cheque');

            // Event listener for Enter key navigation within the form.
            $('#loanForm').on('keydown', 'input, select', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent default browser behavior (e.g., newline in textareas).

                    // Select all visible and enabled form fields (excluding buttons and Select2's internal search field).
                    const $inputs = $('#loanForm').find('input:visible:enabled:not(:button), select:visible:enabled').filter(function () {
                        return !$(this).hasClass('select2-search__field');
                    });
                    const idx = $inputs.index(this); // Get the index of the current field.

                    if (idx > -1 && idx + 1 < $inputs.length) {
                        // If not the last visible field, move focus to the next one.
                        const nextInput = $inputs.eq(idx + 1);
                        if (nextInput.hasClass('select2-hidden-accessible')) {
                            // If the next field is a Select2 dropdown, open it.
                            nextInput.select2('open');
                        } else {
                            // Otherwise, focus the next standard input field.
                            nextInput.focus();
                        }
                    } else {
                        // If it's the last visible field, attempt to submit the form.
                        const form = $(this).closest('form')[0]; // Get the parent form element.
                        if (form.checkValidity()) { // Perform HTML5 form validation.
                            form.submit(); // Submit the form if valid.
                        } else {
                            // Browser will show validation messages for required fields if not valid.
                        }
                    }
                }
            });
        });
    </script>
    <script>
      $('.edit-loan-btn').on('click', function () {
    const loan = $(this).closest('tr').data('loan');

    $('#loan_id').val(loan.id);
    $('#customer_id').val(loan.customer_id).trigger('change');
    $('input[name="loan_type"][value="' + loan.loan_type + '"]').prop('checked', true);
    $('input[name="settling_way"][value="' + loan.settling_way + '"]').prop('checked', true);
    $('input[name="bill_no"]').val(loan.bill_no ?? '');
    $('input[name="description"]').val(loan.description);
    $('input[name="amount"]').val(loan.amount);

    if (loan.settling_way === 'cheque') {
        $('#chequeFields').show();
        $('#billNoSection').hide();
        $('input[name="cheque_date"]').val(loan.cheque_date ?? '');
        $('input[name="cheque_no"]').val(loan.cheque_no ?? '');
        $('input[name="bank"]').val(loan.bank ?? '');
    } else {
        $('#chequeFields').hide();
        $('#billNoSection').show();
    }

    $('#chequeFields input').prop('disabled', loan.settling_way !== 'cheque');
    $('input[name="bill_no"]').prop('disabled', loan.settling_way === 'cheque');

    // Toggle buttons visibility
    $('#addLoanButton').hide();
    $('#updateLoanButton').show();
});


    </script>

@endsection