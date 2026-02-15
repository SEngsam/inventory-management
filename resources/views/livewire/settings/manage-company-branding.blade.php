<div class="container mt-4">

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Company & Branding</h5>
            <small class="text-muted">Manage company details and logo</small>
        </div>

        <div class="card-body">
            <form wire:submit.prevent="save">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Company Name *</label>
                        <input type="text" class="form-control"
                               wire:model.defer="company_name">
                        @error('company_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Legal Name</label>
                        <input type="text" class="form-control"
                               wire:model.defer="legal_name">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tax Number</label>
                        <input type="text" class="form-control"
                               wire:model.defer="tax_number">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control"
                               wire:model.defer="email">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control"
                               wire:model.defer="phone">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Website</label>
                        <input type="text" class="form-control"
                               wire:model.defer="website">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea class="form-control"
                                  rows="2"
                                  wire:model.defer="address"></textarea>
                    </div>

                    <hr class="my-3">

                    <div class="col-md-6">
                        <label class="form-label">Company Logo</label>
                        <input type="file" class="form-control"
                               wire:model="logo">

                        @error('logo')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror

                        <div wire:loading wire:target="logo"
                             class="text-muted small mt-2">
                            Uploading...
                        </div>
                    </div>

                    <div class="col-md-6 text-center">

                        @if($current_logo)
                            <div class="mb-2">
                                <label class="form-label d-block">
                                    Current Logo
                                </label>

                                <img src="{{ asset('storage/'.$current_logo) }}"
                                     class="img-thumbnail"
                                     style="max-height:120px;">
                            </div>

                            <button type="button"
                                    wire:click="removeLogo"
                                    class="btn btn-sm btn-outline-danger">
                                Remove Logo
                            </button>
                        @endif

                        @if ($logo)
                            <div class="mt-3">
                                <label class="form-label d-block">
                                    Preview
                                </label>

                                <img src="{{ $logo->temporaryUrl() }}"
                                     class="img-thumbnail"
                                     style="max-height:120px;">
                            </div>
                        @endif

                    </div>

                </div>

                <div class="mt-4 text-end">
                    <button class="btn btn-primary"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Changes</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
