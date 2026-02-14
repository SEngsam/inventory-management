<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Suppliers List</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);"> Suppliers</a></li>
                <li class="breadcrumb-item active">SupplierS List</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @if (session()->has('message'))
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
                    <div class="toast show align-items-center text-white bg-success border-0 shadow" role="alert"
                        id="successToast" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                {{ session('message') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                                aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Suppliers</h5>
                    <div>
                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary me-2">
                            <i class="mdi mdi-plus"></i> Add
                        </a>
                        <button class="btn btn-danger" wire:click="deleteSelected"
                            @if (count($selectedSuppliers) == 0) disabled @endif>
                            <i class="mdi mdi-trash-can"></i> Delete
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-traffic mb-0">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" wire:model.live="selectAll"></th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($suppliers as $supplier)
                                    <tr wire:key="supplier-{{ $supplier->id }}">
                                        <td>
                                            <input type="checkbox" wire:model="selectedSuppliers"
                                                value="{{ $supplier->id }}" class="me-2" />
                                        </td>
                                        <td>{{ $supplier->name }}</td>
                                        <td>{{ $supplier->company }}</td>
                                        <td>{{ $supplier->phone }}</td>
                                        <td>{{ $supplier->email }}</td>
                                        <td>
                                            <a href="{{ route('supplier.edit', $supplier->id) }}">
                                                <i
                                                    class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                            </a>
                                            <button wire:click="delete({{ $supplier->id }})"
                                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                                class="btn btn-sm btn-outline-danger">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No suppliers found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $suppliers->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if (session()->has('message'))
            const toastEl = document.getElementById('successToast');
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();
        @endif
    });
</script>
