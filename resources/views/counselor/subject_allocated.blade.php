@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Assign Subjects to Teachers (My Classes)')
@section('page_title', 'Assign Subjects to Teachers for My Classes')

@section('teachingstaff') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <p class="text-muted mb-0">Assign subjects to teachers specifically for the classes you counsel.</p>
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
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card card-custom">
        <div class="card-header">
            <i class="bi bi-ui-checks-grid me-2"></i>Select Class & Teacher for Subject Assignment
        </div>
        <div class="card-body">
            <form action="{{ url('subjectallocated') }}" method="GET" id="filterAssignmentFormCounselor">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="program_filter_c" class="form-label">Program (Your Classes)</label>
                        <select name="program" id="program_filter_c" class="form-select" onchange="document.getElementById('filterAssignmentFormCounselor').submit()">
                            <option value="">Select Program</option>
                            @if(isset($programs ) && $programs->count() > 0) {{-- Pass distinct programs for counselor's classes --}}
                                @foreach($programs  as $program)
                                @if($program->coundelor_id==Auth::user()->id)
                                <option value="{{$program->program->program_id}}" {{request('program')==$program->program->program_id?'selected':''}}>{{$program->program->name}}</option>
                                @endif
                                @endforeach
                            @endif
                        </select>
                    </div>

                    @if(isset($sem)) {{-- Pass distinct semesters for selected program & counselor's classes --}}
                    <div class="col-md-2">
                        <label for="semester_filter_c" class="form-label">Semester</label>
                        <select name="sem" id="semester_filter_c" class="form-select" onchange="document.getElementById('filterAssignmentFormCounselor').submit()">
                            <option value="">Select Semester</option>
                            @foreach($sem as $sem)
                           @if($sem->coundelor_id == Auth::user()->id)
                                <option value="{{$sem->sem}}" {{request('sem')==$sem->sem?'selected':''}}>Semester {{$sem->sem}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(isset($year)) {{-- Pass distinct years for selected program/sem & counselor's classes --}}
                    <div class="col-md-2">
                        <label for="year_filter_c" class="form-label">Batch Year</label>
                        <select name="year" id="year_filter_c" class="form-select" onchange="document.getElementById('filterAssignmentFormCounselor').submit()">
                            <option value="">Select Year</option>
                            @foreach($year as $year)
                                @if($year->coundelor_id==Auth::user()->id)
                                <option value="{{$year->year}}" {{request('year')==$year->year?'selected' : ''}}>{{$year->year}}</option>
                                @endif
                                @endforeach
                        </select>
                    </div>
                    @endif

                    @if(isset($devision)) {{-- Pass distinct divisions for selected program/sem/year & counselor's classes --}}
                    <div class="col-md-2">
                        <label for="devision_filter_c" class="form-label">Division</label>
                        <select name="devision" id="devision_filter_c" class="form-select" onchange="document.getElementById('filterAssignmentFormCounselor').submit()">
                            <option value="">Select Division</option>
                            @foreach($devision as $devision)
                                @if($devision->coundelor_id == Auth::user()->id)
                                <option value="{{$devision->devision}}" {{request('devision')==$devision->devision?'selected':''}}>{{$devision->devision}}</option>
                                @endif
                                @endforeach
                        </select>
                    </div>
                    @endif

                    @if(isset($teacher)) {{-- Pass full teacher list --}}
                    <div class="col-md-3">
                        <label for="teacher_filter_c" class="form-label">Teacher</label>
                        <select name="teacher" id="teacher_filter_c" class="form-select" onchange="document.getElementById('filterAssignmentFormCounselor').submit()">
                            <option value="">Select Teacher</option>
                            @foreach($teacher as $teacher)
                                @if($teacher->role != 'admin' && $teacher->role != 'student')
                                <option value="{{$teacher->id}}" {{request('teacher')==$teacher->id?'selected':''}}>{{$teacher->name}}</option>
                                @endif
                                @endforeach
                        </select>
                    </div>
                    @endif
                </div>
            </form>

            @if(isset($class_id) && $class_id && isset($subject))
            <hr class="my-4">
            <h5>Assign Subjects for Teacher: <strong>{{ $teacher->firstWhere('id', request('teacher'))->name ?? 'Selected Teacher' }}</strong></h5>
            <p class="text-muted">
                For Class: {{ $class_details_info ?? 'Details based on filters' }} {{-- Pass $class_details_info from controller --}}
            </p>
            <form action="{{ url('/subjectallocated') }}" method="post">
                @csrf
                <input type="hidden" value="{{ request('teacher') }}" name="teacher">
                <input type="hidden" value="{{ $class_id }}" name="class_id">

                <div class="mb-3 border p-3 rounded">
                    <label class="form-label fw-bold">Available Subjects for the Selected Class:</label>
                    @foreach($subject  as $subject)
                    @if($subject->class_id == $class_id)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"value="{{$subject->subject_id}}" name="subject[]" id="{{$subject->subject_id}}">
                            <label class="form-check-label" for="{{$subject->subject_id}}">
                                {{ $subject->subject_name }} ({{ $subject->subject_code }})
                                - <small class="text-muted">{{ ucfirst($subject->category) }}, {{ $subject->lecture_category == 'T' ? 'Theory' : ($subject->lecture_category == 'P' ? 'Practical' : '') }}</small>
                            </label>
                        </div>
                        @endif
                    @endforeach
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-square me-2"></i>Save Assignments
                </button>
            </form>
            @elseif(request('program') && request('teacher') && !(request('sem') && request('year') && request('devision')))
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle-fill me-2"></i>Please complete all class filters (Semester, Batch Year, Division) to load subjects for assignment.
                </div>
             @elseif(request('program') && request('sem') && request('year') && request('devision') && request('teacher') && (!isset($subjects_for_class) || $subjects_for_class->count() == 0))
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>No subjects found associated with the selected class. Please add subjects to this class configuration first.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection