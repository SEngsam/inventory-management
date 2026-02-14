<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Sales </h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);"> Sales</a></li>
                <li class="breadcrumb-item active">Sales List</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">Sales List</h5>
                    <a href="{{ route('sales.create') }}" class="btn btn-primary">+ Add Sale</a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-traffic mb-0">
                            <thead>
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
                                            <span
                                                class="badge bg-{{ $sale->status === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($sale->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $sale->items_count }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($sale->note, 30) }}</td>
                                        <td>
                                            <a href="{{ route('sales.show', $sale->id) }}" title="View">
                                                <i class="mdi mdi-eye text-info fs-18 border rounded p-1 me-1"></i>
                                            </a>
                                            <a href="{{ route('sales.edit', $sale->id) }}" title="Edit">
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
                        {{ $sales->links() }}

                </div>
            </div>
        </div>
    </div>

</div>
