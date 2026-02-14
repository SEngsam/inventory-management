<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RoleManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public $isEdit = false;
    public $role_id;
    public $name;

    public $selectedPermissions = [];

    public function mount(): void
    {
        abort_unless(auth()->user()->can('users.roles.manage'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    protected function rolesQuery()
    {
        return Role::query()
            ->where('guard_name', 'web')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name');
    }

    public function permissionsGrouped(): array
    {
        $perms = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        $groups = [];

        foreach ($perms as $p) {
            $key = str_contains($p, '.')
                ? Str::before($p, '.')
                : 'misc';

            $groups[$key][] = $p;
        }

        ksort($groups);
        return $groups;
    }

    protected function rules(): array
    {
        $nameRule = Rule::unique('roles', 'name');
        if ($this->role_id) {
            $nameRule = $nameRule->ignore($this->role_id);
        }

        return [
            'name' => ['required', 'string', 'max:255', $nameRule],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('users.roles.manage'), 403);

        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('show-role-modal');
    }

    public function edit($id): void
    {
        abort_unless(auth()->user()->can('users.roles.manage'), 403);

        $role = Role::findOrFail($id);

        $this->role_id = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions()->pluck('name')->toArray();
        $this->isEdit = true;

        $this->dispatch('show-role-modal');
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can('users.roles.manage'), 403);

        $this->validate();

        $role = Role::updateOrCreate(
            ['id' => $this->role_id],
            ['name' => $this->name, 'guard_name' => 'web']
        );

        $role->syncPermissions($this->selectedPermissions);

        $this->dispatch('hide-role-modal');
        $this->resetForm();

        session()->flash('message', $this->isEdit ? 'Role updated successfully.' : 'Role created successfully.');
    }

    public function delete($id): void
    {
        abort_unless(auth()->user()->can('users.roles.manage'), 403);

        $role = Role::find($id);
        if ($role) {
            if (strtolower($role->name) === 'admin') {
                session()->flash('message', 'Admin role cannot be deleted.');
                return;
            }

            $role->delete();
            session()->flash('message', 'Role deleted successfully.');
        }

        $this->resetPage();
    }

    public function toggleGroup($groupKey): void
    {
        $groups = $this->permissionsGrouped();
        $groupPerms = $groups[$groupKey] ?? [];

        $current = collect($this->selectedPermissions);

        $allSelected = collect($groupPerms)->every(fn ($p) => $current->contains($p));

        if ($allSelected) {
            $this->selectedPermissions = $current->reject(fn ($p) => in_array($p, $groupPerms, true))
                ->values()
                ->toArray();
        } else {
            $this->selectedPermissions = $current->merge($groupPerms)
                ->unique()
                ->values()
                ->toArray();
        }
    }

    public function resetForm(): void
    {
        $this->reset(['isEdit', 'role_id', 'name', 'selectedPermissions']);
    }

    public function render()
    {
        $roles = $this->rolesQuery()->paginate(10);
        $permissionsGrouped = $this->permissionsGrouped();

        return view('livewire.users.role-manager', compact('roles', 'permissionsGrouped'));
    }
}
