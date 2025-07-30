<div class="modal fade" id="supplierGrnReportModal" tabindex="-1" aria-labelledby="supplierGrnReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        {{-- IMPORTANT: Change the form action to a new route for this report --}}
        <form action="{{ route('report.supplier_grn.fetch') }}" method="POST" target="_blank">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📄 සැපයුම්කරු අනුව GRN වාර්තාව</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="report_supplier_code" class="form-label">සැපයුම්කරුවා</label>
                        <select name="supplier_code" id="report_supplier_code" class="form-select select2">
                            <option value="all">සියලු සැපයුම්කරුවන්</option>
                            @php
                                // Fetch distinct supplier codes from GrnEntry model
                                $grnSuppliers = \App\Models\GrnEntry::select('supplier_code')->distinct()->pluck('supplier_code');
                            @endphp
                            @foreach($grnSuppliers as $code)
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="report_start_date" class="form-label">ආරම්භ දිනය</label>
                        <input type="date" name="start_date" id="report_start_date" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="report_end_date" class="form-label">අවසන් දිනය</label>
                        <input type="date" name="end_date" id="report_end_date" class="form-control">
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
    $(document).ready(function() {
        // Initialize Select2 for the new supplier dropdown
        $('#report_supplier_code').select2({
            placeholder: "සියලු සැපයුම්කරුවන්",
            allowClear: true
        });
    });
</script>