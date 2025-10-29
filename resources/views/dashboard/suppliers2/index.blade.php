@extends('layouts.app')

@section('content')
<style>
    body { background-color: #99ff99; }
    .bg-light { background-color: #ffffff !important; }
    .list-group-item-action { cursor: pointer; }
    /* Style for the transaction history modal link */
    .view-history-link { cursor: pointer; color: #198754; font-weight: bold; }
    .view-history-link:hover { text-decoration: underline; }
    .text-uppercase {
    text-transform: uppercase;
}
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
        <input type="hidden" name="transaction_id" id="transaction_id"> {{-- For inline edit --}}
        <div class="row mb-3 align-items-end">
            <div class="col-md-3 position-relative">
                <label for="supplier_search" class="form-label">Select Supplier</label>
               <input type="text" id="supplier_search" class="form-control text-uppercase" placeholder="🔍 Type supplier code..." autocomplete="off" required>
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
                <input type="text" id="grn_search" class="form-control" placeholder="🔍 Type GRN code or item name..." autocomplete="off">
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

        <button type="submit" class="btn btn-success" id="submitButton">Save Purchase</button>
        <button type="button" class="btn btn-secondary" id="cancelEditButton" style="display:none;">Cancel Edit</button>
    </form>

    {{-- Payment Form --}}
    <form id="paymentForm" action="{{ route('suppliers2.payment') }}" method="POST" class="border p-3 rounded bg-light" style="display:none;">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4 position-relative">
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
                <div id="paymentBalanceDisplay" class="mt-2 fw-bold" style="display:none;">
                    Current Balance: <span id="current_payment_balance">0.00</span>
                </div>
            </div>

            {{-- NEW DESCRIPTION FIELD FOR PAYMENT FORM --}}
            <div class="col-md-4">
                <label for="payment_description" class="form-label">Description</label>
                <textarea name="description" id="payment_description" class="form-control" rows="1" placeholder="Enter payment details..."></textarea>
            </div>
            {{-- END NEW FIELD --}}

            <div class="col-md-4">
                <label for="payment_amount" class="form-label">Payment Amount</label>
                <input type="number" name="payment_amount" id="payment_amount" class="form-control" step="0.01" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit Payment</button>
    </form>

    <hr class="my-4">

    {{-- Supplier Records & Search Filter (NEW) --}}
    <h5 class="text-success">Supplier Records (Transactions)</h5>
    <div class="mb-3">
       <input type="text" id="supplierTableSearch" class="form-control text-uppercase" placeholder="🔍 Filter by Supplier Code..." autocomplete="off">
    </div>

    <table class="table table-bordered mt-3">
        <thead class="table-success">
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Supplier Code</th>
            <th>Supplier Name</th>
            <th>Amount</th>
            <th>Description</th>
            <th>GRN No</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="supplierTableBody">
        @forelse($suppliers as $supplier)
            <tr data-transaction="{{ $supplier }}">
                <td>{{ $supplier->id }}</td>
                <td>{{ $supplier->date }}</td>
                <td><span class="view-history-link" data-supplier-code="{{ $supplier->supplier_code }}">{{ $supplier->supplier_code }}</span></td>
                <td>{{ $supplier->supplier_name }}</td>
               <td class="{{ $supplier->total_amount < 0 ? 'text-danger' : 'text-success' }}">
    {{ number_format(abs($supplier->total_amount), 2) }}
</td>

                </td>
                <td>{{ $supplier->description ?? '-' }}</td>
                <td>{{ $supplier->grn->code ?? '-' }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning editButton">Edit</button>
                    <form action="{{ route('suppliers2.destroy', $supplier->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this transaction?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted">No transactions found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Transaction Modal (No changes here, but kept for completeness) --}}
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
                {{-- Supplier details (Updated in Controller for data) --}}
                <div class="row mb-2 small">
                    <div class="col-4">📧 **Email:** <span id="modal_supplier_email">N/A</span></div>
                    <div class="col-4">📞 **Phone:** <span id="modal_supplier_phone">N/A</span></div>
                    <div class="col-4">🏠 **Address:** <span id="modal_supplier_address">N/A</span></div>
                </div>
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
                            <th>Date</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>GRN</th>
                            <th>Amount</th>
                            <th>Balance</th>
                        </tr>
                        </thead>
                        <tbody id="transactionHistoryBody"></tbody>
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
// --- Radio toggle ---
const addRadio = document.getElementById('addSupplierRadio');
const payRadio = document.getElementById('paymentRadio');
const supplierForm = document.getElementById('supplierForm');
const paymentForm = document.getElementById('paymentForm');

addRadio.addEventListener('change', () => {
    supplierForm.style.display='block';
    paymentForm.style.display='none';
    document.getElementById('supplier_search').focus();
    updateDescriptionField(); // Update description on radio change
});
payRadio.addEventListener('change', () => {
    supplierForm.style.display='none';
    paymentForm.style.display='block';
    document.getElementById('payment_supplier_search').focus();
    updateDescriptionField(); // Update description on radio change
});

// --- Enter navigation ---
const purchaseFields = ['supplier_search','description','total_amount','grn_search'];
purchaseFields.forEach((id, idx) => {
    document.getElementById(id).addEventListener('keydown', e=>{
        if(e.key==='Enter'){
            e.preventDefault();
            const next = purchaseFields[idx+1]?document.getElementById(purchaseFields[idx+1]):null;
            if(next) next.focus();
            else supplierForm.submit();
        }
    });
});
// Updated payment fields to include the new description
const paymentFields = ['payment_supplier_search', 'payment_description', 'payment_amount'];
paymentFields.forEach((id, idx)=>{
    document.getElementById(id).addEventListener('keydown', e=>{
        if(e.key==='Enter'){
            e.preventDefault();
            const next = paymentFields[idx+1]?document.getElementById(paymentFields[idx+1]):null;
            if(next) next.focus();
            else paymentForm.submit();
        }
    });
});

// --- Live search ---
function setupLiveSearch(inputId, dropdownId, hiddenCodeId, balanceDisplayId, isPurchase=false){
    const input=document.getElementById(inputId);
    const dropdown=document.getElementById(dropdownId);
    const hidden=document.getElementById(hiddenCodeId);
    const balanceDiv=balanceDisplayId?document.getElementById(balanceDisplayId):null;
    const balanceSpan=balanceDiv?balanceDiv.querySelector('span'):null;
    const hiddenName=isPurchase?document.getElementById('supplier_name'):null;

    const updateBalance=(b)=>{
        if(balanceSpan){
            balanceSpan.textContent=b;
            balanceSpan.classList.remove('text-success','text-danger');
            const n=parseFloat(b.replace(/,/g,''));
            balanceSpan.classList.add(n>0?'text-danger':'text-success');
            balanceDiv.style.display=b!=='0.00'?'block':'none';
        }
    };

    input.addEventListener('input', ()=>{
        const f=input.value.toUpperCase().trim();
        let visible=0;
        dropdown.querySelectorAll('button').forEach(btn=>{
            const t=btn.textContent.toUpperCase();
            const match=t.includes(f);
            btn.style.display=match?'block':'none';
            if(match) visible++;
        });
        dropdown.style.display=visible?'block':'none';
        if(f===''){ hidden.value=''; if(isPurchase) document.getElementById('supplier_id').value=''; if(hiddenName) hiddenName.value=''; updateBalance('0.00'); }
    });

    dropdown.addEventListener('click', e=>{
        if(e.target.matches('button')){
            const btn=e.target;
            const id=btn.getAttribute('data-id');
            const code=btn.getAttribute('data-code');
            const balance=btn.getAttribute('data-balance')||'0.00';
            const name=btn.getAttribute('data-name');

            if(inputId==='grn_search'){ input.value=btn.textContent.trim(); hidden.value=id; dropdown.style.display='none'; document.getElementById('submitButton').focus(); return; }

            input.value=`${code} ${name?'— '+name:''}`.trim();
            hidden.value=code;
            if(isPurchase){ document.getElementById('supplier_id').value=id; if(hiddenName) hiddenName.value=name||''; }
            updateBalance(balance);
            dropdown.style.display='none';
            // Update focus for payment form
            if(isPurchase) document.getElementById('description').focus(); else document.getElementById('payment_description').focus();
        }
    });
    document.addEventListener('click', e=>{ if(!dropdown.contains(e.target)&&e.target!==input) dropdown.style.display='none'; });
}

// --- Initialize live searches ---
setupLiveSearch('supplier_search','supplierDropdown','supplier_code','purchaseBalanceDisplay',true);
setupLiveSearch('grn_search','grnDropdown','grn_id');
setupLiveSearch('payment_supplier_search','paymentSupplierDropdown','payment_supplier_code','paymentBalanceDisplay');

// --- Transaction history modal & Edit functions ---
document.addEventListener('DOMContentLoaded', ()=>{
    const modalEl=document.getElementById('transactionModal');
    const tableBody=document.getElementById('transactionHistoryBody');
    let transactionModal;
    if(typeof bootstrap!=='undefined' && bootstrap.Modal) transactionModal=new bootstrap.Modal(modalEl);
    else if(typeof $!=='undefined' && $.fn.modal) transactionModal=$(modalEl);

    document.querySelector('.table-bordered tbody').addEventListener('click', e=>{
        // History
        if(e.target.classList.contains('view-history-link')){
            const code=e.target.getAttribute('data-supplier-code');
            fetchTransactionHistory(code);
        }
        // Inline Edit
        if(e.target.classList.contains('editButton')){
            const tr=e.target.closest('tr');
            const data=JSON.parse(tr.getAttribute('data-transaction').replace(/&quot;/g,'"'));
            populateFormForEdit(data);
        }
    });

    function fetchTransactionHistory(code){
        // The API call is to a route that was updated in the previous turn to include supplier details
        const csrf=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const url="{{ route('suppliers2.transactions') }}?supplier_code="+code;
        tableBody.innerHTML='<tr><td colspan="6" class="text-center">Loading transactions...</td></tr>';

        fetch(url,{ headers:{'X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} })
            .then(r=>r.json())
            .then(data=>{
                // Populate supplier details in modal header
                document.getElementById('modal_supplier_code').textContent=data.supplier_code;
                document.getElementById('modal_supplier_name').textContent=data.supplier_name;
                // NEW Supplier details from the updated controller
                document.getElementById('modal_supplier_email').textContent=data.supplier_email || 'N/A';
                document.getElementById('modal_supplier_phone').textContent=data.supplier_phone || 'N/A';
                document.getElementById('modal_supplier_address').textContent=data.supplier_address || 'N/A';

                document.getElementById('modal_total_purchases').textContent=data.total_purchases;
                document.getElementById('modal_total_payments').textContent=data.total_payments;
                const bEl=document.getElementById('modal_remaining_balance');
                bEl.textContent=data.remaining_balance;
                bEl.classList.remove('text-success','text-danger');
                bEl.classList.add(parseFloat(data.remaining_balance.replace(/,/g,''))>0?'text-danger':'text-success');
                tableBody.innerHTML='';

                if(data.history && data.history.length>0){
                    data.history.forEach(item=>{
                        const row=`<tr>
                            <td>${item.date}</td>
                            <td>${item.type}</td>
                            <td>${item.description}</td>
                            <td>${item.grn_no}</td>
                            <td class="${item.class}">${item.amount}</td>
                            <td>${item.balance}</td>
                        </tr>`;
                        tableBody.insertAdjacentHTML('beforeend',row);
                    });
                } else tableBody.innerHTML='<tr><td colspan="6" class="text-center text-muted">No transactions found.</td></tr>';
                
                if(transactionModal.show) transactionModal.show();
                else if(transactionModal.modal) transactionModal.modal('show');
            }).catch(err=>{console.error(err); tableBody.innerHTML='<tr><td colspan="6" class="text-center text-danger">Error loading data.</td></tr>';});
    }

    // --- Populate form for edit ---
    const cancelEditBtn=document.getElementById('cancelEditButton');
    function populateFormForEdit(data){
        addRadio.checked=true; payRadio.checked=false;
        supplierForm.style.display='block'; paymentForm.style.display='none';
        document.getElementById('transaction_id').value=data.id;
        document.getElementById('supplier_search').value=`${data.supplier_code} — ${data.supplier_name}`;
        document.getElementById('supplier_id').value=data.existing_supplier_id||'';
        document.getElementById('supplier_code').value=data.supplier_code;
        document.getElementById('supplier_name').value=data.supplier_name;
        document.getElementById('description').value=data.description;
        document.getElementById('total_amount').value=data.total_amount;
        document.getElementById('grn_search').value=data.grn?.code||'';
        document.getElementById('grn_id').value=data.grn_id||'';
        document.getElementById('submitButton').textContent='Update Purchase';
        cancelEditBtn.style.display='inline-block';
        cancelEditBtn.onclick=()=>{ window.location.reload(); }
    }
    
    // --- NEW: Table Search Filter ---
    const searchInput = document.getElementById('supplierTableSearch');
    const tableRows = document.getElementById('supplierTableBody').getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toUpperCase();
        let found = false;

        // Iterate through all table rows (excluding the empty message if present)
        for (let i = 0; i < tableRows.length; i++) {
            const row = tableRows[i];
            
            // Skip the 'No transactions found' row if it exists
            if (row.cells.length < 8) { 
                 row.style.display = 'none';
                 continue;
            }

            // Supplier Code is in the 3rd column (index 2)
            const codeCell = row.cells[2]; 
            const codeText = codeCell.textContent || codeCell.innerText;

            if (codeText.toUpperCase().indexOf(filter) > -1) {
                row.style.display = ''; // Show row
                found = true;
            } else {
                row.style.display = 'none'; // Hide row
            }
        }
        
        // This handles showing a 'no results' message if the table was not empty initially
        if (filter !== '' && !found && tableRows.length > 0) {
            // A more robust solution for 'no results' is usually desired,
            // but for simplicity, we'll ensure the existing 'no transactions found' row stays hidden
            // and assume users will see no rows if nothing matches.
        }
    });
});
</script>
<script>
    // --- Auto-fill description based on radio selection ---
function updateDescriptionField() {
    const purchaseDescriptionInput = document.getElementById('description');
    const paymentDescriptionInput = document.getElementById('payment_description');
    
    if (addRadio.checked) {
        purchaseDescriptionInput.value = 'Purchase from Supplier';
        paymentDescriptionInput.value = ''; // Clear payment description when switching to purchase
    } else if (payRadio.checked) {
        paymentDescriptionInput.value = 'Payment to Supplier'; // Updated for clarity
        purchaseDescriptionInput.value = ''; // Clear purchase description when switching to payment
    }
}

// Trigger when radio buttons change
addRadio.addEventListener('change', updateDescriptionField);
payRadio.addEventListener('change', updateDescriptionField);

// Trigger on page load to set initial value
document.addEventListener('DOMContentLoaded', updateDescriptionField);
</script>
@endsection