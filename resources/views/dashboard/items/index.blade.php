@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="mb-4">භාණ්ඩ ලැයිස්තුව (Items List)</h2>

    <a href="{{ route('items.create') }}" class="btn btn-success mb-3">+ නව භාණ්ඩයක් එකතු කරන්න</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th>ක අංකය</th>
                <th>වර්ගය</th>
                <th>මල්ලක අගය</th>
                <th>මල්ලක කුලිය</th>
                <th>මෙහෙයුම්</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <td>{{ $item->no }}</td>
                <td>{{ $item->type }}</td>
                <td>{{ number_format($item->pack_cost, 2) }}</td>
                <td>{{ number_format($item->pack_due, 2) }}</td>
                <td>
                    <a href="{{ route('items.edit', $item->id) }}" class="btn btn-primary btn-sm">යාවත්කාලීන</a>
                    <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('ඔබට මෙම භාණ්ඩය මකන්න අවශ්‍යද?')">මකන්න</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
