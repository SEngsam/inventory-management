<div class="row">
    <div class="col-12">

        @if (session()->has('message'))
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
                <div class="toast show bg-success text-white shadow border-0">
                    <div class="d-flex">
                        <div class="toast-body">{{ session('message') }}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title mb-0">Suppliers</h5>
                <div>
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary me-2">+ Add Supplier</a>
                    <button class="btn btn-danger" wire:click="deleteSelected"
                        {{ empty($selectedSuppliers) ? 'disabled' : '' }}>
                        <i class="mdi mdi-trash-can-outline"></i> Delete
                    </button>
                </div>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><input type="checkbox" wire:model="selectAll"></th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td><input type="checkbox" wire:model="selectedSuppliers" value="{{ $supplier->id }}"></td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->company }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}">
                                        <i class="mdi mdi-pencil fs-18 text-muted border rounded p-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No suppliers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
