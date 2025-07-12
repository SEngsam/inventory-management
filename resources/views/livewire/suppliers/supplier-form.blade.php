<div>
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Supplier
            </h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);"> Suppliers</a></li>
                <li class="breadcrumb-item active"> Add Supplier</li>
            </ol>
        </div>
    </div>
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ isset($supplier) ? 'Edit Supplier' : 'Add Supplier' }}</h5>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="save">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" class="form-control" wire:model.defer="name">
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Company</label>
                    <input type="text" class="form-control" wire:model.defer="company">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" wire:model.defer="email">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" wire:model.defer="phone">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control" rows="2" wire:model.defer="address"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Note</label>
                <textarea class="form-control" rows="2" wire:model.defer="note"></textarea>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">Save Supplier</button>
            </div>
        </form>
    </div>
</div>

</div>
