<div class="modal fade" id="weight_modal" tabindex="-1" aria-labelledby="weight_modal" aria-hidden="true">
    <div class="modal-dialog">
        {{-- Form action points to the POST route for report fetching --}}
        <form action="{{ route('report.supplier_grn.fetch') }}" method="POST" target="_blank">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="weight_modal">📄 GRN කේතය අනුව විකුණුම් වාර්තාව</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Display validation errors if any, e.g., "Please select a GRN code." --}}
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
                        <label for="grn_select" class="form-label">GRN තොරතුරු තෝරන්න</label>
                        {{-- CRITICAL FIX: Added name="grn_code" to send the selected value to the controller --}}
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

                    {{-- If your controller *does not* use 'supplier_code' from the form submission,
                         you can safely remove the following hidden input and its corresponding JS.
                         It's only needed if you want to explicitly send supplier_code separately. --}}
                    <div class="mb-3">
                        <input type="hidden" name="supplier_code" id="supplier_code">
                    </div>

                    <div class="mb-3">
                        <label for="sales_start_date" class="form-label">ආරම්භ දිනය</label>
                        <input type="date" name="start_date" id="sales_start_date" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="sales_end_date" class="form-label">අවසන් දිනය</label>
                        <input type="date" name="end_date" id="sales_end_date" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">වාර්තාව ලබාගන්න</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const grnSelect = document.getElementById('grn_select');
        // Initialize Select2 on your dropdown (requires jQuery to be loaded)
        // Ensure jQuery and Select2 libraries are loaded before this script.
        $(grnSelect).select2({
            dropdownParent: $('#grnSaleReportModal') // Important for Select2 within Bootstrap modals
        });

        // This JS is for filling the hidden 'supplier_code' input.
        // Remove this entire block if you don't need 'supplier_code' sent to the backend.
        const supplierCodeInput = document.getElementById('supplier_code'); // Correctly targets the hidden input's ID

        grnSelect.addEventListener('change', function () {
            const selectedOption = grnSelect.options[grnSelect.selectedIndex];
            // Get the supplier_code from the data attribute of the selected option
            const supplierCode = selectedOption.getAttribute('data-supplier-code');

            // Assign the retrieved supplierCode to the hidden input's value
            supplierCodeInput.value = supplierCode || '';
        });
    });
</script>