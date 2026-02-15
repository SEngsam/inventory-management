<?php

namespace App\Livewire;

use Livewire\Component;

abstract class BaseComponent extends Component
{
    protected string $module = '';
    protected ?string $viewPermission = null;

    protected function perm(string $action): string
    {
        return "{$this->module}.{$action}";
    }

    protected function authorizePermission(string $permission): void
    {
    }

    protected function authorizeView(): void
    {
        $permission = $this->viewPermission ?? $this->perm('view');
        $this->authorizePermission($permission);
    }

    protected function authorizeAction(string $action): void
    {
        $this->authorizePermission($this->perm($action));
    }
}