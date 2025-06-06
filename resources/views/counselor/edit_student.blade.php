@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Edit Student Details')
@section('page_title', 'Edit Student Information')

@section('edit_form') {{-- Keeping this section name as requested --}}

{{-- Manual Page Header (though 'page_title' from layout should handle the H1) --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div></div> {{-- Empty div for spacing if main title is left-aligned --}}
    <a href="{{ url('student_list') }}" class="btn btn-outline-secondary"> {{-- Assuming this is the counselor's student list route --}}
        <i class="bi bi-arrow-left-circle me-2"></i>Back to My Students List
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
        // Assuming $students is the student instance being edited. Renaming for clarity.
        $student = $students;
    @endphp

    <div class="card card-custom">
        <div class="card-header">
            <i class="bi bi-pencil-square me-2"></i>Editing Student: {{ $student->name }} ({{ $student->enrollment_number }})
        </div>
        <div class="card-body p-4">
            <form action="{{ url('/editstudent/'.$student->student_id) }}" method="post">
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
                        @error('s_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="s_email" class="form-label">Student Email ID (Cannot be changed)</label>
                        <input type="email" id="s_email" class="form-control @error('s_email') is-invalid @enderror" name="s_email_display" value="{{ old('s_email', $student->email) }}" disabled>
                        {{-- To ensure the email is still available if needed, even if not submitted due to disabled state --}}
                        {{-- If your backend doesn't attempt to update email if 's_email' isn't in request, this hidden input might not be needed. --}}
                        {{-- <input type="hidden" name="s_email" value="{{ $student->email }}"> --}}
                        @error('s_email') {{-- This error might not trigger if field is disabled and not submitted --}}
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
                        @error('p_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="p_email" class="form-label">Parent Email ID</label>
                        <input type="email" class="form-control @error('p_email') is-invalid @enderror" id="p_email" name="p_email" value="{{ old('p_email', $student->parents_email) }}">
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
                            <option value="" disabled>Select Class</option> {{-- Changed default from selected to disabled --}}
                            @if(isset($classes))
                                @foreach($classes as $class_option)
                                @if(Auth::user()->id==$class_option->coundelor_id)
                                <option value="{{ $class_option->id }}" {{ old('student_class', $student->class_id) == $class_option->id ? 'selected' : '' }}>
                                    {{ $class_option->program->name ?? 'N/A Program' }} / Batch: {{ $class_option->year ?? 'N/A' }} / Sem: {{ $class_option->sem ?? 'N/A' }} / Div: {{ $class_option->devision ?? 'N/A' }}
                                </option>
                                @endif
                                @endforeach
                            @endif
                        </select>
                        @error('student_class')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     {{-- Add Password field if counselors can reset passwords --}}
                    {{--
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Leave blank to keep current">
                        <div class="form-text">Only enter if you want to change the student's password.</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    --}}
                </div>

                <div class="mt-4 pt-2">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="bi bi-save-fill me-2"></i>Update Student
                    </button>
                    <a href="{{ url('student_list') }}" class="btn btn-outline-secondary btn-lg ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection