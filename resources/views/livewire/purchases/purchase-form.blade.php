<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ $purchaseId ? 'Edit Purchase' : 'New Purchase' }}</h5>
        </div>
        <div class="card-body">

            @if (session()->has('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <form wire:submit.prevent="save">

                <!-- Supplier -->
                <div class="mb-3">
                    <label class="form-label">Supplier</label>
                    <select class="form-select" wire:model="supplier_id">
                        <option value="">-- Select Supplier --</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Purchase Date and Reference -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Purchase Date</label>
                        <input type="date" class="form-control" wire:model.defer="purchase_date">
                        @error('purchase_date') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reference No.</label>
                        <input type="text" class="form-control" wire:model.defer="reference_no">
                        @error('reference_no') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr>

                <h6 class="text-uppercase fw-semibold">Items</h6>

                @if ($errors->has('items'))
                    <div class="alert alert-danger">{{ $errors->first('items') }}</div>
                @endif

                @foreach ($items as $index => $item)
                    <div class="row align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Product</label>
                            <select class="form-select" wire:model="items.{{ $index }}.product_id">
                                <option value="">-- Select Product --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error("items.$index.product_id") <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Quantity</label>
                            <input type="number" min="1" class="form-control" wire:model="items.{{ $index }}.quantity">
                            @error("items.$index.quantity") <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Unit Cost</label>
                            <input type="number" step="0.01" min="0" class="form-control" wire:model="items.{{ $index }}.unit_cost">
                            @error("items.$index.unit_cost") <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Total</label>
                            <input type="text" class="form-control bg-light" readonly
                                value="{{ number_format(($item['quantity'] ?? 0) * ($item['unit_cost'] ?? 0), 2) }}">
                        </div>

                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm mt-1" wire:click="removeItem({{ $index }})">
                                <i class="mdi mdi-trash-can-outline"></i> Remove
                            </button>
                        </div>
                    </div>
                @endforeach

                <div class="mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addItem">
                        <i class="mdi mdi-plus"></i> Add Item
                    </button>
                </div>

                <hr>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">Save Purchase</button>
                </div>

            </form>
        </div>
    </div>
</div>
