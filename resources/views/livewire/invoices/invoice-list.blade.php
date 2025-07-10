<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Invoices</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Invoices</a></li>
                <li class="breadcrumb-item active">Invoices List</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Invoices</h5>
                    <div>
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary me-2">
                            <i class="mdi mdi-plus"></i> Create Invoice
                        </a>
                        <button class="btn btn-danger" wire:click="deleteSelected"
                            @if (count($selectedInvoices) == 0) disabled @endif>
                            <i class="mdi mdi-trash-can"></i> Delete
                        </button>
                    </div>

                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-traffic mb-0">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" wire:model.live="selectAll" class="me-2" />
                                    </th>
                                    <th>Invoice Number</th>
                                    <th>Customer</th>
                                    <th>Items Count</th>
                                    <th>Total</th>
                                    <th>Issued At</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                    <tr>
                                        <td>
                                            <input type="checkbox" wire:model.live="selectedInvoices"
                                                value="{{ $invoice->id }}" class="me-2" />
                                        </td>
                                        <td>
                                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-reset">
                                                #{{ $invoice->id }}
                                            </a>
                                        </td>
                                        <td class="d-flex align-items-center">
                                            <img src="{{ asset('assets/images/users/user-1.jpg') }}"
                                                class="avatar avatar-sm rounded-2 me-3">
                                            {{ $invoice->customer->name }}
                                        </td>
                                        <td>
                                            {{ $invoice->items->count() }}
                                        </td>
                                        <td>
                                            {{ number_format($invoice->total, 2) }} ₪
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($invoice->issued_at)->format('d M Y') }}
                                        </td>
                                        <td>
                                            Recorded
                                        </td>
                                        <td>
                                            <a href="{{ route('invoices.show', $invoice->id) }}">
                                                <i class="mdi mdi-eye text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                            </a>
                                            <a href="#">
                                                <i class="mdi mdi-delete text-muted fs-18 rounded-2 border p-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No invoices found yet</td>
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
