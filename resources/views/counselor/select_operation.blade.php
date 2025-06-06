@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Select Attendance Criteria')
@section('page_title', 'Select Criteria for Attendance')

@section('attendent_before') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <p class="text-muted mb-0">Select the subject, unit, lecture, and date to proceed.</p>
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
        <div class="alert alert-danger alert-dismissible fade show" role="alert"> {{-- Corrected to alert-danger --}}
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8"> {{-- Wider column for this form --}}
            <div class="card card-custom">
                <div class="card-header">
                    <i class="bi bi-ui-checks-grid me-2"></i>Specify Your Requirements
                </div>
                <div class="card-body p-4">
                    <form action="{{ url('selectes_data_counselor') }}" method="get"> {{-- Use url() helper --}}
                        {{-- @csrf --}} {{-- Not strictly needed for GET method --}}

                        <div class="row g-3">
                            <div class="col-md-12 mb-3"> {{-- Full width for subject for better readability --}}
                                <label class="form-label" for="subject_select_att">Subject / Class Context <span class="text-danger">*</span></label>
                                <select name="subject" id="subject_select_att" class="form-select @error('subject') is-invalid @enderror" required>
                                    <option value="" disabled {{ !request('subject') ? 'selected' : '' }}>Select Subject and Class</option>
                                    @if(isset($subjects) && count($subjects) > 0)
                                        @foreach($subjects as $subject_assignment) {{-- Renamed $subject to avoid conflict --}}
                                        <option value="{{ $subject_assignment->id }}" {{ request('subject') == $subject_assignment->id ? 'selected' : '' }}>
                                            {{ $subject_assignment->subject->short_name ?? 'N/A Sub' }} -
                                            {{ $subject_assignment->subject->student_class->program->name ?? 'N/A Prog' }} /
                                            Sem {{ $subject_assignment->subject->student_class->sem ?? 'N/A' }} /
                                            Div {{ $subject_assignment->subject->student_class->devision ?? 'N/A' }}
                                            (Batch: {{ $subject_assignment->subject->student_class->year ?? 'N/A' }})
                                        </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No subjects assigned to you found.</option>
                                    @endif
                                </select>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="unit_select_att">Unit Number <span class="text-danger">*</span></label>
                                <select name="unit" id="unit_select_att" class="form-select @error('unit') is-invalid @enderror" required>
                                    <option value="" disabled {{ !request('unit') ? 'selected' : '' }}>Select Unit</option>
                                    @for ($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ request('unit') == $i ? 'selected' : '' }}>Unit {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="leacture_select_att">Lecture Number <span class="text-danger">*</span></label> {{-- Typo "Leacture" kept --}}
                                <select name="leacture" id="leacture_select_att" class="form-select @error('leacture') is-invalid @enderror" required>
                                    <option value="" disabled {{ !request('leacture') ? 'selected' : '' }}>Select Lecture</option>
                                    @for ($i = 1; $i <= 10; $i++) {{-- Assuming up to 10 lectures per unit, adjust as needed --}}
                                    <option value="{{ $i }}" {{ request('leacture') == $i ? 'selected' : '' }}>Lecture {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('leacture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="date_select_att">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="date_select_att" class="form-control @error('date') is-invalid @enderror" value="{{ request('date', date('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-2 text-center">
                            <button class="btn btn-primary btn-lg mx-1" name="submit" type="submit" value="submit" title="Proceed to take/add attendance">
                                <i class="bi bi-plus-circle-fill me-2"></i>Take Attendance
                            </button>
                            <button class="btn btn-warning btn-lg mx-1" name="submit" type="submit" value="edit" title="Proceed to edit existing attendance">
                                <i class="bi bi-pencil-square me-2"></i>Edit Attendance
                            </button>
                            <button class="btn btn-info btn-lg mx-1" name="submit" type="submit" value="generate" title="Generate attendance related report or code">
                                <i class="bi bi-file-earmark-text-fill me-2"></i>Generate Report/Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection