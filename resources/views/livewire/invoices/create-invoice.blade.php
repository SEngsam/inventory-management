<div class="card">
    <div class="card-body">
        <h4 class="mb-4 fw-semibold">🧾 Create New Invoice</h4>

        <div class="mb-3">
            <label class="form-label">Select Customer</label>
            <select wire:model="selectedCustomer" class="form-select">
                <option value="">-- Choose --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <h5 class="mt-4">🛒 Invoice Items</h5>
        @foreach($items as $index => $item)
            <div class="row mb-2 align-items-center">
                <div class="col-md-6">
                    <select wire:model="items.{{ $index }}.product_id" class="form-select">
                        <option value="">-- Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" wire:model="items.{{ $index }}.quantity" min="1"
                        class="form-control" placeholder="Qty">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-danger" wire:click="removeItem({{ $index }})">❌ Remove</button>
                </div>
            </div>
        @endforeach

        <button class="btn btn-secondary" wire:click="addItem">➕ Add Item</button>

        <hr>
        <button class="btn btn-success" wire:click="createInvoice">✅ Create Invoice</button>
    </div>
</div>
