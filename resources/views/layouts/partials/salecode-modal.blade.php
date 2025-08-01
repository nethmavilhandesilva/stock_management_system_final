<div class="modal fade" id="grnSaleReportModal" tabindex="-1" aria-labelledby="grnSaleReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('report.grn_sale.fetch') }}" method="POST" target="_blank">
            @csrf
            <div class="modal-content" style="background-color: #99ff99;">
                <div class="modal-header">
                    <h5 class="modal-title" id="grnSaleReportModalLabel">📄 GRN12 කේතය අනුව විකුණුම් වාර්තාව</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="grn_select" class="form-label" style="font-weight: bold; color: black;">GRN තොරතුරු තෝරන්න</label>
                        <select id="grn_select" class="form-select form-select-sm select2" name="grn_code" required>
                            <option value="">-- GRN තෝරන්න --</option>
                            @foreach ($entries as $entry)
                                <option value="{{ $entry->code }}" data-supplier-code="{{ $entry->supplier_code }}"
                                    data-item-code="{{ $entry->item_code }}"
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
                    
                    <input type="hidden" name="supplier_code" id="grn_supplier_code">
                    <div class="mb-3">
                        <label for="grn_password_field" class="form-label" style="font-weight: bold; color: black;">මුරපදය ඇතුලත් කරන්න</label>
                        <input type="password" id="grn_password_field" class="form-control" placeholder="මුරපදය">
                    </div>
                    <div id="grn_date_range_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="grn_start_date" class="form-label" style="font-weight: bold; color: black;">ආරම්භ දිනය</label>
                            <input type="date" name="start_date" id="grn_start_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="grn_end_date" class="form-label" style="font-weight: bold; color: black;">අවසන් දිනය</label>
                            <input type="date" name="end_date" id="grn_end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">වාර්තාව ලබාගන්න</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const grnSelect = document.getElementById('grn_select');
        const supplierCodeInput = document.getElementById('grn_supplier_code');
        const passwordField = document.getElementById('grn_password_field');
        const dateRangeFields = document.getElementById('grn_date_range_fields');
        const correctPassword = 'nethma123';

        // Initialize Select2 on the dropdown
        $(grnSelect).select2({
            dropdownParent: $('#grnSaleReportModal')
        });

        grnSelect.addEventListener('change', function () {
            const selectedOption = grnSelect.options[grnSelect.selectedIndex];
            const supplierCode = selectedOption.getAttribute('data-supplier-code');
            supplierCodeInput.value = supplierCode || '';
        });

        if (passwordField && dateRangeFields) {
            function checkPassword() {
                if (passwordField.value === correctPassword) {
                    dateRangeFields.style.display = 'block';
                } else {
                    dateRangeFields.style.display = 'none';
                }
            }
            passwordField.addEventListener('input', checkPassword);
            checkPassword();
        }
    });
</script>