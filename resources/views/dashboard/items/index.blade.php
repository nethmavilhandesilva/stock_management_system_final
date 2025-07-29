@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99;
    }

    .custom-card {
        background-color: #006400 !important;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 24px;
    }

    .table thead th {
        background-color: #e6f0ff;
        color: #003366;
        text-align: center;
    }

    .table tbody td {
        vertical-align: middle;
        text-align: center;
    }

    .btn-sm {
        font-size: 0.875rem;
        padding: 6px 12px;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f5ff;
    }

    .btn-success {
        background-color: #198754;
    }
</style>

<div class="container-fluid mt-5">
    <div class="custom-card">
        <h2 class="mb-4 text-center text-primary">භාණ්ඩ ලැයිස්තුව (Items List)</h2>

        <div class="text-end mb-3">
            <a href="{{ route('items.create') }}" class="btn btn-success">
                + නව භාණ්ඩයක් එකතු කරන්න
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead>
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

                    @if($items->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center text-muted">භාණ්ඩ නොමැත</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
