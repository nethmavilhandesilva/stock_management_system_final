@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99 !important;
        font-family: Arial, sans-serif;
    }

    .container {
        padding: 20px;
    }

    h3 {
        margin-bottom: 20px;
        font-weight: bold;
        color: #004d00;
    }

    /* Form Filters */
    form .form-label {
        font-weight: bold;
    }

    form .form-control, form .form-select {
        min-width: 150px;
    }

    form .d-flex.gap-2 button, form .d-flex.gap-2 a {
        min-width: 80px;
    }

    /* Table Styling */
    table {
        background-color: #ffffff;
    }

    table thead {
        background-color: #004d00;
        color: #fff;
        font-weight: bold;
    }

    table tbody tr:hover {
        background-color: #e6ffe6;
        cursor: pointer;
    }

    /* Code search suggestions */
    #codeSuggestions li:hover {
        background-color: #d4f7d4;
        cursor: pointer;
    }

    /* Modal styling */
    .modal-header {
        font-weight: bold;
    }

    .table-secondary {
        background-color: #f0f0f0 !important;
    }

</style>

<div class="container">
    <h3>GRN Sales Report</h3>

    {{-- Filters Inline --}}
    <form method="GET" action="{{ route('grn.sales.report') }}" class="mb-4 d-flex flex-wrap align-items-end gap-3">

        <div>
            <label for="start_date" class="form-label mb-1">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>

        <div>
            <label for="end_date" class="form-label mb-1">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>

        <div>
            <label for="supplier_code" class="form-label mb-1 fw-bold">Supplier Code</label>
            <select name="supplier_code" id="supplier_code" class="form-select">
                <option value="">All</option>
                <option value="L" {{ request('supplier_code') == 'L' ? 'selected' : '' }}>L</option>
                <option value="A" {{ request('supplier_code') == 'A' ? 'selected' : '' }}>A</option>
            </select>
        </div>

        <div class="position-relative" style="width: 250px;">
            <label for="codeSearch" class="form-label mb-1 fw-bold">Search by Code</label>
            <input type="text" id="codeSearch" class="form-control text-uppercase" placeholder="Type to filter by code...">
            <ul id="codeSuggestions" class="list-group position-absolute w-100 shadow-sm" style="display: none; z-index:10; max-height:200px; overflow-y:auto;"></ul>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">Filter</button>
            <a href="{{ route('grn.sales.report') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    {{-- GRN Table --}}
    <table class="table table-sm table-bordered" id="grnTable">
         <thead style="background-color: #cce5ff; color: #004085; font-weight: bold;">
            <tr>
                <th>Code / Item</th>
                <th>Sold Weight</th>
                <th>Sold Packs</th>
                <th>Selling Price</th>
                <th>Total Cost</th>
                <th>Net Sale</th>
                <th>Profit / Loss</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $row)
                @php $profitLoss = $row->netsale - $row->total_cost; @endphp
                <tr class="clickable-row" data-code="{{ $row->code }}">
                    <td class="code">{{ $row->code }} - {{ $row->item_name }}</td>
                    <td>{{ number_format($row->sold_weight, 3) }}</td>
                    <td>{{ number_format($row->sold_packs, 0) }}</td>
                    <td>{{ number_format($row->selling_price, 2) }}</td>
                    <td>{{ number_format($row->total_cost, 2) }}</td>
                    <td>{{ number_format($row->netsale, 2) }}</td>
                    <td style="color: {{ $profitLoss < 0 ? 'red' : 'green' }}">
                        {{ number_format(abs($profitLoss), 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Modal --}}
<div class="modal fade" id="salesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalTitle">Sales Records</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        {{-- GRN Entry Info --}}
        <div class="mb-3">
            <strong id="modalGrnInfo"></strong>
        </div>
        <table class="table table-sm table-bordered" id="salesTable">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Item Name</th>
                    <th>Weight</th>
                    <th>Price / KG</th>
                    <th>Total</th>
                    <th>Packs</th>
                    <th>Bill No</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- JS --}}
<script>
document.addEventListener("DOMContentLoaded", () => {

    // --- Code Search ---
    const searchInput = document.getElementById("codeSearch");
    const suggestionsBox = document.getElementById("codeSuggestions");
    const tableRows = document.querySelectorAll("#grnTable tbody tr");
    const codes = Array.from(document.querySelectorAll(".code")).map(td => td.textContent.trim().toUpperCase()).filter((v,i,a)=>a.indexOf(v)===i);

    searchInput.addEventListener("input", () => {
        const searchValue = searchInput.value.trim().toUpperCase();
        const matches = codes.filter(code => code.startsWith(searchValue));
        suggestionsBox.innerHTML = "";
        if(searchValue && matches.length>0){
            matches.forEach(code=>{
                const li=document.createElement("li");
                li.classList.add("list-group-item","list-group-item-action");
                li.textContent = code;
                li.onclick = ()=>{
                    searchInput.value = code;
                    suggestionsBox.style.display = "none";
                    filterTable(code);
                };
                suggestionsBox.appendChild(li);
            });
            suggestionsBox.style.display = "block";
        } else suggestionsBox.style.display = "none";
        filterTable(searchValue);
    });

    document.addEventListener("click", e=>{
        if(!suggestionsBox.contains(e.target) && e.target!==searchInput)
            suggestionsBox.style.display = "none";
    });

    function filterTable(searchValue){
        tableRows.forEach(row=>{
            const code = row.querySelector(".code").textContent.trim().toUpperCase();
            row.style.display = code.startsWith(searchValue)?"":"none";
        });
    }

    // --- Row click to modal ---
    tableRows.forEach(row=>{
        row.addEventListener("click", ()=>{
            const code = row.dataset.code;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            fetch(`{{ route('grn.sales.fetch') }}?code=${code}&start_date=${startDate}&end_date=${endDate}`)
                .then(res => res.json())
                .then(data => {
                    // Update GRN info at top
                    if(data.grn){
                        const grn = data.grn;
                        document.getElementById('modalGrnInfo').textContent = `${grn.code} - ${grn.item_name} | Total Packs: ${grn.packs} | Total Weight: ${parseFloat(grn.weight).toFixed(3)}`;
                    }

                    // Populate sales table with totals
                    const tbody = document.querySelector("#salesTable tbody");
                    tbody.innerHTML = "";

                    let totalWeight = 0;
                    let totalAmount = 0;
                    let totalPacks = 0;

                    data.sales.forEach(item => {
                        const weight = parseFloat(item.weight) || 0;
                        const total = parseFloat(item.total) || 0;
                        const packs = parseInt(item.packs) || 0;

                        totalWeight += weight;
                        totalAmount += total;
                        totalPacks += packs;

                        tbody.innerHTML += `
                            <tr style="color:${total<0?'red':'inherit'};">
                                <td>${item.code}</td>
                                <td>${item.item_name}</td>
                                <td>${weight.toFixed(3)}</td>
                                <td>${parseFloat(item.price_per_kg).toFixed(2)}</td>
                                <td>${total.toFixed(2)}</td>
                                <td>${packs}</td>
                                <td>${item.bill_no}</td>
                            </tr>
                        `;
                    });

                    // Add Totals row
                    tbody.innerHTML += `
                        <tr class="fw-bold table-secondary">
                            <td colspan="2" class="text-end">Totals:</td>
                            <td>${totalWeight.toFixed(3)}</td>
                            <td></td>
                            <td>${totalAmount.toFixed(2)}</td>
                            <td>${totalPacks}</td>
                            <td></td>
                        </tr>
                    `;

                    new bootstrap.Modal(document.getElementById('salesModal')).show();
                });
        });
    });

});
</script>
@endsection
