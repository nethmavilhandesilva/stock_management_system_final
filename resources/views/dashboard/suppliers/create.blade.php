@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <h2>+ නව සැපයුම්කරු (Add New Supplier)</h2>
    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>කේතය (Code)</label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>නම (Name)</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>ලිපිනය (Address)</label>
            <textarea name="address" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Add Supplier</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
