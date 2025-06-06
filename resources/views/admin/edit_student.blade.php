@extends('admin.layout')

@section('title', 'Edit Student')
{{-- @section('page_title', 'Edit Student Information') --}} {{-- Keeping this commented --}}

@section('edit_form')

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Edit Student Information</h1>
    <div>
        <a href="{{ url('student_list_admin') }}" class="btn btn-outline-secondary"> {{-- Assuming this is the route for the student list --}}
            <i class="bi bi-arrow-left-circle me-2"></i>Back to Student List
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
        // Assuming $students is the student instance being edited. Renaming for clarity.
        $student = $students;
    @endphp

    <div class="card card-custom">
        <div class="card-header">
            <i class="bi bi-pencil-square me-2"></i>Editing Student: {{ $student->name }} ({{ $student->enrollment_number }})
        </div>
        <div class="card-body p-4">
            <form action="{{ url('/editstudent_admin/'.$student->student_id) }}" method="post">
                @csrf
                {{-- Add @method('PUT') or @method('PATCH') if your route expects it --}}

                <h5 class="mb-3 text-primary">Student Details</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $student->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="rollnumber" class="form-label">Enrollment Number <span class="text-danger">*</span></label>
                        <input type="text" id="rollnumber" class="form-control @error('rollnumber') is-invalid @enderror" name="rollnumber" value="{{ old('rollnumber', $student->enrollment_number) }}" required>
                        @error('rollnumber')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="s_phone" class="form-label">Student Phone Number</label>
                        <input type="tel" class="form-control @error('s_phone') is-invalid @enderror" id="s_phone" name="s_phone" value="{{ old('s_phone', $student->phone_number) }}">
                        {{-- Corrected error key from 'department' to 's_phone' based on input name --}}
                        @error('s_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="s_email" class="form-label">Student Email ID</label>
                        <input type="email" id="s_email" class="form-control @error('s_email') is-invalid @enderror" name="s_email" value="{{ old('s_email', $student->email) }}" disabled>
                        @error('s_email') {{-- Assuming error key matches input name --}}
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3 text-primary">Parent Details</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="p_phone" class="form-label">Parent Phone Number</label>
                        <input type="tel" class="form-control @error('p_phone') is-invalid @enderror" id="p_phone" name="p_phone" value="{{ old('p_phone', $student->parents_phone_number) }}">
                        {{-- Corrected error key from 'year' to 'p_phone' based on input name --}}
                        @error('p_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="p_email" class="form-label">Parent Email ID</label>
                        <input type="email" class="form-control @error('p_email') is-invalid @enderror" id="p_email" name="p_email" value="{{ old('p_email', $student->parents_email) }}">
                        {{-- Corrected error key from 'year' to 'p_email' based on input name --}}
                        @error('p_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3 text-primary">Academic Details</h5>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="student_class" class="form-label">Assign to Class <span class="text-danger">*</span></label>
                        <select name="student_class" id="student_class" class="form-select @error('student_class') is-invalid @enderror" required>
                            <option value="" disabled>Select Class</option>
                            @foreach($classes as $class_option) {{-- Renamed $class to $class_option --}}
                            <option value="{{ $class_option->id }}" {{ old('student_class', $student->class_id) == $class_option->id ? 'selected' : '' }}>
                                {{ $class_option->program->name ?? 'N/A Program' }} / Batch: {{ $class_option->year ?? 'N/A' }} / Sem: {{ $class_option->sem ?? 'N/A' }} / Div: {{ $class_option->devision ?? 'N/A' }}
                            </option>
                            @endforeach
                        </select>
                        @error('student_class')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="bi bi-save-fill me-2"></i>Update Student
                    </button>
                    <a href="{{ url('student_list_admin') }}" class="btn btn-outline-secondary btn-lg ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection