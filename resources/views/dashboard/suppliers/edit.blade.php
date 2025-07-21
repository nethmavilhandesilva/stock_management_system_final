@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <h2>✏️ සැපයුම්කරු සංස්කරණය (Edit Supplier)</h2>
    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>කේතය (Code)</label>
            <input type="text" name="code" value="{{ $supplier->code }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>නම (Name)</label>
            <input type="text" name="name" value="{{ $supplier->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>ලිපිනය (Address)</label>
            <textarea name="address" class="form-control" required>{{ $supplier->address }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
