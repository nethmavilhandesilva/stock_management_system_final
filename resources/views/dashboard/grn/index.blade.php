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
        /* Initial hidden columns */
        .total-grn-column,
        .total-grn-header,
        .per-kg-price-column,
        .per-kg-price-header {
            display: none;
        }
        /* Search box style */
        #searchInput {
            max-width: 400px;
            margin-bottom: 1rem;
        }
        /* Compact table */
        #entriesTable {
            font-size: 0.85rem;
        }
        #entriesTable th,
        #entriesTable td {
            padding: 0.3rem 0.5rem;
            vertical-align: middle;
        }
        #entriesTable thead th {
            font-weight: 600;
        }
        .btn-sm {
            font-size: 0.75rem;
            padding: 4px 8px;
        }
        .material-icons {
            font-size: 16px;
        }
        .custom-card {
            padding: 12px 16px;
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

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Column unlock password --}}
            <div class="mb-3">
                <label for="list_password" class="form-label">මුරපදය</label>
                <input type="password" id="list_password" class="form-control" placeholder="View hidden columns...">
            </div>

            
            {{-- Search box --}}
            <input type="text" id="searchInput" class="form-control"
                   placeholder="Search by Code, Supplier Code, Item Code, or Item Name...">

            {{-- GRN Table --}}
            @if($entries->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    කිසිඳු GRN ඇතුළත් කර නොමැත.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle" id="entriesTable">
                        <thead class="table-light">
                        <tr>
                            <th>කේතය</th>
                            <th class="d-none">සැපයුම්කරුගේ කේතය</th>
                            <th class="d-none">අයිතම කේතය</th>
                            <th>අයිතම නාමය</th>
                            <th>පැක්‌</th>
                            <th>බර (kg)</th>
                            <th>ගනුදෙනු දිනය</th>
                            <th>GRN අංකය</th>
                            <th class="total-grn-header">GRN සඳහා මුළු එකතුව</th>
                            <th class="per-kg-price-header">Per KG Price</th>
                            <th>මෙහෙයුම්</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($entries as $entry)
                            <tr class="grn-row" data-entry-id="{{ $entry->id }}">
                                <td class="search-code">{{ $entry->code }}</td>
                                <td class="search-supplier-code d-none">{{ $entry->supplier_code }}</td>
                                <td class="search-item-code d-none">{{ $entry->item_code }}</td>
                                <td class="search-item-name">{{ $entry->item_name }}</td>
                                <td>{{ $entry->original_packs }}</td>
                                <td>{{ $entry->original_weight }}</td>
                                <td>{{ $entry->txn_date }}</td>
                                <td>{{ $entry->grn_no }}</td>
                                <td class="total-grn-column">{{ $entry->total_grn }}</td>
                                <td class="per-kg-price-column">{{ $entry->PerKGPrice }}</td>
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

    {{-- Delete Confirmation Modal --}}
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

            // Context menu
            document.querySelectorAll('.grn-row').forEach(row => {
                row.addEventListener('contextmenu', function (e) {
                    e.preventDefault();
                    currentEntryId = this.dataset.entryId;
                    contextMenu.style.top = `${e.pageY}px`;
                    contextMenu.style.left = `${e.pageX}px`;
                    contextMenu.style.display = 'block';
                });
            });
            document.addEventListener('click', () => contextMenu.style.display = 'none');

            const csrfToken = '{{ csrf_token() }}';

            // Hide action
            document.getElementById('hideOption').addEventListener('click', function () {
                if (currentEntryId) {
                    fetch(`/grn/${currentEntryId}/hide`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                        },
                    }).then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json(); // Assuming the backend returns JSON
                    }).then(() => {
                        alert("Entry marked as hidden in the database.");
                        contextMenu.style.display = 'none';
                    }).catch(error => {
                        console.error('Fetch error:', error);
                        alert("Failed to mark entry as hidden. Please try again.");
                    });
                }
            });

            // Unhide action
            document.getElementById('unhideOption').addEventListener('click', function () {
                if (currentEntryId) {
                    fetch(`/grn/${currentEntryId}/unhide`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                        },
                    }).then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json(); // Assuming the backend returns JSON
                    }).then(() => {
                        alert("Entry marked as visible in the database.");
                        contextMenu.style.display = 'none';
                    }).catch(error => {
                        console.error('Fetch error:', error);
                        alert("Failed to mark entry as visible. Please try again.");
                    });
                }
            });

            // Delete modal dynamic URL
            const deleteModal = document.getElementById('deleteConfirmationModal');
            const deleteForm = document.getElementById('deleteForm');

            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const entryId = button.getAttribute('data-entry-id');
                const deleteUrl = `/grn/${entryId}`;
                deleteForm.action = deleteUrl;
            });
            
            // Unlock hidden columns
            const passwordField = document.getElementById('list_password');
            const totalGrnCells = document.querySelectorAll('.total-grn-column');
            const totalGrnHeader = document.querySelector('.total-grn-header');
            const perKgPriceCells = document.querySelectorAll('.per-kg-price-column');
            const perKgPriceHeader = document.querySelector('.per-kg-price-header');

            function toggleHiddenColumns(show) {
                const displayStyle = show ? 'table-cell' : 'none';
                totalGrnHeader.style.display = displayStyle;
                perKgPriceHeader.style.display = displayStyle;
                totalGrnCells.forEach(cell => cell.style.display = displayStyle);
                perKgPriceCells.forEach(cell => cell.style.display = displayStyle);
            }

            passwordField.addEventListener('input', function () {
                const correctPassword = 'nethma123';
                const isCorrect = passwordField.value === correctPassword;

                toggleHiddenColumns(isCorrect);
                
                if (isCorrect) {
                    passwordField.style.backgroundColor = '#d4edda';
                    passwordField.style.borderColor = '#28a745';
                } else {
                    passwordField.style.backgroundColor = '#f8d7da';
                    passwordField.style.borderColor = '#dc3545';
                }
                if (passwordField.value === '') {
                    passwordField.style.backgroundColor = '';
                    passwordField.style.borderColor = '';
                }
            });

            // Enable Clear Data button
            const verificationField = document.getElementById('verificationField');
            const clearDataButton = document.getElementById('clearDataButton');
            verificationField.addEventListener('input', function () {
                clearDataButton.disabled = (verificationField.value.trim() !== 'nethma123');
            });

            // Search filter
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('#entriesTable tbody tr');
            searchInput.addEventListener('keyup', function () {
                const filter = searchInput.value.toLowerCase();
                rows.forEach(row => {
                    const code = row.querySelector('.search-code').textContent.toLowerCase();
                    const supplierCode = row.querySelector('.search-supplier-code').textContent.toLowerCase();
                    const itemCode = row.querySelector('.search-item-code').textContent.toLowerCase();
                    const itemName = row.querySelector('.search-item-name').textContent.toLowerCase();
                    row.style.display = (code.includes(filter) || supplierCode.includes(filter) ||
                        itemCode.includes(filter) || itemName.includes(filter)) ? '' : 'none';
                });
            });
        });
    </script>
@endpush