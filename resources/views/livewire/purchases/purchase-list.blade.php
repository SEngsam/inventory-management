<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">Purchases</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);"> Purchases</a></li>
            <li class="breadcrumb-item active">Purchases List</li>
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
                <h5 class="card-title mb-0">Purchases List</h5>
                <div>
                    <a href="{{ route('purchases.create') }}" class="btn btn-primary me-2">
                        <i class="mdi mdi-plus"></i> Add
                    </a>

                    <button class="btn btn-danger" wire:click="deleteSelected"
                        {{ empty($selectedPurchases) ? 'disabled' : '' }}>
                        <i class="mdi mdi-trash-can"></i> Delete
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-traffic mb-0">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" wire:model="selectAll" />
                                </th>
                                <th>Reference</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Note</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchases as $purchase)
                                <tr>
                                    <td>
                                        <input type="checkbox" wire:model="selectedPurchases"
                                            value="{{ $purchase->id }}" />
                                    </td>
                                    <td>{{ $purchase->reference_no }}</td>
                                    <td>{{ $purchase->supplier->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $purchase->status === 'received' ? 'success' : ($purchase->status === 'pending' ? 'warning' : 'info') }}">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($purchase->note, 40) }}</td>
                                    <td>
                                        <a href="{{ route('purchases.show', $purchase->id) }}" title="View">
                                            <i class="mdi mdi-eye text-info fs-18 border rounded p-1 me-1"></i>
                                        </a>
                                        <a href="{{ route('purchases.edit', $purchase->id) }}">
                                            <i class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No purchases found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

</div>
