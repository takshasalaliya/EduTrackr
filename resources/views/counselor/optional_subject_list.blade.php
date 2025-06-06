@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'My Student Optional Subject Mappings')
@section('page_title', 'Student Optional Subject Assignments (My Classes)')

@section('optional_subject_list') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <p class="text-muted mb-0">Showing optional subject assignments for students in classes you counsel.</p>
    </div>
    <a href="{{ url('optionalgroup') }}" class="btn btn-primary"> {{-- Assuming this is the counselor's route for assigning optional subjects --}}
        <i class="bi bi-plus-circle-fill me-2"></i>Assign Optional Subjects
    </a>
</div>

<div class="container-fluid"> {{-- Use container-fluid for full width --}}

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
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <i class="bi bi-card-checklist me-2"></i>Assigned Optional Subjects List
            </div>
            {{-- Placeholder for Filters: Consider adding filters for Program, specific Class (counseled by user), Student Name/Enrollment --}}
            {{-- Example (requires controller logic):
            <form action="{{ url('counselor/optional_subject_list/filter') }}" method="get" class="d-flex align-items-center ms-auto">
                <select name="class_filter" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                    <option value="">Filter by My Class</option>
                    @foreach($counselor_classes_for_filter as $class_opt)
                        <option value="{{ $class_opt->id }}" {{ request('class_filter') == $class_opt->id ? 'selected' : '' }}>
                            {{ $class_opt->program->name }} / Sem {{ $class_opt->sem }} / Div {{ $class_opt->devision }}
                        </option>
                    @endforeach
                </select>
                <input type="text" name="student_search" class="form-control form-control-sm" placeholder="Search Student..." value="{{ request('student_search') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary ms-2">Filter</button>
            </form>
            --}}
        </div>
        <div class="card-body p-0">
            @if(isset($datas) && $datas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Enrollment</th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Optional Subject Code</th>
                            <th scope="col">Optional Subject Name</th>
                            <th scope="col">Subject's Class Context</th> {{-- Class context of the subject --}}
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $index => $assignment) {{-- Renamed $data to $assignment --}}
                        <tr>
                            <td>{{ $index + 1 + ($datas instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($datas->currentPage() - 1) * $datas->perPage() : 0) }}</td>
                            <td>{{ $assignment->student->enrollment_number ?? 'N/A' }}</td>
                            <td>{{ $assignment->student->name ?? 'N/A' }}</td>
                            <td>{{ $assignment->subject->subject_code ?? 'N/A' }}</td>
                            <td>{{ $assignment->subject->subject_name ?? 'N/A' }}</td>
                            <td>
                                @if($assignment->subject->student_class && $assignment->subject->student_class->program)
                                {{ $assignment->subject->student_class->program->name }} /
                                Sem {{ $assignment->subject->student_class->sem ?? 'N/A' }} /
                                Div {{ $assignment->subject->student_class->devision ?? 'N/A' }}
                                @else
                                General Subject
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Assignment"
                                        data-bs-toggle="modal" data-bs-target="#deleteOptionalMapModalCounselor"
                                        data-assignment-id="{{ $assignment->id }}"
                                        data-assignment-info="Subject: {{ $assignment->subject->subject_name ?? 'N/A' }} for Student: {{ $assignment->student->name ?? 'N/A' }}">
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
                <i class="bi bi-ui-checks display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">No optional subject assignments found for students in your classes.</p>
                <p class="small text-muted mt-2">You can <a href="{{ url('optionalgroup') }}">assign optional subjects to students</a> to populate this list.</p>
            </div>
            @endif
        </div>
        @if(isset($datas) && $datas instanceof \Illuminate\Pagination\LengthAwarePaginator && $datas->hasPages())
        <div class="card-footer bg-light border-top-0">
            {{ $datas->appends(request()->query())->links() }} {{-- Appends current query string to pagination links --}}
        </div>
        @endif
    </div>
</div>

<!-- Delete Optional Assignment Confirmation Modal -->
<div class="modal fade" id="deleteOptionalMapModalCounselor" tabindex="-1" aria-labelledby="deleteOptionalMapModalCounselorLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteOptionalMapModalCounselorLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to unassign this optional subject: <br><strong id="optionalMapInfoToDeleteCounselor"></strong>?
        <p class="text-danger small mt-2">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteOptionalMapFormCounselor" method="get" action=""> {{-- Action will be set by JS --}}
            <button type="submit" class="btn btn-danger">Yes, Unassign Subject</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteOptionalMapModalCounselor');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var assignmentId = button.getAttribute('data-assignment-id');
            var assignmentInfo = button.getAttribute('data-assignment-info');

            var modalBodyStrong = deleteModal.querySelector('#optionalMapInfoToDeleteCounselor');
            var deleteForm = deleteModal.querySelector('#deleteOptionalMapFormCounselor');

            modalBodyStrong.innerHTML = assignmentInfo; // Use innerHTML if info might contain <br> or other HTML

            // For your current GET route: '{{ url("delete_optional") }}/' + assignmentId;
            deleteForm.action = 'delete_optional/' + assignmentId; // Using your current GET route

            // If using GET, remove method spoofing and set form method to GET
            const methodInput = deleteForm.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }
            deleteForm.method = 'GET';
        });
    }
});
</script>
