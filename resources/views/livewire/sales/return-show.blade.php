<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Return Details</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('sale-returns.index') }}">Returns</a></li>
                <li class="breadcrumb-item active">Return Details</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="panel-body">
                        <div class="clearfix">
                            <div class="float-start d-flex align-items-center">
                                <img src="{{ asset('assets/images/logo-sm.png') }}" class="me-2" alt="logo" height="24">
                                <h4 class="mb-0 caption fw-semibold fs-18">Tapeli</h4>
                            </div>
                            <div class="float-end text-end">
                                <h4 class="fs-18">
                                    Return #{{ $return->reference_no }}<br>
                                    <strong class="fs-15 fw-normal">Return Number</strong>
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
                                    <p class="mb-0"><strong>Return Date:</strong> {{ \Carbon\Carbon::parse($return->return_date)->format('F d, Y') }}</p>
                                    <p class="mt-2 mb-0"><strong>Original Invoice:</strong> #{{ $return->sale->reference_no ?? '-' }}</p>
                                    <p class="mt-2 mb-0"><strong>Customer:</strong> {{ $return->sale->customer->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Return Items Table --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive rounded-2">
                                    <table class="table mt-4 mb-4 table-centered border">
                                        <thead class="rounded-2">
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($return->items as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->product->name ?? '-' }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                                    <td>${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="4" class="text-end fw-semibold">Total Returned:</td>
                                                <td class="fw-bold">
                                                    ${{ number_format($return->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        @if($return->note)
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Note:</strong> {{ $return->note }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="d-print-none">
                            <div class="float-end">
                                <a href="javascript:window.print()" class="btn btn-dark border-0">
                                    <i class="mdi mdi-printer me-1"></i> Print
                                </a>
                                <a href="{{ route('sale-returns.index') }}" class="btn btn-primary">Back to List</a>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
