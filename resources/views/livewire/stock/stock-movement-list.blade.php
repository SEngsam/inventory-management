<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">{{ __('Stock movements') }}</h4>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row g-2">
                <div class="col-md-6">
                    <select class="form-select" wire:model.live="productId" aria-label="{{ __('Product') }}">
                        <option value="">{{ __('All products') }}</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }} ({{ $product->sku }}) · {{ __('in stock: :count', ['count' => $product->stock_quantity]) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="type" aria-label="{{ __('Type') }}">
                        <option value="">{{ __('All types') }}</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}">{{ __('stock.types.'.$type->value) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-traffic mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th class="text-end">{{ __('Change') }}</th>
                            <th class="text-end">{{ __('Balance') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Note') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $movement)
                            <tr wire:key="movement-{{ $movement->id }}">
                                <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $movement->product->name }}</td>
                                <td>{{ __('stock.types.'.$movement->type->value) }}</td>
                                <td class="text-end fw-semibold {{ $movement->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                </td>
                                <td class="text-end">{{ $movement->balance_after }}</td>
                                <td>
                                    @switch($movement->reference_type)
                                        @case('sale')
                                            <a href="{{ route('sales.show', $movement->reference_id) }}">{{ $movement->reference?->reference_no ?? '#'.$movement->reference_id }}</a>
                                            @break
                                        @case('sale_return')
                                            <a href="{{ route('sale-returns.show', $movement->reference_id) }}">{{ $movement->reference?->reference_no ?? '#'.$movement->reference_id }}</a>
                                            @break
                                        @case('purchase')
                                            <a href="{{ route('purchases.show', $movement->reference_id) }}">{{ $movement->reference?->reference_no ?? '#'.$movement->reference_id }}</a>
                                            @break
                                        @case('invoice')
                                            <a href="{{ route('invoices.show', $movement->reference_id) }}">{{ $movement->reference?->invoice_number ?? '#'.$movement->reference_id }}</a>
                                            @break
                                        @default
                                            <span class="text-muted">-</span>
                                    @endswitch
                                </td>
                                <td>{{ $movement->user?->name ?? '-' }}</td>
                                <td class="text-muted">{{ $movement->note }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">{{ __('No stock movements yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $movements->links() }}
        </div>
    </div>
</div>
