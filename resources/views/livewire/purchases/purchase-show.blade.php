<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="card-title mb-0">Purchase #{{ $purchase->reference_no }}</h5>
        <a href="{{ route('purchase.index') }}" class="btn btn-light">← Back</a>
    </div>

    <div class="card-body">
        <h6 class="mb-2">Supplier Info</h6>
        <p><strong>Name:</strong> {{ $purchase->supplier->name ?? 'N/A' }}</p>
        <p><strong>Company:</strong> {{ $purchase->supplier->company }}</p>
        <p><strong>Phone:</strong> {{ $purchase->supplier->phone }}</p>
        <p><strong>Email:</strong> {{ $purchase->supplier->email }}</p>

        <hr class="my-3" />

        <h6>Items</h6>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Cost</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_cost, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="mt-3"><strong>Status:</strong>
            <span class="badge bg-{{
                $purchase->status === 'received' ? 'success' :
                ($purchase->status === 'pending' ? 'warning' : 'info') }}">
                {{ ucfirst($purchase->status) }}
            </span>
        </p>

        <p><strong>Note:</strong> {{ $purchase->note }}</p>
    </div>
</div>
