<?php

namespace App\Livewire\Products;

use App\Models\Unit;
use Livewire\Component;

class UnitManager extends Component
{
    public $units;
    public $unit_id;
    public $isEdit = false;
    public $name;
    public $short_code;
    public $base_unit_id;
    public $operator;
    public $operator_value;
    public $selectedUnits = [];

    public function mount()
    {
        abort_unless(auth()->user()->can('products.units.manage'), 403);
        $this->loadUnits();
    }

    public function loadUnits()
    {
        $this->units = Unit::all();
    }

    public function save()
    {
        abort_unless(auth()->user()->can('products.units.manage'), 403);

        $isUpdating = (bool) $this->unit_id;

        $this->validate([
            'name' => 'required|string|max:255',
            'short_code' => 'nullable|string|max:10|unique:units,short_code,' . $this->unit_id,
            'base_unit_id' => 'nullable|exists:units,id',
        ]);

        if ($this->base_unit_id) {
            $this->validate([
                'operator' => 'required|in:*,/',
                'operator_value' => 'required|numeric|min:0.0001',
            ]);
        } else {
            $this->operator = null;
            $this->operator_value = null;
        }

        Unit::updateOrCreate(
            ['id' => $this->unit_id],
            [
                'name' => $this->name,
                'short_code' => $this->short_code,
                'base_unit_id' => $this->base_unit_id,
                'operator' => $this->base_unit_id ? $this->operator : null,
                'operator_value' => $this->base_unit_id ? $this->operator_value : null,
            ]
        );

        $this->reset(['unit_id', 'name', 'short_code', 'base_unit_id', 'operator', 'operator_value', 'isEdit']);
        $this->loadUnits();
        $this->dispatch('hide-unit-modal');

        session()->flash(
            'message',
            $isUpdating
                ? 'Unit updated successfully.'
                : 'Unit created successfully.'
        );
    }

    public function deleteSelected()
    {
        abort_unless(auth()->user()->can('products.units.manage'), 403);

        if (count($this->selectedUnits) > 0) {
            Unit::whereIn('id', $this->selectedUnits)->delete();

            $this->selectedUnits = [];
            $this->loadUnits();

            session()->flash('message', 'Selected units deleted successfully.');
        }
    }

    public function edit($id)
    {
        abort_unless(auth()->user()->can('products.units.manage'), 403);

        $unit = Unit::findOrFail($id);

        $this->unit_id = $unit->id;
        $this->name = $unit->name;
        $this->short_code = $unit->short_code;
        $this->base_unit_id = $unit->base_unit_id;
        $this->operator = $unit->operator;
        $this->operator_value = $unit->operator_value;
        $this->isEdit = true;

        $this->dispatch('show-unit-modal');
    }

    public function render()
    {
        return view('livewire.products.unit-manager', [
            'units' => $this->units,
        ]);
    }
}