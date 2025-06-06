@extends('admin.layout')

@section('title', 'Subject List')
{{-- @section('page_title', 'Manage Subjects') --}}

@section('subject_table')

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Manage Subjects</h1>
    <div>
        <a href="{{ url('add_subject_admin') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill me-2"></i>Add New Subject
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
            <i class="bi bi-journals me-2"></i>All Subjects
        </div>

        {{-- Filters Section --}}
        <div class="card-body border-bottom">
            {{-- Client-side Search (applied to current page data) --}}
            <div class="row">
                <div class="col-md-12 mb-3">
                     <label for="subject_search_client" class="form-label">Search by Subject Name or Code (on this page):</label>
                     <input type="text" id="subject_search_client" class="form-control form-control-sm" placeholder="Enter Subject Name or Code...">
                </div>
            </div>

            {{-- Server-side Filters Form (reloads page) --}}
            <form action="{{ url('subject_list_filter_admin') }}" method="get" class="row g-3 align-items-end pt-3 border-top">
            <div class="col-md-4">
                    <label for="filter_class" class="form-label">Filter by Class:</label>
                    <select name="class_filter" id="filter_class" class="form-select form-select-sm">
                        <option value="all">All Classes</option>
                        {{-- Ensure $programs is passed from your controller --}}
                        @if(isset($programs))
                            @foreach($programs as $class_item_filter)
                                <option value="{{ $class_item_filter->id }}" {{ request('class_filter') == $class_item_filter->id ? 'selected' : '' }}>
                                    {{ $class_item_filter->program->name ?? 'N/A Program' }} / Sem {{ $class_item_filter->sem ?? 'N/A' }} / Div {{ $class_item_filter->devision ?? 'N/A' }} (Year: {{ $class_item_filter->year ?? 'N/A' }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>    
                @if(isset($classes_for_filter_dropdown))
            <div class="col-md-4">
                    <label for="filter_category" class="form-label">Filter by Category:</label>
                    <select name="field" id="filter_category" class="form-select form-select-sm">
                        <option value="all" {{ request('field', 'all') == 'all' ? 'selected' : '' }}>All Categories</option>
                        <option value="required" {{ request('field') == 'required' ? 'selected' : '' }}>Required</option>
                        <option value="optional" {{ request('field') == 'optional' ? 'selected' : '' }}>Optional</option>
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
            @if(isset($subjects) && $subjects->count() > 0)
            <div class="table-responsive" id="subjectsTableContainer"> {{-- Added ID for JS targeting --}}
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Subject Name</th>
                            <th scope="col">Short Name</th>
                            <th scope="col">Code</th>
                            <th scope="col">Category</th>
                            <th scope="col">Type (L/P)</th>
                            <th scope="col">Associated Classes</th>
                            <th scope="col">Last Updated</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $index => $subject)
                        <tr>
                            <td>{{ $index + 1 + ($subjects instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($subjects->currentPage() - 1) * $subjects->perPage() : 0) }}</td>
                            <td>{{ $subject->subject_name }}</td>
                            <td>{{ $subject->short_name ?? 'N/A' }}</td>
                            <td>{{ $subject->subject_code }}</td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($subject->category) }}</span></td>
                            <td>
                                @if($subject->lecture_category == 'T')
                                    <span class="badge bg-primary">Theory (T)</span>
                                @elseif($subject->lecture_category == 'P')
                                    <span class="badge bg-success">Practical (P)</span>
                                @else
                                    {{ $subject->lecture_category ?? 'N/A' }} {{-- Corrected from l_category --}}
                                @endif
                            </td>
                            <td>
                                @if($subject->classes && $subject->classes->count() > 0)
                                    @foreach($subject->classes as $class_item)
                                        <span class="badge bg-light text-dark mb-1 d-block text-start"> {{-- Added text-start for better alignment if text wraps --}}
                                            {{ $class_item->program->name ?? 'N/A' }} / Sem {{ $class_item->sem ?? 'N/A' }} / Div {{ $class_item->devision ?? 'N/A' }} ({{ $class_item->year ?? 'N/A' }})
                                        </span>
                                    @endforeach
                                @elseif($subject->student_class) {{-- Fallback --}}
                                    <span class="badge bg-light text-dark">
                                    {{ $subject->student_class->program->name ?? 'N/A Program' }} /
                                    {{ $subject->student_class->year ?? 'N/A Batch' }} /
                                    Sem {{ $subject->student_class->sem ?? 'N/A' }} /
                                    Div {{ $subject->student_class->devision ?? 'N/A' }}
                                    </span>
                                @else
                                    Not Assigned
                                @endif
                            </td>
                            <td>{{ $subject->updated_at ? $subject->updated_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td class="text-center">
                                <a href="{{ url('/edit_subject_admin/'.$subject->subject_id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Subject">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Subject"
                                        data-bs-toggle="modal" data-bs-target="#deleteSubjectModal"
                                        data-subject-id="{{ $subject->subject_id }}" data-subject-name="{{ $subject->subject_name }}">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- DIV FOR CLIENT-SIDE NO RESULTS MESSAGE --}}
            <div id="noClientSubjectDataMessage" class="text-center p-4" style="display: none;">
                <i class="bi bi-search display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">No subjects found matching your current page search.</p>
            </div>
            @else
            <div class="text-center p-4" id="noServerSubjectDataMessage"> {{-- Added ID --}}
                <i class="bi bi-journal-x display-4 text-muted mb-3"></i>
                @php
                    $serverFiltersApplied = (request('field', 'all') !== 'all') || !empty(request('class_filter'));
                @endphp
                @if($serverFiltersApplied)
                    <p class="text-muted mb-0">No subjects found matching your current filter criteria.</p>
                    <p class="small text-muted mt-2">Try adjusting your filters or <a href="{{ url(request()->path()) }}">clear all filters</a>.</p>
                @else
                    <p class="text-muted mb-0">No subjects found in the system.</p>
                    <p class="small text-muted mt-2">You can <a href="{{ url('add_subject_admin') }}">add subjects</a> to populate this list.</p>
                @endif
            </div>
            @endif
        </div>
        @if(isset($subjects) && $subjects instanceof \Illuminate\Pagination\LengthAwarePaginator && $subjects->hasPages())
        <div class="card-footer bg-light border-top-0" id="subjectPaginationFooter"> {{-- Added ID --}}
            {{ $subjects->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Subject Confirmation Modal -->
<div class="modal fade" id="deleteSubjectModal" tabindex="-1" aria-labelledby="deleteSubjectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteSubjectModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the subject: <strong id="subjectNameToDelete"></strong>?
        <p class="text-danger small mt-2">This action cannot be undone and might affect class schedules or student records associated with this subject.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteSubjectForm" method="get" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Subject</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    // Delete Subject Modal Script
    var deleteSubjectModal = document.getElementById('deleteSubjectModal');
    if (deleteSubjectModal) {
        deleteSubjectModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var subjectId = button.getAttribute('data-subject-id');
            var subjectName = button.getAttribute('data-subject-name');

            var modalBodyStrong = deleteSubjectModal.querySelector('#subjectNameToDelete');
            var deleteForm = deleteSubjectModal.querySelector('#deleteSubjectForm');

            modalBodyStrong.textContent = subjectName;
            deleteForm.action = '{{ url("delete_subject_admin") }}/' + subjectId;

            const methodInput = deleteForm.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }
            deleteForm.method = 'GET';
        });
    }

    // Client-Side Search for Subjects
    const clientSearchInput = document.getElementById('subject_search_client');
    const tableContainer = document.getElementById('subjectsTableContainer');
    const tableBody = tableContainer ? tableContainer.querySelector('table tbody') : null;
    const noClientDataMsg = document.getElementById('noClientSubjectDataMessage');
    const paginationFooter = document.getElementById('subjectPaginationFooter');
    
    // Check if tableBody actually exists before proceeding
    if (clientSearchInput && tableBody) {
        const tableRowsNodeList = tableBody.querySelectorAll('tr');
        if (tableRowsNodeList.length > 0) { // Only add listener if there are rows
            const tableRowsArray = Array.from(tableRowsNodeList);

            const isPaginatedJS = {{ (isset($subjects) && $subjects instanceof \Illuminate\Pagination\LengthAwarePaginator) ? 'true' : 'false' }};
            const currentPageJS = isPaginatedJS ? {{ (isset($subjects) && $subjects instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $subjects->currentPage() : 1 }} : 1;
            const perPageJS = isPaginatedJS ? {{ (isset($subjects) && $subjects instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $subjects->perPage() : 0 }} : 0;
            const baseIndexForPaginated = isPaginatedJS ? (currentPageJS - 1) * perPageJS : 0;

            clientSearchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleRowCount = 0;

                tableRowsArray.forEach(row => {
                    const subjectNameCell = row.cells[1];
                    const subjectCodeCell = row.cells[3];
                    
                    const subjectNameText = subjectNameCell ? subjectNameCell.textContent.toLowerCase() : '';
                    const subjectCodeText = subjectCodeCell ? subjectCodeCell.textContent.toLowerCase() : '';

                    if (subjectNameText.includes(searchTerm) || subjectCodeText.includes(searchTerm)) {
                        row.style.display = '';
                        visibleRowCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Re-number the '#' column for visible rows or restore original
                let currentVisibleIndex = 0;
                tableRowsArray.forEach((row, originalIndexOnPage) => {
                    if (searchTerm === '') { // Search cleared
                        row.style.display = ''; // Make sure it's visible
                        row.cells[0].textContent = baseIndexForPaginated + originalIndexOnPage + 1;
                    } else { // Active search
                        if (row.style.display !== 'none') {
                            currentVisibleIndex++;
                            row.cells[0].textContent = currentVisibleIndex;
                        }
                    }
                });
                
                if (searchTerm === '') { // If search is cleared, all rows on page are "visible"
                    visibleRowCount = tableRowsArray.length;
                }

                // Manage visibility of table, "no client data" message, and pagination
                if (tableContainer) {
                    tableContainer.style.display = visibleRowCount > 0 ? '' : 'none';
                }
                if (noClientDataMsg) {
                    noClientDataMsg.style.display = visibleRowCount === 0 && searchTerm !== '' ? '' : 'none';
                }
                if (paginationFooter) {
                    // Show pagination if client search has results OR if search term is empty (showing original page data)
                    paginationFooter.style.display = visibleRowCount > 0 ? '' : 'none';
                }
            });
        }
    } else if (clientSearchInput && !tableBody) {
        // If there's a search input but no table body (e.g. server returned no data at all),
        // you might want to disable the input or provide a different UX.
        // For now, it simply won't attach the event listener if tableBody is null.
        // console.log("Client search input present, but no table body to search.");
    }
});
</script>
