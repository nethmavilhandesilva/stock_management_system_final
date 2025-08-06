<!-- Modal -->
<div class="modal fade" id="dayStartModal" tabindex="-1" aria-labelledby="dayStartModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('sales.dayStart') }}" method="POST">
        @csrf
        <div class="modal-content">
            <div class="modal-header" style="background-color: #4CAF50; color: white;">
                <h5 class="modal-title" id="dayStartModalLabel">🌅 Confirm Day Start</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 20px; background-color: #f9f9f9;">
                <p style="font-size: 16px;">Are you sure you want to start a new day?</p>
                <p style="font-size: 15px; color: #555;">All current sales will be <strong>archived</strong> and removed.</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::now()->addDay()->format('Y-m-d') }}</p>
            </div>
            <div class="modal-footer" style="background-color: #f1f1f1;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn" style="background-color: #4CAF50; color: white;">✅ Yes, Start New Day</button>
            </div>
        </div>
    </form>
  </div>
</div>
