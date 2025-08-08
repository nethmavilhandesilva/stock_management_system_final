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
    <style>
    /* Custom dark green background for the form */
    .bg-custom-dark {
        background-color: #004d00 !important;
        color: #fff;
    }

    /* Style for labels to make them visible on the dark background */
    .text-form-label {
        color: #fff !important;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 0.2rem;
    }

    /* Smaller padding and font for form controls */
    .form-control-sm, .form-select-sm {
        padding: 0.15rem 0.4rem !important;
        font-size: 0.75rem !important;
        border: 1px solid black !important;
        color: black !important;
        font-weight: bold !important;
        background-color: white !important;
    }

    /* Overwrite the strong tag color to white */
    .bg-custom-dark strong {
        color: #fff !important;
    }
</style>

    <div class="container my-4">
        <div class="custom-card">

            <h3 class="mb-4">Customers Loans</h3>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Loan Entry Form --}}
          <form action="{{ route('customers-loans.store') }}" method="POST" id="loanForm" class="p-3 border border-2 border-dark rounded bg-custom-dark">
    @csrf
    <div class="row gy-2">
        <div class="col-12 mb-2">
            <label class="text-form-label"><strong>Loan Type:</strong></label><br>
            <label class="me-3"><input type="radio" name="loan_type" value="old" checked> වෙළෙන්දාගේ ලාද පරණ නය</label>
            <label><input type="radio" name="loan_type" value="today"> වෙළෙන්දාගේ අද දින නය ගැනීම</label>
        </div>

        <input type="hidden" name="loan_id" id="loan_id">

        <div class="col-md-4">
            <label for="customer_id" class="text-form-label">ගෙණුම්කරු</label>
            <select class="form-select form-select-sm" id="customer_id" name="customer_id" required>
                <option value="">-- Select Customer --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->short_name }} - {{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="bill_no" class="text-form-label">Bill No</label>
            <input type="text" class="form-control form-control-sm" name="bill_no">
        </div>

        <div class="col-md-2">
            <label for="amount" class="text-form-label">මුදල</label>
            <input type="number" step="0.01" class="form-control form-control-sm" name="amount" required>
        </div>

        <div class="col-md-4" id="settlingWaySection">
            <label class="text-form-label"><strong>Settling Way:</strong></label><br>
            <label class="me-3"><input type="radio" name="settling_way" value="cash" checked> Cash</label>
            <label><input type="radio" name="settling_way" value="cheque"> Cheque</label>
        </div>

        <div class="col-md-5">
            <label for="description" class="text-form-label">විස්තරය</label>
            <input type="text" class="form-control form-control-sm" name="description" id="description" required>
            <span id="totalAmountDisplay" class="text-white-50" style="font-weight: bold; font-size: 0.9rem;"></span>
        </div>

     <div id="chequeFields" class="col-md-5 ms-auto d-none">
    <div class="border rounded p-2 bg-light" style="border-color: #006600 !important;">
        <h6 class="text-success fw-bold mb-2" style="border-bottom: 1px solid #006600;">Cheque Details</h6>
        <div class="row g-2">
            <div class="col-4">
                <label for="cheque_date" class="form-label mb-1">Cheque Date</label>
                <input type="date" class="form-control form-control-sm" name="cheque_date" value="{{ date('Y-m-d') }}" disabled>
            </div>
            <div class="col-4">
                <label for="cheque_no" class="form-label mb-1">Cheque No</label>
                <input type="text" class="form-control form-control-sm" name="cheque_no" disabled>
            </div>
            <div class="col-4">
                <label for="bank" class="form-label mb-1">Bank</label>
                <input type="text" class="form-control form-control-sm" name="bank" id="bank" disabled>
            </div>
        </div>
    </div>
</div>
        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-light text-dark" id="addLoanButton" style="display:none;">Add Loan</button>
            <button type="submit" class="btn btn-success" id="updateLoanButton" style="display:none;">Update Loan</button>
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
         */
        
        function updateDescription() {
            const loanType = document.querySelector('input[name="loan_type"]:checked').value;
            const settlingWay = document.querySelector('input[name="settling_way"]:checked').value;
            const descriptionField = document.getElementById('description');
            const bankField = document.getElementById('bank');
            const customerId = $('#customer_id').val();
            const totalAmountDisplay = $('#totalAmountDisplay');

            // Clear previous content
            totalAmountDisplay.text('');
            descriptionField.value = "";

            if (loanType === 'old') {
                descriptionField.value = "වෙළෙන්දාගේ ලාද පරණ නය";
                if (settlingWay === 'cheque') {
                    const bankName = bankField.value.trim();
                    descriptionField.value = `Cheque payment from ${bankName || 'bank'}`;
                }
            } else if (loanType === 'today') {
                descriptionField.value = "වෙළෙන්දාගේ අද දින නය ගැනීම";
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
                }
            }
        }

        // --- NEW FUNCTION: To handle visibility of the settling way section
        function toggleLoanTypeDependentFields() {
            const loanType = $('input[name="loan_type"]:checked').val();
            const isTodayLoan = loanType === 'today';
            const settlingWay = $('input[name="settling_way"]:checked').val();
            const isCheque = settlingWay === 'cheque';

            // Show/hide the entire settling way section
            if (isTodayLoan) {
                $('#settlingWaySection').addClass('d-none');
                $('#settlingWaySection input').prop('disabled', true);
            } else {
                $('#settlingWaySection').removeClass('d-none');
                $('#settlingWaySection input').prop('disabled', false);
            }

            // Manage the visibility of the cheque fields and bill no field
            if (isCheque && !isTodayLoan) { // Cheque is selected, and it's not a 'today' loan
                $('#chequeFields').removeClass('d-none');
                $('#billNoSection').addClass('d-none');
                $('#chequeFields input').prop('disabled', false);
                $('input[name="bill_no"]').prop('disabled', true);
            } else { // Cash is selected, or it's a 'today' loan
                $('#chequeFields').addClass('d-none');
                $('#chequeFields input').prop('disabled', true);
                
                // Show Bill No field and enable it
                $('#billNoSection').removeClass('d-none');
                $('input[name="bill_no"]').prop('disabled', false);
            }

            updateDescription();
        }

        $(document).ready(function() {
            $('#customer_id, #filter_customer').select2({
                placeholder: "-- Select Customer --",
                allowClear: true,
                width: '100%'
            });

            $('#customer_id').on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-container--open .select2-search__field').focus();
                }, 50);
            });

            // Event listener for settling_way and loan_type radio buttons
            $('input[name="settling_way"]').on('change', toggleLoanTypeDependentFields);
            $('input[name="loan_type"]').on('change', toggleLoanTypeDependentFields);

            // Event listener: Update description in real-time as bank name is typed
            $('#bank').on('input', updateDescription);

            // Also trigger updateDescription when customer selection changes
            $('#customer_id').on('change', updateDescription);

            // Initial form setup on page load
            $('#loanForm')[0].reset();
            
            $('#updateLoanButton').hide();
            $('#totalAmountDisplay').text('');

            // CRUCIAL: Trigger the function on page load to set initial state
            $('input[name="loan_type"][value="old"]').prop('checked', true);
            $('input[name="settling_way"][value="cash"]').prop('checked', true);
            toggleLoanTypeDependentFields();

            // Edit Loan Button Handler
            $('.edit-loan-btn').on('click', function() {
                const loan = $(this).closest('tr').data('loan');

                $('#loan_id').val(loan.id);
                $('#customer_id').val(loan.customer_id).trigger('change');
                $('input[name="loan_type"][value="' + loan.loan_type + '"]').prop('checked', true);
                $('input[name="amount"]').val(loan.amount);
                $('input[name="description"]').val(loan.description);

                if (loan.loan_type === 'today') {
                    $('input[name="settling_way"][value="cash"]').prop('checked', false);
                    $('input[name="settling_way"][value="cheque"]').prop('checked', false);
                    $('input[name="bill_no"]').val(loan.bill_no ?? '');
                    $('input[name="cheque_date"]').val('');
                    $('input[name="cheque_no"]').val('');
                    $('input[name="bank"]').val('');
                } else if (loan.settling_way) {
                    $('input[name="settling_way"][value="' + loan.settling_way + '"]').prop('checked', true);
                    if (loan.settling_way === 'cash') {
                        $('input[name="bill_no"]').val(loan.bill_no ?? '');
                        $('input[name="cheque_date"]').val('');
                        $('input[name="cheque_no"]').val('');
                        $('input[name="bank"]').val('');
                    } else { // Cheque
                        $('input[name="cheque_date"]').val(loan.cheque_date ?? '');
                        $('input[name="cheque_no"]').val(loan.cheque_no ?? '');
                        $('input[name="bank"]').val(loan.bank ?? '');
                        $('input[name="bill_no"]').val('');
                    }
                } else {
                    // Default to cash for old loans without a settling way
                    $('input[name="settling_way"][value="cash"]').prop('checked', true);
                    $('input[name="bill_no"]').val(loan.bill_no ?? '');
                }
                
                toggleLoanTypeDependentFields();

                updateDescription();
                $('#addLoanButton').hide();
                $('#updateLoanButton').show();
            });
        });
    </script>
@endsection