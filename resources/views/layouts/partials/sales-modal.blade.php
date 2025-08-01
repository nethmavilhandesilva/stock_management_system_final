<div class="modal fade" id="reportFilterModal1" tabindex="-1" aria-labelledby="reportFilterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('report.sales.filter') }}" method="GET" target="_blank">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="reportFilterModalLabel">📊 විකුණුම් වාර්තා පෙරහන් කරන්න</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Supplier Code Filter --}}
                    <div class="mb-3">
                        <label for="filter_supplier_code" class="form-label">සැපයුම්කරු කේතය</label>
                        <select name="supplier_code" id="filter_supplier_code" class="form-select form-select-sm select2-supplier">
                            <option value="">-- සියලුම සැපයුම්කරුවන් --</option>
                            @php
                                $suppliers = \App\Models\Supplier::all();
                            @endphp
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->code }}">{{ $supplier->name }} ({{ $supplier->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Customer Code Filter --}}
                    <div class="mb-3">
                        <label for="filter_customer_code" class="form-label">පාරිභෝගික කේතය</label>
                        <select name="customer_code" id="filter_customer_code" class="form-select form-select-sm select2-customer">
                            <option value="">-- සියලුම පාරිභෝගිකයන් --</option>
                            @php
                                $customers = \App\Models\Sale::all();
                            @endphp
                            @foreach($customers as $customer)
                                <option value="{{ $customer->customer_code }}">{{ $customer->customer_code }} ({{ $customer->customer_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Item Code Filter --}}
                    <div class="mb-3">
                        <label for="filter_item_code" class="form-label">අයිතම කේතය</label>
                        <select name="item_code" id="filter_item_code" class="form-select form-select-sm select2-item">
                            <option value="">-- සියලුම අයිතම --</option>
                            @php
                                $items = \App\Models\Item::all();
                            @endphp
                            @foreach($items as $item)
                                <option value="{{ $item->no }}">{{ $item->no }} - {{ $item->type }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Password field to unlock date and order-by filters --}}
                    <div class="mb-3">
                        <label for="report_password_field" class="form-label">මුරපදය ඇතුලත් කරන්න</label>
                        <input type="password" id="report_password_field" class="form-control form-control-sm" placeholder="මුරපදය">
                    </div>

                    {{-- Date Range and Order By Filters (Hidden by default) --}}
                    <div id="advanced_filters" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="filter_start_date" class="form-label">ආරම්භක දිනය</label>
                                <input type="date" name="start_date" id="filter_start_date" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="filter_end_date" class="form-label">අවසන් දිනය</label>
                                <input type="date" name="end_date" id="filter_end_date" class="form-control form-control-sm">
                            </div>
                        </div>

                        {{-- Order By Filter --}}
                        <div class="mb-3">
                            <label for="order_by" class="form-label">අනුපිළිවෙල</label>
                            <select name="order_by" id="order_by" class="form-select form-select-sm">
                              
                                <option value="id_asc">සාමාන්‍ය (පැරණි සිට නව)</option>
                                <option value="customer_code_asc">පාරිභෝගික කේතය (A-Z)</option>
                            
                                <option value="item_name_asc">අයිතමයේ නම (A-Z)</option>
                               
                              
                                <option value="total_asc">මුළු මුදල (අඩුම සිට වැඩිම)</option>
                               
                                <option value="weight_asc">බර (අඩුම සිට වැඩිම)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100"><i class="material-icons me-2">print</i>වාර්තාව ලබාගන්න</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize Select2 for the dropdowns
        if ($('#filter_supplier_code').length) {
            $('#filter_supplier_code').select2({
                dropdownParent: $('#reportFilterModal1'),
                placeholder: "-- සියලුම සැපයුම්කරුවන් --",
                allowClear: true
            });
        }
        if ($('#filter_customer_code').length) {
            $('#filter_customer_code').select2({
                dropdownParent: $('#reportFilterModal1'),
                placeholder: "-- සියලුම පාරිභෝගිකයන් --",
                allowClear: true
            });
        }
        if ($('#filter_item_code').length) {
            $('#filter_item_code').select2({
                dropdownParent: $('#reportFilterModal1'),
                placeholder: "-- සියලුම අයිතම --",
                allowClear: true
            });
        }

        // Add password protection functionality
        const passwordField = document.getElementById('report_password_field');
        const advancedFilters = document.getElementById('advanced_filters');
        const correctPassword = 'nethma123';

        if (passwordField && advancedFilters) {
            function checkPassword() {
                if (passwordField.value === correctPassword) {
                    advancedFilters.style.display = 'block';
                } else {
                    advancedFilters.style.display = 'none';
                }
            }

            passwordField.addEventListener('input', checkPassword);
            checkPassword(); // Run on load in case the field is pre-filled
        }
    });
</script>