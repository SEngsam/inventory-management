<?php

namespace App\Livewire;

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
        $this->loadUnits();
    }

    public function loadUnits()
    {
        $this->units = Unit::all();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'short_code' => 'nullable|string|max:10|unique:units,short_code',
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


        $this->reset(['name', 'short_code', 'base_unit_id', 'operator', 'operator_value']);
        $this->loadUnits();
        $this->dispatch('hide-unit-modal');
        $message = $this->isEdit ? 'Unit updated successfully.' : 'Unit created successfully.';
        session()->flash('message', $message);
    }

    public function deleteSelected()
    {
        if (count($this->selectedUnits) > 0) {
            Unit::whereIn('id', $this->selectedUnits)->delete();

            // أفرغ المحددات
            $this->selectedUnits = [];

            // إعادة تحميل البيانات إذا لازم (مثلاً إذا خزنت بالخاصية units)
            $this->loadUnits();

            session()->flash('message', 'Selected units deleted successfully.');
        }
    }
    public function edit($id)
    {
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
        return view('livewire.unit-manager', [
            'units' => $this->units,
        ]);
    }
}
