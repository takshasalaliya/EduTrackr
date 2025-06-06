@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Add New Subject')
@section('page_title', 'Add New Subject')

@section('addsubject') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div></div>
    <a href="https://drive.google.com/file/d/1VskKKVoWagbbdjcDI_IP8IAR0hRCOshT/view?usp=sharing" target="_blank" class="btn btn-outline-success">
        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Sample Excel File
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
        <div class="alert alert-danger alert-dismissible fade show" role="alert"> {{-- Corrected to alert-danger --}}
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- Add Subject Form --}}
        <div class="col-lg-8 mb-4">
            <div class="card card-custom">
                <div class="card-header">
                    <i class="bi bi-book-half me-2"></i>New Subject Details
                </div>
                <div class="card-body p-4">
                    <form action="{{ url('add_subject') }}" method="post"> {{-- Use url() helper --}}
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Subject Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="e.g., Data Structures" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shortname" class="form-label">Short Name / Abbreviation</label>
                                <input type="text" id="shortname" class="form-control @error('shortname') is-invalid @enderror" name="shortname" value="{{ old('shortname') }}" placeholder="e.g., DS">
                                @error('shortname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="code">Subject Code <span class="text-danger">*</span></label>
                                <input type="text" id="code" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" placeholder="e.g., CS201" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subject Type (Category) <span class="text-danger">*</span></label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('category') is-invalid @enderror" type="radio" name="category" id="category_required_counselor" value="required" {{ old('category') == 'required' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="category_required_counselor">Required</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('category') is-invalid @enderror" type="radio" name="category" id="category_optional_counselor" value="optional" {{ old('category') == 'optional' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="category_optional_counselor">Optional</label>
                                    </div>
                                </div>
                                @error('category')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                             <div class="col-md-6 mb-3">
                                <label class="form-label">Lecture/Practical (L/P) <span class="text-danger">*</span></label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('l_category') is-invalid @enderror" type="radio" name="l_category" id="l_category_t_counselor" value="T" {{ old('l_category') == 'T' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="l_category_t_counselor">Theory (T)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('l_category') is-invalid @enderror" type="radio" name="l_category" id="l_category_p_counselor" value="P" {{ old('l_category') == 'P' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="l_category_p_counselor">Practical (P)</label>
                                    </div>
                                </div>
                                @error('l_category')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assign to Classes You Counsel (Optional)</label>
                            <p class="form-text">Select one or more classes you counsel to associate this subject with.</p>
                            <div class="border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                                @php $counselorClassesAvailable = false; @endphp
                                @if(isset($classes) && count($classes) > 0 && isset(Auth::user()->id))
                                    @foreach($classes as $class_option)
                                        @if($class_option->coundelor_id == Auth::user()->id) {{-- Typo 'coundelor_id' kept --}}
                                            @php $counselorClassesAvailable = true; @endphp
                                            <div class="form-check">
                                                <input class="form-check-input @error('class') is-invalid @enderror" type="checkbox" name="class[]" value="{{ $class_option->id }}" id="class_assign_{{ $class_option->id }}"
                                                       {{ (is_array(old('class')) && in_array($class_option->id, old('class'))) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="class_assign_{{ $class_option->id }}">
                                                    {{ $class_option->program->name ?? 'N/A Program' }} / Sem: {{ $class_option->sem ?? 'N/A' }} / Div: {{ $class_option->devision ?? 'N/A' }} (Batch: {{ $class_option->year ?? 'N/A' }})
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if(!$counselorClassesAvailable)
                                        <p class="text-muted m-0">You are not currently assigned as a counselor to any classes.</p>
                                    @endif
                                @else
                                    <p class="text-muted m-0">No classes available or you are not logged in.</p>
                                @endif
                            </div>
                             @error('class')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('class.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4 pt-2">
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="bi bi-plus-circle-fill me-2"></i>Add Subject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Upload Excel Form --}}
        <div class="col-lg-4 mb-4">
            <div class="card card-custom">
                <div class="card-header">
                    <i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Bulk Add Subjects
                </div>
                <div class="card-body p-4">
                    <p class="small text-muted">
                        Upload an Excel file to add multiple subjects at once.
                        Ensure the file follows the format specified in the sample file.
                    </p>
                    <form action="{{ url('c_excel_subject') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_subject_file_counselor" class="form-label">Excel File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('excel_subject') is-invalid @enderror" name="excel_subject" id="excel_subject_file_counselor" required accept=".xlsx, .xls, .csv">
                            @error('excel_subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button class="btn btn-success w-100" type="submit">
                            <i class="bi bi-upload me-2"></i>Upload Excel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection