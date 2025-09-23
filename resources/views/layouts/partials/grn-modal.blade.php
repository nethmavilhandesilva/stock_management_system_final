<div class="modal fade" id="codeSelectModal" tabindex="-1" aria-labelledby="codeSelectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="GET" action="{{ route('grn.report') }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="codeSelectModalLabel">Select Code for GRN Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="code" class="form-label">Code</label>
                    <select class="form-select" name="code" id="code">
                        <option value="">Leave empty for all records</option>
                        {{-- Loop through the codes shared by the View Composer --}}
                        @foreach($codes as $code)
                            <option value="{{ $code }}">{{ $code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                      <a href="{{ route('grn.sendEmail') }}" class="btn btn-info">
            📧 Daily Email Report
        </a>
                    <button type="submit" class="btn btn-success">Generate Report</button>
                </div>
            </div>
        </form>
    </div>
</div>