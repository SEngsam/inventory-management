<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Create Sale Return</h4>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Sale Return Form</h5>
        </div>

        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Reference No</label>
                        <input type="text" class="form-control" wire:model="reference_no" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Return Date</label>
                        <input type="date" class="form-control" wire:model="return_date">
                        @error('return_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label>Select Sale</label>
                    <select class="form-select" wire:model="sale_id">
                        <option value="">-- Select Sale --</option>
                        @foreach ($sales as $sale)
                            <option value="{{ $sale->id }}">
                                {{ $sale->reference_no }} - {{ $sale->customer->name ?? 'No Customer' }}
                            </option>
                        @endforeach
                    </select>
                    @error('sale_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <hr>

                <h6 class="fw-semibold mb-3">Return Items</h6>

                @foreach ($items as $index => $item)
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-5">
                            <label>Product</label>
                            <input type="text" class="form-control" value="{{ $item['product_name'] }}" readonly>
                        </div>

                        <div class="col-md-3">
                            <label>Quantity</label>
                            <input type="number" min="1" class="form-control" wire:model="items.{{ $index }}.quantity">
                            @error("items.$index.quantity") <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-3">
                            <label>Unit Price</label>
                            <input type="number" step="0.01" class="form-control" wire:model="items.{{ $index }}.unit_price">
                            @error("items.$index.unit_price") <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endforeach

                <div class="mb-3">
                    <label>Note</label>
                    <textarea class="form-control" wire:model.defer="note" rows="3"></textarea>
                </div>

                <div class="text-end">
                    <button class="btn btn-success" type="submit">Save Return</button>
                </div>
            </form>
        </div>
    </div>
</div>
