<?php

namespace App\Livewire\Settings;

use App\Models\Currency;
use Livewire\Component;

class ManageCurrencies extends Component
{
    public $currencies;
    public $currency_id;
    public $name, $symbol, $rate;
    public $is_active = true;
    public $selectedCurrency = [], $selectAll = false;

    protected $rules = [
        'name' => 'required|string',
        'symbol' => 'required|string',
        'rate' => 'required|numeric|min:0.0001',
    ];

    public function render()
    {
        $this->currencies = Currency::orderByDesc('is_default')->get();
        return view('livewire.settings.manage-currencies');
    }

    public function store()
    {
        $this->validate();

        Currency::create([
            'name' => $this->name,
            'symbol' => $this->symbol,
            'rate' => $this->rate,
            'is_active' => $this->is_active,
        ]);

        $this->resetInput();
        session()->flash('message', 'Currency added successfully!');
    }

    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        $this->currency_id = $id;
        $this->name = $currency->name;
        $this->symbol = $currency->symbol;
        $this->rate = $currency->rate;
        $this->is_active = $currency->is_active;
    }

    public function update()
    {
        $this->validate();

        Currency::findOrFail($this->currency_id)->update([
            'name' => $this->name,
            'symbol' => $this->symbol,
            'rate' => $this->rate,
            'is_active' => $this->is_active,
        ]);

        $this->resetInput();
        session()->flash('message', 'Currency updated successfully!');
    }

    public function delete($id)
    {
        Currency::findOrFail($id)->delete();
        $this->resetInput();
        session()->flash('message', 'Currency deleted');
    }

    public function makeDefault($id)
    {
        Currency::where('is_default', true)->update(['is_default' => false]);
        Currency::findOrFail($id)->update(['is_default' => true]);
        session()->flash('message', 'Default currency set!');
    }

    public function resetInput()
    {
        $this->currency_id = null;
        $this->name = '';
        $this->symbol = '';
        $this->rate = '';
        $this->is_active = true;
    }
}


