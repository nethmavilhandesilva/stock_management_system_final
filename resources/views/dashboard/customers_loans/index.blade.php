@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Customers Loans</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('customers-loans.store') }}" method="POST" id="loanForm">
        @csrf

        <div class="row">
            <div class="col-md-12 mb-3">
                <label><strong>Loan Type:</strong></label><br>
                <label><input type="radio" name="loan_type" value="old" checked> Customers Old Loans</label>
                <label class="ms-3"><input type="radio" name="loan_type" value="today"> Customers Taking Loans Today</label>
            </div>

            <div class="col-md-6 mb-3">
                <label for="customer_id">Customer</label>
                <select class="form-select" name="customer_id" required>
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->short_name }} - {{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label><strong>Settling Way:</strong></label><br>
                <label><input type="radio" name="settling_way" value="cash" checked> Cash</label>
                <label class="ms-3"><input type="radio" name="settling_way" value="cheque"> Cheque</label>
            </div>

            <div class="col-md-6 mb-3" id="billNoSection">
                <label for="bill_no">Bill No</label>
                <input type="text" class="form-control" name="bill_no">
            </div>

            <div class="col-md-6 mb-3">
                <label for="description">Description</label>
                <input type="text" class="form-control" name="description" id="description" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="amount">Amount</label>
                <input type="number" step="0.01" class="form-control" name="amount" required>
            </div>

            <div id="chequeFields" style="display: none;">
                <div class="col-md-4 mb-3">
                    <label for="cheque_date">Cheque Date</label>
                    <input type="date" class="form-control" name="cheque_date" value="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="cheque_no">Cheque No</label>
                    <input type="text" class="form-control" name="cheque_no">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="bank">Bank</label>
                    <input type="text" class="form-control" name="bank" id="bank">
                </div>
            </div>

            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Add Loan</button>
            </div>
        </div>
    </form>

    <hr class="my-4">

    <h4>Loan Records</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Loan Type</th>
                <th>Settling Way</th>
                <th>Bill No</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $loan)
                <tr>
                    <td>{{ $loan->customer->short_name }} - {{ $loan->customer->name }}</td>
                    <td>{{ $loan->description }}</td>
                    <td>{{ number_format($loan->amount, 2) }}</td>
                    <td>{{ ucfirst($loan->loan_type) }}</td>
                    <td>{{ ucfirst($loan->settling_way) }}</td>
                    <td>{{ $loan->bill_no ?? '-' }}</td>
                    <td>{{ $loan->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    // Show/hide cheque or cash fields
    document.querySelectorAll('input[name="settling_way"]').forEach(el => {
        el.addEventListener('change', function () {
            const isCheque = this.value === 'cheque';
            document.getElementById('chequeFields').style.display = isCheque ? 'flex' : 'none';
            document.getElementById('billNoSection').style.display = isCheque ? 'none' : 'block';
        });
    });

    // Autofill description if old loan selected
    document.querySelectorAll('input[name="loan_type"]').forEach(el => {
        el.addEventListener('change', function () {
            if (this.value === 'old') {
                document.getElementById('description').value = "Customers old loans";
            } else {
                document.getElementById('description').value = "";
            }
        });
    });

    // On page load
    document.addEventListener('DOMContentLoaded', function () {
        if (document.querySelector('input[name="loan_type"]:checked').value === 'old') {
            document.getElementById('description').value = "Customers old loans";
        }
    });
</script>
@endsection
