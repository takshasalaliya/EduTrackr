@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Student - Optional Subject Mapping (My Classes)')
@section('page_title', 'Assign Optional Subjects to Students in My Classes')

@section('optional_subject') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <p class="text-muted mb-0">Assign optional subjects to students within the classes you counsel.</p>
    </div>
    {{-- No specific "Add New" button here as this page is for the assignment process itself --}}
</div>

<div class="container-fluid"> {{-- Use container-fluid for full width --}}

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
            <form action="{{ url('optionalgroup') }}" method="get" id="filterOptionalSubjectFormCounselor"> {{-- Use url() helper --}}
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="filter_program_counselor" class="form-label">Program (Your Classes)</label>
                        <select name="field" id="filter_program_counselor" class="form-select" onchange="document.getElementById('filterOptionalSubjectFormCounselor').submit()">
                            <option value="">Select Program</option>
                            {{-- $programs_counselor should contain distinct programs from classes counseled by Auth::user() --}}
                            @if(isset($programs))
                               @foreach($programs as $program)
              
              @if($program->coundelor_id == Auth::user()->id)
              
              <option value="{{$program->program->program_id}}" {{ $program->program->program_id == request('field') ? 'selected' : '' }}>{{$program->program->name}}</option>
              @endif
              @endforeach
                            @endif
                        </select>
                    </div>

                    @if(isset($sem))
                    <div class="col-md-2">
                        <label for="filter_sem_counselor" class="form-label">Semester</label>
                        <select name="sem" id="filter_sem_counselor" class="form-select" onchange="document.getElementById('filterOptionalSubjectFormCounselor').submit()">
                            <option value="">Select Semester</option>
                            {{-- $sem_counselor should be distinct semesters for the selected program from counselor's classes --}}
                            @foreach($sem as $sem)
    @if($sem->coundelor_id == Auth::user()->id)
    <option value="{{$sem->sem}}" {{$sem->sem==request('sem') ? 'selected':''}}>{{'Semester'.$sem->sem}}</option>
    @endif
    @endforeach
                        </select>
                    </div>
                    @endif

                    @if(isset($year))
                    <div class="col-md-2">
                        <label for="filter_year_counselor" class="form-label">Batch Year</label>
                        <select name="year" id="filter_year_counselor" class="form-select" onchange="document.getElementById('filterOptionalSubjectFormCounselor').submit()">
                            <option value="">Select Year</option>
                             {{-- $year_counselor should be distinct years for selected program/sem from counselor's classes --}}
                             @foreach($year as $year)
    @if($year->coundelor_id == Auth::user()->id)
    <option value="{{$year->year}}" {{$year->year==request('year') ? 'selected':''}}>{{$year->year}}</option>
    @endif
    @endforeach
                        </select>
                    </div>
                    @endif

                    @if(isset($devision))
                    <div class="col-md-2">
                        <label for="filter_devision_counselor" class="form-label">Division</label>
                        <select name="devision" id="filter_devision_counselor" class="form-select" onchange="document.getElementById('filterOptionalSubjectFormCounselor').submit()">
                            <option value="">Select Division</option>
                            {{-- $devision_counselor should be distinct divisions for selected program/sem/year from counselor's classes --}}
                            @foreach($devision as $devision)
    @if($devision->coundelor_id == Auth::user()->id)
    <option value="{{$devision->devision}}" {{$devision->devision==request('devision') ? 'selected':''}}>{{$devision->devision}}</option>
    @endif
    @endforeach
                        </select>
                    </div>
                    @endif
                     <div class="col-md-3">
                        <a href="{{ url('optionalgroup') }}" class="btn btn-outline-secondary w-100 mt-md-4">Reset Filters</a>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @if($valid) {{-- $valid flag and class_details_info indicates filters are complete --}}
    {{-- Manual Subject-Student Mapping Form --}}
    <div class="card card-custom mb-4">
        <div class="card-header">
            <i class="bi bi-ui-checks me-2"></i>Assign Optional Subjects to Students
        </div>
        <div class="card-body">
            <form action="{{ url('optionalgroup') }}" method="post">
                @csrf
                {{-- Hidden inputs to pass filter values if needed by POST controller for context --}}
                <input type="hidden" name="field" value="{{ request('field') }}">
                <input type="hidden" name="sem" value="{{ request('sem') }}">
                <input type="hidden" name="year" value="{{ request('year') }}">
                <input type="hidden" name="devision" value="{{ request('devision') }}">
                @if(isset($class_id_from_filters)) <input type="hidden" name="class_id" value="{{ $class_id_from_filters }}"> @endif


                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Select Optional Subjects <span class="text-danger">*</span></label>
                        <div class="border p-2 rounded" style="max-height: 300px; overflow-y: auto;">
                            @if(isset($subject) && count($subject) > 0) {{-- Assuming $subject is the list of available optional subjects for the class --}}
                                @foreach($subject as $subject_item)
                                <div class="form-check">
                                    <input class="form-check-input @error('subject') is-invalid @enderror" type="checkbox" name="subject[]" value="{{ $subject_item->subject_id }}" id="c_subject_{{ $subject_item->subject_id }}"
                                           {{ (is_array(old('subject')) && in_array($subject_item->subject_id, old('subject'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="c_subject_{{ $subject_item->subject_id }}">
                                        {{ $subject_item->subject_name }} ({{ $subject_item->subject_code }})
                                    </label>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted m-0">No optional subjects found for the selected class.</p>
                            @endif
                        </div>
                        @error('subject') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @error('subject.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Select Students (who have not opted yet) <span class="text-danger">*</span></label>
                        <div class="border p-2 rounded" style="max-height: 300px; overflow-y: auto;">
                             @if(isset($students) && count($students) > 0)
                                @php $studentAvailableForOpting = false; @endphp
                                @foreach($students as $student_item)
                                    @if($student_item->optional == 'no')
                                        @php $studentAvailableForOpting = true; @endphp
                                        <div class="form-check">
                                            <input class="form-check-input @error('student') is-invalid @enderror" type="checkbox" name="student[]" value="{{ $student_item->student_id }}" id="c_student_{{ $student_item->student_id }}"
                                                   {{ (is_array(old('student')) && in_array($student_item->student_id, old('student'))) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="c_student_{{ $student_item->student_id }}">
                                                {{ $student_item->name }} ({{ $student_item->enrollment_number }})
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                                @if(!$studentAvailableForOpting)
                                     <p class="text-muted m-0">All students in this class have already opted for subjects or no eligible students found.</p>
                                @endif
                            @else
                                <p class="text-muted m-0">No students found for the selected class.</p>
                            @endif
                        </div>
                        @error('student') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @error('student.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="bi bi-check2-all me-2"></i>Assign Selected Subjects to Students
                    </button>
                </div>
            </form>
        </div>
    </div>
    @elseif(!(isset($valid) && $valid) && (request('field') || request('sem') || request('year') || request('devision')))
        <div class="alert alert-info mt-4">
            <i class="bi bi-info-circle-fill me-2"></i>Please complete all filters (Program, Semester, Batch Year, and Division) to load students and subjects for assignment.
        </div>
    @endif
    {{-- No Excel upload form here based on your provided counselor HTML --}}
</div>
@endsection