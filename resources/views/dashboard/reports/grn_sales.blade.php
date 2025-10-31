@extends('layouts.app')

@section('content')
<div class="container">
    <h3>GRN Sales Report</h3>

    {{-- 🔍 Date Range Filter --}}
    {{-- 🔍 Date Range + Code Search Filter (All in One Line) --}}
<form method="GET" action="{{ route('grn.sales.report') }}" class="mb-4 d-flex flex-wrap align-items-end gap-3">

    {{-- Start Date --}}
    <div>
        <label for="start_date" class="form-label mb-1">Start Date</label>
        <input type="date" name="start_date" id="start_date" class="form-control"
               value="{{ request('start_date') }}">
    </div>

    {{-- End Date --}}
    <div>
        <label for="end_date" class="form-label mb-1">End Date</label>
        <input type="date" name="end_date" id="end_date" class="form-control"
               value="{{ request('end_date') }}">
    </div>

    {{-- Code Search --}}
    <div class="position-relative" style="width: 250px;">
        <label for="codeSearch" class="form-label mb-1 fw-bold">Search by Code</label>
        <input type="text" id="codeSearch" class="form-control text-uppercase" placeholder="Type to filter by code...">
        <ul id="codeSuggestions" 
            class="list-group position-absolute w-100 shadow-sm" 
            style="display: none; z-index: 10; max-height: 200px; overflow-y: auto;">
        </ul>
    </div>

    {{-- Buttons --}}
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success">Filter</button>
        <a href="{{ route('grn.sales.report') }}" class="btn btn-secondary">Reset</a>
    </div>

</form>


    {{-- 🧮 Data Table --}}
    <table class="table table-sm table-bordered" id="grnTable">
        <thead>
            <tr>
                <th>Code</th>
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
                @php
                    $profitLoss = $row->netsale - $row->total_cost;
                @endphp
                <tr>
                    <td class="code">{{ $row->code }}-{{ $row->item_name }}</td>
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

{{-- 🧠 Dynamic JS Filter --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("codeSearch");
    const suggestionsBox = document.getElementById("codeSuggestions");
    const tableRows = document.querySelectorAll("#grnTable tbody tr");

    // Collect unique codes for autocomplete
    const codes = Array.from(document.querySelectorAll(".code"))
        .map(td => td.textContent.trim().toUpperCase())
        .filter((v, i, a) => a.indexOf(v) === i);

    searchInput.addEventListener("input", () => {
        const searchValue = searchInput.value.trim().toUpperCase();

        // Filter dropdown suggestions
        const matches = codes.filter(code => code.startsWith(searchValue));
        suggestionsBox.innerHTML = "";

        if (searchValue && matches.length > 0) {
            matches.forEach(code => {
                const li = document.createElement("li");
                li.classList.add("list-group-item", "list-group-item-action");
                li.textContent = code;
                li.onclick = () => {
                    searchInput.value = code;
                    suggestionsBox.style.display = "none";
                    filterTable(code);
                };
                suggestionsBox.appendChild(li);
            });
            suggestionsBox.style.display = "block";
        } else {
            suggestionsBox.style.display = "none";
        }

        // Filter table rows live
        filterTable(searchValue);
    });

    // Hide dropdown if clicked outside
    document.addEventListener("click", (e) => {
        if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
            suggestionsBox.style.display = "none";
        }
    });

    // Function to filter table rows
    function filterTable(searchValue) {
        tableRows.forEach(row => {
            const code = row.querySelector(".code").textContent.trim().toUpperCase();
            row.style.display = code.startsWith(searchValue) ? "" : "none";
        });
    }
});
</script>
@endsection
