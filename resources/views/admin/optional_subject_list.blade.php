@extends('admin.layout')

@section('title', 'Student Optional Subject Mappings')

@section('optional_subject_list')

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Student Optional Subject Assignments</h1>
    <div>
        <a href="{{ url('optionalgroup_admin') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill me-2"></i>Assign Optional Subjects
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
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <i class="bi bi-card-checklist me-2"></i>Assigned Optional Subjects List
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="card-body border-bottom">
            <form action="{{ '/optional_subject_list_admin' }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="enrollment_search_filter" class="form-label">Search by Enrollment or Name:</label>
                    <input type="text" class="form-control form-control-sm" name="enrollment_search" id="enrollment_search_filter" placeholder="Enter Enrollment No. or Student Name" value="{{ request('enrollment_search') }}">
                </div>
                <div class="col-md-3">
                    <label for="class_filter" class="form-label">Filter by Class:</label>
                    <select name="class_filter" id="class_filter" class="form-select form-select-sm">
                        <option value="">All Classes</option>
                        @if(isset($classes_for_filter))
                            @foreach($classes_for_filter as $class_option)
                                <option value="{{ $class_option->id }}" {{ request('class_filter') == $class_option->id ? 'selected' : '' }}>
                                    {{ $class_option->program->name ?? 'N/A Program' }} / Batch: {{ $class_option->year ?? 'N/A' }} / Sem: {{ $class_option->sem ?? 'N/A' }} / Div: {{ $class_option->devision ?? 'N/A' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
              
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-funnel-fill me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Main Content: Table or No Data Message --}}
        <div class="card-body p-0">
            @if(isset($datas) && $datashow==1)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" id="enrollmentHeader" style="cursor:pointer;">Student Enrollment <span class="sort-indicator ms-1">⇵</span></th>
                                <th scope="col">Student Name</th>
                                <th scope="col">Optional Subject Code</th>
                                <th scope="col">Optional Subject Name</th>
                                <th scope="col">Student's Class</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datas as $index => $assignment)
                            <tr>
                                <td>{{ $index + 1 + ($datas instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($datas->currentPage() - 1) * $datas->perPage() : 0) }}</td>
                                <td>{{ $assignment->student->enrollment_number ?? 'N/A' }}</td>
                                <td>{{ $assignment->student->name ?? 'N/A' }}</td>
                                <td>{{ $assignment->subject->subject_code ?? 'N/A' }}</td>
                                <td>{{ $assignment->subject->subject_name ?? 'N/A' }}</td>
                                <td>
                                    @if($assignment->student && $assignment->student->class)
                                        {{ $assignment->student->class->program->name ?? 'N/A Program' }} /
                                        Sem {{ $assignment->student->class->sem ?? 'N/A' }} /
                                        Div {{ $assignment->student->class->devision ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Assignment"
                                            data-bs-toggle="modal" data-bs-target="#deleteOptionalAssignmentModal"
                                            data-assignment-id="{{ $assignment->id }}"
                                            data-assignment-info="Subject: {{ $assignment->subject->subject_name ?? 'N/A' }} for Student: {{ $assignment->student->name ?? 'N/A' }} ({{ $assignment->student->enrollment_number ?? '' }})">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- DIV FOR CLIENT-SIDE NO RESULTS MESSAGE (shown when client search on existing table data finds nothing) --}}
                <div id="noClientDataMessage" class="text-center p-4" style="display: none;">
                    <i class="bi bi-search display-4 text-muted mb-3"></i>
                    <p class="text-muted mb-0">No assignments found matching .</p>
                </div>
            @endif
        </div>
        @if($datashow==0)
        <p class="text-muted mb-0">No students with optional subject assignments found matching your current filters.</p>
        <p class="small text-muted mt-2">Try adjusting your filters or <a href="{{ url(request()->path()) }}">clear filters</a>.</p>
        @endif
        @if(isset($datas) && $datas instanceof \Illuminate\Pagination\LengthAwarePaginator && $datas->hasPages())
        <div class="card-footer bg-light border-top-0">
            {{ $datas->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Optional Assignment Confirmation Modal -->
<div class="modal fade" id="deleteOptionalAssignmentModal" tabindex="-1" aria-labelledby="deleteOptionalAssignmentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteOptionalAssignmentModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this optional subject assignment: <br><strong id="optionalAssignmentInfoToDelete"></strong>?
        <p class="text-danger small mt-2">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteOptionalAssignmentForm" method="get" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Assignment</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Existing Modal Script
    var deleteModal = document.getElementById('deleteOptionalAssignmentModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var assignmentId = button.getAttribute('data-assignment-id');
            var assignmentInfo = button.getAttribute('data-assignment-info');

            var modalBodyStrong = deleteModal.querySelector('#optionalAssignmentInfoToDelete');
            var deleteForm = deleteModal.querySelector('#deleteOptionalAssignmentForm');

            modalBodyStrong.innerHTML = assignmentInfo;
            deleteForm.action = '{{ url("delete_optional_admin") }}/' + assignmentId;
            
            const methodInput = deleteForm.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }
            deleteForm.method = 'GET';
        });
    }

    // Table Sorting Script for "Student Enrollment"
    const enrollmentHeader = document.getElementById('enrollmentHeader');
    const tableBody = document.querySelector('.table-responsive table tbody');

    if (enrollmentHeader && tableBody) { // Check if tableBody exists
        enrollmentHeader.addEventListener('click', function() {
            let currentSortOrder = this.dataset.sortOrder || 'none';

            if (currentSortOrder === 'asc') {
                currentSortOrder = 'desc';
            } else {
                currentSortOrder = 'asc';
            }
            this.dataset.sortOrder = currentSortOrder;

            updateSortIndicatorEnrollment(this, currentSortOrder);

            const rows = Array.from(tableBody.querySelectorAll('tr'));
            if (rows.length === 0) return;

            rows.sort((rowA, rowB) => {
                const enrollA = rowA.cells[1].textContent.trim();
                const enrollB = rowB.cells[1].textContent.trim();
                const comparison = enrollA.localeCompare(enrollB, undefined, { numeric: true, sensitivity: 'base' });
                return currentSortOrder === 'asc' ? comparison : -comparison;
            });

            rows.forEach(row => tableBody.appendChild(row));

            // Re-number the '#' column for VISIBLE rows after sorting
            let visibleSortedIndex = 0;
            rows.forEach((row) => {
                if (row.style.display !== 'none') { // Only re-number visible rows
                    visibleSortedIndex++;
                    row.cells[0].textContent = visibleSortedIndex;
                }
            });
        });
    }

    function updateSortIndicatorEnrollment(headerEl, order) {
        const indicatorSpan = headerEl.querySelector('.sort-indicator');
        if (indicatorSpan) {
            if (order === 'asc') {
                indicatorSpan.innerHTML = '▲';
            } else if (order === 'desc') {
                indicatorSpan.innerHTML = '▼';
            } else {
                indicatorSpan.innerHTML = '⇵';
            }
        }
    }

    // Client-Side Search Script
    const searchInput = document.getElementById('enrollment_search_filter');
    const tableRowsNodeList = tableBody ? tableBody.querySelectorAll('tr') : []; // Get rows only if tableBody exists
    const tableResponsiveDiv = document.querySelector('.table-responsive');
    const noClientDataMessageDiv = document.getElementById('noClientDataMessage');
    const paginationFooter = document.querySelector('.card-footer.bg-light.border-top-0'); // More specific selector

    // Check if there are rows to search
    if (searchInput && tableRowsNodeList.length > 0) {
        const tableRowsArray = Array.from(tableRowsNodeList);

        const isPaginatedJS = {{ (isset($datas) && $datas instanceof \Illuminate\Pagination\LengthAwarePaginator) ? 'true' : 'false' }};
        const currentPageJS = isPaginatedJS ? {{ (isset($datas) && $datas instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $datas->currentPage() : 1 }} : 1;
        const perPageJS = isPaginatedJS ? {{ (isset($datas) && $datas instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $datas->perPage() : 0 }} : 0;
        const baseIndexForPaginated = isPaginatedJS ? (currentPageJS - 1) * perPageJS : 0;

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleRowCount = 0;

            tableRowsArray.forEach(row => {
                const enrollmentCell = row.cells[1];
                const nameCell = row.cells[2];
                const enrollmentText = enrollmentCell ? enrollmentCell.textContent.toLowerCase() : '';
                const nameText = nameCell ? nameCell.textContent.toLowerCase() : '';

                if (enrollmentText.includes(searchTerm) || nameText.includes(searchTerm)) {
                    row.style.display = '';
                    visibleRowCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            let currentVisibleIndexForSearch = 0;
            tableRowsArray.forEach((row, originalIndexOnPage) => {
                if (searchTerm === '') {
                    row.style.display = ''; 
                    row.cells[0].textContent = baseIndexForPaginated + originalIndexOnPage + 1;
                } else {
                    if (row.style.display !== 'none') {
                        currentVisibleIndexForSearch++;
                        row.cells[0].textContent = currentVisibleIndexForSearch;
                    }
                }
            });
            
            if (searchTerm === '') {
                visibleRowCount = tableRowsArray.length;
            }

            if (tableResponsiveDiv) {
                tableResponsiveDiv.style.display = visibleRowCount > 0 ? '' : 'none';
            }
            if (paginationFooter) {
                paginationFooter.style.display = visibleRowCount > 0 ? '' : 'none';
            }
            if (noClientDataMessageDiv) {
                noClientDataMessageDiv.style.display = visibleRowCount === 0 && searchTerm !== '' ? '' : 'none';
            }
        });
    }
});
</script>
