<div>


    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Currencies</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
                <li class="breadcrumb-item active">Currencies</li>
            </ol>
        </div>
    </div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Currency</h5>
                </div><!-- end card header -->
                <div class="card-body">

                    <form class="row align-items-end g-3" wire:submit.prevent="{{ $currency_id ? 'update' : 'store' }}">
                        <!-- Currency Name -->
                        <div class="col-md-3">
                            <label for="currencyName" class="form-label">Currency Name</label>
                            <input type="text" class="form-control" id="currencyName" wire:model="name"
                                placeholder="Currency Name">
                        </div>

                        <!-- Symbol -->
                        <div class="col-md-2">
                            <label for="currencySymbol" class="form-label">Symbol</label>
                            <input type="text" class="form-control" id="currencySymbol" wire:model="symbol"
                                placeholder="Symbol">
                        </div>

                        <!-- Rate -->
                        <div class="col-md-2">
                            <label for="currencyRate" class="form-label">Rate</label>
                            <input type="text" class="form-control" id="currencyRate" wire:model="rate"
                                placeholder="Rate">
                        </div>

                        <!-- Active -->
                        <div class="col-md-2 d-flex align-items-center pt-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="isActive" wire:model="is_active">
                                <label class="form-check-label" for="isActive">Active</label>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="col-md-3 pt-4">
                            <button class="btn btn-primary ">
                                {{ $currency_id ? 'Update' : 'Add' }}
                            </button>
                        </div>
                    </form>


                    <hr>
                    <div class="table-responsive">
                        <table class="table table-traffic mb-0">
                            <thead>
                                <tr>
                                    <th>
                                        <input class="form-check-input" type="checkbox" wire:model="selectAll" />
                                    </th>
                                    <th>Name</th>
                                    <th>Symbol</th>
                                    <th>Rate</th>
                                    <th>Default</th>
                                    <th >Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($currencies as $currency)
                                    <tr wire:key="currency-{{ $currency->id }}">
                                        <td>
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="selectedCurrency" value="{{ $currency->id }}" />
                                        </td>
                                        <td>{{ $currency->name }}</td>
                                        <td>{{ $currency->symbol }}</td>
                                        <td>{{ $currency->rate }}</td>
                                        <td>
                                            @if ($currency->is_default)
                                                <span class="badge bg-success">Default</span>
                                            @else
                                                <button class="btn btn-sm btn-outline-info"
                                                    wire:click="makeDefault({{ $currency->id }})">Set Default</button>
                                            @endif
                                        </td>
                                        <td >
                                            <button class="btn btn-warning btn-sm"
                                                wire:click="edit({{ $currency->id }})">Edit</button>
                                            <button class="btn btn-danger btn-sm"
                                                wire:click="delete({{ $currency->id }})">Delete</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No currencies added yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
