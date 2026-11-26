<div class="container mt-4">
  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <form wire:submit.prevent="save">
    <div class="mb-3">
      <label>{{ __('System name') }}</label>
      <input type="text" class="form-control" wire:model.lazy="system_name">
    </div>

    <div class="mb-3">
      <label>{{ __('Display language') }}</label>
      <select wire:model="system_language" class="form-select">
        <option value="ar">العربية</option>
        <option value="en">English</option>
      </select>
    </div>

    <div class="mb-3">
      <label>{{ __('Logo URL') }}</label>
      <input type="text" class="form-control" wire:model.lazy="logo_url">
    </div>

    <button class="btn btn-primary">{{ __('Save settings') }}</button>
  </form>
</div>
