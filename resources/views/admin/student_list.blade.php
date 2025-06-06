@extends('admin.layout')

@section('title', 'Student List')
{{-- @section('page_title', 'Manage Students') --}} {{-- Keeping this commented --}}

@section('student_table')

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Manage Students</h1>
    <div>
        <a href="{{ url('add_student_admin') }}" class="btn btn-primary"> {{-- Assuming 'add_student_admin' is the route for adding students (could be the bulk upload page or a manual add page) --}}
            <i class="bi bi-person-plus-fill me-2"></i>Add New Student(s)
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
                <i class="bi bi-people-fill me-2"></i>All Students
            </div>
            <form action="{{ url('search_student_admin') }}" method="GET" class="d-flex align-items-center ms-auto" style="max-width: 400px;">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="search" id="student_search" placeholder="Search by Enrollment No." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
            <form action="{{ url('filter_student_list') }}" method="get" class="d-flex align-items-center">
                <label for="filter_role" class="form-label me-2 mb-0 visually-hidden">Filter by Class:</label>
                <select name="filter" id="filter_role" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 180px;">
                    <option value="0" >All Class</option>
                    @foreach($classes as $class_option) {{-- Renamed $class to $class_option --}}
                            <option value="{{ $class_option->id }}" {{ request('filter') == $class_option->id ? 'selected' : '' }}>
                                {{ $class_option->program->name ?? 'N/A Program' }} / Batch: {{ $class_option->year ?? 'N/A' }} / Sem: {{ $class_option->sem ?? 'N/A' }} / Div: {{ $class_option->devision ?? 'N/A' }}
                            </option>
                            @endforeach
                </select>
                {{-- Optional: Add a submit button if onchange is not preferred or for accessibility --}}
                {{-- <button type="submit" class="btn btn-sm btn-outline-secondary ms-2">Filter</button> --}}
            </form>
        </div>
        <div class="card-body p-0">
            @if(isset($students) && $students->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            {{-- MODIFIED LINE BELOW --}}
                            <th scope="col" id="enrollNoHeader" style="cursor:pointer;">Enroll. No. <span class="sort-indicator ms-1">⇵</span></th>
                            <th scope="col">Student Phone</th>
                            <th scope="col">Student Email</th>
                            <th scope="col">Parent Phone</th>
                            <th scope="col">Parent Email</th>
                            <th scope="col">Class Details</th>
                            <th scope="col">Last Updated</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                        <tr>
                            <td>{{ $index + 1 + ($students instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($students->currentPage() - 1) * $students->perPage() : 0) }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->enrollment_number }}</td>
                            <td>{{ $student->phone_number ?? 'N/A' }}</td>
                            <td>{{ $student->email ?? 'N/A' }}</td>
                            <td>{{ $student->parents_phone_number ?? 'N/A' }}</td>
                            <td>{{ $student->parents_email ?? 'N/A' }}</td>
                            <td>
                                @if($student->class)
                                    {{ $student->class->program->name ?? 'N/A Program' }} /
                                    Batch: {{ $student->class->year ?? 'N/A' }} /
                                    Sem: {{ $student->class->sem ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $student->updated_at ? $student->updated_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td class="text-center">
                                <a href="{{ url('/edit_student_admin/'.$student->student_id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Student">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Student"
                                        data-bs-toggle="modal" data-bs-target="#deleteStudentModal"
                                        data-student-id="{{ $student->student_id }}" data-student-name="{{ $student->name }}">
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
                <i class="bi bi-emoji-frown display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">
                    @if(request('search'))
                        No students found matching your search criteria: "<strong>{{ request('search') }}</strong>".
                    @else
                        No students found in the system.
                    @endif
                </p>
                 @if(request('search'))
                    <p class="small text-muted mt-2">Try a different search term or <a href="{{ url('student_list_admin') }}">view all students</a>.</p>
                @else
                     <p class="small text-muted mt-2">You can <a href="{{ url('add_student_admin') }}">add students</a> to populate this list.</p>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Student Confirmation Modal -->
<div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteStudentModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the student: <strong id="studentNameToDelete"></strong>?
        <p class="text-danger small mt-2">This action cannot be undone and will remove all associated records for this student.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteStudentForm" method="get" action=""> {{-- Action will be set by JS --}}
            <button type="submit" class="btn btn-danger">Yes, Delete Student</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    // Existing Modal Script
    var deleteStudentModal = document.getElementById('deleteStudentModal');
    if (deleteStudentModal) {
        deleteStudentModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var studentId = button.getAttribute('data-student-id');
            var studentName = button.getAttribute('data-student-name');

            var modalBodyStrong = deleteStudentModal.querySelector('#studentNameToDelete');
            var deleteForm = deleteStudentModal.querySelector('#deleteStudentForm');

            modalBodyStrong.textContent = studentName;
            deleteForm.action = '{{ url("delete_student_admin") }}/' + studentId; // Adjusted to use url() helper
            
            // Ensure form method is GET as per your existing setup
            const methodInput = deleteForm.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }
            deleteForm.method = 'GET';
        });
    }

    // New Table Sorting Script for "Enroll. No."
    const enrollHeader = document.getElementById('enrollNoHeader');
    const tableBody = document.querySelector('.table-responsive table tbody'); // Target tbody more specifically

    if (enrollHeader && tableBody) {
        enrollHeader.addEventListener('click', function() {
            let currentSortOrder = this.dataset.sortOrder || 'none';

            // Toggle sort order: none -> asc -> desc -> asc -> ...
            if (currentSortOrder === 'asc') {
                currentSortOrder = 'desc';
            } else {
                currentSortOrder = 'asc'; // Default to ascending from 'none' or 'desc'
            }
            this.dataset.sortOrder = currentSortOrder;

            updateSortIndicator(this, currentSortOrder);

            const rows = Array.from(tableBody.querySelectorAll('tr'));
            if (rows.length === 0) return; // No rows to sort

            // Sort rows based on "Enroll. No." (3rd cell, index 2)
            rows.sort((rowA, rowB) => {
                const enrollA = rowA.cells[2].textContent.trim();
                const enrollB = rowB.cells[2].textContent.trim();

                // Use localeCompare with numeric option for natural sort of alphanumeric strings
                const comparison = enrollA.localeCompare(enrollB, undefined, { numeric: true, sensitivity: 'base' });

                return currentSortOrder === 'asc' ? comparison : -comparison;
            });

            // Re-append sorted rows to the table body
            rows.forEach(row => tableBody.appendChild(row));

            // Update '#' column (serial number) for the current view
            // This re-numbers based on the new client-side sort order for the current page
            rows.forEach((row, index) => {
                row.cells[0].textContent = index + 1;
            });
        });
    }

    function updateSortIndicator(headerEl, order) {
        const indicatorSpan = headerEl.querySelector('.sort-indicator');
        if (indicatorSpan) {
            if (order === 'asc') {
                indicatorSpan.innerHTML = '▲'; // Up arrow ▲
            } else if (order === 'desc') {
                indicatorSpan.innerHTML = '▼'; // Down arrow ▼
            } else { // 'none' or any other state
                indicatorSpan.innerHTML = '⇵'; // Neutral up/down arrow ↕
            }
        }
    }
});
</script>