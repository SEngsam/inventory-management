<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">New Sale</h5>
    </div>

    <div class="card-body">
        @if (session()->has('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <form wire:submit.prevent="save">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Reference</label>
                    <input type="text" class="form-control" wire:model.defer="reference_no" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sale Date</label>
                    <input type="date" class="form-control" wire:model.defer="sale_date">
                </div>
            </div>

            <hr>
            <h6 class="text-uppercase fw-semibold">Items</h6>

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
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" min="1" class="form-control" wire:model="items.{{ $index }}.quantity">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Unit Price</label>
                        <input type="number" step="0.01" class="form-control"
                            wire:model="items.{{ $index }}.unit_price">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Total</label>
                        <input type="text" class="form-control bg-light" readonly
                            value="{{ number_format($item['quantity'] * $item['unit_price'], 2) }}">
                    </div>

                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm mt-1"
                            wire:click="removeItem({{ $index }})">
                            <i class="mdi mdi-trash-can-outline"></i>
                        </button>
                    </div>
                </div>
            @endforeach

            <div class="mb-3">
                <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addItem">
                    <i class="mdi mdi-plus"></i> Add Item
                </button>
            </div>

            <div class="mb-3">
                <label class="form-label">Note</label>
                <textarea class="form-control" rows="2" wire:model.defer="note"></textarea>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">Save Sale</button>
            </div>
        </form>
    </div>
</div>
