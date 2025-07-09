@extends('layouts.vertical', ['title' => "Purchases"])

@section('content')
<div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">   Add Purchase
</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);"> Purchases</a></li>
            <li class="breadcrumb-item active"> Add Purchase</li>
        </ol>
    </div>
</div>

@if (isset($purchase))

    <livewire:purchases.purchase-form   :purchase="$purchase"/>
@else
    <livewire:purchases.purchase-form  />

@endif
@endsection
