<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Sale Returns</h5>
        <a href="{{ route('sale-returns.create') }}" class="btn btn-primary btn-sm">
            <i class="mdi mdi-plus"></i> New Return
        </a>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reference</th>
                    <th>Sale Ref</th>
                    <th>Customer</th>
                    <th>Return Date</th>
                    <th>Total Returned</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($returns as $return)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $return->reference_no }}</td>
                        <td>{{ $return->sale->reference_no ?? '-' }}</td>
                        <td>{{ $return->sale->customer->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($return->return_date)->format('Y-m-d') }}</td>
                        <td>
                            {{ number_format($return->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}
                            {{ config('app.currency_symbol', '$') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No returns found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
