<div>

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
        </div>
    </div>

    <!-- start row -->
    <div class="row">
        <div class="col-md-12 col-xl-12">
            <div class="row g-3">

                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="fs-14 mb-1">Total Products</div>
                            </div>

                            <div class="d-flex align-items-baseline mb-2">
                                <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{ $productCount }}</div>
                                <div class="me-auto">
                                    <span class="text-primary d-inline-flex align-items-center">
                                        15%
                                        <i data-feather="trending-up" class="ms-1"
                                            style="height: 22px; width: 22px;"></i>
                                    </span>
                                </div>
                            </div>
                            <div id="website-visitors" class="apex-charts"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="fs-14 mb-1">Total Customers</div>
                            </div>

                            <div class="d-flex align-items-baseline mb-2">
                                <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{ $customerCount }}</div>
                                <div class="me-auto">
                                    <span class="text-danger d-inline-flex align-items-center">
                                        10%
                                        <i data-feather="trending-down" class="ms-1"
                                            style="height: 22px; width: 22px;"></i>
                                    </span>
                                </div>
                            </div>
                            <div id="conversion-visitors" class="apex-charts"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="fs-14 mb-1">Total Sales</div>
                            </div>

                            <div class="d-flex align-items-baseline mb-2">
                                <div class="fs-22 mb-0 me-2 fw-semibold text-black">${{ number_format($salesTotal, 2) }}
                                </div>
                                <div class="me-auto">
                                    <span class="text-success d-inline-flex align-items-center">
                                        25%
                                        <i data-feather="trending-up" class="ms-1"
                                            style="height: 22px; width: 22px;"></i>
                                    </span>
                                </div>
                            </div>
                            <div id="session-visitors" class="apex-charts"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="fs-14 mb-1">Total Returns</div>
                            </div>

                            <div class="d-flex align-items-baseline mb-2">
                                <div class="fs-22 mb-0 me-2 fw-semibold text-black">
                                    ${{ number_format($returnsTotal, 2) }}</div>
                                <div class="me-auto">
                                    <span class="text-success d-inline-flex align-items-center">
                                        4%
                                        <i data-feather="trending-up" class="ms-1"
                                            style="height: 22px; width: 22px;"></i>
                                    </span>
                                </div>
                            </div>
                            <div id="active-users" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end sales -->
    </div> <!-- end row -->

    <!-- Start Monthly Sales -->
    <div class="row">
        <div class="col-md-6 col-xl-8">
            <div class="card">

                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                            <i data-feather="bar-chart" class="widgets-icons"></i>
                        </div>
                        <h5 class="card-title mb-0">Monthly Sales</h5>
                    </div>
                </div>

                <div class="card-body">
                    <div id="monthly-sales" class="apex-charts"></div>
                </div>

            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card overflow-hidden">

                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                            <i data-feather="shopping-cart" class="widgets-icons"></i>
                        </div>
                        <h5 class="card-title mb-0">Top Selling Products</h5>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-traffic mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Sales</th>
                                    <th colspan="2">Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $maxQuantity = collect($topSellingProducts)->max('quantity') ?: 1;
                                @endphp
                                @foreach ($topSellingProducts as $product)
                                    @php
                                        $percentage = round(($product['quantity'] / $maxQuantity) * 100, 2);
                                    @endphp
                                    <tr>
                                        <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;"
                                            title="{{ $product['name'] }}">
                                            {{ $product['name'] }}
                                        </td>
                                        <td>{{ $product['quantity'] }}</td>
                                        <td class="w-50">
                                            <div class="progress progress-md mt-0">
                                                <div class="progress-bar bg-primary"
                                                    style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <!-- End Monthly Sales -->

    <div class="row">
        <div class="col-md-6 col-xl-6">
            <div class="card overflow-hidden">

                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                            <i data-feather="rotate-ccw" class="widgets-icons"></i>
                        </div>
                        <h5 class="card-title mb-0">Recent Returns</h5>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-traffic mb-0">
                            <thead>
                                <tr>
                                    <th>Reference No</th>
                                    <th>Return Date</th>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentReturns as $return)
                                    <tr>
                                        <td>{{ $return->reference_no }}</td>
                                        <td>{{ $return->return_date->format('Y-m-d') }}</td>
                                        <td>{{ $return->customer?->name ?? 'N/A' }}</td>
                                        <td>${{ number_format($return->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No recent returns found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-6">
            <div class="card overflow-hidden">

                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                            <i data-feather="dollar-sign" class="widgets-icons"></i>
                        </div>
                        <h5 class="card-title mb-0">Recent Sales</h5>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-traffic mb-0">
                            <thead>
                                <tr>
                                    <th>Ref No</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentSales as $sale)
                                    <tr>
                                        <td>
                                            <a href="{{ route('sales.show', $sale->id) }}" class="text-primary">
                                                {{ $sale->reference_no }}
                                            </a>
                                        </td>
                                        <td>{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                                        <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                        <td class="text-end">
                                            ${{ number_format($sale->items->sum('total'), 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No recent sales found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div </div>
        </div>
    </div>
    @push('script')
        @php
            $formattedMonthlySales = array_map(
                fn($v) => (float) number_format($v, 2, '.', ''),
                array_values($monthlySales),
            );
        @endphp



        <script>
            window.salesChartData = {
                labels: @json(array_map(fn($m) => date('m/01/' . now()->year, strtotime(now()->year . "-$m-01")), array_keys($monthlySales))),
                values: @json(array_values($monthlySales))
            };

            const monthlySalesData = @json(array_values($formattedMonthlySales));
            console.log(monthlySalesData);
            const monthlySalesLabels = @json(array_map(function ($month) {
                    return date('m/01/Y', strtotime("2025-$month-01"));
                }, array_keys($monthlySales)));
        </script>

        <script src="https://apexcharts.com/samples/assets/stock-prices.js"></script>
        @vite(['resources/js/pages/analytics-dashboard.init.js'])
    @endpush
