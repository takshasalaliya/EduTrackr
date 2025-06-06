@extends('admin.layout')

@section('title', 'Manage Programs')
@section('page_title', 'Manage Programs') {{-- For the H1 in the main content area --}}

@section('add_field')

<div class="container-fluid">
    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Add Program Form --}}
    <div class="card card-custom mb-4">
        <div class="card-header">
            <i class="bi bi-plus-circle-fill me-2"></i>Add New Program
        </div>
        <div class="card-body">
            <form action="{{ url('field') }}" method="post"> {{-- Use url() helper for routes --}}
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-9">
                        <label for="program_name" class="form-label">Program Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('field') is-invalid @enderror" name="field" id="program_name" placeholder="e.g., Bachelor of Computer Applications" value="{{ old('field') }}" required>
                        @error('field')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save-fill me-2"></i>Save Program
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Program List Table --}}
    <div class="card card-custom">
        <div class="card-header">
            <i class="bi bi-list-ul me-2"></i>Existing Programs
        </div>
        <div class="card-body p-0">
            @if(isset($datas) && $datas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Program Name</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $index => $data)
                        <tr>
                            <td>{{ $index + 1 + ($datas instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($datas->currentPage() - 1) * $datas->perPage() : 0) }}</td>
                            <td>{{ $data->name }}</td>
                            <td class="text-center">
                                {{-- Edit Button (Optional) - You would need a route and controller method for this --}}
                                {{-- <a href="{{ url('program/edit/'.$data->program_id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Program">
                                    <i class="bi bi-pencil-square"></i>
                                </a> --}}
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Program"
                                        data-bs-toggle="modal" data-bs-target="#deleteProgramModal"
                                        data-program-id="{{ $data->program_id }}" data-program-name="{{ $data->name }}">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center p-4">
                <p class="text-muted mb-0">No programs found. Add one using the form above.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Program Confirmation Modal -->
<div class="modal fade" id="deleteProgramModal" tabindex="-1" aria-labelledby="deleteProgramModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteProgramModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the program: <strong id="programNameToDelete"></strong>?
        <p class="text-danger small mt-2">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteProgramForm" method="get" action=""> {{-- Action will be set by JS --}}
            
             {{-- Or use your current GET method logic --}}
            <button type="submit" class="btn btn-danger">Yes, Delete Program</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteProgramModal = document.getElementById('deleteProgramModal');
    if (deleteProgramModal) {
        deleteProgramModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var programId = button.getAttribute('data-program-id');
            var programName = button.getAttribute('data-program-name');

            var modalTitle = deleteProgramModal.querySelector('.modal-title');
            var modalBodyStrong = deleteProgramModal.querySelector('#programNameToDelete');
            var deleteForm = deleteProgramModal.querySelector('#deleteProgramForm');

            modalBodyStrong.textContent = programName;

            // Adjust the action URL based on your route definition
            // If you are using a GET request for deletion as per your original code:
            deleteForm.action = '/delete_program/' + programId;
            // If you prefer a DELETE request (recommended RESTful practice):
            // deleteForm.action = '{{ url("/programs") }}/' + programId;
            // And ensure your route is Route::delete('/programs/{id}', ...);
        });
    }
});
</script>
