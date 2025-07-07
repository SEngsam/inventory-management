@extends('layouts.vertical', ['title' => "Product"])

@section('content')
<div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">   Product
</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">Products</a></li>
            <li class="breadcrumb-item active">Product List</li>
        </ol>
    </div>
</div>
    <livewire:product-list  />
@endsection
