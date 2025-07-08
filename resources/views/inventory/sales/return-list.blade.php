@extends('layouts.vertical', ['title' => 'Sale Returns'])

@section('content')
<div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">Sale Returns List</h4>
    </div>
</div>

<livewire:sales.return-list />

@endsection
