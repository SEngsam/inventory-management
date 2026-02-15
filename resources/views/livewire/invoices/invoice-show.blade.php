<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Invoices</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Invoices</a></li>
                <li class="breadcrumb-item active">Invoice Details</li>
            </ol>
        </div>
    </div>

    @php
        $badge = match ($invoice->status) {
            'paid' => 'success',
            'issued' => 'primary',
            'draft' => 'secondary',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="panel-body">

                        <div class="clearfix">
                            <div class="float-start d-flex justify-content-center">
                                <img src="{{ asset('assets/images/logo-sm.png') }}" class="me-2" alt="logo"
                                    height="24">
                                <h4 class="mb-0 caption fw-semibold fs-18">Tapeli</h4>
                            </div>

                            <div class="float-end text-end">
                                <h4 class="fs-18 mb-1">
                                    Invoice #{{ $invoice->invoice_number }}
                                </h4>
                                <div class="text-muted">Invoice Number</div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="float-start mt-3">
                                    <address class="mb-0">
                                        <strong>{{ $invoice->customer?->name ?? 'N/A' }}</strong><br>
                                        {{ $invoice->customer?->address ?? 'No address available' }}<br>
                                        <abbr title="Phone">P:</abbr> {{ $invoice->customer?->phone ?? 'N/A' }}
                                    </address>
                                </div>

                                <div class="float-end mt-3 text-end">
                                    <p class="mb-0">
                                        <strong>Issued Date: </strong>
                                        {{ $invoice->issued_at ? \Carbon\Carbon::parse($invoice->issued_at)->format('d M Y') : '—' }}
                                    </p>

                                    <p class="mt-2 mb-0">
                                        <strong>Status: </strong>
                                        <span
                                            class="badge bg-{{ $badge }}">{{ strtoupper($invoice->status) }}</span>
                                    </p>

                                    <p class="mt-2 mb-0">
                                        <strong>Invoice ID: </strong> #{{ $invoice->id }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive rounded-2">
                                    <table class="table mt-4 mb-4 table-centered border">
                                        <thead class="table-light rounded-2">
                                            <tr>
                                                <th style="width:60px">#</th>
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th style="width:120px">Quantity</th>
                                                <th style="width:140px">Unit Cost</th>
                                                <th style="width:140px">Tax</th>
                                                <th style="width:160px">Line Total</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($invoice->items as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->product?->name ?? '—' }}</td>
                                                    <td>{{ $item->product?->description ?? '—' }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>${{ number_format((float) $item->unit_price, 2) }}</td>
                                                    <td>${{ number_format((float) $item->line_tax, 2) }}</td>
                                                    <td>${{ number_format((float) $item->line_total, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">
                                                        No items found for this invoice.
                                                    </td>
                                                </tr>
                                            @endforelse

                                            <tr>
                                                <td colspan="5"></td>
                                                <td colspan="2">
                                                    <table class="table table-sm text-nowrap mb-0 table-borderless">
                                                        <tbody>
                                                            <tr>
                                                                <td>Sub-total :</td>
                                                                <td class="fw-medium fs-15 text-end">
                                                                    ${{ number_format((float) $invoice->subtotal, 2) }}
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>Tax :</td>
                                                                <td class="fw-medium fs-15 text-end">
                                                                    ${{ number_format((float) $invoice->tax_total, 2) }}
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>Discount :</td>
                                                                <td class="fw-medium fs-15 text-end">
                                                                    ${{ number_format((float) $invoice->discount_total, 2) }}
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="fw-bold">Total Due :</td>
                                                                <td class="fw-bold fs-16 text-success text-end">
                                                                    ${{ number_format((float) $invoice->total, 2) }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="d-print-none">
                            <div class="float-end d-flex gap-2">

                                <a href="{{ route('export', ['format' => 'pdf', 'type' => 'invoice', 'id' => $invoice->id]) }}"
                                    class="btn btn-dark border-0" target="_blank">
                                    <i class="mdi mdi-printer me-1"></i>PDF
                                </a>

                                <a href="{{ route('export', ['format' => 'excel', 'type' => 'invoice', 'id' => $invoice->id]) }}"
                                    class="btn btn-success border-0">
                                    <i class="mdi mdi-file-excel me-1"></i>Excel
                                </a>

                                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                                    Back
                                </a>
                            </div>

                            <div class="clearfix"></div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
