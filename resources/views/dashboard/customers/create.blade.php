@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Add Customer</h2>

    <form action="{{ route('customers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Address</label>
            <input name="address" class="form-control">
        </div>
        <div class="mb-3">
            <label>Telephone No</label>
            <input name="telephone_no" class="form-control">
        </div>
        <div class="mb-3">
            <label>ණය සීමාව</label>
            <input type="number" step="0.01" name="credit_limit" class="form-control" required>
        </div>
        <button class="btn btn-success">Add</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
