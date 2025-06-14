@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Student Attendance Details by Subject')
@section('page_title', 'Student Attendance Details')

@section('subject_table_attendent') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <p class="text-muted mb-0">Detailed attendance records for students by selected subject.</p>
    </div>
    <div class="btn-group" role="group" aria-label="Export Actions">
        @if(request('subject')) {{-- Show export buttons only if a subject is selected --}}
            <a href="{{ url('generate_pdf_counselor/'.request('subject')) }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf-fill me-2"></i>Download PDF
            </a>
            <a href="{{ url('generate_excel_counselor/'.request('subject')) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel-fill me-2"></i>Download Excel
            </a>
        @endif
    </div>
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

    {{-- Filter Form --}}
    <div class="card card-custom mb-4">
        <div class="card-body">
            <form action="{{ url('attendent_list_counselor') }}" method="get" id="filterAttendanceListForm"> {{-- Use url() helper --}}
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label for="subject_filter_att_list" class="form-label">Select Subject / Class Context <span class="text-danger">*</span></label>
                        <select name="subject" id="subject_filter_att_list" class="form-select @error('subject') is-invalid @enderror" onchange="document.getElementById('filterAttendanceListForm').submit()" required>
                            <option value="" disabled {{ !request('subject') ? 'selected' : '' }}>Select Subject and Class</option>
                            @if(isset($subject)) {{-- Pass this from controller --}}
                                @foreach($subject as $subj_option)
                                <option value="{{ $subj_option->id }}" {{ request('subject') == $subj_option->id ? 'selected' : '' }}>
                                    {{ $subj_option->subject->short_name ?? 'N/A Sub' }} -
                                    {{ $subj_option->subject->student_class->program->name ?? 'N/A Prog' }} /
                                    Sem {{ $subj_option->subject->student_class->sem ?? 'N/A' }} /
                                    Div {{ $subj_option->subject->student_class->devision ?? 'N/A' }}
                                    (Batch: {{ $subj_option->subject->student_class->year ?? 'N/A' }})
                                </option>
                                @endforeach
                            @else
                                <option value="" disabled>No subjects assigned to you or your classes found.</option>
                            @endif
                        </select>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                         <button type="submit" class="btn btn-primary w-100">View Attendance</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @if(isset($student))
    <div class="card card-custom">
        {{-- MODIFIED: Card header now includes a search input --}}
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <i class="bi bi-table me-2"></i>Student Attendance Summary for Selected Subject
            </div>
            <div class="ms-auto" style="max-width: 300px;">
                <input type="text" id="liveSearchInput" class="form-control form-control-sm" placeholder="Search by Enrollment or Name...">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- MODIFIED: Added ID to table --}}
                <table class="table table-hover table-striped mb-0" id="attendanceTable">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            {{-- MODIFIED: Made Enrollment header sortable --}}
                            <th scope="col" id="enrollmentHeader" style="cursor: pointer;">
                                Enrollment No. <i class="bi bi-arrow-down-up small text-muted"></i>
                            </th>
                            <th scope="col">Student Name</th>
                            <th scope="col" class="text-center">Total Lectures</th>
                            <th scope="col">First Attended</th>
                            <th scope="col">Last Attended</th>
                            <th scope="col" class="text-center">Present</th>
                            <th scope="col" class="text-center">Attendance %</th>
                           
                        </tr>
                    </thead>
                    {{-- MODIFIED: Added ID to tbody --}}
                    <tbody id="attendanceTableBody">
                    @php
                    $num=0;
                    @endphp
                        {{-- Data processing logic remains UNCHANGED as requested --}}
                        @foreach($valid as $data)
                        <tr>
                        <?php
                        $sum=0;
                        $present=0;
                        $enter=0;
                        $date=[];
                        foreach ($student as $d) {
                            if ($d->student->enrollment_number == $data && $d->staff_id == $id) {
                                $date[]=$d->created_at;
                                $sum++;
                                $to=explode(" ",$d->created_at);
                                $name=$d->student->name;
                                $message=$d->student->student_id;
                                if($d->attendance=='present'){
                                    $present++;
                                }
                            }
                        }
                        sort($date);
                        $from=$date[0];
                        $from=explode(" ",$from);
                        $to = end($date);
                        $to=explode(" ",$to);
                        $pertentage=number_format($present/$sum*100,2);
                        ?>
                        <td>{{$num=+1}}</td>
                        {{-- MODIFIED: Added classes for JS to find these cells --}}
                        <td class="enrollment-cell">{{$data}}</td>
                        <td class="name-cell">{{$name}}</td>
                        <td>{{$sum}}</td>
                        <td>{{$from[0]}}</td>
                        <td>{{$to[0]}}</td>
                        <td>{{$present}}</td>
                        <td>{{$pertentage}}%</td>
                      
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- MODIFIED: Added a message for when search yields no results --}}
            <div id="noResultsMessage" class="text-center p-4" style="display: none;">
                <p class="text-muted mb-0">No matching records found.</p>
            </div>
        </div>
    </div>
    @elseif(request('subject'))
        <div class="alert alert-warning text-center mt-4 py-4">
            <h4 class="alert-heading"><i class="bi bi-info-circle-fill me-2"></i>No Attendance Data</h4>
            <p>No attendance records found for the selected subject. Please ensure attendance has been taken.</p>
        </div>
    @else
        <div class="alert alert-info text-center mt-4 py-4">
            <p><i class="bi bi-info-circle-fill me-2"></i>Please select a subject from the dropdown above to view the attendance list.</p>
        </div>
    @endif
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('liveSearchInput');
    const tableBody = document.getElementById('attendanceTableBody');
    const noResultsMessage = document.getElementById('noResultsMessage');
    const enrollmentHeader = document.getElementById('enrollmentHeader');

    // Ensure the table body exists before adding listeners
    if (tableBody) {
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        let sortDirection = 'none'; // 'asc', 'desc', 'none'

        // 1. Live Search Functionality
        if(searchInput) {
            searchInput.addEventListener('keyup', () => {
                const searchTerm = searchInput.value.toLowerCase();
                let visibleRows = 0;

                rows.forEach(row => {
                    const enrollment = row.querySelector('.enrollment-cell')?.textContent.toLowerCase() || '';
                    const name = row.querySelector('.name-cell')?.textContent.toLowerCase() || '';

                    if (enrollment.includes(searchTerm) || name.includes(searchTerm)) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show or hide the "no results" message
                noResultsMessage.style.display = visibleRows === 0 ? '' : 'none';
            });
        }

        // 2. Sorting Functionality for Enrollment Number
        if (enrollmentHeader) {
            enrollmentHeader.addEventListener('click', () => {
                const headerIcon = enrollmentHeader.querySelector('i');
                // Determine new sort direction
                if (sortDirection === 'asc') {
                    sortDirection = 'desc';
                    headerIcon.className = 'bi bi-arrow-down small';
                } else {
                    sortDirection = 'asc';
                    headerIcon.className = 'bi bi-arrow-up small';
                }

                // Get rows to sort from the DOM
                const rowsToSort = Array.from(tableBody.querySelectorAll('tr'));

                rowsToSort.sort((a, b) => {
                    const valA = a.querySelector('.enrollment-cell')?.textContent.trim() || '';
                    const valB = b.querySelector('.enrollment-cell')?.textContent.trim() || '';

                    // Use localeCompare for proper string/numeric sorting
                    const comparison = valA.localeCompare(valB, undefined, { numeric: true });

                    return sortDirection === 'asc' ? comparison : -comparison;
                });

                // Re-append sorted rows to the table body
                rowsToSort.forEach(row => tableBody.appendChild(row));
            });
        }
    }
});
</script>
