@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>පාරිභෝගිකයා සංස්කරණය කරන්න</h2>

    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>කෙටි නම</label>
            <input name="name" value="{{ $customer->short_name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>සම්පූර්ණ නම</label>
            <input name="name" value="{{ $customer->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>ලිපිනය</label>
            <input name="address" value="{{ $customer->address }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>දුරකථන අංකය</label>
            <input name="telephone_no" value="{{ $customer->telephone_no }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>ණය සීමාව</label>
            <input type="number" step="0.01" name="credit_limit" value="{{ $customer->credit_limit }}" class="form-control" required>
        </div>
        <button class="btn btn-primary">එක් කරන්න</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">අවලංගු කරන්න</a>
    </form>
</div>
@endsection
