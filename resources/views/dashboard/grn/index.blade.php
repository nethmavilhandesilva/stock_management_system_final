@extends('layouts.app')

@section('content')
{{-- This link is typically in layouts.app, but added here for self-containment if needed --}}
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12"> {{-- Using col-md-12 for full width for the table --}}
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">GRN Entry List</h2>
                    <a href="{{ route('grn.create') }}" class="btn btn-primary">
                        <i class="material-icons align-middle me-1">add_circle_outline</i> Add New GRN Entry
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($entries->isEmpty())
                    <div class="alert alert-info" role="alert">
                        No GRN entries found.
                    </div>
                @else
                    <div class="table-responsive"> {{-- Added table-responsive for better mobile viewing --}}
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-center">#</th>
                                    <th scope="col" class="text-center">Auto Purchase No</th>
                                    <th scope="col" class="text-center">Code</th>
                                    <th scope="col" class="text-center">Supplier Code</th>
                                    <th scope="col" class="text-center">Item Code</th>
                                    <th scope="col" class="text-center">Item Name</th>
                                    <th scope="col" class="text-center">Packs</th>
                                    <th scope="col" class="text-center">Weight</th>
                                    <th scope="col" class="text-center">Txn Date</th>
                                    <th scope="col" class="text-center">GRN No</th>
                                    <th scope="col" class="text-center">Warehouse</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entries as $entry)
                                <tr class="text-center">
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
                                        <a href="{{ route('grn.edit', $entry->id) }}" class="btn btn-sm btn-info me-1">
                                            <i class="material-icons" style="font-size: 16px;">edit</i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-entry-id="{{ $entry->id }}">
                                            <i class="material-icons" style="font-size: 16px;">delete</i>
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
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
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
                    <button type="submit" class="btn btn-danger">Delete</button>
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
            var button = event.relatedTarget; // Button that triggered the modal
            var entryId = button.getAttribute('data-entry-id'); // Extract info from data-* attributes
            var actionUrl = '{{ route('grn.destroy', ':id') }}';
            actionUrl = actionUrl.replace(':id', entryId);
            var form = deleteConfirmationModal.querySelector('#deleteForm');
            form.setAttribute('action', actionUrl);
        });
    });
</script>
@endpush
