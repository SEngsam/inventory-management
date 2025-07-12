<!-- resources/views/livewire/admin/user-manager.blade.php -->
<div>
    <div class="row">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Users List</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="javascript: void(0);"> Users</a>
                    </li>
                    <li class="breadcrumb-item active">Users List</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Suppliers</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-traffic mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th class="text-end">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="text-end">
                                    <select wire:change="changeRole({{ $user->id }}, $event.target.value)">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                @if ($user->hasRole($role->name)) selected @endif>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
