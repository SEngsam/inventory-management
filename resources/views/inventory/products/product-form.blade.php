@extends('layouts.vertical', ['title' => !isset($id) ? "Add Product":"Edit Product"])

@section('content')
<div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">    {{ !isset($id) ? 'Add Product' : 'Edit Product' }}
</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">Products</a></li>
            <li class="breadcrumb-item active">{{ !isset($id)? "Add Product":"Edit Product"}}</li>
        </ol>
    </div>
</div>

@if (isset($id))
    <livewire:products.product-form :productId="$id"/>
@else
    <livewire:products.product-form />
@endif
@endsection
