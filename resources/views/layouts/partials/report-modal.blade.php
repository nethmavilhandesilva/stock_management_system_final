<!-- Report Filter Modal -->
<div class="modal fade" id="reportFilterModal" tabindex="-1" aria-labelledby="reportFilterModalLabel" aria-hidden="true">
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
                        <label for="supplier_code" class="form-label">සැපයුම්කරුවන්ගේ කේතය</label>
                        <select name="supplier_code" id="supplier_code" class="form-select">
                            <option value="all">සියලු සැපයුම්කරුවන්</option>
                            @php
                                $supplierCodes = \App\Models\Sale::select('supplier_code')->distinct()->pluck('supplier_code');
                            @endphp
                            @foreach($supplierCodes as $code)
                                <option value="{{ $code }}">{{ $code }}</option>
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
