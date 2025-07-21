@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Edit Item</h2>

    <form action="{{ route('items.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>No</label>
            <input type="text" name="no" class="form-control" value="{{ $item->no }}" required>
        </div>
        <div class="mb-3">
            <label>Type</label>
            <input type="text" name="type" class="form-control" value="{{ $item->type }}" required>
        </div>
        <div class="mb-3">
            <label>Pack Cost</label>
            <input type="number" name="pack_cost" step="0.01" class="form-control" value="{{ $item->pack_cost }}" required>
        </div>
        <div class="mb-3">
            <label>Pack Due</label>
            <input type="number" name="pack_due" step="0.01" class="form-control" value="{{ $item->pack_due }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Item</button>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
