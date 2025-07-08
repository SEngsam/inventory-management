@extends('layouts.vertical', ['title' => isset($return) ? 'Edit Sale Return' : 'Add Sale Return'])

@section('content')
<div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">
            {{ isset($return) ? 'Edit Sale Return' : 'Add Sale Return' }}
        </h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="{{ route('sale-returns.index') }}">Sale Returns</a></li>
            <li class="breadcrumb-item active">{{ isset($return) ? 'Edit Sale Return' : 'Add Sale Return' }}</li>
        </ol>
    </div>
</div>

@if (isset($return))
    <livewire:sales.return-form :return="$return" />
@else
    <livewire:sales.return-form />
@endif
@endsection
