@extends('admin.layout')

@section('title', 'Teacher-Subject Assignments List')
{{-- @section('page_title', 'View Teacher-Subject Assignments') --}}

@section('subject_table') {{-- Keeping this section name as per your usage --}}

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Teacher-Subject Assignments</h1>
    <div>
        <a href="{{ url('subjectallocated_admin') }}" class="btn btn-primary">
            <i class="bi bi-link-45deg me-2"></i>Assign Subject to Teacher
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
        <div class="card-header">
            <i class="bi bi-person-check-fill me-2"></i>Assigned Subjects List
        </div>

        {{-- Filters Section --}}
        <div class="card-body border-bottom">
            {{-- Client-side Search (applied to current page data) --}}
            <div class="row">
                <div class="col-md-12 mb-3">
                     <label for="assignment_search_client" class="form-label">Search by Subject or Teacher (on this page):</label>
                     <input type="text" id="assignment_search_client" class="form-control form-control-sm" placeholder="Enter Subject Name/Code or Teacher Name...">
                </div>
            </div>

            {{-- Server-side Filters Form (reloads page) --}}
            {{-- Assuming your filter route is 'teachingstaff_list_filter_admin' or similar --}}
            <form action="{{ '/teachingstaff_list_filter_admin' }}" method="get" class="row g-3 align-items-end pt-3 border-top">
            <div class="col-md-4">
                    <label for="filter_class" class="form-label">Filter by Class:</label>
                    <select name="class_filter" id="filter_class" class="form-select form-select-sm">
                        <option value="">All Classes</option>
                        {{-- Ensure $classes_for_filter_dropdown is passed from your controller --}}
                        @if(isset($classes_for_filter_dropdown))
                            @foreach($classes_for_filter_dropdown as $class_item_filter)
                                <option value="{{ $class_item_filter->id }}" {{ request('class_filter') == $class_item_filter->id ? 'selected' : '' }}>
                                    {{ $class_item_filter->program->name ?? 'N/A Program' }} / Sem {{ $class_item_filter->sem ?? 'N/A' }} / Div {{ $class_item_filter->devision ?? 'N/A' }} (Year: {{ $class_item_filter->year ?? 'N/A' }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                @if(isset($teacher))
                <div class="col-md-4">
                    <label for="filter_teacher" class="form-label">Filter by Teacher:</label>
                    <select name="teacher_filter" id="filter_teacher" class="form-select form-select-sm">
                        <option value="">All Teachers</option>
                        {{-- Ensure $teachers_for_filter is passed from your controller --}}
                        @if(isset($teacher))
                            @foreach($teacher as $teacher_item)
                                @if($teacher_item->role != 'admin' && $teacher_item->role != 'student') {{-- Your existing condition --}}
                                <option value="{{ $teacher_item->id }}" {{ request('teacher_filter') == $teacher_item->id ? 'selected' : '' }}>
                                    {{ $teacher_item->name }} ({{ $teacher_item->short_name ?? '' }})
                                </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
                @endif
                <div class="col-md-4">
                    <button type="submit" class="btn btn-info btn-sm w-100">
                        <i class="bi bi-funnel-fill me-1"></i>Apply Filters & Reload
                    </button>
                </div>
            </form>
        </div>


        <div class="card-body p-0">
            @if(isset($teachingstaffs))
            <div class="table-responsive" id="assignmentsTableContainer"> {{-- Added ID for JS targeting --}}
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Subject Name</th>
                            <th scope="col">Subject Code</th>
                            <th scope="col">Teacher Name</th>
                            <th scope="col">Teacher Email</th>
                            <th scope="col">Assigned Class for Subject</th>
                            <th scope="col">Last Updated (Assignment)</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachingstaffs as $index => $assignment)
                        <tr>
                            <td>{{ $index + 1 + ($teachingstaffs instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($teachingstaffs->currentPage() - 1) * $teachingstaffs->perPage() : 0) }}</td>
                            <td>{{ $assignment->subject->subject_name ?? 'N/A' }}</td>
                            <td>{{ $assignment->subject->subject_code ?? 'N/A' }}</td>
                            <td>{{ $assignment->teacher->name ?? 'N/A' }} ({{ $assignment->teacher->short_name ?? '' }})</td>
                            <td>{{ $assignment->teacher->email ?? 'N/A' }}</td>
                            <td>
                                @if($assignment->class_instance)
                                    {{ $assignment->class_instance->program->name ?? 'N/A Program' }} /
                                    Sem {{ $assignment->class_instance->sem ?? 'N/A' }} /
                                    Div {{ $assignment->class_instance->devision ?? 'N/A' }}
                                    (Batch: {{ $assignment->class_instance->year ?? 'N/A' }})
                                @elseif($assignment->subject->student_class) {{-- Fallback to subject's primary class --}}
                                    {{ $assignment->subject->student_class->program->name ?? 'N/A Program' }} /
                                    Sem {{ $assignment->subject->student_class->sem ?? 'N/A' }} /
                                    Div {{ $assignment->subject->student_class->devision ?? 'N/A' }}
                                    (Batch: {{ $assignment->subject->student_class->year ?? 'N/A' }})
                                @else
                                    Class info not specified
                                @endif
                            </td>
                            <td>{{ $assignment->updated_at ? $assignment->updated_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Assignment"
                                        data-bs-toggle="modal" data-bs-target="#deleteAssignmentModal"
                                        data-assignment-id="{{ $assignment->id }}"
                                        data-assignment-info="Subject: {{ $assignment->subject->subject_name ?? 'N/A' }} to Teacher: {{ $assignment->teacher->name ?? 'N/A' }}">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- DIV FOR CLIENT-SIDE NO RESULTS MESSAGE --}}
            <div id="noClientAssignmentDataMessage" class="text-center p-4" style="display: none;">
                <i class="bi bi-search display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">No assignments found matching your current page search.</p>
            </div>
            @else
            <div class="text-center p-4" id="noServerAssignmentDataMessage"> {{-- Added ID --}}
                <i class="bi bi-person-video3 display-4 text-muted mb-3"></i>
                @php
                    $serverFiltersApplied = !empty(request('teacher_filter')) || !empty(request('class_filter'));
                @endphp
                @if($serverFiltersApplied)
                    <p class="text-muted mb-0">No subject assignments found matching your current filter criteria.</p>
                    <p class="small text-muted mt-2">Try adjusting your filters or <a href="{{ url(request()->path()) }}">clear all filters</a>.</p>
                @else
                    <p class="text-muted mb-0">No subject assignments found.</p>
                    <p class="small text-muted mt-2">You can <a href="{{ url('subjectallocated_admin') }}">assign subjects to teachers</a> to populate this list.</p>
                @endif
            </div>
            @endif
        </div>
        @if(isset($teachingstaffs) && $teachingstaffs instanceof \Illuminate\Pagination\LengthAwarePaginator && $teachingstaffs->hasPages())
        <div class="card-footer bg-light border-top-0" id="assignmentPaginationFooter"> {{-- Added ID --}}
            {{ $teachingstaffs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Assignment Confirmation Modal -->
<div class="modal fade" id="deleteAssignmentModal" tabindex="-1" aria-labelledby="deleteAssignmentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteAssignmentModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this assignment: <strong id="assignmentInfoToDelete"></strong>?
        <p class="text-danger small mt-2">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteAssignmentForm" method="get" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Assignment</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    // Delete Assignment Modal Script
    var deleteAssignmentModal = document.getElementById('deleteAssignmentModal');
    if (deleteAssignmentModal) {
        deleteAssignmentModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var assignmentId = button.getAttribute('data-assignment-id');
            var assignmentInfo = button.getAttribute('data-assignment-info');

            var modalBodyStrong = deleteAssignmentModal.querySelector('#assignmentInfoToDelete');
            var deleteForm = deleteAssignmentModal.querySelector('#deleteAssignmentForm');

            modalBodyStrong.textContent = assignmentInfo;
            deleteForm.action = '{{ url("delete_staff_admin") }}/' + assignmentId; // Your existing route

            const methodInput = deleteForm.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }
            deleteForm.method = 'GET';
        });
    }

    // Client-Side Search for Assignments (Subject or Teacher)
    const clientSearchInput = document.getElementById('assignment_search_client');
    const tableContainer = document.getElementById('assignmentsTableContainer');
    const tableBody = tableContainer ? tableContainer.querySelector('table tbody') : null;
    const noClientDataMsg = document.getElementById('noClientAssignmentDataMessage');
    const paginationFooter = document.getElementById('assignmentPaginationFooter');

    if (clientSearchInput && tableBody) {
        const tableRowsNodeList = tableBody.querySelectorAll('tr');
        if (tableRowsNodeList.length > 0) {
            const tableRowsArray = Array.from(tableRowsNodeList);

            const isPaginatedJS = {{ (isset($teachingstaffs) && $teachingstaffs instanceof \Illuminate\Pagination\LengthAwarePaginator) ? 'true' : 'false' }};
            const currentPageJS = isPaginatedJS ? {{ (isset($teachingstaffs) && $teachingstaffs instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $teachingstaffs->currentPage() : 1 }} : 1;
            const perPageJS = isPaginatedJS ? {{ (isset($teachingstaffs) && $teachingstaffs instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $teachingstaffs->perPage() : 0 }} : 0;
            const baseIndexForPaginated = isPaginatedJS ? (currentPageJS - 1) * perPageJS : 0;

            clientSearchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleRowCount = 0;

                tableRowsArray.forEach(row => {
                    const subjectNameCell = row.cells[1]; // Subject Name
                    const subjectCodeCell = row.cells[2]; // Subject Code
                    const teacherNameCell = row.cells[3]; // Teacher Name

                    const subjectNameText = subjectNameCell ? subjectNameCell.textContent.toLowerCase() : '';
                    const subjectCodeText = subjectCodeCell ? subjectCodeCell.textContent.toLowerCase() : '';
                    const teacherNameText = teacherNameCell ? teacherNameCell.textContent.toLowerCase() : '';

                    if (subjectNameText.includes(searchTerm) || 
                        subjectCodeText.includes(searchTerm) || 
                        teacherNameText.includes(searchTerm)) {
                        row.style.display = '';
                        visibleRowCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Re-number the '#' column
                let currentVisibleIndex = 0;
                tableRowsArray.forEach((row, originalIndexOnPage) => {
                    if (searchTerm === '') {
                        row.style.display = '';
                        row.cells[0].textContent = baseIndexForPaginated + originalIndexOnPage + 1;
                    } else {
                        if (row.style.display !== 'none') {
                            currentVisibleIndex++;
                            row.cells[0].textContent = currentVisibleIndex;
                        }
                    }
                });

                if (searchTerm === '') {
                    visibleRowCount = tableRowsArray.length;
                }

                if (tableContainer) {
                    tableContainer.style.display = visibleRowCount > 0 ? '' : 'none';
                }
                if (noClientDataMsg) {
                    noClientDataMsg.style.display = visibleRowCount === 0 && searchTerm !== '' ? '' : 'none';
                }
                if (paginationFooter) {
                    paginationFooter.style.display = visibleRowCount > 0 ? '' : 'none';
                }
            });
        }
    }
});
</script>
