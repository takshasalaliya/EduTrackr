@extends('admin.layout')

@section('title', 'Student - Optional Subject Mapping')
{{-- @section('page_title', 'Assign Optional Subjects to Students') --}} {{-- Keeping this commented --}}

@section('optional_subject')

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Student - Optional Subject Mapping</h1>
    <div>
        <a href="https://docs.google.com/spreadsheets/d/1BiWllOIRSZ8PdgCa5DpGI4c-dCbhRtPm/edit?usp=sharing&ouid=109161218020310229957&rtpof=true&sd=true" target="_blank" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Sample Excel for Bulk Assign
        </a>
    </div>
</div>

<div class="container-fluid">

    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert"> {{-- Corrected to alert-danger --}}
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filter Form --}}
    <div class="card card-custom mb-4">
        <div class="card-header">
            <i class="bi bi-filter-circle-fill me-2"></i>Select Class to Map Optional Subjects
        </div>
        <div class="card-body">
            <form action="{{ url('optionalgroup_admin') }}" method="get" id="filterOptionalSubjectForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="filter_program_field" class="form-label">Program</label>
                        <select name="field" id="filter_program_field" class="form-select" onchange="document.getElementById('filterOptionalSubjectForm').submit()">
                            <option value="">Select Program</option>
                            @if(isset($programs))
                                @foreach($programs as $program_item_wrapper) {{-- Renamed $program to avoid conflict --}}
                                    @if(isset($program_item_wrapper->program))
                                        <option value="{{ $program_item_wrapper->program->program_id }}" {{ $program_item_wrapper->program->program_id == request('field') ? 'selected' : '' }}>
                                            {{ $program_item_wrapper->program->name }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>

                    @if(isset($sem) && count($sem) > 0)
                    <div class="col-md-2">
                        <label for="filter_sem" class="form-label">Semester</label>
                        <select name="sem" id="filter_sem" class="form-select" onchange="document.getElementById('filterOptionalSubjectForm').submit()">
                            <option value="">Select Semester</option>
                            @foreach($sem as $sem_item)
                            <option value="{{ $sem_item->sem }}" {{ $sem_item->sem == request('sem') ? 'selected' : '' }}>Semester {{ $sem_item->sem }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(isset($year) && count($year) > 0)
                    <div class="col-md-2">
                        <label for="filter_year" class="form-label">Batch Year</label>
                        <select name="year" id="filter_year" class="form-select" onchange="document.getElementById('filterOptionalSubjectForm').submit()">
                            <option value="">Select Year</option>
                            @foreach($year as $year_item)
                            <option value="{{ $year_item->year }}" {{ $year_item->year == request('year') ? 'selected' : '' }}>{{ $year_item->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(isset($devision) && count($devision) > 0)
                    <div class="col-md-2">
                        <label for="filter_devision" class="form-label">Division</label>
                        <select name="devision" id="filter_devision" class="form-select" onchange="document.getElementById('filterOptionalSubjectForm').submit()">
                            <option value="">Select Division</option>
                            @foreach($devision as $devision_item)
                            <option value="{{ $devision_item->devision }}" {{ $devision_item->devision == request('devision') ? 'selected' : '' }}>{{ $devision_item->devision }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    {{-- Reset button might be useful if filters get stuck --}}
                    <div class="col-md-3">
                        <a href="{{ url('optionalgroup_admin') }}" class="btn btn-outline-secondary w-100">Reset Filters</a>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @if(isset($valid) && $valid)
    {{-- Manual Subject-Student Mapping Form --}}
    <div class="card card-custom mb-4">
        <div class="card-header">
            <i class="bi bi-ui-checks me-2"></i>Assign Optional Subjects to Students for Selected Class
        </div>
        <div class="card-body">
            <form action="{{ url('optionalgroup_admin') }}" method="post">
                @csrf
                {{-- Hidden inputs to pass filter values if needed by POST controller --}}
                <input type="hidden" name="field" value="{{ request('field') }}">
                <input type="hidden" name="sem" value="{{ request('sem') }}">
                <input type="hidden" name="year" value="{{ request('year') }}">
                <input type="hidden" name="devision" value="{{ request('devision') }}">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Select Optional Subjects <span class="text-danger">*</span></label>
                        <div class="border p-2 rounded" style="max-height: 300px; overflow-y: auto;">
                            @if(isset($subject) && count($subject) > 0)
                                @foreach($subject as $subject_item) {{-- Renamed $subject to avoid conflict --}}
                                <div class="form-check">
                                    <input class="form-check-input @error('subject') is-invalid @enderror" type="checkbox" name="subject[]" value="{{ $subject_item->subject_id }}" id="subject_{{ $subject_item->subject_id }}"
                                           {{ (is_array(old('subject')) && in_array($subject_item->subject_id, old('subject'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="subject_{{ $subject_item->subject_id }}">
                                        {{ $subject_item->subject_name }} ({{ $subject_item->subject_code }})
                                    </label>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted m-0">No optional subjects found for the selected class criteria.</p>
                            @endif
                        </div>
                        @error('subject') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @error('subject.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Select Students (who have not opted yet) <span class="text-danger">*</span></label>
                        <div class="border p-2 rounded" style="max-height: 300px; overflow-y: auto;">
                            @if(isset($students) && count($students) > 0)
                                @php $studentAvailable = false; @endphp
                                @foreach($students as $student_item) {{-- Renamed $student to avoid conflict --}}
                                    @if($student_item->optional == 'no')
                                        @php $studentAvailable = true; @endphp
                                        <div class="form-check">
                                            <input class="form-check-input @error('student') is-invalid @enderror" type="checkbox" name="student[]" value="{{ $student_item->student_id }}" id="student_{{ $student_item->student_id }}"
                                                   {{ (is_array(old('student')) && in_array($student_item->student_id, old('student'))) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="student_{{ $student_item->student_id }}">
                                                {{ $student_item->name }} ({{ $student_item->enrollment_number }})
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                                @if(!$studentAvailable)
                                     <p class="text-muted m-0">All students in this class have already opted for subjects or no students found.</p>
                                @endif
                            @else
                                <p class="text-muted m-0">No students found for the selected class criteria.</p>
                            @endif
                        </div>
                        @error('student') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @error('student.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="bi bi-check2-all me-2"></i>Assign Selected Subjects to Selected Students
                    </button>
                </div>
            </form>
            {{-- Excel Download Link - needs $class_id_for_download from controller --}}
            @if(isset($class_id_for_download))
            <div class="mt-3">
                <a href="{{ url('/excel_dowload/'.$class_id_for_download) }}" class="btn btn-info">
                    <i class="bi bi-download me-2"></i>Download Current Mappings for this Class
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

    @if(!isset($valid) || !$valid)
    {{-- Bulk Upload Form if filters not complete or $valid is false --}}
    <div class="card card-custom">
        <div class="card-header">
             <i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Bulk Assign Optional Subjects via Excel
        </div>
        <div class="card-body">
            @if(!(request('field') && request('sem') && request('year') && request('devision')))
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill me-2"></i>Please select Program, Semester, Batch Year, and Division above to enable manual assignment or to proceed with targeted bulk upload for a specific class.
                </div>
            @endif
            <form action="{{ url('excel_optional') }}" method="post" enctype="multipart/form-data" class="row g-3 align-items-end">
                @csrf
                {{-- Hidden inputs to pass filter values if needed by POST controller for context --}}
                <input type="hidden" name="field_excel" value="{{ request('field') }}">
                <input type="hidden" name="sem_excel" value="{{ request('sem') }}">
                <input type="hidden" name="year_excel" value="{{ request('year') }}">
                <input type="hidden" name="devision_excel" value="{{ request('devision') }}">

                <div class="col-md-8">
                    <label for="excel_optional_file" class="form-label">Upload Excel File</label>
                    <input type="file" class="form-control @error('excel_optional') is-invalid @enderror" id="excel_optional_file" name="excel_optional" required accept=".xlsx, .xls, .csv">
                    @error('excel_optional')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <button class="btn btn-success w-100" type="submit">
                        <i class="bi bi-upload me-2"></i>Upload & Process
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection