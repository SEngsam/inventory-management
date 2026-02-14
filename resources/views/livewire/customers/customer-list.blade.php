<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Customers List</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Customers</a></li>
                <li class="breadcrumb-item active">Customers List</li>
            </ol>
        </div>
    </div>

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
            <h5 class="card-title mb-0">Customers</h5>
            <div>
                <a href="{{ route('customers.create') }}" class="btn btn-primary me-2">
                    <i class="mdi mdi-plus"></i> Add
                </a>
                <button class="btn btn-danger" wire:click="deleteSelected"
                    @if (count($selectedCustomers) == 0) disabled @endif>
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
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Note</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr wire:key="customer-{{ $customer->id }}">
                                <td>
                                    <input type="checkbox" wire:model="selectedCustomers" value="{{ $customer->id }}"
                                        class="me-2" />
                                </td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->company }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ Str::limit($customer->note, 30) }}</td>
                                <td>
                                    <a href="{{ route('customer.edit', $customer->id) }}">
                                        <i class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                    </a>
                                    <button wire:click="delete({{ $customer->id }})"
                                        onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No customers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
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
@endpush
