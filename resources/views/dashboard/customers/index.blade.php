@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Customers</h2>
    <a href="{{ route('customers.create') }}" class="btn btn-primary mb-3">+ Add Customer</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Telephone</th>
                <th>ණය සීමාව</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->address }}</td>
                <td>{{ $customer->telephone_no }}</td>
                <td>Rs. {{ number_format($customer->credit_limit, 2) }}</td>
                <td>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this customer?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
