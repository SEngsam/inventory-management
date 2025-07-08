<div class="card">
    <div class="card-header"><h5 class="card-title mb-0">Create Sale Return</h5></div>

    <div class="card-body">
        @if (session()->has('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <form wire:submit.prevent="save">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Reference No</label>
                    <input type="text" class="form-control" wire:model="reference_no" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Return Date</label>
                    <input type="date" class="form-control" wire:model="return_date">
                </div>
            </div>

            <div class="mb-3">
                <label>Select Sale</label>
                <select class="form-select" wire:model="sale_id">
                    <option value="">-- Select Sale --</option>
                    @foreach($sales as $sale)
                        <option value="{{ $sale->id }}">{{ $sale->reference_no }} - {{ $sale->customer->name ?? 'No Customer' }}</option>
                    @endforeach
                </select>
            </div>

            <hr>
            <h6 class="fw-semibold">Return Items</h6>

            @foreach ($items as $index => $item)
                <div class="row mb-2 align-items-end">
                    <div class="col-md-4">
                        <label>Product</label>
                        <input type="text" class="form-control" value="{{ $item['product_name'] }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label>Quantity</label>
                        <input type="number" min="1" class="form-control" wire:model="items.{{ $index }}.quantity">
                    </div>

                    <div class="col-md-3">
                        <label>Unit Price</label>
                        <input type="number" step="0.01" class="form-control" wire:model="items.{{ $index }}.unit_price">
                    </div>
                </div>
            @endforeach

            <div class="mb-3 mt-3">
                <label>Note</label>
                <textarea class="form-control" wire:model.defer="note"></textarea>
            </div>

            <div class="text-end">
                <button class="btn btn-success">Save Return</button>
            </div>
        </form>
    </div>
</div>
