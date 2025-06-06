@extends('admin.layout')

@section('title', 'Class List')
{{-- @section('page_title', 'Manage Classes') --}} {{-- Keeping this commented --}}

@section('class_list')

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Manage Classes</h1>
    <div>
        <a href="{{ url('add_class') }}" class="btn btn-primary"> {{-- Assuming 'add_class' is the route/url for adding classes --}}
            <i class="bi bi-plus-square-fill me-2"></i>Add New Class
        </a>
    </div>
</div>

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

    <div class="card card-custom">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-list-task me-2"></i>All Classes
            </div>
            <form action="{{ url('class_list_filter') }}" method="get" class="d-flex align-items-center">
                <label for="filter_program" class="form-label me-2 mb-0 visually-hidden">Filter by Program:</label>
                <select name="field" id="filter_program" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 220px;">
                    <option value="all" {{ ($select ?? 'all') == 'all' ? 'selected' : '' }}>All Programs</option>
                    @foreach($programs as $program)
                    <option value="{{ $program->program_id }}" {{ ($select ?? '') == $program->program_id ? 'selected' : '' }}>{{ $program->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            @if(isset($classes) && $classes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Class ID</th>
                            <th scope="col">Program</th>
                            <th scope="col">Academic Batch</th>
                            <th scope="col">Semester</th>
                            <th scope="col">Division</th>
                            <th scope="col">Counselor</th>
                            <th scope="col">Last Updated</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $index => $classItem) {{-- Renamed $class to $classItem to avoid conflict with CSS class --}}
                        <tr>
                            <td>{{ $index + 1 + ($classes instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($classes->currentPage() - 1) * $classes->perPage() : 0) }}</td>
                            <td>{{ $classItem->id }}</td>
                            <td>{{ $classItem->program->name ?? 'N/A' }}</td>
                            <td>{{ $classItem->year }}</td>
                            <td>{{ $classItem->sem }}</td>
                            <td>{{ $classItem->devision }}</td>
                            <td>{{ $classItem->teacher->name ?? 'N/A' }}</td>
                            <td>{{ $classItem->updated_at ? $classItem->updated_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td class="text-center">
                                <a href="{{ url('edit_class/'.$classItem->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Class">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Class"
                                        data-bs-toggle="modal" data-bs-target="#deleteClassModal"
                                        data-class-id="{{ $classItem->id }}" data-class-info="{{ ($classItem->program->name ?? 'Class') . ' Sem ' . $classItem->sem . ' Div ' . $classItem->devision }}">
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
                <i class="bi bi-exclamation-circle display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">No classes found matching your criteria.</p>
                 @if(($select ?? 'all') != 'all')
                    <p class="small text-muted">Try selecting "All Programs" in the filter.</p>
                @endif
            </div>
            @endif
        </div>
        @if(isset($classes) && $classes instanceof \Illuminate\Pagination\LengthAwarePaginator && $classes->hasPages())
        <div class="card-footer bg-light border-top-0">
            {{ $classes->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Class Confirmation Modal -->
<div class="modal fade" id="deleteClassModal" tabindex="-1" aria-labelledby="deleteClassModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteClassModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the class: <strong id="classInfoToDelete"></strong>?
        <p class="text-danger small mt-2">This action cannot be undone and might affect associated student records and attendance data.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteClassForm" method="get" action=""> {{-- Action will be set by JS --}}
            <button type="submit" class="btn btn-danger">Yes, Delete Class</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteClassModal = document.getElementById('deleteClassModal');
    if (deleteClassModal) {
        deleteClassModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var classId = button.getAttribute('data-class-id');
            var classInfo = button.getAttribute('data-class-info');

            var modalBodyStrong = deleteClassModal.querySelector('#classInfoToDelete');
            var deleteForm = deleteClassModal.querySelector('#deleteClassForm');

            modalBodyStrong.textContent = classInfo;

            // Adjust based on your delete route.
            // If using your current GET route: {{ url("/delete_class") }}/' + classId;
            // For RESTful DELETE (recommended):
            // deleteForm.action = '{{ url("/classes") }}/' + classId;

            // For your current GET route:
            deleteForm.action = '/delete_class/' + classId;
            // Remove method spoofing if it exists and set method to GET
            const methodInput = deleteForm.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }
            deleteForm.method = 'GET';
        });
    }
});
</script>
