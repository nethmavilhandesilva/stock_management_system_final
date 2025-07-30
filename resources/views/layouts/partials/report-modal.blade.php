<!-- Report Filter Modal -->
<div class="modal fade" id="reportFilterModal" tabindex="-1" aria-labelledby="reportFilterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('report.fetch') }}" method="POST" target="_blank">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📄 Generate Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="grn_select" class="form-label">සැපයුම්කරු තොරතුරු තෝරන්න</label>
                        <input type="hidden" name="supplier_code" id="supplier_code"> <!-- This will be filled by JS -->

                        <select id="grn_select" class="form-select form-select-sm select2">
                            <option value="">-- සැපයුම්කරු තෝරන්න --</option>
                            @foreach ($entries as $entry)
                                <option value="{{ $entry->code }}" data-supplier-code="{{ $entry->supplier_code }}"
                                    data-code="{{ $entry->code }}" data-item-code="{{ $entry->item_code }}"
                                    data-item-name="{{ $entry->item_name }}" data-weight="{{ $entry->weight }}"
                                    data-price="{{ $entry->price_per_kg }}" data-total="{{ $entry->total }}"
                                    data-packs="{{ $entry->packs }}" data-grn-no="{{ $entry->grn_no }}"
                                    data-txn-date="{{ $entry->txn_date }}"
                                    data-original-weight="{{ $entry->original_weight }}"
                                    data-original-packs="{{ $entry->original_packs }}">
                                    {{ $entry->code }} | {{ $entry->supplier_code }} | {{ $entry->item_code }} |
                                    {{ $entry->item_name }} | {{ $entry->packs }} | {{ $entry->grn_no }} |
                                    {{ $entry->txn_date }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="mb-3">
                        <label for="start_date" class="form-label">ආරම්භ දිනය</label>
                        <input type="date" name="start_date" id="start_date" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="end_date" class="form-label">අවසන් දිනය</label>
                        <input type="date" name="end_date" id="end_date" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">ඉදිරිපත් කරන්න</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const grnSelect = document.getElementById('grn_select');
        const supplierCodeInput = document.getElementById('supplier_code');

        grnSelect.addEventListener('change', function () {
            const selectedOption = grnSelect.options[grnSelect.selectedIndex];
            const supplierCode = selectedOption.getAttribute('data-supplier-code');

            supplierCodeInput.value = supplierCode || '';
        });
    });
</script>
