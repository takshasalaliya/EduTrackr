@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Mark Attendance')
@section('page_title', 'Mark Student Attendance')

@section('attendent') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <a href="{{ url('select_counselor') }}" class="btn btn-outline-secondary"> {{-- Link back to criteria selection --}}
            <i class="bi bi-arrow-left-circle me-2"></i>Change Selection
        </a>
    </div>
    {{-- No extra buttons needed here as form provides actions --}}
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

    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9"> {{-- Wider column for this form --}}
            <div class="card card-custom">
                <div class="card-header text-center">
                    <h4 class="mb-0"><i class="bi bi-calendar-check-fill me-2"></i>Fill Attendance</h4>
                </div>
                <div class="card-body p-4">
                    @php
                        $headerInfoDisplayed = false;
                        $currentSubjectAssignment = null;
                        if (isset($datas) && isset($pervious->subject)) {
                            foreach($datas as $data_item) {
                                if($data_item->id == $pervious->subject) {
                                    $currentSubjectAssignment = $data_item;
                                    break;
                                }
                            }
                        }
                    @endphp

                    @if($currentSubjectAssignment)
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading">Lecture Details:</h5>
                            <p class="mb-1">
                                <strong>Class:</strong>
                                {{ $currentSubjectAssignment->subject->student_class->program->name ?? 'N/A' }} /
                                Batch: {{ $currentSubjectAssignment->subject->student_class->year ?? 'N/A' }} /
                                Sem: {{ $currentSubjectAssignment->subject->student_class->sem ?? 'N/A' }} /
                                Div: {{ $currentSubjectAssignment->subject->student_class->devision ?? 'N/A' }}
                            </p>
                            <p class="mb-1"><strong>Subject:</strong> {{ $currentSubjectAssignment->subject->subject_name ?? 'N/A' }} ({{ $currentSubjectAssignment->subject->subject_code ?? 'N/A' }})</p>
                            <p class="mb-1"><strong>Teacher:</strong> {{ $currentSubjectAssignment->teacher->name ?? 'N/A' }}</p>
                            <p class="mb-0">
                                <strong>Unit:</strong> {{ $pervious->unit ?? 'N/A' }} |
                                <strong>Lecture No:</strong> {{ $pervious->leacture ?? 'N/A' }} | {{-- Typo "leacture" kept --}}
                                <strong>Date:</strong> {{ isset($pervious->date) ? \Carbon\Carbon::parse($pervious->date)->format('d M Y') : 'N/A' }}
                            </p>
                        </div>
                        @php $headerInfoDisplayed = true; @endphp
                    @else
                        <div class="alert alert-warning">Could not load lecture details based on previous selection.</div>
                    @endif

                    @if($headerInfoDisplayed && isset($attendent) && $attendent == 'yes')
                        <form action="{{ url('final_attendent_counselor') }}" method="post">
                            @csrf
                            <input type="hidden" name="staff_id" value="{{ $pervious->subject ?? '' }}">
                            <input type="hidden" name="leacture" value="{{ $pervious->leacture ?? '' }}"> {{-- Typo "leacture" kept --}}
                            <input type="hidden" name="date" value="{{ $pervious->date ?? '' }}">
                            <input type="hidden" name="unit" value="{{ $pervious->unit ?? '' }}">

                            <div class="mb-3 d-flex justify-content-end">
                                <button type="button" id="markAllPresentBtn" class="btn btn-sm btn-outline-success me-2"><i class="bi bi-check2-all"></i> Mark All Present</button>
                                <button type="button" id="markAllAbsentBtn" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i> Mark All Absent</button>
                            </div>

                            @php $studentCount = 0; @endphp

                            {{-- Regular Class Students --}}
                            @if($yes)
                                @foreach($students as $student_item)
                                    @if($currentSubjectAssignment->subject->class_id == $student_item->class_id)
                                        @php $studentCount++; @endphp
                                        <div class="row align-items-center mb-2 py-2 border-bottom student-attendance-row">
                                            <div class="col-md-1 text-end">{{ $studentCount }}.</div>
                                            <div class="col-md-7 student-name-display">
                                                {{ $student_item->name }} ({{ $student_item->enrollment_number }})
                                            </div>
                                            <div class="col-md-4 text-md-end text-center">
                                                <input type="hidden" name="student[]" value="{{ $student_item->student_id }}">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="attendance_status[{{ $student_item->student_id }}]" value="present" id="p_{{ $student_item->student_id }}" required>
                                                    <label class="form-check-label" for="p_{{ $student_item->student_id }}">Present</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="attendance_status[{{ $student_item->student_id }}]" value="absent" id="a_{{ $student_item->student_id }}" required>
                                                    <label class="form-check-label" for="a_{{ $student_item->student_id }}">Absent</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif

                            {{-- Optional Subject Students --}}
                            @if(isset($optional))
                                @foreach($optional as $optional_student_assignment)
                                    @if($optional_student_assignment && isset($optional_student_assignment->student))
                                        @php $studentCount++; @endphp
                                        <div class="row align-items-center mb-2 py-2 border-bottom student-attendance-row">
                                             <div class="col-md-1 text-end">{{ $studentCount }}.</div>
                                            <div class="col-md-7 student-name-display">
                                                {{ $optional_student_assignment->student->name }} ({{ $optional_student_assignment->student->enrollment_number }})
                                                <span class="badge bg-info text-dark ms-2">Optional</span>
                                            </div>
                                            <div class="col-md-4 text-md-end text-center">
                                                <input type="hidden" name="student[]" value="{{ $optional_student_assignment->student->student_id }}">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="attendance_status[{{ $optional_student_assignment->student->student_id }}]" value="present" id="p_opt_{{ $optional_student_assignment->student->student_id }}" required>
                                                    <label class="form-check-label" for="p_opt_{{ $optional_student_assignment->student->student_id }}">Present</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="attendance_status[{{ $optional_student_assignment->student->student_id }}]" value="absent" id="a_opt_{{ $optional_student_assignment->student->student_id }}" required>
                                                    <label class="form-check-label" for="a_opt_{{ $optional_student_assignment->student->student_id }}">Absent</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif

                            @error('subject') {{-- Assuming this error was for a general form issue, not specific subject selection here --}}
                                <div class="text-danger mt-2 mb-2 d-block">⚠️ {{ $message }}</div>
                            @enderror
                            @error('attendance_status.*') {{-- For errors related to individual student attendance status --}}
                                <div class="text-danger mt-2 mb-2 d-block">⚠️ {{ $message }}</div>
                            @enderror


                            @if($studentCount == 0)
                                <div class="alert alert-warning text-center mt-3">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                                    No students are mapped to this subject/class combination for attendance, or all students are from optional subjects and none opted. Please check student-class and student-optional subject mappings.
                                </div>
                            @else
                                <div class="mt-4 pt-2 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-lg me-2"></i>Submit Attendance
                                    </button>
                                </div>
                            @endif
                        </form>
                    @elseif(isset($attendent) && $attendent == 'no' && empty(session('success')))
                        <div class="alert alert-danger text-center">
                            <h4 class="alert-heading"><i class="bi bi-calendar-x-fill me-2"></i>Attendance Already Submitted!</h4>
                            <p>Attendance has already been taken for this subject, unit, lecture, and date.</p>
                            <hr>
                            <p class="mb-0">If you need to make changes, please use the "Edit Attendance" option from the selection page.</p>
                        </div>
                    @elseif(!$headerInfoDisplayed)
                        <div class="alert alert-danger text-center">
                             <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Selection Error!</h4>
                             <p>Could not load details for the selected subject/lecture. Please go back and try selecting again.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    const markAllPresentBtn = document.getElementById('markAllPresentBtn');
    const markAllAbsentBtn = document.getElementById('markAllAbsentBtn');
    const studentRows = document.querySelectorAll('.student-attendance-row');

    if (markAllPresentBtn) {
        markAllPresentBtn.addEventListener('click', function() {
            studentRows.forEach(row => {
                const presentRadio = row.querySelector('input[type="radio"][value="present"]');
                if (presentRadio) {
                    presentRadio.checked = true;
                }
            });
        });
    }

    if (markAllAbsentBtn) {
        markAllAbsentBtn.addEventListener('click', function() {
            studentRows.forEach(row => {
                const absentRadio = row.querySelector('input[type="radio"][value="absent"]');
                if (absentRadio) {
                    absentRadio.checked = true;
                }
            });
        });
    }
});
</script>
