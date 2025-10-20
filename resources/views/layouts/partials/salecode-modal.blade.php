<div class="modal fade" id="grnSaleReportModal" tabindex="-1" aria-labelledby="grnSaleReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('report.grn_sale.fetch') }}" method="POST" target="_blank">
            @csrf
            <div class="modal-content" style="background-color: #99ff99;">
                <div class="modal-header">
                    <h5 class="modal-title" id="grnSaleReportModalLabel">📄 GRN කේතය අනුව විකුණුම් වාර්තාව</h5>
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

                    <!-- GRN Select -->
                    <div class="mb-3">
                        <label for="grn_select" class="form-label" style="font-weight: bold; color: black;">GRN තොරතුරු තෝරන්න</label>
                        <select id="grn_select" class="form-select form-select-sm select2" name="grn_code" required>
                            <option value="" selected disabled>-- GRN තෝරන්න --</option>
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

                    <!-- Password Field -->
                    <div class="mb-3">
                        <label for="grn_password_field" class="form-label" style="font-weight: bold; color: black;">මුරපදය ඇතුලත් කරන්න</label>
                        <input type="password" id="grn_password_field" class="form-control" placeholder="මුරපදය">
                    </div>

                    <!-- Date Range Fields -->
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
                    <a href="{{ route('report.email.grn-sales') }}" class="btn btn-info">
                        📧 Daily Email Report
                    </a>
                    <button type="submit" class="btn btn-primary w-100">වාර්තාව ලබාගන්න</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const grnSelect = document.getElementById('grn_select');
    const supplierCodeInput = document.getElementById('grn_supplier_code');
    const passwordField = document.getElementById('grn_password_field');
    const dateRangeFields = document.getElementById('grn_date_range_fields');
    const grnSaleReportModal = document.getElementById('grnSaleReportModal');
    const correctPassword = 'nethma123';

    // Initialize Select2 with custom matcher
    $(grnSelect).select2({
        dropdownParent: $('#grnSaleReportModal'),
        placeholder: "-- GRN තෝරන්න --",
        allowClear: true,
        minimumResultsForSearch: 0,
        matcher: function(params, data) {
            if ($.trim(params.term) === '') return data;
            const term = params.term.trim().toUpperCase();
            const optionText = data.text.trim().toUpperCase();
            if (optionText.startsWith(term)) return data;
            return null;
        }
    });

    // Clear selection initially
    grnSelect.selectedIndex = 0;

    // Auto-fill supplier code when a GRN is selected
    grnSelect.addEventListener('change', function () {
        const selectedOption = grnSelect.options[grnSelect.selectedIndex];
        const supplierCode = selectedOption.getAttribute('data-supplier-code');
        supplierCodeInput.value = supplierCode || '';
    });

    // Show/hide date range fields based on password
    if(passwordField && dateRangeFields) {
        function checkPassword() {
            if(passwordField.value === correctPassword) {
                dateRangeFields.style.display = 'block';
            } else {
                dateRangeFields.style.display = 'none';
            }
        }
        passwordField.addEventListener('input', checkPassword);
        checkPassword();
    }

    // Refresh page on modal close
    if(grnSaleReportModal) {
        grnSaleReportModal.addEventListener('hidden.bs.modal', function () {
            window.location.reload();
        });
    }
});
</script>
