@extends('admin.layout')

@section('title', 'Subject & Teacher Assignment')
{{-- @section('page_title', 'Assign Subjects to Teachers') --}} {{-- Keeping this commented --}}

@section('teachingstaff')

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Assign Subjects to Teachers</h1>
    <div>
        <a href="https://docs.google.com/spreadsheets/d/1IglJfoj9kI0Co6IUDcqU9OwXHL-19wV7/edit?usp=sharing&ouid=109161218020310229957&rtpof=true&sd=true" target="_blank" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Sample Excel for Bulk Assign
        </a>
    </div>
</div>

<div class="container-fluid">

    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {!! session('success') !!} {{-- Allow HTML for potential detailed success messages --}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {!! session('error') !!} {{-- Allow HTML for potential detailed error messages --}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Bulk Upload Form --}}
    <div class="card card-custom mb-4">
        <div class="card-header">
            <i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Bulk Assign Subjects via Excel
        </div>
        <div class="card-body">
            <form action="{{ url('subject_maping') }}" method="post" enctype="multipart/form-data" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-8">
                    <label for="excel_subject_maping" class="form-label">Upload Excel File</label>
                    <input type="file" class="form-control @error('subject_maping') is-invalid @enderror" id="excel_subject_maping" name="subject_maping" required accept=".xlsx, .xls, .csv">
                    @error('subject_maping')
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


    {{-- Manual Assignment Form --}}
    <div class="card card-custom">
        <div class="card-header">
            <i class="bi bi-ui-checks-grid me-2"></i>Manual Subject Assignment
        </div>
        <div class="card-body">
            <p class="text-muted">Select filters to narrow down the class and teacher, then assign subjects.</p>

            <form action="{{ url('subjectallocated_admin') }}" method="GET" id="filterForm">
                {{-- @csrf --}} {{-- Not needed for GET requests, but doesn't harm --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="program_filter" class="form-label">Program</label>
                        <select name="program" id="program_filter" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Select Program</option>
                            @foreach($programs as $program_item)
                            <option value="{{ $program_item->program_id }}" {{ request('program') == $program_item->program_id ? 'selected' : '' }}>{{ $program_item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(isset($sem) && count($sem) > 0)
                    <div class="col-md-2">
                        <label for="semester_filter" class="form-label">Semester</label>
                        <select name="sem" id="semester_filter" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Select Semester</option>
                            @foreach($sem as $sem_item)
                            <option value="{{ $sem_item->sem }}" {{ request('sem') == $sem_item->sem ? 'selected' : '' }}>Semester {{ $sem_item->sem }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(isset($year) && count($year) > 0)
                    <div class="col-md-2">
                        <label for="year_filter" class="form-label">Batch Year</label>
                        <select name="year" id="year_filter" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Select Year</option>
                            @foreach($year as $year_item)
                            <option value="{{ $year_item->year }}" {{ request('year') == $year_item->year ? 'selected' : '' }}>{{ $year_item->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(isset($devision) && count($devision) > 0)
                    <div class="col-md-2">
                        <label for="devision_filter" class="form-label">Division</label>
                        <select name="devision" id="devision_filter" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Select Division</option>
                            @foreach($devision as $devision_item)
                            <option value="{{ $devision_item->devision }}" {{ request('devision') == $devision_item->devision ? 'selected' : '' }}>{{ $devision_item->devision }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(isset($teacher) && count($teacher)) {{-- Renamed $teacher to $teacher_list for clarity --}}
                    <div class="col-md-3">
                        <label for="teacher_filter" class="form-label">Teacher</label>
                        <select name="teacher" id="teacher_filter" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Select Teacher</option>
                            @foreach($teacher as $teacher_item)
                                @if($teacher_item->role != 'admin' && $teacher_item->role != 'student')
                                <option value="{{ $teacher_item->id }}" {{ request('teacher') == $teacher_item->id ? 'selected' : '' }}>{{ $teacher_item->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
                {{-- Excel Download button for specific filtered view - This needs specific route and controller logic --}}
                @if(request('program') && request('sem') && request('year') && request('devision') && request('teacher'))
                    {{-- Assuming $devision_item->id or a similar class identifier is available for the download link if the last filter ($devision) implies a specific class ID --}}
                    {{-- You need to ensure $class_id for the download is correctly determined based on filters --}}
                    {{-- For simplicity, let's assume your controller sets $class_id_for_download if all filters leading to a specific class are set --}}
                 
                    <div class="mb-3">
                        <a href="{{'/excel_teacher_subject/'.$class_id}}" class="btn btn-info btn-sm">
                            <i class="bi bi-download me-2"></i>Download Current Teacher-Subject Assignments
                        </a>
                    </div>
                  
                @endif
            </form>

            @if(isset($class_id) && $class_id && isset($subject) && count($subject) > 0 && request('teacher'))
            <hr class="my-4">
            <h5>Assign Subjects for: <strong>{{ $teacher->firstWhere('id', request('teacher'))->name ?? 'Selected Teacher' }}</strong></h5>
            <p class="text-muted">Class: Based on selected filters (Program, Semester, Batch, Division).</p>
            <form action="{{ url('/subjectallocated_admin') }}" method="post">
                @csrf
                <input type="hidden" value="{{ request('teacher') }}" name="teacher">
                <input type="hidden" value="{{ $class_id }}" name="class_id"> {{-- Pass class_id if needed by controller --}}

                <div class="mb-3 border p-3 rounded">
                    <label class="form-label fw-bold">Available Subjects for the Selected Class:</label>
                    @php $subjectFoundForClass = false; @endphp
                    @foreach($subject as $subject_item)
                        @if($subject_item->class_id == $class_id) {{-- Assuming subject has a direct class_id or is filtered by class --}}
                            @php $subjectFoundForClass = true; @endphp
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ $subject_item->subject_id }}" name="subject[]" id="subject_{{ $subject_item->subject_id }}"
                                       {{ (is_array(old('subject')) && in_array($subject_item->subject_id, old('subject'))) || (isset($assigned_subjects) && in_array($subject_item->subject_id, $assigned_subjects)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="subject_{{ $subject_item->subject_id }}">
                                    {{ $subject_item->subject_name }} ({{ $subject_item->subject_code }})
                                    - <small class="text-muted">{{ ucfirst($subject_item->category) }}, {{ $subject_item->l_category == 'T' ? 'Theory' : ($subject_item->l_category == 'P' ? 'Practical' : '') }}</small>
                                </label>
                            </div>
                        @endif
                    @endforeach
                    @if(!$subjectFoundForClass)
                        <p class="text-warning m-0">No subjects found specifically linked to this exact class configuration. Ensure subjects are correctly associated with classes or programs/semesters.</p>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-square me-2"></i>Save 
                </button>
            </form>
            @elseif(request('program') && request('teacher'))
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle-fill me-2"></i>Please complete all filters (Semester, Batch, Division) to load subjects for assignment.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection