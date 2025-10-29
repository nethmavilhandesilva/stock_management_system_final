@extends('layouts.app')

@section('content')
<style>
    body { background-color: #99ff99; }
    .bg-light { background-color: #ffffff !important; }
    .list-group-item-action { cursor: pointer; }
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
                    @foreach($existingSuppliers as $ex)
                        <button type="button" class="list-group-item list-group-item-action"
                                data-id="{{ $ex->id }}" data-code="{{ $ex->code }}" data-name="{{ $ex->name }}">
                            {{ $ex->code }}{{ $ex->name ? ' — ' . $ex->name : '' }}
                        </button>
                    @endforeach
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
                    @foreach($existingSuppliers as $supplier)
                        <button type="button" class="list-group-item list-group-item-action"
                                data-code="{{ $supplier->code }}">
                            {{ $supplier->code }} — {{ $supplier->name }}
                        </button>
                    @endforeach
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
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this supplier?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted">No suppliers found.</td></tr>
        @endforelse
        </tbody>
    </table>
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
            else supplierForm.submit();
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
            else paymentForm.submit();
        }
    });
});

// --- Live search setup function ---
function setupLiveSearch(inputId, dropdownId, hiddenId, isPurchaseForm=false){
    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    const hidden = hiddenId ? document.getElementById(hiddenId) : null;

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
        if(hidden && filter===''){ hidden.value=''; }
        if(isPurchaseForm && document.getElementById('supplier_id') && filter===''){ document.getElementById('supplier_id').value=''; document.getElementById('supplier_name').value=''; }
    });

    dropdown.addEventListener('click', e => {
        if(e.target.matches('button')){
            input.value = e.target.textContent.trim();
            if(isPurchaseForm){
                document.getElementById('supplier_id').value = e.target.getAttribute('data-id');
                document.getElementById('supplier_code').value = e.target.getAttribute('data-code');
                document.getElementById('supplier_name').value = e.target.getAttribute('data-name') || '';
            } else if(hidden){
    const codeAttr = e.target.getAttribute('data-code');
    const idAttr = e.target.getAttribute('data-id');
    hidden.value = codeAttr || idAttr || '';
}

            dropdown.style.display='none';
        }
    });

    // Hide dropdown on outside click
    document.addEventListener('click', e => {
        if(!dropdown.contains(e.target) && e.target!==input) dropdown.style.display='none';
    });
}

// --- Setup live searches ---
setupLiveSearch('supplier_search','supplierDropdown','supplier_code', true);
setupLiveSearch('grn_search','grnDropdown','grn_id');
setupLiveSearch('payment_supplier_search','paymentSupplierDropdown','payment_supplier_code');
</script>
@endsection
