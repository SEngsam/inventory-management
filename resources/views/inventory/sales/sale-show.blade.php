@extends('layouts.vertical', ['title' => "List Sales"])

@section('content')
<div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">Show Sale</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);"> Sales</a></li>
            <li class="breadcrumb-item active">Show Sale</li>
        </ol>
    </div>
</div>
    <livewire:sales.sale-show  :sale="$sale"/>
@endsection
