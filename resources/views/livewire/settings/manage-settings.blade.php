<div class="container mt-4">
  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <form wire:submit.prevent="save">
    <div class="mb-3">
      <label>اسم النظام:</label>
      <input type="text" class="form-control" wire:model.lazy="system_name">
    </div>

    <div class="mb-3">
      <label>لغة العرض:</label>
      <select wire:model="system_language" class="form-select">
        <option value="ar">العربية</option>
        <option value="en">English</option>
      </select>
    </div>

    <div class="mb-3">
      <label>رابط الشعار:</label>
      <input type="text" class="form-control" wire:model.lazy="logo_url">
    </div>

    <button class="btn btn-primary">حفظ الإعدادات</button>
  </form>
</div>
