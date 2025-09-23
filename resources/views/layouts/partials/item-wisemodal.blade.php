<div class="modal fade" id="itemReportModal" tabindex="-1" aria-labelledby="itemReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('report.item.fetch') }}" method="POST" target="_blank">
            @csrf
            <div class="modal-content" style="background-color: #99ff99;">
                <div class="modal-header">
                    <h5 class="modal-title">📦 අයිතමය අනුව වාර්තාව</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="item_password" class="form-label" style="font-weight: bold; color: black;">පස්වර්ඩ් ඇතුල් කරන්න</label>
                        <input type="password" id="item_password" class="form-control" placeholder="පස්වර්ඩ්">
                    </div>

                    <div class="mb-3">
                        <label for="item_code_select" class="form-label" style="font-weight: bold; color: black;">අයිතමය</label>
                        <select name="item_code" id="item_code_select" class="form-select" required>
                            <option value="">-- අයිතමයක් තෝරන්න --</option>
                            @php
                                $items = \App\Models\Item::all();
                            @endphp
                            @foreach($items as $item)
                                <option value="{{ $item->no }}" data-supplier-code="{{ $item->supplier_code ?? '' }}">
                                       {{ $item->no }}-{{ $item->type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="item_date_range_container" style="display: none;">
                        <div class="mb-3">
                            <label for="item_start_date" class="form-label" style="font-weight: bold; color: black;">ආරම්භ දිනය</label>
                            <input type="date" name="start_date" id="item_start_date" class="form-control" placeholder="Leave empty to use today">
                        </div>

                        <div class="mb-3">
                            <label for="item_end_date" class="form-label" style="font-weight: bold; color: black;">අවසන් දිනය</label>
                            <input type="date" name="end_date" id="item_end_date" class="form-control" placeholder="Leave empty to use today">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                      <a href="{{ route('report.itemwise.email') }}" class="btn btn-info">
            📧 Daily Email Report
        </a>
                    <button type="submit" class="btn btn-primary w-100">වාර්තාව ලබාගන්න</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const itemSelect = document.getElementById('item_code_select');
        const supplierCodeInput = document.getElementById('item_supplier_code');
        const passwordInput = document.getElementById('item_password');
        const dateRangeContainer = document.getElementById('item_date_range_container');
        const itemReportModal = document.getElementById('itemReportModal');
        const correctPassword = 'nethma123';

        // Auto-fill supplier code when selecting an item
        itemSelect.addEventListener('change', function () {
            const selectedOption = itemSelect.options[itemSelect.selectedIndex];
            const supplierCode = selectedOption.getAttribute('data-supplier-code');
            supplierCodeInput.value = supplierCode || '';
        });

        // Show/hide date range when password is correct
        passwordInput.addEventListener('input', function () {
            if (passwordInput.value === correctPassword) {
                dateRangeContainer.style.display = 'block';
            } else {
                dateRangeContainer.style.display = 'none';
                document.getElementById('item_start_date').value = '';
                document.getElementById('item_end_date').value = '';
            }
        });
        
        // Add the event listener to refresh the page when the modal closes
        if (itemReportModal) {
            itemReportModal.addEventListener('hidden.bs.modal', function () {
                window.location.reload();
            });
        }
    });
</script>