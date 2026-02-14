<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedUsers = [];
    public $selectAll = false;

    public $isEdit = false;
    public $user_id;

    public $name;
    public $email;
    public $password;
    public $role;

    public $roles = [];

    public function mount(): void
    {
        abort_unless(auth()->user()->can('users.view'), 403);
        $this->roles = Role::query()->orderBy('name')->pluck('name')->toArray();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->selectAll = false;
        $this->selectedUsers = [];
    }

    protected function usersQuery()
    {
        return User::query()
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest();
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedUsers = $this->usersQuery()
                ->paginate(10)
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    protected function rules(): array
    {
        $emailRule = Rule::unique('users', 'email');
        if ($this->user_id) {
            $emailRule = $emailRule->ignore($this->user_id);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', $emailRule],
            'password' => [$this->isEdit ? 'nullable' : 'required', 'string', 'min:6'],
            'role' => ['required', 'string', Rule::in($this->roles)],
        ];
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('users.create'), 403);

        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('show-user-modal');
    }

    public function edit($id): void
    {
        abort_unless(auth()->user()->can('users.update'), 403);

        $user = User::findOrFail($id);

        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = null;
        $this->role = $user->getRoleNames()->first();

        $this->isEdit = true;
        $this->dispatch('show-user-modal');
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can($this->isEdit ? 'users.update' : 'users.create'), 403);

        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->user_id], $data);

        $user->syncRoles([$this->role]);

        $this->dispatch('hide-user-modal');
        $this->resetForm();

        session()->flash('message', $this->isEdit ? 'User updated successfully.' : 'User created successfully.');
    }

    public function deleteSelected(): void
    {
        abort_unless(auth()->user()->can('users.delete'), 403);

        if (!empty($this->selectedUsers)) {
            User::whereIn('id', $this->selectedUsers)->delete();

            $this->selectedUsers = [];
            $this->selectAll = false;
            $this->resetPage();

            session()->flash('message', 'Selected users deleted successfully.');
        }
    }

    public function delete($id): void
    {
        abort_unless(auth()->user()->can('users.delete'), 403);

        $user = User::find($id);
        if ($user) {
            $user->delete();
            session()->flash('message', 'User deleted successfully.');
        }

        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->reset(['isEdit', 'user_id', 'name', 'email', 'password', 'role']);
    }

    public function render()
    {
        $users = $this->usersQuery()->paginate(10);
        return view('livewire.users.user-manager', compact('users'));
    }
}
