@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'My Teacher-Subject Assignments')
@section('page_title', 'Teacher-Subject Assignments in My Classes')

@section('subject_table') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <p class="text-muted mb-0">Showing subject assignments for classes you counsel.</p>
    </div>
    <a href="{{ url('subjectallocated') }}" class="btn btn-primary"> {{-- Assuming this is the counselor's route for assigning subjects --}}
        <i class="bi bi-link-45deg me-2"></i>Assign Subject to Teacher
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
                <i class="bi bi-person-check-fill me-2"></i>Assigned Subjects List
            </div>
            <form action="{{ url('teachingstaff_list_filter') }}" method="get" class="d-flex align-items-center ms-auto"> {{-- Use url() helper --}}
                <label for="filter_teacher_counselor" class="form-label me-2 mb-0 visually-hidden">Filter by Teacher:</label>
                <select name="teacher" id="filter_teacher_counselor" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 220px;">
                    <option value="all" {{ ($select ?? 'all') == 'all' ? 'selected' : '' }}>All Teachers in My Classes</option>
                    @if(isset($teacher) && $teacher->count() > 0) {{-- Pass $teacher from controller --}}
                        @foreach($teacher as $teacher_item)
                            @if($teacher_item->role != 'admin' && $teacher_item->role != 'student') {{-- Keep this role filter if needed --}}
                            <option value="{{ $teacher_item->id }}" {{ ($select ?? '') == $teacher_item->id ? 'selected' : '' }}>{{ $teacher_item->name }} ({{ $teacher_item->short_name ?? '' }})</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            @php
                $counselorAssignments = [];
                if(isset($teachingstaffs) && isset(Auth::user()->id)) {
                    foreach($teachingstaffs as $assignment) {
                        // Filter by counselor's class
                        if(isset($assignment->class_instance) && $assignment->class_instance->coundelor_id == Auth::user()->id) { // Using class_instance from assignment
                             // Further filter by selected teacher if $select is not 'all'
                            if (($select ?? 'all') == 'all' || (isset($assignment->teacher) && $assignment->teacher->id == $select)) {
                                $counselorAssignments[] = $assignment;
                            }
                        }
                        // Fallback if class_instance is not on assignment, but on subject (less ideal for specific assignments)
                        elseif (isset($assignment->subject->student_class) && $assignment->subject->student_class->coundelor_id == Auth::user()->id) {
                            if (($select ?? 'all') == 'all' || (isset($assignment->teacher) && $assignment->teacher->id == $select)) {
                                $counselorAssignments[] = $assignment;
                            }
                        }
                    }
                }
            @endphp

            @if(count($counselorAssignments) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Subject Name</th>
                            <th scope="col">Subject Code</th>
                            <th scope="col">Teacher Name</th>
                            <th scope="col">Teacher Email</th>
                            <th scope="col">Assigned Class (Counselor's Class)</th>
                            <th scope="col">Assignment Updated</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($counselorAssignments as $index => $assignment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $assignment->subject->subject_name ?? 'N/A' }}</td>
                            <td>{{ $assignment->subject->subject_code ?? 'N/A' }}</td>
                            <td>{{ $assignment->teacher->name ?? 'N/A' }} ({{ $assignment->teacher->short_name ?? '' }})</td>
                            <td>{{ $assignment->teacher->email ?? 'N/A' }}</td>
                            <td>
                                {{-- This assumes the $assignment object has a direct 'class_instance' relationship
                                     representing the specific class this subject is assigned to for this teacher. --}}
                                @if($assignment->class_instance && $assignment->class_instance->program)
                                    {{ $assignment->class_instance->program->name }} /
                                    Sem {{ $assignment->class_instance->sem ?? 'N/A' }} /
                                    Div {{ $assignment->class_instance->devision ?? 'N/A' }}
                                    (Batch: {{ $assignment->class_instance->year ?? 'N/A' }})
                                @elseif($assignment->subject->student_class && $assignment->subject->student_class->program)
                                {{-- Fallback to subject's primary class --}}
                                    {{ $assignment->subject->student_class->program->name }} /
                                    Sem {{ $assignment->subject->student_class->sem ?? 'N/A' }} /
                                    Div {{ $assignment->subject->student_class->devision ?? 'N/A' }}
                                    (Batch: {{ $assignment->subject->student_class->year ?? 'N/A' }})
                                @else
                                    Class context not specified
                                @endif
                            </td>
                            <td>{{ $assignment->updated_at ? $assignment->updated_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Assignment"
                                        data-bs-toggle="modal" data-bs-target="#deleteStaffAssignmentModalCounselor"
                                        data-assignment-id="{{ $assignment->id }}"
                                        data-assignment-info="Subject: {{ $assignment->subject->subject_name ?? 'N/A' }} from Teacher: {{ $assignment->teacher->name ?? 'N/A' }}">
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
                <i class="bi bi-person-video3 display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">
                    @if(($select ?? 'all') != 'all')
                        No subject assignments found for the selected teacher in your classes.
                    @else
                        No subject assignments found for the classes you counsel.
                    @endif
                </p>
                 <p class="small text-muted mt-2">You can <a href="{{ url('subjectallocated') }}">assign subjects to teachers</a> for your classes.</p>
            </div>
            @endif
        </div>
        {{-- Pagination: See note in student list for client-side filtered lists --}}
    </div>
</div>

<!-- Delete Staff Assignment Confirmation Modal -->
<div class="modal fade" id="deleteStaffAssignmentModalCounselor" tabindex="-1" aria-labelledby="deleteStaffAssignmentModalCounselorLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteStaffAssignmentModalCounselorLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to remove this subject assignment: <br><strong id="staffAssignmentInfoToDeleteCounselor"></strong>?
        <p class="text-danger small mt-2">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteStaffAssignmentFormCounselor" method="get" action=""> {{-- Action will be set by JS --}}
           
            <button type="submit" class="btn btn-danger">Yes, Remove Assignment</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteStaffAssignmentModalCounselor');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var assignmentId = button.getAttribute('data-assignment-id');
            var assignmentInfo = button.getAttribute('data-assignment-info');

            var modalBodyStrong = deleteModal.querySelector('#staffAssignmentInfoToDeleteCounselor');
            var deleteForm = deleteModal.querySelector('#deleteStaffAssignmentFormCounselor');

            modalBodyStrong.innerHTML = assignmentInfo; // Use innerHTML if info contains line breaks

            // For your current GET route: '{{ url("delete_staff") }}/' + assignmentId;
            deleteForm.action = 'delete_staff/' + assignmentId; // Using your current GET route

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
