<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Return Details</h5>
        <a href="{{ route('sale-returns.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <strong>Return Ref:</strong> {{ $return->reference_no }}
            </div>
            <div class="col-md-4">
                <strong>Return Date:</strong> {{ \Carbon\Carbon::parse($return->return_date)->format('Y-m-d') }}
            </div>
            <div class="col-md-4">
                <strong>Customer:</strong> {{ $return->sale->customer->name ?? 'N/A' }}
            </div>
        </div>

        <h6 class="fw-bold mt-3">Returned Items</h6>
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($return->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name ?? 'Deleted Product' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ number_format($item->unit_price * $item->quantity, 2) }} {{ config('app.currency_symbol', '$') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-end mt-3">
            <strong>Total:</strong>
            {{ number_format($return->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}
            {{ config('app.currency_symbol', '$') }}
        </div>

        @if ($return->note)
            <div class="mt-4">
                <strong>Note:</strong>
                <p class="text-muted">{{ $return->note }}</p>
            </div>
        @endif
    </div>
</div>
