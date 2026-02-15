<div class="container mt-4">
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="card shadow-sms">
        <div class="card-header">
            <h5 class="mb-0">Invoice Settings</h5>
            <small class="text-muted">Control numbering, format, and print options.</small>
        </div>

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Prefix</label>
                    <input type="text" class="form-control" wire:model.defer="prefix">
                    @error('prefix') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Reset Numbering</label>
                    <select class="form-select" wire:model.defer="reset">
                        <option value="yearly">Yearly</option>
                        <option value="never">Never</option>
                    </select>
                    @error('reset') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Next Number</label>
                    <input type="number" class="form-control" wire:model.defer="next_number" min="1">
                    @error('next_number') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Due Days Default</label>
                    <input type="number" class="form-control" wire:model.defer="due_days_default" min="0" max="365">
                    @error('due_days_default') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Invoice Format</label>
                    <input type="text" class="form-control" wire:model.defer="format"
                           placeholder="INV-{YYYY}-{000000}">
                    <div class="form-text">
                        Tokens: {YYYY}, {YY}, {MM}, {DD}, {000000} (number).
                    </div>
                    @error('format') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Date Format</label>
                    <input type="text" class="form-control" wire:model.defer="date_format" placeholder="d/m/Y">
                    @error('date_format') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Paper</label>
                    <select class="form-select" wire:model.defer="paper">
                        <option value="A4">A4</option>
                        <option value="thermal">Thermal</option>
                    </select>
                    @error('paper') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Footer Note</label>
                    <textarea class="form-control" rows="3" wire:model.defer="footer_note"></textarea>
                    @error('footer_note') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Terms & Conditions</label>
                    <textarea class="form-control" rows="3" wire:model.defer="terms"></textarea>
                    @error('terms') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" wire:model.defer="show_logo" id="showLogo">
                        <label class="form-check-label" for="showLogo">Show Company Logo</label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" wire:model.defer="show_tax_number" id="showTax">
                        <label class="form-check-label" for="showTax">Show Tax Number</label>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer d-flex justify-content-end">
            <button class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove>Save</span>
                <span wire:loading>Saving...</span>
            </button>
        </div>
    </div>
</div>
