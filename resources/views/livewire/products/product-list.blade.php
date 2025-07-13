<div>

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Products</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Products</a></li>
                <li class="breadcrumb-item active">Products List</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            @if (session()->has('message'))
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
                    <div class="toast show align-items-center text-white bg-success border-0 shadow" role="alert"
                        id="successToast" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                {{ session('message') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                                aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Products List</h5>
                    <div>
                        <a href="{{ route('products.create') }}" class="btn btn-primary me-2">
                            <i class="mdi mdi-plus"></i> Add
                        </a>

                        <button class="btn btn-danger" wire:click="deleteSelected"
                            {{ empty($selectedProducts) ? 'disabled' : '' }}>
                            <i class="mdi mdi-trash-can"></i> Delete
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-traffic mb-0">
                            <thead>
                                <tr>
                                    <th>
                                        <input class="form-check-input" type="checkbox" wire:model="selectAll" />
                                    </th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr wire:key="product-{{ $product->id }}">
                                        <td>
                                            <input class="form-check-input" type="checkbox" wire:model="selectedProducts"
                                                value="{{ $product->id }}" />
                                        </td>
                                        <td>
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    width="40" height="40" class="rounded-circle" />
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->brand->name ?? '-' }}</td>
                                        <td>{{ $product->category->name ?? '-' }}</td>
                                        <td>{{ number_format($product->price ?? $product->product_price, 2) }}</td>
                                        <td>
                                            <a href="{{ route('products.edit', $product->id) }}">
                                                <i class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination Links --}}
                        <div class="mt-3">
                            {{ $products->links() }}
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

@push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session()->has('message'))
                const toastEl = document.getElementById('successToast');
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 3000
                });
                toast.show();
            @endif
        });
    </script>
@endpush
