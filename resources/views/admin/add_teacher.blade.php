@extends('admin.layout')

@section('title', 'Add New Teacher')
{{-- @section('page_title', 'Add New Teacher') --}} {{-- Keeping this commented as you are using 'add_teacher' for content --}}

@section('add_teacher')

{{-- If you still need page_title, you'd add it here or make 'add_teacher' yield into a specific content area that is below the page_title in your layout --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Add New Teacher</h1>
    <div>
        <a href="https://docs.google.com/spreadsheets/d/1Pl731v0ze10bFOy1icGR2BEz_NuIpQyI/edit?usp=sharing&ouid=109161218020310229957&rtpof=true&sd=true" target="_blank" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Sample Excel File
        </a>
    </div>
</div>


<div class="container-fluid"> {{-- Use container-fluid for full width within content area --}}

    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- Add Teacher Form --}}
        <div class="col-lg-8 mb-4">
            <div class="card card-custom">
                <div class="card-header">
                    <i class="bi bi-person-plus-fill me-2"></i>Teacher Details
                </div>
                <div class="card-body p-4">
                    <form action="{{ url('add_teacher_admin') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="e.g., John Doe" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shortname" class="form-label">Short Name <span class="text-danger">*</span></label>
                                <input type="text" id="shortname" class="form-control @error('shortname') is-invalid @enderror" name="shortname" value="{{ old('shortname') }}" placeholder="e.g., JD" required>
                                @error('shortname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="e.g., 9876543210" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="emailAddress" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" id="emailAddress" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="e.g., teacher@example.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select Role</option>
                                    <option value="reader" {{ old('role') == 'reader' ? 'selected' : '' }}>Reader</option>
                                    <option value="counselor" {{ old('role') == 'counselor' ? 'selected' : '' }}>Counselor</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-2">
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="bi bi-person-check-fill me-2"></i>Submit Teacher
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
                    <i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Bulk Upload Teachers
                </div>
                <div class="card-body p-4">
                    <p class="small text-muted">
                        Upload an Excel file to add multiple teachers at once.
                        Ensure the file follows the format specified in the sample file.
                    </p>
                    <form action="{{ url('excel_teacher') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_teacher_file" class="form-label">Excel File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('excel_teacher') is-invalid @enderror" id="excel_teacher_file" name="excel_teacher" required accept=".xlsx, .xls, .csv">
                             @error('excel_teacher')
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