@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'My Subjects List')
@section('page_title', 'Subjects in My Classes')

@section('subject_table') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <p class="text-muted mb-0">Showing subjects associated with classes you counsel.</p>
    </div>
    <a href="{{ url('add_subject') }}" class="btn btn-primary"> {{-- Assuming this is the counselor's route for adding subjects --}}
        <i class="bi bi-plus-circle-fill me-2"></i>Add New Subject
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
                <i class="bi bi-journals me-2"></i>Subjects List
            </div>
            <form action="{{ url('subject_list_filter') }}" method="get" class="d-flex align-items-center ms-auto">
                <label for="filter_class_program" class="form-label me-2 mb-0 visually-hidden">Filter by Program/Class:</label>
                <select name="field" id="filter_class_program" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 250px;">
                    <option value="all" {{ ($select ?? 'all') == 'all' ? 'selected' : '' }}>All My Classes/Programs</option>
                    @if(isset($programs) && count($programs) > 0 && isset(Auth::user()->id)) {{-- Assuming $programs is passed from controller --}}
                        @foreach($programs as $class_filter_option)
                            {{-- This assumes $programs contains class objects the counselor is assigned to --}}
                            @if($class_filter_option->coundelor_id == Auth::user()->id) {{-- Typo 'coundelor_id' kept --}}
                                <option value="{{ $class_filter_option->program->program_id }}_{{ $class_filter_option->id }}" {{ ($select ?? '') == ($class_filter_option->program->program_id . '_' . $class_filter_option->id) ? 'selected' : '' }}>
                                    {{ $class_filter_option->program->name ?? 'N/A Program' }} / Div: {{ $class_filter_option->devision ?? 'N/A' }} (Sem: {{ $class_filter_option->sem ?? 'N/A' }}, Batch: {{ $class_filter_option->year ?? 'N/A' }})
                                </option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            @php
                $counselorSubjects = [];
                if(isset($subjects) && isset(Auth::user()->id)) {
                    foreach($subjects as $subject) {
                        // Check if subject is associated with a class counseled by the current user
                        // This relies on $subject->student_class relationship
                        if(isset($subject->student_class) && $subject->student_class->coundelor_id == Auth::user()->id) {
                            // Further filter if a specific class/program filter is applied via $select
                            if (($select ?? 'all') == 'all') {
                                $counselorSubjects[] = $subject;
                            } elseif (isset($subject->student_class->program)) {
                                $filter_value_parts = explode('_', $select);
                                $selected_program_id = $filter_value_parts[0] ?? null;
                                $selected_class_id_from_filter = $filter_value_parts[1] ?? null; // If filter value includes class_id

                                // Match by program_id and optionally by class_id if the filter is that specific
                                if ($subject->student_class->program->program_id == $selected_program_id) {
                                    if ($selected_class_id_from_filter && $subject->student_class->id == $selected_class_id_from_filter) {
                                        $counselorSubjects[] = $subject;
                                    } elseif (!$selected_class_id_from_filter) { // If filter is just by program_id
                                        $counselorSubjects[] = $subject;
                                    }
                                }
                            }
                        }
                    }
                }
            @endphp

            @if(count($counselorSubjects) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Subject Name</th>
                            <th scope="col">Short Name</th>
                            <th scope="col">Code</th>
                            <th scope="col">Category</th>
                            <th scope="col">Type (L/P)</th>
                            <th scope="col">Associated Class Details</th>
                            <th scope="col">Last Updated</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($counselorSubjects as $index => $subject)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $subject->subject_name }}</td>
                            <td>{{ $subject->short_name ?? 'N/A' }}</td>
                            <td>{{ $subject->subject_code }}</td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($subject->category ?? 'N/A') }}</span></td>
                            <td>
                                @if(isset($subject->lecture_category))
                                    @if($subject->lecture_category == 'T')
                                        <span class="badge bg-primary">Theory (T)</span>
                                    @elseif($subject->lecture_category == 'P')
                                        <span class="badge bg-success">Practical (P)</span>
                                    @else
                                        {{ $subject->lecture_category }}
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($subject->student_class && $subject->student_class->program)
                                    {{ $subject->student_class->program->name }} /
                                    Batch: {{ $subject->student_class->year ?? 'N/A' }} /
                                    Sem: {{ $subject->student_class->sem ?? 'N/A' }} /
                                    Div: {{ $subject->student_class->devision ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $subject->updated_at ? $subject->updated_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td class="text-center">
                                <a href="{{ url('/edit_subject/'.$subject->subject_id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Subject">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Subject"
                                        data-bs-toggle="modal" data-bs-target="#deleteSubjectModalCounselor"
                                        data-subject-id="{{ $subject->subject_id }}" data-subject-name="{{ $subject->subject_name }}">
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
                <i class="bi bi-journal-x display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">
                    @if(($select ?? 'all') != 'all')
                        No subjects found for the selected filter in your classes.
                    @else
                        No subjects found in the classes you counsel.
                    @endif
                </p>
                <p class="small text-muted mt-2">Ensure subjects are assigned to your classes, or try a different filter.</p>
            </div>
            @endif
        </div>
        {{-- Pagination: See note below --}}
    </div>
</div>

<!-- Delete Subject Confirmation Modal -->
<div class="modal fade" id="deleteSubjectModalCounselor" tabindex="-1" aria-labelledby="deleteSubjectModalCounselorLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteSubjectModalCounselorLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the subject: <strong id="subjectNameToDeleteCounselor"></strong>?
        <p class="text-danger small mt-2">This action cannot be undone and might affect class schedules or student records.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteSubjectFormCounselor" method="get" action=""> {{-- Action will be set by JS --}}
            <button type="submit" class="btn btn-danger">Yes, Delete Subject</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteSubjectModalCounselor');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var subjectId = button.getAttribute('data-subject-id');
            var subjectName = button.getAttribute('data-subject-name');

            var modalBodyStrong = deleteModal.querySelector('#subjectNameToDeleteCounselor');
            var deleteForm = deleteModal.querySelector('#deleteSubjectFormCounselor');

            modalBodyStrong.textContent = subjectName;

            // For your current GET route: '{{ url("delete_subject") }}/' + subjectId;
            deleteForm.action = 'delete_subject/' + subjectId; // Using your current GET route

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
