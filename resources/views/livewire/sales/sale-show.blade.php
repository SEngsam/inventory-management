<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">تفاصيل الفاتورة</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">المبيعات</a></li>
                <li class="breadcrumb-item active">تفاصيل الفاتورة</li>
            </ol>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="panel-body">
                        <div class="clearfix">
                            <div class="float-start d-flex justify-content-center align-items-center">
                                <img src="{{ asset('assets/images/logo-sm.png') }}" class="me-2" alt="logo"
                                    height="24">
                                <h4 class="mb-0 caption fw-semibold fs-18">Tapeli</h4>
                            </div>
                            <div class="float-end text-end">
                                <h4 class="fs-18">
                                    Invoice #{{ $sale->reference_no ?? 'N/A' }}<br>
                                    <strong class="fs-15 fw-normal">Invoice Number</strong>
                                </h4>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="float-start mt-3">
                                    <address>
                                        <strong>{{ config('app.company_name', 'Your Company Name') }}</strong><br>
                                        {{ config('app.company_address', 'Company Address') }}<br>
                                        {{ config('app.company_city', 'City, Country') }}<br>
                                        <abbr title="Phone">P:</abbr> {{ config('app.company_phone', '000-000-0000') }}
                                    </address>
                                </div>
                                <div class="float-end mt-3 text-end">
                                    <p class="mb-0"><strong>Invoice Date:</strong>
                                        {{ \Carbon\Carbon::parse($sale->sale_date)->format('F d, Y') }}</p>
                                    <p class="mt-2 mb-0"><strong>Status:</strong>
                                        <span
                                            class="badge bg-{{ $sale->status == 'pending' ? 'warning' : ($sale->status == 'paid' ? 'success' : 'secondary') }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </p>
                                    <p class="mt-2 mb-0"><strong>Order ID:</strong> #{{ $sale->id }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Sale Items Table --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive rounded-2">
                                    <table class="table mt-4 mb-4 table-centered border">
                                        <thead class="rounded-2">
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Description</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sale->items as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->product?->name ?? '-' }}</td>
                                                    <td>{{ $item->product?->description ?? '-' }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                                    <td>${{ number_format($item->quantity * $item->unit_price, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2">
                                                    <table class="table table-sm text-nowrap mb-0 table-borderless">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <p class="mb-0">Subtotal:</p>
                                                                </td>
                                                                <td>
                                                                    <p class="mb-0 fw-medium fs-15">
                                                                        ${{ number_format($sale->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p class="mb-0">Discount:</p>
                                                                </td>
                                                                <td>
                                                                    <p class="mb-0 fw-medium fs-15">
                                                                        ${{ number_format($sale->discount ?? 0, 2) }}
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p class="mb-0">VAT <span
                                                                            class="text-danger">({{ $sale->vat_percentage ?? 0 }}%)</span>:
                                                                    </p>
                                                                </td>
                                                                <td>
                                                                    <p class="mb-0 fw-medium fs-15">
                                                                        ${{ number_format($sale->vat_amount ?? 0, 2) }}
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p class="mb-0">Amount Due:</p>
                                                                </td>
                                                                <td>
                                                                    <p class="mb-0 fw-medium fs-15">
                                                                        ${{ number_format($sale->due_amount ?? 0, 2) }}
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p class="mb-0 fs-14">Grand Total:</p>
                                                                </td>
                                                                <td>
                                                                    <p class="mb-0 fw-medium fs-16 text-success">
                                                                        ${{ number_format($sale->total_amount, 2) }}
                                                                    </p>
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

                        {{-- Action Buttons --}}
                        <div class="d-print-none">
                            <div class="float-end">
                                <a href="javascript:window.print()" class="btn btn-dark border-0">
                                    <i class="mdi mdi-printer me-1"></i> Print
                                </a>
                                <a href="{{ route('sales.index') }}" class="btn btn-primary">Back to List</a>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
