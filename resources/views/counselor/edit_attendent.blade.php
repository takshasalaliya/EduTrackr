@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Edit Attendance')
@section('page_title', 'Edit Student Attendance')

@section('edit_attendent') {{-- Keeping this section name as requested --}}

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
                    <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Attendance Records</h4>
                </div>
                <div class="card-body p-4">
                    @php
                        $headerInfoDisplayed = false;
                        $currentSubjectAssignment = null;
                        if (isset($datas) && isset($pervious->subject)) { // $datas holds subject assignment info
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

                    @if($headerInfoDisplayed)
                        <form action="{{ url('edit_attendendent_counselor') }}" method="post"> {{-- Use url() helper --}}
                            @csrf
                            {{-- Hidden fields to identify the lecture being edited --}}
                            <input type="hidden" value="{{ $pervious->subject ?? '' }}" name="staff_id">
                            <input type="hidden" value="{{ $pervious->unit ?? '' }}" name="unit">
                            <input type="hidden" value="{{ $pervious->leacture ?? '' }}" name="leacture"> {{-- Typo "leacture" kept --}}
                            <input type="hidden" value="{{ $pervious->date ?? '' }}" name="date">

                            @if(isset($attendent) && $attendent->count() > 0) {{-- $attendent is the collection of existing attendance records --}}
                                @php $studentEditCount = 0; @endphp
                                @foreach($attendent as $attendance_record)
                                    @if(isset($attendance_record->student)) {{-- Ensure student relation is loaded --}}
                                        @php $studentEditCount++; @endphp
                                        <div class="row align-items-center mb-2 py-2 border-bottom student-attendance-edit-row">
                                            <div class="col-md-1 text-end">{{ $studentEditCount }}.</div>
                                            <div class="col-md-7 student-name-display">
                                                {{ $attendance_record->student->name }} ({{ $attendance_record->student->enrollment_number }})
                                            </div>
                                            <div class="col-md-4 text-md-end text-center">
                                                {{-- Hidden input to ensure all students in this edit list are submitted --}}
                                                <input type="hidden" name="student_ids[]" value="{{ $attendance_record->student->student_id }}">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="attendance_status[{{ $attendance_record->student->student_id }}]" value="present" id="edit_p_{{ $attendance_record->student->student_id }}"
                                                           {{ $attendance_record->attendance == 'present' ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="edit_p_{{ $attendance_record->student->student_id }}">Present</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="attendance_status[{{ $attendance_record->student->student_id }}]" value="absent" id="edit_a_{{ $attendance_record->student->student_id }}"
                                                           {{ $attendance_record->attendance == 'absent' ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="edit_a_{{ $attendance_record->student->student_id }}">Absent</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                @if($studentEditCount == 0)
                                    <div class="alert alert-warning text-center mt-3">
                                        <i class="bi bi-info-circle-fill me-2"></i>
                                        No existing attendance records found for this lecture to edit.
                                    </div>
                                @else
                                    <div class="mt-4 pt-2 text-center">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-save-fill me-2"></i>Update Attendance
                                        </button>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning text-center mt-3">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    No attendance records found for this selection. It might not have been taken yet.
                                    You can <a href="{{ url('select_counselor') }}?subject={{$pervious->subject}}&unit={{$pervious->unit}}&leacture={{$pervious->leacture}}&date={{$pervious->date}}&submit=submit">take attendance</a> instead.
                                </div>
                            @endif
                            @error('attendance_status.*') {{-- For errors related to individual student attendance status --}}
                                <div class="text-danger mt-2 mb-2 d-block text-center">⚠️ {{ $message }}</div>
                            @enderror
                        </form>
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

{{-- No custom JS needed for "Mark All" here as it's an edit form with pre-filled states --}}