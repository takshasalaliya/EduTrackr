@extends('admin.layout')

@section('title', 'Teacher List')
{{-- @section('page_title', 'Teacher List') --}} {{-- Keeping this commented --}}

@section('teacher')

{{-- Manual Page Header if not using @section('page_title') in layout --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Teacher List</h1>
    <div>
        <a href="{{ url('add_teacher') }}" class="btn btn-primary"> {{-- Assuming 'add_teacher' is the route/url for adding teachers --}}
            <i class="bi bi-person-plus-fill me-2"></i>Add New Teacher
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
                <i class="bi bi-list-ul me-2"></i>All Teachers
            </div>
            <form action="{{ url('filter_teacher_list') }}" method="get" class="d-flex align-items-center">
                <label for="filter_role" class="form-label me-2 mb-0 visually-hidden">Filter by Role:</label>
                <select name="filter" id="filter_role" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 180px;">
                    <option value="all" {{ ($select ?? 'all') == 'all' ? 'selected' : '' }}>All Roles</option>
                    <option value="reader" {{ ($select ?? '') == 'reader' ? 'selected' : '' }}>Reader</option>
                    <option value="counselor" {{ ($select ?? '') == 'counselor' ? 'selected' : '' }}>Counselor</option>
                    <option value="admin" {{ ($select ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                {{-- Optional: Add a submit button if onchange is not preferred or for accessibility --}}
                {{-- <button type="submit" class="btn btn-sm btn-outline-secondary ms-2">Filter</button> --}}
            </form>
        </div>
        <div class="card-body p-0">
            @if(isset($teachers) && $teachers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Short Name</th>
                            <th scope="col">Mobile No.</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col" class="text-center">Is Counselor?</th>
                            {{-- Consider removing plain password display for security --}}
                            {{-- <th scope="col">Password</th> --}}
                            <th scope="col">Last Updated</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $count = 1; @endphp
                        @foreach($teachers as $teacher)
                            {{-- Assuming you still want to filter out 'student' role here,
                                 though ideally this filter should happen in the controller query --}}
                            @if($teacher->role != 'student')
                            <tr>
                                <td>{{ $count++ + ($teachers instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($teachers->currentPage() - 1) * $teachers->perPage() : 0) }}</td>
                                <td>{{ $teacher->name }}</td>
                                <td>{{ $teacher->short_name }}</td>
                                <td>{{ $teacher->phone_number }}</td>
                                <td>{{ $teacher->email }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($teacher->role) }}</span></td>
                                <td class="text-center">
                                    @if($teacher->role == 'counselor')
                                        <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Yes</span>
                                    @else
                                        <span class="badge bg-light text-dark"><i class="bi bi-x-circle-fill"></i> No</span>
                                    @endif
                                </td>
                                {{-- Displaying plain passwords is a major security risk.
                                     It's better to have a "Reset Password" functionality.
                                     If you absolutely must show it for some internal reason (not recommended):
                                <td>{{ $teacher->plain_password }}</td>
                                --}}
                                <td>{{ $teacher->updated_at ? $teacher->updated_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                <td class="text-center">
                                    <a href="{{ url('/edit_teacher/'.$teacher->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Teacher">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Teacher"
                                            data-bs-toggle="modal" data-bs-target="#deleteTeacherModal"
                                            data-teacher-id="{{ $teacher->id }}" data-teacher-name="{{ $teacher->name }}">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center p-4">
                <i class="bi bi-exclamation-circle display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">No teachers found matching your criteria.</p>
                @if(($select ?? 'all') != 'all')
                    <p class="small text-muted">Try selecting "All Roles" in the filter.</p>
                @endif
            </div>
            @endif
        </div>
       
</div>

<!-- Delete Teacher Confirmation Modal -->
<div class="modal fade" id="deleteTeacherModal" tabindex="-1" aria-labelledby="deleteTeacherModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteTeacherModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the teacher: <strong id="teacherNameToDelete"></strong>?
        <p class="text-danger small mt-2">This action cannot be undone and might affect associated records.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteTeacherForm" method="get" action=""> {{-- Action will be set by JS --}}
          
            <button type="submit" class="btn btn-danger">Yes, Delete Teacher</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteTeacherModal = document.getElementById('deleteTeacherModal');
    if (deleteTeacherModal) {
        deleteTeacherModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var teacherId = button.getAttribute('data-teacher-id');
            var teacherName = button.getAttribute('data-teacher-name');

            var modalBodyStrong = deleteTeacherModal.querySelector('#teacherNameToDelete');
            var deleteForm = deleteTeacherModal.querySelector('#deleteTeacherForm');

            modalBodyStrong.textContent = teacherName;

            // Adjust based on your delete route. Assuming RESTful DELETE:
            // deleteForm.action = '{{ url("/teachers") }}/' + teacherId;
            // If using your current GET route:
            deleteForm.action = '/delete_teacher/' + teacherId;
             // For GET route, you'd remove the @method('DELETE') from the form or adjust server-side logic
             // It's better to use POST/DELETE for destructive actions.
             // To keep GET for now and make the form work:

        });
    }
});
</script>
