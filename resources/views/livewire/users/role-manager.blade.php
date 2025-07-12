<!-- resources/views/livewire/admin/role-manager.blade.php -->
<div>
    <h4>Role Management</h4>

    @foreach($roles as $role)
        <button wire:click="edit({{ $role->id }})">{{ ucfirst($role->name) }}</button>
    @endforeach

    @if($selectedRoleId)
        <h5>Edit Permissions</h5>
        @foreach($permissions as $permission)
            <label>
                <input type="checkbox"
                       value="{{ $permission->name }}"
                       wire:model="selectedPermissions">
                {{ $permission->name }}
            </label>
        @endforeach

        <button wire:click="updateRolePermissions">Update</button>
        @if(session()->has('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
    @endif
</div>
