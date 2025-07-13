<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleManager extends Component
{
    public $roles, $permissions;
    public $selectedRoleId;
    public $selectedPermissions = [];

    public function mount()
    {
        $this->roles = Role::all();
        $this->permissions = Permission::all();
    }

    public function edit($id)
    {
        $this->selectedRoleId = $id;
        $role = Role::find($id);
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
    }

    public function updateRolePermissions()
    {
        $role = Role::find($this->selectedRoleId);
        $role->syncPermissions($this->selectedPermissions);
        session()->flash('message', 'Permissions updated!');
    }

    public function render()
    {
        return view('livewire.users.role-manager');
    }
}
