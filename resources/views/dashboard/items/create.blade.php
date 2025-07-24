@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
       

        <!-- Main Content (Form) -->
        <div class="col-md-9">
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <h2 class="mb-4">නව භාණ්ඩය එක් කරන්න (Add New Item)</h2>

                <form action="{{ route('items.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="no" class="form-label"> අංකය</label>
                        <input type="text" id="no" name="no" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">වර්ගය</label>
                        <input type="text" id="type" name="type" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="pack_cost" class="form-label">මල්ලක අගය</label>
                        <input type="number" id="pack_cost" name="pack_cost" step="0.01" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="pack_due" class="form-label">මල්ලක කුලිය</label>
                        <input type="number" id="pack_due" name="pack_due" step="0.01" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success me-2">එක් කරන්න</button>
                    <a href="{{ route('items.index') }}" class="btn btn-secondary">අවලංගු කරන්න</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
