<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Storage;

class ManageCompanyBranding extends Component
{
    use WithFileUploads;

    public $company_name;
    public $legal_name;
    public $tax_number;
    public $email;
    public $phone;
    public $website;
    public $address;

    public $logo;           
    public $current_logo;   

    public function mount(SettingsService $settings): void
    {
        $this->company_name = $settings->get('company.name', 'Inventory Management System');
        $this->legal_name   = $settings->get('company.legal_name');
        $this->tax_number   = $settings->get('company.tax_number');
        $this->email        = $settings->get('company.email');
        $this->phone        = $settings->get('company.phone');
        $this->website      = $settings->get('company.website');
        $this->address      = $settings->get('company.address');
        $this->current_logo = $settings->get('company.logo');
    }

    protected function rules(): array
    {
        return [
            'company_name' => ['required','string','max:150'],
            'legal_name'   => ['nullable','string','max:150'],
            'tax_number'   => ['nullable','string','max:100'],
            'email'        => ['nullable','email','max:150'],
            'phone'        => ['nullable','string','max:50'],
            'website'      => ['nullable','url','max:150'],
            'address'      => ['nullable','string','max:255'],
            'logo'         => ['nullable','image','mimes:png,jpg,jpeg,webp','max:2048'],
        ];
    }

    public function save(SettingsService $settings): void
    {
        $this->validate();

         $settings->setMany([
            'company.name'       => $this->company_name,
            'company.legal_name' => $this->legal_name,
            'company.tax_number' => $this->tax_number,
            'company.email'      => $this->email,
            'company.phone'      => $this->phone,
            'company.website'    => $this->website,
            'company.address'    => $this->address,
        ]);

       
        if ($this->logo) {
             if ($this->current_logo && Storage::disk('public')->exists($this->current_logo)) {
                Storage::disk('public')->delete($this->current_logo);
            }

             $ext  = $this->logo->getClientOriginalExtension();
            $path = "branding/company-logo.{$ext}";

            Storage::disk('public')->putFileAs('branding', $this->logo, "company-logo.{$ext}");

            $settings->set('company.logo', $path);
            $this->current_logo = $path;
 
            $this->reset('logo');
        }

        session()->flash('message', 'Company & branding saved.');
        $this->dispatch('saved');  
    }

    public function removeLogo(SettingsService $settings): void
    {
        if ($this->current_logo && Storage::disk('public')->exists($this->current_logo)) {
            Storage::disk('public')->delete($this->current_logo);
        }

        $settings->forget('company.logo');
        $this->current_logo = null;

        session()->flash('message', 'Logo removed.');
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.settings.manage-company-branding');
    }
}
