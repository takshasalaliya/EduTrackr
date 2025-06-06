@extends('admin.layout')

@section('title', 'Edit Teacher')
{{-- @section('page_title', 'Edit Teacher Information') --}} {{-- Keeping this commented --}}

@section('edit_teacher')

{{-- Manual Page Header if not using @section('page_title') in layout --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Edit Teacher Information</h1>
    <div>
        <a href="{{ url('teacher_list') }}" class="btn btn-outline-secondary"> {{-- Assuming 'teacher_list' is the route for the teacher list --}}
            <i class="bi bi-arrow-left-circle me-2"></i>Back to Teacher List
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
        {{-- Corrected alert type to danger for errors --}}
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card card-custom">
        <div class="card-header">
            <i class="bi bi-pencil-square me-2"></i>Editing Teacher: {{ $teacher->name }}
        </div>
        <div class="card-body p-4">
            <form action="{{ url('/edit_teacher/'.$teacher->id) }}" method="post">
                @csrf
                {{-- Since HTML forms don't natively support PUT/PATCH, Laravel uses a hidden field --}}
                {{-- You might need @method('PUT') or @method('PATCH') here if your route expects it.
                     If your route is POST, then just @csrf is fine. --}}

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $teacher->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="shortname" class="form-label">Short Name <span class="text-danger">*</span></label>
                        <input type="text" id="shortname" class="form-control @error('shortname') is-invalid @enderror" name="shortname" value="{{ old('shortname', $teacher->short_name) }}" required>
                        {{-- Corrected error key from 'rollnumber' to 'shortname' assuming it was a typo --}}
                        @error('shortname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $teacher->phone_number) }}" required>
                        {{-- Corrected error key from 'department' to 'phone' assuming it was a typo --}}
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="emailAddress" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" id="emailAddress" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $teacher->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="" disabled>Select Role</option>
                            <option value="reader" {{ old('role', $teacher->role) == 'reader' ? 'selected' : '' }}>Reader</option>
                            <option value="counselor" {{ old('role', $teacher->role) == 'counselor' ? 'selected' : '' }}>Counselor</option>
                            <option value="admin" {{ old('role', $teacher->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Leave blank to keep current password">
                        <div class="form-text">Only enter a new password if you want to change it.</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="bi bi-save-fill me-2"></i>Update Teacher
                    </button>
                    <a href="{{ url('teacher_list') }}" class="btn btn-outline-secondary btn-lg ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection