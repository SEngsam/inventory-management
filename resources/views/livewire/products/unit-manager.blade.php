<div>
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">Units</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">Products</a></li>
            <li class="breadcrumb-item active">Units</li>
        </ol>
    </div>
</div>
<div class="row">
     <div class="col-12">
         @if (session()->has('message'))
             <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
                 <div class="toast show align-items-center text-white bg-success border-0 shadow" role="alert"
                     id="successToast" aria-live="assertive" aria-atomic="true">
                     <div class="d-flex">
                         <div class="toast-body">
                             {{ session('message') }}
                         </div>
                         <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                             aria-label="Close"></button>
                     </div>
                 </div>
             </div>
         @endif

         <div class="card">
             <div class="card-header d-flex justify-content-between align-items-center">
                 <h5 class="card-title mb-0">Units List</h5>

                 <div>


                     <button class="btn btn-primary me-2" onclick="showUnitModal()"><i
                             class="mdi mdi-plus"></i>Add</button>
                     <button class="btn btn-danger" wire:click="deleteSelected"
                         @if (count($selectedUnits) == 0) disabled @endif>
                         <i class="mdi mdi-trash-can"></i>Delete
                     </button>
                 </div>
             </div>


             <div class="card-body">
                 <div class="table-responsive">
                     <table class="table table-traffic mb-0">
                         <thead>
                             <tr>
                                 <th>#</th>
                                 <th>Name</th>
                                 <th>Short Code</th>
                                 <th>Base Unit</th>
                                 <th>Operator</th>
                                 <th>Operator Value</th>
                                 <th>Actions</th>
                             </tr>
                         </thead>
                         <tbody>
                             @forelse ($units as $unit)
                                 <tr>
                                     <td>
                                         <input type="checkbox" wire:model.live="selectedUnits"
                                             value="{{ $unit->id }}" class="me-2" />

                                     </td>
                                     <td>{{ $unit->name }}</td>
                                     <td>{{ $unit->short_code ?? '-' }}</td>
                                     <td>{{ $unit->baseUnit ? $unit->baseUnit->name : '-' }}</td>
                                     <td>{{ $unit->operator ?? '-' }}</td>
                                     <td>{{ $unit->operator_value ?? '-' }}</td>
                                     <td>
                                         <a  href="javascript:void(0);" wire:click="edit({{ $unit->id }})">
                                             <i class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i>
                                         </a>
                                     </td>



                                 </tr>
                             @empty
                                 <tr>
                                     <td colspan="6" class="text-center text-muted">No units found.</td>
                                 </tr>
                             @endforelse
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
         {{-- Modal --}}
         <div class="modal fade" id="unitModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
             <div class="modal-dialog">
                 <div class="modal-content">
                     <form wire:submit.prevent="save">
                         <div class="modal-header">
                             <h5 class="modal-title">{{ $isEdit ? 'Edit Unit' : 'Add Unit' }}</h5>
                             <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                         </div>
                         <div class="modal-body">
                             <div class="mb-3">
                                 <label>Name</label>
                                 <input type="text" wire:model.defer="name" class="form-control" />
                                 @error('name')
                                     <small class="text-danger">{{ $message }}</small>
                                 @enderror
                             </div>
                             <div class="mb-3">
                                 <label>Short Code</label>
                                 <input type="text" wire:model.defer="short_code" class="form-control" />
                                 @error('short_code')
                                     <small class="text-danger">{{ $message }}</small>
                                 @enderror
                             </div>
                             <div class="mb-3">
                                 <label>Base Unit</label>
                                 <select class="form-select" wire:model.live="base_unit_id">
                                     <option value="">Select base unit</option>
                                     @foreach ($units as $u)
                                         <option value="{{ $u->id }}">{{ $u->name }}</option>
                                     @endforeach
                                 </select>
                                 @error('base_unit_id')
                                     <small class="text-danger">{{ $message }}</small>
                                 @enderror
                             </div>

                             @if ($base_unit_id)
                                 <div class="mb-3">
                                     <label>Operator</label>
                                     <select class="form-select" wire:model="operator">
                                         <option value="">Select operator</option>
                                         <option value="*">Multiply (*)</option>
                                         <option value="/">Divide (/)</option>
                                     </select>
                                     @error('operator')
                                         <small class="text-danger">{{ $message }}</small>
                                     @enderror
                                 </div>

                                 <div class="mb-3">
                                     <label>Operator Value</label>
                                     <input type="number" step="0.01" wire:model="operator_value"
                                         class="form-control" />
                                     @error('operator_value')
                                         <small class="text-danger">{{ $message }}</small>
                                     @enderror
                                 </div>
                             @endif

                         </div>
                         <div class="modal-footer">
                             <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                             <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Save' }}</button>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
     </div>
 </div>
</div>
@push('script')
 <script>
     function showUnitModal() {
         const modal = new bootstrap.Modal(document.getElementById('unitModal'));
         modal.show();
     }

     window.addEventListener('show-unit-modal', () => {
         const modal = new bootstrap.Modal(document.getElementById('unitModal'));
         modal.show();
     });
     window.addEventListener('hide-unit-modal', () => {
         const modal = bootstrap.Modal.getInstance(document.getElementById('unitModal'));
         modal.hide();
     });

     document.addEventListener("DOMContentLoaded", function() {
         @if (session()->has('message'))
             const toastEl = document.getElementById('successToast');
             const toast = new bootstrap.Toast(toastEl, {
                 delay: 3000
             });
             toast.show();
         @endif
     });
 </script>
@endpush
