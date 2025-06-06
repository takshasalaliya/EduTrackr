@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'My Students List')
@section('page_title', 'My Students List')

@section('student_table') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <p class="text-muted mb-0">Showing students from classes you counsel.</p>
    </div>
    <a href="{{ url('add_student') }}" class="btn btn-primary"> {{-- Assuming this is the counselor's route for adding students --}}
        <i class="bi bi-person-plus-fill me-2"></i>Add New Student(s)
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
                <i class="bi bi-people-fill me-2"></i>Students You Counsel
            </div>
            <div class="ms-auto" style="max-width: 400px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by Name or Enrollment No...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @php
                $counselorStudents = [];
                if(isset($students) && isset(Auth::user()->id)) {
                    foreach($students as $student) {
                        // Ensure student object and class relationship exist, and coundelor_id is not null
                        if(isset($student->class) && $student->class->coundelor_id !== null && $student->class->coundelor_id == Auth::user()->id) { // Typo 'coundelor_id' kept
                            $counselorStudents[] = $student;
                        }
                    }
                }
            @endphp

            @if(count($counselorStudents) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="studentTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col" id="enrollmentSortHeader" style="cursor: pointer;">
                                Enroll. No. <i class="bi bi-arrow-down-up sort-icon ms-1"></i>
                            </th>
                            <th scope="col">Student Phone</th>
                            <th scope="col">Student Email</th>
                            <th scope="col">Parent Phone</th>
                            <th scope="col">Parent Email</th>
                            <th scope="col">Class Details</th>
                            <th scope="col">Last Updated</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody"> {{-- Added ID for JS targeting --}}
                        @foreach($counselorStudents as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->enrollment_number }}</td>
                            <td>{{ $student->phone_number ?? 'N/A' }}</td>
                            <td>{{ $student->email ?? 'N/A' }}</td>
                            <td>{{ $student->parents_phone_number ?? 'N/A' }}</td>
                            <td>{{ $student->parents_email ?? 'N/A' }}</td>
                            <td>
                                @if($student->class && $student->class->program)
                                    {{ $student->class->program->name }} /
                                    Batch: {{ $student->class->year ?? 'N/A' }} /
                                    Sem: {{ $student->class->sem ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $student->updated_at ? $student->updated_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td class="text-center">
                                <a href="{{ url('/edit_student/'.$student->student_id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Student">
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
                <p class="text-muted mb-0">No students found in the classes you counsel.</p>
                <p class="small text-muted mt-2">If you have been assigned new classes, the student list might update shortly or after adding students to those classes.</p>
            </div>
            @endif
        </div>
        {{-- Pagination comments remain as is --}}
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
        Are you sure you want to delete the student: <strong id="studentNameToDeleteInModal"></strong>?
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
    // Client-side search
    const searchInput = document.getElementById('searchInput');
    // const studentTable = document.getElementById('studentTable'); // studentTableBody is more specific
    const studentTableBody = document.getElementById('studentTableBody'); // Target tbody for rows
    const tableRows = studentTableBody ? Array.from(studentTableBody.querySelectorAll('tr')) : []; // Convert to array for filtering if needed, or iterate directly

    if (searchInput && studentTableBody) { // Check studentTableBody as well
        searchInput.addEventListener('keyup', function () {
            const filter = this.value.toLowerCase().trim();
            const rowsToSearch = studentTableBody.querySelectorAll('tr'); // Get current rows in tbody

            rowsToSearch.forEach(function (row) {
                const name = row.cells[1].textContent.toLowerCase();
                const enrollment = row.cells[2].textContent.toLowerCase();

                if (name.includes(filter) || enrollment.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Delete Modal
    var deleteStudentModal = document.getElementById('deleteStudentModal');
    if (deleteStudentModal) {
        deleteStudentModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var studentId = button.getAttribute('data-student-id');
            var studentName = button.getAttribute('data-student-name');

            var modalBodyStrong = deleteStudentModal.querySelector('#studentNameToDeleteInModal');
            var deleteForm = deleteStudentModal.querySelector('#deleteStudentForm');

            modalBodyStrong.textContent = studentName;
            deleteForm.action = '{{ url("delete_student") }}/' + studentId; // Corrected URL join

            // If using GET, remove method spoofing and set form method to GET
            const methodInput = deleteForm.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }
            deleteForm.method = 'GET';
        });
    }

    // Enrollment Number Sorting
    const enrollmentSortHeader = document.getElementById('enrollmentSortHeader');
    // const studentTableBody is already defined above
    let enrollmentSortOrder = 'none'; // 'none', 'asc', 'desc'

    if (enrollmentSortHeader && studentTableBody) {
        enrollmentSortHeader.addEventListener('click', function() {
            const icon = enrollmentSortHeader.querySelector('.sort-icon');
            const rows = Array.from(studentTableBody.querySelectorAll('tr')); // Get all rows from tbody
            const enrollmentColIndex = 2; // Enrollment No. is the 3rd column (index 2)

            // Determine new sort order
            if (enrollmentSortOrder === 'asc') {
                enrollmentSortOrder = 'desc';
            } else {
                enrollmentSortOrder = 'asc';
            }

            rows.sort((rowA, rowB) => {
                const cellA = rowA.querySelectorAll('td')[enrollmentColIndex].textContent.trim();
                const cellB = rowB.querySelectorAll('td')[enrollmentColIndex].textContent.trim();
                
                // Use localeCompare for alphanumeric sorting
                const comparison = cellA.localeCompare(cellB, undefined, { numeric: true, sensitivity: 'base' });
                
                return enrollmentSortOrder === 'asc' ? comparison : -comparison;
            });

            // Update icon
            if (enrollmentSortOrder === 'asc') {
                icon.classList.remove('bi-arrow-down-up', 'bi-sort-alpha-up');
                icon.classList.add('bi-sort-alpha-down');
            } else { // desc
                icon.classList.remove('bi-arrow-down-up', 'bi-sort-alpha-down');
                icon.classList.add('bi-sort-alpha-up');
            }

            // Re-append sorted rows
            studentTableBody.innerHTML = ''; // Clear existing rows
            rows.forEach(row => studentTableBody.appendChild(row));

            // Update # column (serial number)
            const firstColumnCells = studentTableBody.querySelectorAll('tr > td:first-child');
            firstColumnCells.forEach((cell, index) => {
                cell.textContent = index + 1;
            });

            // Re-apply search filter if active
            if (searchInput.value.trim() !== '') {
                 searchInput.dispatchEvent(new Event('keyup'));
            }
        });
    }
});
</script>
