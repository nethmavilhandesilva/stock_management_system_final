<!-- Modal -->
<div class="modal fade" id="supplierSelectModal2" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="GET" action="{{ route('grn.report2') }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter by GRN Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="codeSearch" class="form-label">Search GRN Code</label>
                    <input type="text" id="codeSearch" class="form-control text-uppercase mb-2" placeholder="Type code, item code, or item name...">

                    <div class="search-list border rounded" id="codeList" style="max-height: 300px; overflow-y: auto;">
                        @foreach($allCodes as $entry)
                            <div class="search-item p-2" style="cursor:pointer;"
                                 data-code="{{ $entry->code }}"
                                 data-item-code="{{ $entry->item_code }}"
                                 data-item-name="{{ $entry->item_name }}"
                                 data-txn-date="{{ $entry->txn_date }}"
                                 onclick="selectCode(this)">
                                <strong>{{ $entry->code }}</strong> | {{ $entry->item_code }} | {{ $entry->item_name }} | {{ \Carbon\Carbon::parse($entry->txn_date)->format('Y-m-d') }}
                            </div>
                        @endforeach
                    </div>

                    <input type="hidden" name="code" id="selectedCode">
                    <input type="hidden" name="item_code" id="selectedItemCode">
                    <input type="hidden" name="item_name" id="selectedItemName">
                    <input type="hidden" name="txn_date" id="selectedTxnDate">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script for live search and selection -->
<script>
    const searchInput = document.getElementById('codeSearch');
    const codeList = document.getElementById('codeList');

    const selectedCodeInput = document.getElementById('selectedCode');
    const selectedItemCodeInput = document.getElementById('selectedItemCode');
    const selectedItemNameInput = document.getElementById('selectedItemName');
    const selectedTxnDateInput = document.getElementById('selectedTxnDate');

    function selectCode(el) {
        selectedCodeInput.value = el.dataset.code;
        selectedItemCodeInput.value = el.dataset.itemCode;
        selectedItemNameInput.value = el.dataset.itemName;
        selectedTxnDateInput.value = el.dataset.txnDate;

        searchInput.value = el.dataset.code; // show code in input
        // hide all items after selecting
        Array.from(codeList.children).forEach(item => item.style.display = 'none');
    }

    searchInput.addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        Array.from(codeList.children).forEach(item => {
            // check if any of the fields contain the filter
            const code = item.dataset.code.toLowerCase();
            const itemCode = item.dataset.itemCode.toLowerCase();
            const itemName = item.dataset.itemName.toLowerCase();

            if (code.includes(filter) || itemCode.includes(filter) || itemName.includes(filter)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Reset input and show all items when modal opens or closes
    const modal = document.getElementById('supplierSelectModal2');
    modal.addEventListener('shown.bs.modal', () => {
        searchInput.value = '';
        Array.from(codeList.children).forEach(item => item.style.display = 'block');
    });
    modal.addEventListener('hidden.bs.modal', () => {
        searchInput.value = '';
        Array.from(codeList.children).forEach(item => item.style.display = 'block');
    });
</script>
