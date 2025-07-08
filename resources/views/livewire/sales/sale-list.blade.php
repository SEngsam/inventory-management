<div class="row">
    <div class="col-12">
        @if (session()->has('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title mb-0">Sales</h5>
                <a href="{{ route('sales.create') }}" class="btn btn-primary">+ Add Sale</a>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Reference</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Note</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $index => $sale)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $sale->reference_no }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td>{{ $sale->items_count }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($sale->note, 30) }}</td>
                                <td>
                                    <a href="{{ route('sale.show', $sale->id) }}" title="View">
                                        <i class="mdi mdi-eye text-info fs-18 border rounded p-1 me-1"></i>
                                    </a>
                                    <a href="{{ route('sale.edit', $sale->id) }}" title="Edit">
                                        <i class="mdi mdi-pencil text-muted fs-18 border rounded p-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No sales found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
