<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;

class ManageSettings extends Component
{
    public $system_name, $system_language, $logo_url;

    public function mount()
    {
        $this->system_name = Setting::get('system_name', config('app.name'));
        $this->system_language = Setting::get('system_language', 'ar');
        $this->logo_url = Setting::get('logo_url', '');
    }

    public function save()
    {
        Setting::set('system_name', $this->system_name);
        Setting::set('system_language', $this->system_language);
        Setting::set('logo_url', $this->logo_url);

        session()->flash('message', __('messages.settings.saved'));
    }

    public function render()
    {
        return view('livewire.settings.manage-settings');
    }
}
