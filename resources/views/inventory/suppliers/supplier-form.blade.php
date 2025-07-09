@extends('layouts.vertical', ['title' => isset($supplier)?'Edit Supplier':'Add Supplier'])

@section('content')
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Supplier
            </h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);"> Suppliers</a></li>
                <li class="breadcrumb-item active"> Add Supplier</li>
            </ol>
        </div>
    </div>
 @if (isset($supplier))
    <livewire:suppliers.supplier-form :supplier="$supplier"/>
@else
    <livewire:suppliers.supplier-form  />

@endif
@endsection
