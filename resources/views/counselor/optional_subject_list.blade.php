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

            {{-- START: NEW FILTER SECTION --}}
            <div class="d-flex align-items-center gap-2 ms-auto">
                {{-- New Server-side Subject Filter Form --}}
                <form action="{{ url('/filter_optionalsuject') }}" method="GET" class="d-flex">
                    <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Filter by Subject</option>
                        {{-- This requires a $subjects_for_filter variable from your controller --}}
                        @if(isset($subjects_for_filter))
                        @foreach($subjects_for_filter[0] as $subject)
                        <option value="{{ $subject['subject_id'] }}" 
                              {{ request('filter') == $subject['subject_id'] ? 'selected' : '' }}>
                                {{ $subject['subject_name'] }}
                        </option>
                        @endforeach

                        @endif
                    </select>
                </form>

                {{-- Existing Client-side Search Input --}}
                <div style="max-width: 250px;">
                    <input type="text" id="liveSearchInput" class="form-control form-control-sm" placeholder="Search results on this page...">
                </div>
            </div>
            {{-- END: NEW FILTER SECTION --}}

        </div>
        <div class="card-body p-0">
            @if(isset($datas))
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="assignmentsTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            {{-- Make Enrollment header sortable --}}
                            <th scope="col" id="enrollmentHeader" style="cursor: pointer;">
                                Enrollment <i class="bi bi-arrow-down-up small text-muted"></i>
                            </th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Optional Subject Code</th>
                            <th scope="col">Optional Subject Name</th>
                            <th scope="col">Subject's Class Context</th> {{-- Class context of the subject --}}
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="assignmentsTableBody">
                        @foreach($datas as $index => $assignment) {{-- Renamed $data to $assignment --}}
                        <tr>
                            <td>{{ $index + 1 + ($datas instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($datas->currentPage() - 1) * $datas->perPage() : 0) }}</td>
                            <td class="enrollment-cell">{{ $assignment->student->enrollment_number ?? 'N/A' }}</td>
                            <td class="name-cell">{{ $assignment->student->name ?? 'N/A' }}</td>
                            <td>{{ $assignment->subject->subject_code ?? 'N/A' }}</td>
                            <td class="subject-name-cell">{{ $assignment->subject->subject_name ?? 'N/A' }}</td>
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
            <div id="noResultsMessage" class="text-center p-4" style="display: none;">
                <p class="text-muted mb-0">No matching records found.</p>
            </div>
            @else
            <div class="text-center p-4">
                <i class="bi bi-ui-checks display-4 text-muted mb-3"></i>
                @if(request('subject_filter'))
                    <p class="text-muted mb-0">No assignments found for the selected subject.</p>
                    <p class="small text-muted mt-2">
                        <a href="{{ url()->current() }}">Clear Filter</a>
                    </p>
                @else
                    <p class="text-muted mb-0">No optional subject assignments found for students in your classes.</p>
                    <p class="small text-muted mt-2">You can <a href="{{ url('optionalgroup') }}">assign optional subjects to students</a> to populate this list.</p>
                @endif
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

<!-- Delete Optional Assignment Confirmation Modal (No changes here) -->
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


{{-- The existing script remains the same --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Original script for the delete modal
    const deleteModal = document.getElementById('deleteOptionalMapModalCounselor');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const assignmentId = button.getAttribute('data-assignment-id');
            const assignmentInfo = button.getAttribute('data-assignment-info');
            const modalBodyStrong = deleteModal.querySelector('#optionalMapInfoToDeleteCounselor');
            const deleteForm = deleteModal.querySelector('#deleteOptionalMapFormCounselor');

            modalBodyStrong.innerHTML = assignmentInfo;
            // Using your specified GET route
            deleteForm.action = '{{ url("delete_optional") }}/' + assignmentId;
            deleteForm.method = 'GET';
        });
    }

    // --- Script for Live Search and Sorting (No changes needed) ---

    const searchInput = document.getElementById('liveSearchInput');
    const tableBody = document.getElementById('assignmentsTableBody');
    const noResultsMessage = document.getElementById('noResultsMessage');
    const enrollmentHeader = document.getElementById('enrollmentHeader');
    
    if (tableBody) {
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        let sortDirection = 'none'; // 'asc', 'desc', 'none'

        // 1. Live Search Functionality
        searchInput.addEventListener('keyup', () => {
            const searchTerm = searchInput.value.toLowerCase();
            let visibleRows = 0;

            rows.forEach(row => {
                const enrollment = row.querySelector('.enrollment-cell')?.textContent.toLowerCase() || '';
                const name = row.querySelector('.name-cell')?.textContent.toLowerCase() || '';
                const subject = row.querySelector('.subject-name-cell')?.textContent.toLowerCase() || '';
                
                if (enrollment.includes(searchTerm) || name.includes(searchTerm) || subject.includes(searchTerm)) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show or hide the "no results" message
            noResultsMessage.style.display = visibleRows === 0 ? '' : 'none';
        });

        // 2. Sorting Functionality for Enrollment Number
        enrollmentHeader.addEventListener('click', () => {
            const headerIcon = enrollmentHeader.querySelector('i');
            if (sortDirection === 'asc') {
                sortDirection = 'desc';
                headerIcon.className = 'bi bi-arrow-down small';
            } else {
                sortDirection = 'asc';
                headerIcon.className = 'bi bi-arrow-up small';
            }
            const rowsToSort = Array.from(tableBody.querySelectorAll('tr'));
            rowsToSort.sort((a, b) => {
                const valA = a.querySelector('.enrollment-cell')?.textContent.trim() || '';
                const valB = b.querySelector('.enrollment-cell')?.textContent.trim() || '';
                const comparison = valA.localeCompare(valB, undefined, { numeric: true });
                return sortDirection === 'asc' ? comparison : -comparison;
            });
            rowsToSort.forEach(row => tableBody.appendChild(row));
        });
    }
});
</script>