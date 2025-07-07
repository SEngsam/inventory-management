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
                <h5 class="card-title mb-0">Categories List</h5>
                <div>
                    <button class="btn btn-primary me-2" onclick="showCategoryModal()">
                        <i class="mdi mdi-plus"></i> Add
                    </button>
                    <button class="btn btn-danger" wire:click="deleteSelected"
                        @if (count($selectedCategories) == 0) disabled @endif>
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
                                <th>Name</th>
                                <th>Code</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td>
                                        <input type="checkbox" wire:model.live="selectedCategories"
                                            value="{{ $category->id }}" class="me-2" />
                                    </td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->code }}</td>
                                    <td>
                                        <a href="javascript:void(0);" wire:click="edit({{ $category->id }})">
                                            <i class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit.prevent="save">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $isEdit ? 'Edit Category' : 'Add Category' }}</h5>
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
                                <label>Code</label>
                                <input type="text" wire:model.defer="code" class="form-control" />
                                @error('code')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="resetForm" class="btn btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Save' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Script -->
<script>
    function showCategoryModal() {
        const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
        modal.show();
    }

    window.addEventListener('show-category-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
        modal.show();
    });

    window.addEventListener('hide-category-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('categoryModal'));
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
