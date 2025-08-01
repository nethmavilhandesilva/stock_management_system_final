<!-- Report Filter Modal -->
<div class="modal fade" id="reportFilterModal" tabindex="-1" aria-labelledby="reportFilterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('report.fetch') }}" method="POST" target="_blank">
            @csrf
            <div class="modal-content" style="background-color: #99ff99;">

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="password" class="form-label" style="font-weight: bold; color: black;">පස්වර්ඩ් ඇතුල් කරන්න</label>
                        <input type="password" id="password" class="form-control" placeholder="පස්වර්ඩ්">
                    </div>

                    <div class="mb-3">
                        <label for="grn_select" class="form-label" style="font-weight: bold; color: black;">සැපයුම්කරු තොරතුරු තෝරන්න</label>
                        <input type="hidden" name="supplier_code" id="supplier_code">

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

                    <!-- Date range inputs wrapped in a container to toggle visibility -->
                    <div id="date-range-container" style="display: none;">
                        <div class="mb-3">
                            <label for="start_date" class="form-label" style="font-weight: bold; color: black;">ආරම්භ දිනය</label>
                            <input type="date" name="start_date" id="start_date" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label" style="font-weight: bold; color: black;">අවසන් දිනය</label>
                            <input type="date" name="end_date" id="end_date" class="form-control">
                        </div>
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

        const passwordInput = document.getElementById('password');
        const dateRangeContainer = document.getElementById('date-range-container');

        grnSelect.addEventListener('change', function () {
            const selectedOption = grnSelect.options[grnSelect.selectedIndex];
            const supplierCode = selectedOption.getAttribute('data-supplier-code');
            supplierCodeInput.value = supplierCode || '';
        });

        // Show or hide date range inputs based on password
        passwordInput.addEventListener('input', function () {
            const correctPassword = 'nethma123';
            if (passwordInput.value === correctPassword) {
                dateRangeContainer.style.display = 'block';
            } else {
                dateRangeContainer.style.display = 'none';
                // Optional: Clear the date inputs when hidden
                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
            }
        });
    });
</script>
