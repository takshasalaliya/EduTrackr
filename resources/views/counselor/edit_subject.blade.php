@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Edit Subject Details')
@section('page_title', 'Edit Subject Information')

@section('addsubject') {{-- Keeping this section name as requested --}}

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div></div>
    <a href="{{ url('subject_list') }}" class="btn btn-outline-secondary"> {{-- Assuming this is the counselor's subject list route --}}
        <i class="bi bi-arrow-left-circle me-2"></i>Back to My Subjects List
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

    @php
        // Assuming $subjects is the subject instance being edited.
        $subject = $subjects;
    @endphp

    <div class="card card-custom">
        <div class="card-header">
            <i class="bi bi-pencil-square me-2"></i>Editing Subject: {{ $subject->subject_name }} ({{ $subject->subject_code }})
        </div>
        <div class="card-body p-4">
            <form action="{{ url('/edit_subject/'.$subject->subject_id) }}" method="post">
                @csrf
                {{-- Add @method('PUT') or @method('PATCH') if your route expects it --}}

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="name_counselor_edit">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" id="name_counselor_edit" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $subject->subject_name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="shortname_counselor_edit">Short Name / Abbreviation</label>
                        <input type="text" id="shortname_counselor_edit" class="form-control @error('shortname') is-invalid @enderror" name="shortname" value="{{ old('shortname', $subject->short_name) }}">
                        @error('shortname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="code_counselor_edit">Subject Code <span class="text-danger">*</span></label>
                        <input type="text" id="code_counselor_edit" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code', $subject->subject_code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subject Type (Category) <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('category') is-invalid @enderror" type="radio" name="category" id="category_required_counselor_edit" value="required" {{ old('category', $subject->category) == 'required' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="category_required_counselor_edit">Required</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('category') is-invalid @enderror" type="radio" name="category" id="category_optional_counselor_edit" value="optional" {{ old('category', $subject->category) == 'optional' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="category_optional_counselor_edit">Optional</label>
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
                                <input class="form-check-input @error('l_category') is-invalid @enderror" type="radio" name="l_category" id="l_category_t_counselor_edit" value="T" {{ old('l_category', $subject->lecture_category ?? '') == 'T' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="l_category_t_counselor_edit">Theory (T)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('l_category') is-invalid @enderror" type="radio" name="l_category" id="l_category_p_counselor_edit" value="P" {{ old('l_category', $subject->lecture_category ?? '') == 'P' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="l_category_p_counselor_edit">Practical (P)</label>
                            </div>
                        </div>
                        @error('l_category') {{-- Ensure error key matches 'l_category' --}}
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Assign to Class (from classes you counsel)</label>
                    <p class="form-text">Select the primary class this subject belongs to from the list of classes you counsel.</p>
                    <div class="border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                        @php $counselorClassesForSubjectEdit = false; @endphp
                        @if(isset($classes) && count($classes) > 0 && isset(Auth::user()->id))
                            @foreach($classes as $class_option)
                                @if($class_option->coundelor_id == Auth::user()->id) {{-- Typo 'coundelor_id' kept --}}
                                    @php $counselorClassesForSubjectEdit = true; @endphp
                                    <div class="form-check">
                                        <input class="form-check-input @error('class') is-invalid @enderror" type="radio" name="class" value="{{ $class_option->id }}" id="class_edit_{{ $class_option->id }}"
                                               {{ old('class', $subject->class_id) == $class_option->id ? 'checked' : '' }}>
                                        <label class="form-check-label" for="class_edit_{{ $class_option->id }}">
                                            {{ $class_option->program->name ?? 'N/A Program' }} / Sem: {{ $class_option->sem ?? 'N/A' }} / Div: {{ $class_option->devision ?? 'N/A' }} (Batch: {{ $class_option->year ?? 'N/A' }})
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                             @if(!$counselorClassesForSubjectEdit)
                                <p class="text-muted m-0">You are not currently assigned as a counselor to any classes to associate this subject with.</p>
                            @endif
                        @else
                            <p class="text-muted m-0">No classes available or you are not logged in.</p>
                        @endif
                    </div>
                    @error('class')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mt-4 pt-2">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="bi bi-save-fill me-2"></i>Update Subject
                    </button>
                    <a href="{{ url('subject_list') }}" class="btn btn-outline-secondary btn-lg ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection