@extends('layouts.app')

@section('content')
    {{-- Material Icons --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

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
        
        /* Initial state of the hidden column */
        .total-grn-column,
        .total-grn-header {
            display: none;
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

            <div class="mb-3">
                <label for="list_password" class="form-label">මුරපදය</label>
                <input type="password" id="list_password" class="form-control" placeholder="View hidden column...">
            </div>

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
                                <th>ස්වයංක්‍රීය මිලදී ගැනීම් අංකය</th>
                                <th>කේතය</th>
                                <th>සැපයුම්කරු කේතය</th>
                                <th>අයිතම කේතය</th>
                                <th>අයිතම නාමය</th>
                                <th>පැක්‌</th>
                                <th>බර (kg)</th>
                                <th>ගනුදෙනු දිනය</th>
                                <th>GRN අංකය</th>
                                <th>ගබඩා අංකය</th>
                                <th class="total-grn-header">GRN සඳහා මුළු එකතුව</th>
                                <th>මෙහෙයුම්</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                                <tr class="grn-row" data-entry-id="{{ $entry->id }}">
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
                                    <td class="total-grn-column">{{ $entry->total_grn }}</td>
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
    <style>
        .custom-context-menu {
            position: absolute;
            z-index: 9999;
            background-color: white;
            border: 1px solid #ccc;
            padding: 5px 0;
            box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.2);
            display: none;
            width: 140px;
        }

        .custom-context-menu li {
            list-style: none;
            padding: 8px 12px;
            cursor: pointer;
        }

        .custom-context-menu li:hover {
            background-color: #f1f1f1;
        }
    </style>

    <ul class="custom-context-menu" id="contextMenu">
        <li id="hideOption">Hide</li>
        <li id="unhideOption">Don't Hide</li>
    </ul>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const contextMenu = document.getElementById('contextMenu');
            let currentEntryId = null;

            document.querySelectorAll('.grn-row').forEach(row => {
                row.addEventListener('contextmenu', function (e) {
                    e.preventDefault();
                    currentEntryId = this.dataset.entryId;
                    contextMenu.style.top = `${e.pageY}px`;
                    contextMenu.style.left = `${e.pageX}px`;
                    contextMenu.style.display = 'block';
                });
            });

            document.addEventListener('click', () => {
                contextMenu.style.display = 'none';
            });

            const csrfToken = '{{ csrf_token() }}';

            document.getElementById('hideOption').addEventListener('click', function () {
                if (currentEntryId) {
                    fetch(`/grn/${currentEntryId}/hide`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                        },
                    }).then(() => {
                        alert("Entry marked as hidden in the database.");
                        contextMenu.style.display = 'none';
                    });
                }
            });

            document.getElementById('unhideOption').addEventListener('click', function () {
                if (currentEntryId) {
                    fetch(`/grn/${currentEntryId}/unhide`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                        },
                    }).then(() => {
                        alert("Entry marked as visible in the database.");
                        contextMenu.style.display = 'none';
                    });
                }
            });

            // Password logic for the hidden column
            const passwordField = document.getElementById('list_password');
            const totalGrnCells = document.querySelectorAll('.total-grn-column');
            const totalGrnHeader = document.querySelector('.total-grn-header');
            
            passwordField.addEventListener('input', function () {
                const correctPassword = 'nethma123';
                const isPasswordCorrect = passwordField.value === correctPassword;

                if (isPasswordCorrect) {
                    totalGrnCells.forEach(cell => cell.style.display = 'table-cell');
                    totalGrnHeader.style.display = 'table-cell';
                    passwordField.style.backgroundColor = '#d4edda'; // Light green for success
                    passwordField.style.borderColor = '#28a745';
                } else {
                    totalGrnCells.forEach(cell => cell.style.display = 'none');
                    totalGrnHeader.style.display = 'none';
                    passwordField.style.backgroundColor = '#f8d7da'; // Light red for incorrect
                    passwordField.style.borderColor = '#dc3545';
                }

                // Clear the styling if the password field is empty
                if (passwordField.value === '') {
                    passwordField.style.backgroundColor = '';
                    passwordField.style.borderColor = '';
                }
            });

        });
    </script>
@endpush
