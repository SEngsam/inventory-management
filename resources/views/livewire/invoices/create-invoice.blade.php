<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-0 fw-semibold">🧾 Create New Invoice</h4>
                <small class="text-muted">Select customer and add products with quantities.</small>
            </div>
        </div>

        {{-- Global errors --}}
        @error('items')
            <div class="alert alert-danger py-2">{{ $message }}</div>
        @enderror

        {{-- Customer --}}
        <div class="mb-3">
            <label class="form-label">Select Customer <span class="text-danger">*</span></label>
            <select wire:model="selectedCustomer" class="form-select @error('selectedCustomer') is-invalid @enderror">
                <option value="">-- Choose --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            @error('selectedCustomer')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <hr class="my-4">

        <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0">🛒 Invoice Items</h5>
            <button type="button"
                    class="btn btn-sm btn-secondary"
                    wire:click="addItem"
                    wire:loading.attr="disabled">
                ➕ Add Item
            </button>
        </div>

        <div class="mt-3">
            @php
                $selectedProductIds = collect($items)->pluck('product_id')->filter()->all();
                $productsById = collect($products)->keyBy('id');
                $subtotal = 0;
            @endphp

            @foreach($items as $index => $item)
                @php
                    $pid = $item['product_id'] ?? null;
                    $qty = (int) ($item['quantity'] ?? 1);
                    $price = $pid && $productsById->has($pid) ? (float) $productsById[$pid]->price : 0;
                    $lineTotal = $price * max($qty, 0);
                    $subtotal += $lineTotal;
                @endphp

                <div class="row g-2 align-items-end mb-2">
                    {{-- Product --}}
                    <div class="col-md-6">
                        <label class="form-label small mb-1">Product</label>
                        <select wire:model="items.{{ $index }}.product_id"
                                class="form-select @error('items.'.$index.'.product_id') is-invalid @enderror">
                            <option value="">-- Product --</option>
                            @foreach($products as $product)
                                @php
                                    $disabled = in_array($product->id, $selectedProductIds, true)
                                                && (int)($pid) !== (int)($product->id);
                                @endphp
                                <option value="{{ $product->id }}" @disabled($disabled)>
                                    {{ $product->name }} — {{ number_format($product->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('items.'.$index.'.product_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Quantity --}}
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Qty</label>
                        <input type="number"
                               min="1"
                               class="form-control @error('items.'.$index.'.quantity') is-invalid @enderror"
                               wire:model="items.{{ $index }}.quantity"
                               placeholder="Qty">
                        @error('items.'.$index.'.quantity')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Price --}}
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Price</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $price ? number_format($price, 2) : '-' }}"
                               disabled>
                    </div>

                    {{-- Line total + remove --}}
                    <div class="col-md-2 d-flex gap-2 align-items-end">
                        <div class="flex-grow-1">
                            <label class="form-label small mb-1">Total</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $pid ? number_format($lineTotal, 2) : '-' }}"
                                   disabled>
                        </div>

                        <button type="button"
                                class="btn btn-outline-danger"
                                wire:click="removeItem({{ $index }})"
                                wire:loading.attr="disabled"
                                title="Remove">
                            ❌
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <hr class="my-4">

        {{-- Summary --}}
        <div class="d-flex justify-content-end">
            <div style="min-width: 280px;">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Subtotal</span>
                    <strong>{{ number_format($subtotal, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <span class="text-muted">Tax</span>
                    <strong>0.00</strong>
                </div>
                <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                    <span class="text-muted">Total</span>
                    <strong>{{ number_format($subtotal, 2) }}</strong>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            <button type="button"
                    class="btn btn-success"
                    wire:click="createInvoice"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>✅ Create Invoice</span>
                <span wire:loading>Creating...</span>
            </button>
        </div>
    </div>
</div>
