@extends('layouts.app')

@section('content')
<style>
    body { background-color: #99ff99; }
    .bg-light { background-color: #ffffff !important; }
    .list-group-item-action { cursor: pointer; }
    /* Style for the transaction history modal link */
    .view-history-link { cursor: pointer; color: #198754; font-weight: bold; }
    .view-history-link:hover { text-decoration: underline; }
</style>

<div class="container mt-4">
    <h3 class="text-success mb-3">Supplier Management</h3>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Radio buttons --}}
    <div class="mb-3">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="action_type" id="addSupplierRadio" checked>
            <label class="form-check-label fw-bold" for="addSupplierRadio">Purchase from Supplier</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="action_type" id="paymentRadio">
            <label class="form-check-label fw-bold" for="paymentRadio">Settle to Supplier</label>
        </div>
    </div>

    {{-- Purchase Form --}}
    <form id="supplierForm" action="{{ route('suppliers2.store') }}" method="POST" class="border p-3 rounded bg-light">
        @csrf
        <div class="row mb-3 align-items-end">
            <div class="col-md-3 position-relative">
                <label for="supplier_search" class="form-label">Select Supplier</label>
                <input type="text" id="supplier_search" class="form-control" placeholder="🔍 Type supplier code..." autocomplete="off" required>
                <input type="hidden" name="existing_supplier_id" id="supplier_id">
                <input type="hidden" name="supplier_code" id="supplier_code">
                <input type="hidden" name="supplier_name" id="supplier_name">

                <div id="supplierDropdown" class="list-group position-absolute w-100 shadow-sm"
                     style="z-index:10; display:none; max-height:200px; overflow-y:auto;">
                    @foreach($existingSuppliersWithBalance as $ex)
                        <button type="button" class="list-group-item list-group-item-action"
                                data-id="{{ $ex->id }}" data-code="{{ $ex->code }}" data-name="{{ $ex->name }}" data-balance="{{ number_format($ex->balance, 2) }}">
                            {{ $ex->code }}{{ $ex->name ? ' — ' . $ex->name : '' }} (Bal: {{ number_format($ex->balance, 2) }})
                        </button>
                    @endforeach
                </div>
                 {{-- Balance display for Purchase Form --}}
                <div id="purchaseBalanceDisplay" class="mt-2 fw-bold" style="display:none;">
                    Current Balance: <span id="current_supplier_balance">0.00</span>
                </div>
            </div>

            <div class="col-md-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="1" placeholder="Enter description..."></textarea>
            </div>

            <div class="col-md-3">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="number" name="total_amount" id="total_amount" class="form-control" step="0.01" required>
            </div>

            <div class="col-md-3 position-relative">
                <label for="grn_search" class="form-label">Search GRN</label>
                <input type="text" id="grn_search" class="form-control" placeholder="🔍 Type GRN code or item name..." autocomplete="off" required>
                <input type="hidden" name="grn_id" id="grn_id" required>

                <div id="grnDropdown" class="list-group position-absolute w-100 shadow-sm"
                     style="z-index:10; display:none; max-height:200px; overflow-y:auto;">
                    @foreach($grnOptions as $id => $grnNo)
                        <button type="button" class="list-group-item list-group-item-action"
                                data-id="{{ $id }}" data-name="{{ $grnNo }}">{{ $grnNo }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Save Purchase</button>
    </form>

    {{-- Payment Form --}}
    <form id="paymentForm" action="{{ route('suppliers2.payment') }}" method="POST" class="border p-3 rounded bg-light" style="display:none;">
        @csrf
        <div class="row mb-3">
            <div class="col-md-6 position-relative">
                <label for="payment_supplier_search" class="form-label">Select Supplier</label>
                <input type="text" id="payment_supplier_search" class="form-control" placeholder="🔍 Type supplier code..." autocomplete="off" required>
                <input type="hidden" name="supplier_code" id="payment_supplier_code">

                <div id="paymentSupplierDropdown" class="list-group position-absolute w-100 shadow-sm"
                     style="z-index:10; display:none; max-height:200px; overflow-y:auto;">
                    @foreach($existingSuppliersWithBalance as $supplier)
                        <button type="button" class="list-group-item list-group-item-action"
                                data-code="{{ $supplier->code }}" data-balance="{{ number_format($supplier->balance, 2) }}">
                            {{ $supplier->code }} — {{ $supplier->name }} (Bal: {{ number_format($supplier->balance, 2) }})
                        </button>
                    @endforeach
                </div>
                 {{-- Balance display for Payment Form --}}
                <div id="paymentBalanceDisplay" class="mt-2 fw-bold" style="display:none;">
                    Current Balance: <span id="current_payment_balance">0.00</span>
                </div>
            </div>

            <div class="col-md-6">
                <label for="payment_amount" class="form-label">Payment Amount</label>
                <input type="number" name="payment_amount" id="payment_amount" class="form-control" step="0.01" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit Payment</button>
    </form>

    <hr class="my-4">

    {{-- Supplier Records (Individual Transactions) --}}
    <h5 class="text-success">Supplier Records (Transactions)</h5>
    <table class="table table-bordered mt-3">
        <thead class="table-success">
        <tr>
            <th>ID</th>
            <th>Supplier Code</th>
            <th>Supplier Name</th>
            <th>Amount</th>
            <th>Description</th>
            <th>GRN No</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->id }}</td>
                <td><span class="view-history-link" data-supplier-code="{{ $supplier->supplier_code }}">{{ $supplier->supplier_code }}</span></td>
                <td>{{ $supplier->supplier_name }}</td>
                <td class="{{ $supplier->total_amount < 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($supplier->total_amount, 2) }}
                </td>
                <td>{{ $supplier->description ?? '-' }}</td>
                <td>{{ $supplier->grn->code ?? '-' }}</td>
                <td>
                    <a href="{{ route('suppliers2.edit', $supplier->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('suppliers2.destroy', $supplier->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this transaction?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted">No transactions found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Supplier Transaction Modal (Assuming Bootstrap 5 or 4) --}}
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="transactionModalLabel">Supplier Transaction History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="mb-3">
                    Supplier: <span id="modal_supplier_code" class="fw-bold"></span> - <span id="modal_supplier_name"></span>
                </h6>
                <div class="row mb-3 fw-bold">
                    <div class="col-4 text-success">Total Purchases: <span id="modal_total_purchases">0.00</span></div>
                    <div class="col-4 text-danger">Total Payments: <span id="modal_total_payments">0.00</span></div>
                    <div class="col-4">Remaining Balance: <span id="modal_remaining_balance" class="text-danger">0.00</span></div>
                </div>
                <hr>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-striped">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Date/Time</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>GRN</th>
                                <th>Amount</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody id="transactionHistoryBody">
                            {{-- Transactions will be loaded here by JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
// --- Radio button toggle + focus ---
const addRadio = document.getElementById('addSupplierRadio');
const payRadio = document.getElementById('paymentRadio');
const supplierForm = document.getElementById('supplierForm');
const paymentForm = document.getElementById('paymentForm');

addRadio.addEventListener('change', () => {
    supplierForm.style.display='block';
    paymentForm.style.display='none';
    document.getElementById('supplier_search').focus();
});
payRadio.addEventListener('change', () => {
    supplierForm.style.display='none';
    paymentForm.style.display='block';
    document.getElementById('payment_supplier_search').focus();
});

// --- Purchase form enter-key navigation ---
const purchaseFields = ['supplier_search', 'description', 'total_amount', 'grn_search'];
purchaseFields.forEach((id, idx) => {
    const el = document.getElementById(id);
    el.addEventListener('keydown', e => {
        if(e.key==='Enter'){
            e.preventDefault();
            const nextEl = document.getElementById(purchaseFields[idx+1]);
            if(nextEl) nextEl.focus();
            else if(idx === purchaseFields.length - 1) supplierForm.submit();
        }
    });
});

// --- Payment form enter-key navigation ---
const paymentFields = ['payment_supplier_search', 'payment_amount'];
paymentFields.forEach((id, idx) => {
    const el = document.getElementById(id);
    el.addEventListener('keydown', e => {
        if(e.key==='Enter'){
            e.preventDefault();
            const nextEl = document.getElementById(paymentFields[idx+1]);
            if(nextEl) nextEl.focus();
            else if(idx === paymentFields.length - 1) paymentForm.submit();
        }
    });
});

// --- Live search setup function ---
function setupLiveSearch(inputId, dropdownId, hiddenCodeId, balanceDisplayId, isPurchaseForm=false){
    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    const hiddenCode = document.getElementById(hiddenCodeId);
    const balanceDisplayDiv = document.getElementById(balanceDisplayId);
    const balanceSpan = balanceDisplayDiv ? balanceDisplayDiv.querySelector('span') : null;
    const hiddenName = isPurchaseForm ? document.getElementById('supplier_name') : null;

    // Helper to update the balance display
    const updateBalanceDisplay = (balance) => {
        if (balanceSpan) {
            balanceSpan.textContent = balance;
            balanceSpan.classList.remove('text-success', 'text-danger');
            const numericBalance = parseFloat(balance.replace(/,/g, ''));
            
            if (numericBalance > 0) {
                balanceSpan.classList.add('text-danger'); // Red for positive balance (money owed)
            } else if (numericBalance < 0) {
                balanceSpan.classList.add('text-success'); // Green for negative balance (pre-payment/credit)
            } else {
                 balanceSpan.classList.add('text-success'); // Green for 0.00
            }
            
            // Only show the balance display if a supplier is selected/balance is non-zero
            balanceDisplayDiv.style.display = balance !== '0.00' ? 'block' : 'none';
        }
    };

    input.addEventListener('input', () => {
        const filter = input.value.toUpperCase().trim();
        let visible=0;
        dropdown.querySelectorAll('button').forEach(btn=>{
            const text = btn.textContent.toUpperCase();
            const match = text.includes(filter);
            btn.style.display = match ? 'block' : 'none';
            if(match) visible++;
        });
        dropdown.style.display = visible ? 'block' : 'none';

        // Clear hidden fields and balance display if input is cleared
        if(filter === ''){
            hiddenCode.value='';
            if(isPurchaseForm) document.getElementById('supplier_id').value='';
            if(hiddenName) hiddenName.value='';
            updateBalanceDisplay('0.00');
        }
    });

    dropdown.addEventListener('click', e => {
        if(e.target.matches('button')){
            const button = e.target;
            const code = button.getAttribute('data-code');
            const balance = button.getAttribute('data-balance') || '0.00';
            const name = button.getAttribute('data-name');

            // Set input value to Code/Name (without Balance for cleaner input)
            input.value = `${code} ${name ? '— ' + name : ''}`.trim();

            hiddenCode.value = code;

            if(isPurchaseForm){
                document.getElementById('supplier_id').value = button.getAttribute('data-id');
                if(hiddenName) hiddenName.value = name || '';
            }

            updateBalanceDisplay(balance);
            dropdown.style.display='none';

            // Optional: Move focus to the next field after selection
            if(isPurchaseForm) document.getElementById('description').focus();
            else document.getElementById('payment_amount').focus();
        }
    });

    // Hide dropdown on outside click
    document.addEventListener('click', e => {
        if(!dropdown.contains(e.target) && e.target!==input) dropdown.style.display='none';
    });
}

// --- Setup live searches ---
setupLiveSearch('supplier_search','supplierDropdown','supplier_code', 'purchaseBalanceDisplay', true);
setupLiveSearch('grn_search','grnDropdown','grn_id'); 
setupLiveSearch('payment_supplier_search','paymentSupplierDropdown','payment_supplier_code', 'paymentBalanceDisplay');

// --- Transaction History Modal Logic ---
document.addEventListener('DOMContentLoaded', () => {
    // Determine the modal handler (Bootstrap 5/4)
    const modalElement = document.getElementById('transactionModal');
    let transactionModal;
    
    // Check if Bootstrap 5 (bootstrap.Modal) is available
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        transactionModal = new bootstrap.Modal(modalElement);
    } 
    // Check if Bootstrap 4 (jQuery/$.fn.modal) is available
    else if (typeof $ !== 'undefined' && $.fn.modal) {
        transactionModal = $(modalElement);
    } else {
        console.error("Bootstrap JS not loaded correctly for modal.");
        return;
    }

    const tableBody = document.getElementById('transactionHistoryBody');

    // Attach click listener to table body for dynamic links (event delegation)
    document.querySelector('.table-bordered tbody').addEventListener('click', function(e) {
        if (e.target.classList.contains('view-history-link')) {
            const supplierCode = e.target.getAttribute('data-supplier-code');
            if (supplierCode) {
                fetchTransactionHistory(supplierCode);
            }
        }
    });

    function fetchTransactionHistory(supplierCode) {
        // Fetch the CSRF token (needed for Laravel AJAX requests if using POST/PUT/DELETE, but good practice to include)
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Ensure you have the route defined: Route::get('/suppliers/transactions', [SupplierController2::class, 'getSupplierTransactions'])->name('suppliers2.transactions');
        const url = "{{ route('suppliers2.transactions') }}?supplier_code=" + supplierCode;

        tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Loading transactions...</td></tr>';
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update summary details
            document.getElementById('modal_supplier_code').textContent = data.supplier_code;
            document.getElementById('modal_supplier_name').textContent = data.supplier_name;
            document.getElementById('modal_total_purchases').textContent = data.total_purchases;
            document.getElementById('modal_total_payments').textContent = data.total_payments;
            
            // Update remaining balance and color
            const balanceEl = document.getElementById('modal_remaining_balance');
            balanceEl.textContent = data.remaining_balance;
            balanceEl.classList.remove('text-success', 'text-danger');
            
            const numericBalance = parseFloat(data.remaining_balance.replace(/,/g, ''));
            if (numericBalance > 0) {
                balanceEl.classList.add('text-danger'); // Red for positive balance (money owed)
            } else {
                balanceEl.classList.add('text-success'); // Green for negative/zero balance
            }


            tableBody.innerHTML = ''; // Clear previous data
            if (data.history && data.history.length > 0) {
                data.history.forEach(item => {
                    const row = `
                        <tr>
                            <td>${item.date}</td>
                            <td>${item.type}</td>
                            <td>${item.description}</td>
                            <td>${item.grn_no}</td>
                            <td class="${item.class}">${item.amount}</td>
                            <td>${item.balance}</td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No transactions found for this supplier.</td></tr>';
            }

            // Show modal
            if (transactionModal.show) {
                transactionModal.show();
            } else if (transactionModal.modal) {
                transactionModal.modal('show');
            }
        })
        .catch(error => {
            console.error('Error fetching transaction history:', error);
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading data.</td></tr>';
            // Still attempt to show modal with error
             if (transactionModal.show) {
                transactionModal.show();
            } else if (transactionModal.modal) {
                transactionModal.modal('show');
            }
        });
    }
});
</script>
@endsection