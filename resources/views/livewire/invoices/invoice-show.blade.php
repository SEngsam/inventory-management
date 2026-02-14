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
                <div class="card-body">
                    <div class="panel-body">
                        <div class="clearfix">
                            <div class="float-start d-flex justify-content-center">
                                <img src="{{ asset('assets/images/logo-sm.png') }}" class="me-2" alt="logo"
                                    height="24">
                                <h4 class="mb-0 caption fw-semibold fs-18">Tapeli</h4>
                            </div>
                            <div class="float-end">
                                <h4 class="fs-18">Invoice #{{ $invoice->id }}<br>
                                    <strong class="fs-15 fw-normal">Invoice Number</strong>
                                </h4>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="float-start mt-3">
                                    <address>
                                        <strong>{{ $invoice->customer->name }}</strong><br>
                                        {{ $invoice->customer->address ?? 'No address available' }}<br>
                                        <abbr title="Phone">P:</abbr> {{ $invoice->customer->phone ?? 'N/A' }}
                                    </address>
                                </div>
                                <div class="float-end mt-3">
                                    <p class="mb-0"><strong>Issued Date: </strong>
                                        {{ \Carbon\Carbon::parse($invoice->issued_at)->format('d M Y') }}</p>
                                    <p class="mt-2 mb-0"><strong>Status: </strong> <span
                                            class="label label-pink">Recorded</span></p>
                                    <p class="mt-2 mb-0"><strong>Invoice ID: </strong> #{{ $invoice->id }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive rounded-2">
                                    <table class="table mt-4 mb-4 table-centered border">
                                        <thead class="rounded-2">
                                            <tr>
                                                <th>#</th>
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th>Quantity</th>
                                                <th>Unit Cost</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invoice->items as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->product->name }}</td>
                                                    <td>{{ $item->product->description ?? '—' }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>${{ number_format($item->price, 2) }}</td>
                                                    <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2">
                                                    <table class="table table-sm text-nowrap mb-0 table-borderless">
                                                        <tbody>
                                                            <tr>
                                                                <td>Sub-total :</td>
                                                                <td class="fw-medium fs-15">
                                                                    ${{ number_format($invoice->total, 2) }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>VAT <span class="text-danger">(15%)</span> :</td>
                                                                <td class="fw-medium fs-15">
                                                                    ${{ number_format($invoice->total * 0.15, 2) }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Total Due :</td>
                                                                <td class="fw-medium fs-16 text-success">
                                                                    ${{ number_format($invoice->total * 1.15, 2) }}
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
                            <div class="float-end">
                                <a href="javascript:window.print()" class="btn btn-dark border-0">
                                    <i class="mdi mdi-printer me-1"></i>Print
                                </a>
                                <a href="#" class="btn btn-primary">Submit</a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>



</div>
