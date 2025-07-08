
@extends('layouts.vertical', ['title' => 'Brand'])

@section('content')
<div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">{{ $customer ? 'Edit Customer' : 'Add Customer' }}</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">customers</a></li>
            <li class="breadcrumb-item active">{{ $customer ? 'Edit Customer' : 'Add Customer' }}</li>
        </ol>
    </div>
</div>
@if ($customer)

<livewire:customers.customer-form :customer="$customer" />
@else
<livewire:customers.customer-form />
@endif



@endsection
