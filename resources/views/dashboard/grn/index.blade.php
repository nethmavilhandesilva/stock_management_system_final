@extends('layouts.app')

@section('content')
    {{-- Material Icons --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        body {
            background-color: #99ff99;
        }

        .custom-card {
            background-color:#006400 !important;
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

        .table-hover tbody tr:hover {
            background-color: #f1f5ff;
        }

        .btn-sm {
            font-size: 0.875rem;
            padding: 6px 10px;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-info {
            background-color: #0dcaf0;
            border-color: #0dcaf0;
            color: #fff;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>

    <div class="container-fluid mt-5">
        <div class="custom-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary mb-0">📄 GRN ලැයිස්තුව (Entry List)</h2>
                <a href="{{ route('grn.create') }}" class="btn btn-primary">
                    <i class="material-icons align-middle me-1">add_circle_outline</i> නව GRN එක් කරන්න
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($entries->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    කිසිඳු GRN ඇතුළත් කර නොමැත.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>ස්වයංක්‍රීය මිලදී ගැනීම් අංකය</th> {{-- Auto Purchase No --}}
                                <th>කේතය</th> {{-- Code --}}
                                <th>සැපයුම්කරු කේතය</th> {{-- Supplier Code --}}
                                <th>අයිතම කේතය</th> {{-- Item Code --}}
                                <th>අයිතම නාමය</th> {{-- Item Name --}}
                                <th>පැක්‌</th> {{-- Packs --}}
                                <th>බර (kg)</th> {{-- Weight --}}
                                <th>ගනුදෙනු දිනය</th> {{-- Txn Date --}}
                                <th>GRN අංකය</th> {{-- GRN No --}}
                                <th>ගබඩා අංකය</th> {{-- Warehouse --}}
                                <th>මෙහෙයුම්</th> {{-- Actions --}}
                            </tr>

                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $entry->auto_purchase_no }}</td>
                                    <td>{{ $entry->code }}</td>
                                    <td>{{ $entry->supplier_code }}</td>
                                    <td>{{ $entry->item_code }}</td>
                                    <td>{{ $entry->item_name }}</td>
                                    <td>{{ $entry->packs }}</td>
                                    <td>{{ $entry->weight }}</td>
                                    <td>{{ $entry->txn_date }}</td>
                                    <td>{{ $entry->grn_no }}</td>
                                    <td>{{ $entry->warehouse_no }}</td>
                                    <td>
                                        <a href="{{ route('grn.edit', $entry->id) }}" class="btn btn-sm btn-info me-1" title="Edit">
                                            <i class="material-icons">edit</i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteConfirmationModal" data-entry-id="{{ $entry->id }}">
                                            <i class="material-icons">delete</i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete GRN Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this GRN entry? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
            deleteConfirmationModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var entryId = button.getAttribute('data-entry-id');
                var actionUrl = '{{ route('grn.destroy', ':id') }}'.replace(':id', entryId);
                var form = deleteConfirmationModal.querySelector('#deleteForm');
                form.setAttribute('action', actionUrl);
            });
        });
    </script>
@endpush