<div class="modal fade" id="weight_modal" tabindex="-1" aria-labelledby="weight_modal_label" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('report.supplier_grn.fetch') }}" method="POST" target="_blank">
            @csrf
            <div class="modal-content" style="background-color: #99ff99;">
                <div class="modal-header">
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
                        <label for="weight_password_field" class="form-label" style="font-weight: bold; color: black;">මුරපදය ඇතුලත් කරන්න</label>
                        <input type="password" id="weight_password_field" class="form-control" placeholder="මුරපදය">
                    </div>
                    <div id="weight_date_range_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="weight_start_date" class="form-label" style="font-weight: bold; color: black;">ආරම්භ දිනය</label>
                            <input type="date" name="start_date" id="weight_start_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="weight_end_date" class="form-label" style="font-weight: bold; color: black;">අවසන් දිනය</label>
                            <input type="date" name="end_date" id="weight_end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">වාර්තාව ලබාගන්න</button>
                </div>
                  <div class="mt-3">
        {{-- New button to send the daily report --}}
        <a href="{{ route('report.email.daily') }}" class="btn btn-info">
            📧 Daily Email Report
        </a>
    </div>
            </div>
        </form>
    </div>
</div>

---

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const passwordField = document.getElementById('weight_password_field');
        const dateRangeFields = document.getElementById('weight_date_range_fields');
        const correctPassword = 'nethma123';
        
        // Use an ID selector to be specific
        const weightModal = document.querySelector('#weight_modal');

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

        if (weightModal) {
            weightModal.addEventListener('hidden.bs.modal', function (event) {
                window.location.reload();
            });
        }
    });
</script>

