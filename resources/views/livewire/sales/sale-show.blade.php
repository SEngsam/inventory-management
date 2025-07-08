<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Sale #{{ $sale->reference }}</h1>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p><strong>Date:</strong> {{ $sale->sale_date }}</p>
                    <p><strong>Customer:</strong> {{ $sale->customer->name ?? 'N/A' }}</p>
                    <p><strong>Total:</strong> {{ number_format($sale->total, 2) }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($sale->payment_status) }}</p>

                    <hr>

                    <h5>Sale Items:</h5>
                    <ul>
                        @foreach ($sale->items as $item)
                            <li>
                                {{ $item->product->name }} — {{ $item->quantity }} × {{ $item->unit_price }} =
                                {{ $item->subtotal }}
                            </li>
                        @endforeach
                    </ul>
                    <p><strong>Customer:</strong>
                        {{ $sale->customer?->name ?? 'N/A' }}
                        @if ($sale->customer?->company)
                            ({{ $sale->customer->company }})
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
