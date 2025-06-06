@extends('admin.layout')

@section('title', 'Edit Subject')
{{-- @section('page_title', 'Edit Subject Information') --}} {{-- Keeping this commented --}}

@section('addsubject') {{-- Keeping this section name as per your request --}}

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Edit Subject Information</h1>
    <div>
        <a href="{{ url('subject_list_admin') }}" class="btn btn-outline-secondary"> {{-- Assuming this is the route for the subject list --}}
            <i class="bi bi-arrow-left-circle me-2"></i>Back to Subject List
        </a>
    </div>
</div>

<div class="container-fluid">

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
        // Assuming $subjects is the subject instance being edited. Renaming for clarity.
        $subject = $subjects;
    @endphp

    <div class="card card-custom">
        <div class="card-header">
            <i class="bi bi-pencil-square me-2"></i>Editing Subject: {{ $subject->subject_name }} ({{ $subject->subject_code }})
        </div>
        <div class="card-body p-4">
            <form action="{{ url('/edit_subject_admin/'.$subject->subject_id) }}" method="post">
                @csrf
                {{-- Add @method('PUT') or @method('PATCH') if your route expects it --}}

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="name">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $subject->subject_name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="shortname">Short Name / Abbreviation</label>
                        <input type="text" id="shortname" class="form-control @error('shortname') is-invalid @enderror" name="shortname" value="{{ old('shortname', $subject->short_name) }}">
                        @error('shortname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="code">Subject Code <span class="text-danger">*</span></label>
                        <input type="text" id="code" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code', $subject->subject_code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subject Type (Category) <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('category') is-invalid @enderror" type="radio" name="category" id="category_required" value="required" {{ old('category', $subject->category) == 'required' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="category_required">Required</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('category') is-invalid @enderror" type="radio" name="category" id="category_optional" value="optional" {{ old('category', $subject->category) == 'optional' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="category_optional">Optional</label>
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
                                <input class="form-check-input @error('l_category') is-invalid @enderror" type="radio" name="l_category" id="l_category_t" value="T" {{ old('l_category', $subject->lecture_category ?? '') == 'T' ? 'checked' : '' }} required> {{-- Assuming field is lecture_category --}}
                                <label class="form-check-label" for="l_category_t">Theory (T)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('l_category') is-invalid @enderror" type="radio" name="l_category" id="l_category_p" value="P" {{ old('l_category', $subject->lecture_category ?? '') == 'P' ? 'checked' : '' }} required> {{-- Assuming field is lecture_category --}}
                                <label class="form-check-label" for="l_category_p">Practical (P)</label>
                            </div>
                        </div>
                        @error('l_category')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Assign to Class (If subject is specific to one class)</label>
                    <p class="form-text">If this subject is taught in multiple classes, manage assignments through "Subject & Teacher Mapping" or a similar section.</p>
                    <div class="border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                        @if(isset($classes) && count($classes) > 0)
                            @foreach($classes as $class_option)
                            <div class="form-check">
                                <input class="form-check-input @error('class') is-invalid @enderror" type="radio" name="class" value="{{ $class_option->id }}" id="class_{{ $class_option->id }}"
                                       {{ old('class', $subject->class_id) == $class_option->id ? 'checked' : '' }}> {{-- Assuming subject has a class_id for single assignment --}}
                                <label class="form-check-label" for="class_{{ $class_option->id }}">
                                    {{ $class_option->program->name ?? 'N/A Program' }} / Sem: {{ $class_option->sem ?? 'N/A' }} / Div: {{ $class_option->devision ?? 'N/A' }} (Batch: {{ $class_option->year ?? 'N/A' }})
                                </label>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted m-0">No classes available to assign. Please add classes first.</p>
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
                    <a href="{{ url('subject_list_admin') }}" class="btn btn-outline-secondary btn-lg ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection