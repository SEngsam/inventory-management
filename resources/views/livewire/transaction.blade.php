<div class="p-4">
    @if ($message)
      <div class="mb-4 text-green-700">{{ $message }}</div>
    @endif

    <div class="mb-2">
        <label>المنتج</label>
        <select wire:model="productId" class="border p-1">
          <option value="">اختر منتج</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}">{{ $p->name }} (متوفر: {{ $p->quantity }})</option>
          @endforeach
        </select>
    </div>

    <div class="mb-2">
        <label>نوع العملية</label>
        <select wire:model="type" class="border p-1">
          <option value="purchase">شراء</option>
          <option value="sale">بيع</option>
        </select>
    </div>

    <div class="mb-2">
        <label>الكمية</label>
        <input type="number" wire:model="quantity" min="1" class="border p-1" />
    </div>

    <button wire:click="submit" class="bg-blue-600 text-white px-4 py-2">حفظ العملية</button>
</div>
