<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Sale Returns List</h4>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Sale Returns</h5>
            <a href="{{ route('sale-returns.create') }}" class="btn btn-primary btn-sm">
                <i class="mdi mdi-plus"></i> New Return
            </a>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-3">
                    <label>From Date</label>
                    <input type="date" class="form-control" wire:model="filter.from">
                </div>
                <div class="col-md-3">
                    <label>To Date</label>
                    <input type="date" class="form-control" wire:model="filter.to">
                </div>
                <div class="col-md-4">
                    <label>Customer</label>
                    <select class="form-select" wire:model="filter.customer_id">
                        <option value="">All Customers</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-traffic mb-0">
                        <thead>
                            <tr>
                                <th>
                                    <input class="form-check-input" type="checkbox" wire:model="selectAll" />
                                </th>
                                <th>Reference</th>
                                <th>Sale Ref</th>
                                <th>Customer</th>
                                <th>Return Date</th>
                                <th>Total Returned</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($returns as $return)
                                <tr wire:key="return-{{ $return->id }}">
                                    <td>
                                        <input class="form-check-input" type="checkbox" wire:model="selectedReturns"
                                            value="{{ $return->id }}" />
                                    </td>
                                    <td>{{ $return->reference_no }}</td>
                                    <td>{{ $return->sale->reference_no ?? '-' }}</td>
                                    <td>{{ $return->sale->customer->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($return->return_date)->format('Y-m-d') }}</td>
                                    <td>
                                        {{ number_format($return->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}
                                        {{ config('app.currency_symbol', '$') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('sale-returns.show', $return->id) }}">
                                            <i class="mdi mdi-eye text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                        </a>
                                        {{-- {{ route('sale-returns.pdf', $return->id) }} --}}
                                        <a href="" target="_blank">
                                            <i class="mdi mdi-printer text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                        </a>
                                        <button wire:click="confirmDelete({{ $return->id }})"
                                            onclick="return confirm('Are you sure?')">
                                            <i class="mdi mdi-delete text-danger fs-18 rounded-2 border p-1"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No sale returns found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $returns->links() }}
                    </div>
                </div>
            </div>
            <div class="mt-3">
                {{ $returns->links() }}
            </div>

        </div>
    </div>
</div>
