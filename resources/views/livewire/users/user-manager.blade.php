<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Users List</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Users</a></li>
                <li class="breadcrumb-item active">Users List</li>
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
            <h5 class="card-title mb-0">Users</h5>

            <div>
                <button class="btn btn-primary me-2" wire:click="openCreate">
                    <i class="mdi mdi-plus"></i> Add
                </button>

                <button class="btn btn-danger" wire:click="deleteSelected"
                    @if (count($selectedUsers) == 0) disabled @endif>
                    <i class="mdi mdi-trash-can"></i> Delete
                </button>
            </div>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Search name/email..."
                        wire:model.live="search">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-traffic mb-0">
                    <thead>
                        <tr>
                            <th><input type="checkbox" wire:model.live="selectAll"></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $u)
                            <tr wire:key="user-{{ $u->id }}">
                                <td>
                                    <input type="checkbox" wire:model="selectedUsers" value="{{ $u->id }}" class="me-2" />
                                </td>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->getRoleNames()->first() ?? '-' }}</td>
                                <td>
                                    <a href="javascript:void(0);" wire:click="edit({{ $u->id }})">
                                        <i class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                    </a>

                                    <button wire:click="delete({{ $u->id }})"
                                        onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEdit ? 'Edit User' : 'Add User' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="resetForm"></button>
                </div>

                <div class="modal-body">
                    <form wire:submit.prevent="save">

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input wire:model.defer="name" type="text" class="form-control">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input wire:model.defer="email" type="email" class="form-control">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password {{ $isEdit ? '(optional)' : '' }}</label>
                            <input wire:model.defer="password" type="password" class="form-control">
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select wire:model.defer="role" class="form-select">
                                <option value="">Select role</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                            @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
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
        window.addEventListener('show-user-modal', function () {
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        });

        window.addEventListener('hide-user-modal', function () {
            const el = document.getElementById('userModal');
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
