<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">{{ $productId ? 'Edit Product' : 'Add Product' }}</h5>
            </div>

            <div class="card-body">
                @if (session()->has('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                <form wire:submit.prevent="save" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-6">

                            <div class="mb-3">
                                <label class="form-label">Product Name*</label>
                                <input type="text" class="form-control" wire:model.defer="name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Product Image</label><br>
                                @if ($product_image && !$new_product_image)
                                    <img src="{{ asset('storage/' . $product_image) }}" width="100"><br>
                                @endif
                                <input type="file" wire:model="new_product_image">
                                <div wire:loading wire:target="new_product_image">Uploading...</div>
                                @error('new_product_image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" wire:model.defer="category_id">
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Brand</label>
                                <select class="form-select" wire:model.defer="brand_id">
                                    <option value="">-- Select Brand --</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Order Tax</label>
                                <input type="number" step="0.01" class="form-control" wire:model.defer="order_tax">
                                @error('order_tax')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tax Type</label>
                                <select class="form-select" wire:model.defer="tax_type">
                                    <option value="percent">Percent</option>
                                    <option value="fixed">Fixed</option>
                                </select>
                                @error('tax_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="col-lg-6">

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="3" wire:model.defer="description"></textarea>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Product Price</label>
                                <input type="number" step="0.01" class="form-control"
                                    wire:model.defer="product_price">
                                @error('product_price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Warranty Period</label>
                                <input type="text" class="form-control" wire:model.defer="warranty_period">
                                @error('warranty_period')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Guarantee</label>
                                <input type="text" class="form-control" wire:model.defer="guarantee">
                                @error('guarantee')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Guarantee Period</label>
                                <input type="text" class="form-control" wire:model.defer="guarantee_period">
                                @error('guarantee_period')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" wire:model.defer="has_imei"
                                    id="has_imei">
                                <label class="form-check-label" for="has_imei">Product Has IMEI / Serial Number</label>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" wire:model.defer="not_for_selling"
                                    id="not_for_selling">
                                <label class="form-check-label" for="not_for_selling">This Product Not For
                                    Selling</label>
                            </div>

                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit"
                            class="btn btn-success me-2">{{ $productId ? 'Update' : 'Create' }}</button>
                        <a href="{{ route('product.list') }}" class="btn btn-secondary">Cancel</a>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
