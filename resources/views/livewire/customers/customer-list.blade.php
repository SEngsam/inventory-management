<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Customers</h1>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="mb-3 d-flex justify-content-between">
        <a href="{{ route('customers.create') }}" class="btn btn-primary">+ Add Customer</a>
        <input wire:model.debounce.300ms="search" type="text" class="form-control w-25" placeholder="Search...">
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
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
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->company }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ Str::limit($customer->note, 30) }}</td>
                            <td>
                                <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <button class="btn btn-sm btn-danger" wire:click="deleteCustomer({{ $customer->id }})"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $customers->links() }}
        </div>
    </div>
</div>
