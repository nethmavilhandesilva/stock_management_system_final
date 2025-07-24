@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f3f6fb;
    }

    .form-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        padding: 30px;
    }

    .form-label {
        font-weight: 600;
        color: #333;
    }

    .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }

    .btn-primary {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }

    .btn-primary:hover {
        background-color: #4338ca;
        border-color: #4338ca;
    }

    .btn-secondary {
        background-color: #6c757d;
    }
</style>

<div class="container mt-5">
    <div class="form-card mx-auto" style="max-width: 600px;">
        <h3 class="mb-4 text-center text-primary">අයිතමය සංස්කරණය කරන්න</h3>

        <form action="{{ route('items.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="no" class="form-label">අංකය</label>
                <input type="text" name="no" id="no" class="form-control" value="{{ $item->no }}" required>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">වර්ගය</label>
                <input type="text" name="type" id="type" class="form-control" value="{{ $item->type }}" required>
            </div>

            <div class="mb-3">
                <label for="pack_cost" class="form-label">මල්ලක අගය</label>
                <input type="number" name="pack_cost" id="pack_cost" step="0.01" class="form-control" value="{{ $item->pack_cost }}" required>
            </div>

            <div class="mb-3">
                <label for="pack_due" class="form-label">මල්ලක කුලිය</label>
                <input type="number" name="pack_due" id="pack_due" step="0.01" class="form-control" value="{{ $item->pack_due }}" required>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-primary px-4">එක් කරන්න</button>
                <a href="{{ route('items.index') }}" class="btn btn-secondary px-4">අවලංගු කරන්න</a>
            </div>
        </form>
    </div>
</div>
@endsection
