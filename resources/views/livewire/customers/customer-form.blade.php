<div class="container-fluid p-0">
    <h1 class="h3 mb-3">
        {{ $customer ? 'Edit Customer' : 'Add New Customer' }}
    </h1>

    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="save">

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input wire:model.defer="name" type="text" class="form-control">
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input wire:model.defer="email" type="email" class="form-control">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input wire:model.defer="phone" type="text" class="form-control">
                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Company</label>
                    <input wire:model.defer="company" type="text" class="form-control">
                    @error('company') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea wire:model.defer="address" class="form-control" rows="2"></textarea>
                    @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Note</label>
                    <textarea wire:model.defer="note" class="form-control" rows="2"></textarea>
                    @error('note') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <button type="submit" class="btn btn-success">
                    {{ $customer ? 'Update' : 'Save' }}
                </button>
                <a href="{{ route('customer.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
