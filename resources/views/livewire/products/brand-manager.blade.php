<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Category</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Products</a></li>
                <li class="breadcrumb-item active">Category</li>
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
                    <h5 class="card-title mb-0">Brands List</h5>
                    <div>
                        <button class="btn btn-primary me-2" onclick="showBrandModal()">
                            <i class="mdi mdi-plus"></i> Add
                        </button>
                        <button class="btn btn-danger" wire:click="deleteSelected"
                            @if (count($selectedBrands) == 0) disabled @endif>
                            <i class="mdi mdi-trash-can"></i> Delete
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-traffic mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($brands as $brand)
                                    <tr>
                                        <td>
                                            <input type="checkbox" wire:model.live="selectedBrands"
                                                value="{{ $brand->id }}" class="me-2" />
                                        </td>
                                        <td>
                                            @if ($brand->image)
                                                <img src="{{ asset('storage/' . $brand->image) }}" width="40"
                                                    height="40" class="rounded-circle" />
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>
                                        <td>{{ $brand->name }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($brand->description, 50) }}</td>
                                        <td>
                                            <a href="javascript:void(0);" wire:click="edit({{ $brand->id }})">
                                                <i
                                                    class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No brands found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="brandModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form wire:submit.prevent="save">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ $isEdit ? 'Edit Brand' : 'Add Brand' }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    wire:click="resetForm"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Name</label>
                                    <input type="text" wire:model.defer="name" class="form-control" />
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label>Description</label>
                                    <textarea wire:model.defer="description" class="form-control" rows="3"></textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label>Image</label>
                                    <input type="file" wire:model="newImage" class="form-control" />
                                    @error('newImage')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                    @if ($newImage)
                                        <div class="mt-2">
                                            <span>Preview:</span><br>
                                            <img src="{{ $newImage->temporaryUrl() }}" class="rounded" width="100">
                                        </div>
                                    @elseif ($image)
                                        <div class="mt-2">
                                            <span>Current Image:</span><br>
                                            <img src="{{ asset('storage/' . $image) }}" class="rounded" width="100">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" wire:click="resetForm" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    {{ $isEdit ? 'Update' : 'Save' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@push('script')
    <script>
        function showBrandModal() {
            const modal = new bootstrap.Modal(document.getElementById('brandModal'));
            modal.show();
        }

        window.addEventListener('show-brand-modal', () => {
            const modal = new bootstrap.Modal(document.getElementById('brandModal'));
            modal.show();
        });

        window.addEventListener('hide-brand-modal', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('brandModal'));
            modal.hide();
        });

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
