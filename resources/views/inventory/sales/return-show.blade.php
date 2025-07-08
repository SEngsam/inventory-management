@extends('layouts.vertical', ['title' => 'Sale Return Details'])

@section('content')
<div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">Sale Return Details</h4>
    </div>

    <div class="text-end">
        <a href="{{ route('sale-returns.index') }}" class="btn btn-secondary">
            <i class="mdi mdi-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<livewire:sales.return-show :return="$return" />

@endsection
