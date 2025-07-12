<?php
namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserManager extends Component
{
    public $users;

    public function mount()
    {
        $this->users = User::with('roles')->get();
    }

    public function changeRole($userId, $roleName)
    {
        $user = User::findOrFail($userId);
        $user->syncRoles([$roleName]);
        $this->users = User::with('roles')->get(); // refresh
    }

    public function render()
    {
        return view('livewire.users.user-manager', [
            'roles' => Role::all()
        ]);
    }
}
