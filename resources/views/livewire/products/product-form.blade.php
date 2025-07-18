<div>
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">{{ $productId ? 'Edit Product' : 'Add Product' }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Products</a></li>
                <li class="breadcrumb-item active">{{ $productId ? 'Edit Product' : 'Add Product' }} </li>
            </ol>
        </div>
    </div>

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
                            <!-- Product Name -->
                            <div class="mb-3">
                                <label class="form-label">Product Name*</label>
                                <input type="text" class="form-control" wire:model.defer="name">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- SKU -->
                            <div class="mb-3">
                                <label class="form-label">SKU*</label>
                                <input type="text" class="form-control" wire:model.defer="sku">
                                @error('sku') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Product Code -->
                            <div class="mb-3">
                                <label class="form-label">Product Code</label>
                                <input type="text" class="form-control" wire:model.defer="code">
                                @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Product Image -->
                            <div class="mb-3">
                                <label class="form-label">Product Image</label><br>
                                @if ($image && !$new_product_image)
                                    <img src="{{ asset('storage/' . $image) }}" width="100" class="mb-2"><br>
                                @endif
                                <input type="file" wire:model="new_product_image">
                                <div wire:loading wire:target="new_product_image">Uploading...</div>
                                @error('new_product_image') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Category -->
                            <div class="mb-3">
                                <label class="form-label">Category*</label>
                                <select class="form-select" wire:model.defer="category_id">
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Brand -->
                            <div class="mb-3">
                                <label class="form-label">Brand</label>
                                <select class="form-select" wire:model.defer="brand_id">
                                    <option value="">-- Select Brand --</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Unit -->
                            <div class="mb-3">
                                <label class="form-label">Unit</label>
                                <select class="form-select" wire:model.defer="unit_id">
                                    <option value="">-- Select Unit --</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                                @error('unit_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Stock Quantity -->
                            <div class="mb-3">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" class="form-control" wire:model.defer="stock_quantity">
                                @error('stock_quantity') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <!-- Order Tax -->
                            <div class="mb-3">
                                <label class="form-label">Order Tax</label>
                                <input type="number" step="0.01" class="form-control" wire:model.defer="order_tax">
                                @error('order_tax') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tax Type -->
                            <div class="mb-3">
                                <label class="form-label">Tax Type</label>
                                <select class="form-select" wire:model.defer="tax_type">
                                    <option value="percent">Percent</option>
                                    <option value="fixed">Fixed</option>
                                </select>
                                @error('tax_type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Price -->
                            <div class="mb-3">
                                <label class="form-label">Price*</label>
                                <input type="number" step="0.01" class="form-control" wire:model.defer="price">
                                @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Warranty Period -->
                            <div class="mb-3">
                                <label class="form-label">Warranty Period</label>
                                <input type="text" class="form-control" wire:model.defer="warranty_period">
                                @error('warranty_period') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Guarantee Checkbox -->
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" wire:model.defer="guarantee" id="guarantee">
                                <label class="form-check-label" for="guarantee">Has Guarantee</label>
                            </div>

                            <!-- Guarantee Period -->
                            <div class="mb-3">
                                <label class="form-label">Guarantee Period</label>
                                <input type="text" class="form-control" wire:model.defer="guarantee_period">
                                @error('guarantee_period') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Product Type -->
                            <div class="mb-3">
                                <label class="form-label">Product Type</label>
                                <select class="form-select" wire:model.defer="type">
                                    <option value="standard">Standard</option>
                                    <option value="service">Service</option>
                                </select>
                                @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="3" wire:model.defer="description"></textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Has IMEI Checkbox -->
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" wire:model.defer="has_imei" id="has_imei">
                                <label class="form-check-label" for="has_imei">Has IMEI / Serial Number</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success me-2">{{ $productId ? 'Update' : 'Create' }}</button>
                        <a href="{{ route('products.list') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

</div>
