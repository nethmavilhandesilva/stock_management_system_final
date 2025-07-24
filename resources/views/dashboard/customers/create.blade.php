@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>පාරිභෝගිකයා එක් කරන්න</h2>

    <form action="{{ route('customers.store') }}" method="POST">
        @csrf
          <div class="mb-3">
            <label for="short_name_field">කෙටි නම</label> {{-- Added for="short_name_field" for accessibility --}}
            <input type="text" name="short_name" id="short_name_field" class="form-control" required> {{-- FIX IS HERE: changed name to short_name and added type="text" and id --}}
        </div>
         
        <div class="mb-3">
            <label>සම්පූර්ණ නම</label>
            <input name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>ලිපිනය</label>
            <input name="address" class="form-control">
        </div>
        <div class="mb-3">
            <label>දුරකථන අංකය</label>
            <input name="telephone_no" class="form-control">
        </div>
        <div class="mb-3">
            <label>ණය සීමාව (Rs.)</label>
            <input type="number" step="0.01" name="credit_limit" class="form-control" required>
        </div>
        <button class="btn btn-success">එක් කරන්න</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">අවලංගු කරන්න</a>
    </form>
</div>
@endsection
