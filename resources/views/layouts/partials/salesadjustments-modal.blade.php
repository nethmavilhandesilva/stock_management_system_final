<div class="modal fade" id="reportFilterModal9" tabindex="-1" aria-labelledby="reportFilterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('reports.salesadjustment.filter') }}" method="POST" target="_blank">
            @csrf
            <div class="modal-content" style="background-color: #99ff99;">
                <div class="modal-header" style="border-bottom: 1px solid #dee2e6;">
                    <h5 class="modal-title" id="reportFilterModalLabel" style="color: black; font-weight: bold;">
                        ග්‍රාහක විකුණුම් වාර්තාව
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="adjustment_password" class="form-label" style="font-weight: bold; color: black;">
                            පස්වර්ඩ් ඇතුල් කරන්න (Enter Password)
                        </label>
                        <input type="password" id="adjustment_password" name="password" class="form-control" placeholder="පස්වර්ඩ්">
                    </div>

                    <div class="mb-3">
                        <label for="adjustment_grn_select" class="form-label" style="font-weight: bold; color: black;">
                            සැපයුම්කරු තොරතුරු තෝරන්න (Select Supplier Information)
                        </label>
                        <input type="hidden" name="supplier_code" id="adjustment_supplier_code">
                        <select id="adjustment_grn_select" class="form-select form-select-sm select2" name="code">
                            <option value="">-- සැපයුම්කරු තෝරන්න (Select Supplier) --</option>
                            @foreach ($entries as $entry)
                                <option value="{{ $entry->code }}"
                                    data-supplier-code="{{ $entry->supplier_code }}"
                                    data-code="{{ $entry->code }}"
                                    data-item-code="{{ $entry->item_code }}"
                                    data-item-name="{{ $entry->item_name }}"
                                    data-weight="{{ $entry->weight }}"
                                    data-price="{{ $entry->price_per_kg }}"
                                    data-total="{{ $entry->total }}"
                                    data-packs="{{ $entry->packs }}"
                                    data-grn-no="{{ $entry->grn_no }}"
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

                    <div id="adjustment_date_range_container" style="display: none;">
                        <div class="mb-3">
                            <label for="adjustment_start_date" class="form-label" style="font-weight: bold; color: black;">
                                ආරම්භ දිනය (Start Date)
                            </label>
                            <input type="date" name="start_date" id="adjustment_start_date" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="adjustment_end_date" class="form-label" style="font-weight: bold; color: black;">
                                අවසන් දිනය (End Date)
                            </label>
                            <input type="date" name="end_date" id="adjustment_end_date" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="border-top: 1px solid #dee2e6;">
                    <button type="submit" class="btn btn-primary w-100">
                        ඉදිරිපත් කරන්න (Submit)
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Select2
        $('#adjustment_grn_select').select2({
            dropdownParent: $('#reportFilterModal9')
        });

        const supplierCodeInput = document.getElementById('adjustment_supplier_code');
        const passwordInput = document.getElementById('adjustment_password');
        const dateRangeContainer = document.getElementById('adjustment_date_range_container');

        $('#adjustment_grn_select').on('select2:select', function (e) {
            const selectedOption = e.params.data.element;
            const supplierCode = $(selectedOption).data('supplier-code');
            supplierCodeInput.value = supplierCode || '';
        });

        $('#adjustment_grn_select').on('select2:unselect', function () {
            supplierCodeInput.value = '';
        });

        passwordInput.addEventListener('input', function () {
            const correctPassword = 'nethma123';
            if (passwordInput.value === correctPassword) {
                dateRangeContainer.style.display = 'block';
            } else {
                dateRangeContainer.style.display = 'none';
                document.getElementById('adjustment_start_date').value = '';
                document.getElementById('adjustment_end_date').value = '';
            }
        });
    });
</script>
