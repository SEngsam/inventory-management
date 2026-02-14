<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Roles</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Users</a></li>
                <li class="breadcrumb-item active">Roles</li>
            </ol>
        </div>
    </div>

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
            <h5 class="card-title mb-0">Roles</h5>

            <div>
                <button class="btn btn-primary" wire:click="openCreate">
                    <i class="mdi mdi-plus"></i> Add
                </button>
            </div>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Search role..."
                        wire:model.live="search">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-traffic mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Permissions</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr wire:key="role-{{ $role->id }}">
                                <td>{{ $role->name }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $role->permissions()->count() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" wire:click="edit({{ $role->id }})">
                                        <i class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                    </a>

                                    <button wire:click="delete({{ $role->id }})"
                                        onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEdit ? 'Edit Role' : 'Add Role' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="resetForm"></button>
                </div>

                <div class="modal-body">
                    <form wire:submit.prevent="save">

                        <div class="mb-3">
                            <label class="form-label">Role name</label>
                            <input wire:model.defer="name" type="text" class="form-control">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0">Permissions</label>
                            <small class="text-muted">Selected: {{ count($selectedPermissions) }}</small>
                        </div>

                        <div class="row g-3">
                            @foreach ($permissionsGrouped as $groupKey => $perms)
                                <div class="col-12 col-md-6">
                                    <div class="border rounded p-2">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="fw-semibold text-capitalize">{{ $groupKey }}</div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                wire:click="toggleGroup('{{ $groupKey }}')">
                                                Toggle
                                            </button>
                                        </div>

                                        <div class="d-flex flex-column gap-1" style="max-height: 220px; overflow:auto;">
                                            @foreach ($perms as $perm)
                                                <label class="d-flex align-items-center gap-2">
                                                    <input type="checkbox"
                                                        wire:model="selectedPermissions"
                                                        value="{{ $perm }}">
                                                    <span class="small">{{ $perm }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                wire:click="resetForm">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                {{ $isEdit ? 'Update' : 'Save' }}
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

</div>

@push('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        window.addEventListener('show-role-modal', function () {
            const modal = new bootstrap.Modal(document.getElementById('roleModal'));
            modal.show();
        });

        window.addEventListener('hide-role-modal', function () {
            const el = document.getElementById('roleModal');
            const modal = bootstrap.Modal.getInstance(el);
            if (modal) modal.hide();
        });

        @if (session()->has('message'))
            const toastEl = document.getElementById('successToast');
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        @endif
    });
</script>
@endpush
