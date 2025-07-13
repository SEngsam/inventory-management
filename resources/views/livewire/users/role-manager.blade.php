<!-- resources/views/livewire/admin/role-manager.blade.php -->
<div>
    <div class="row">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0"> User Roles</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="javascript: void(0);"> Users</a>
                    </li>
                    <li class="breadcrumb-item active">User Roles</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Roles</h5>
        </div>

        <div class="card-body">
            @foreach ($roles as $role)
                <button wire:click="edit({{ $role->id }})">{{ ucfirst($role->name) }}</button>
            @endforeach

            @if ($selectedRoleId)
                <h5>Edit Permissions</h5>
                @foreach ($permissions as $permission)
                    <label>
                        <input type="checkbox" value="{{ $permission->name }}" wire:model="selectedPermissions">
                        {{ $permission->name }}
                    </label>
                @endforeach

                <button wire:click="updateRolePermissions">Update</button>
                @if (session()->has('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif
            @endif
        </div>
    </div>
</div>
