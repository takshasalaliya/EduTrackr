@extends('admin.layout')

@section('title', 'Add New Class')
{{-- @section('page_title', 'Add New Class') --}} {{-- Keeping this commented --}}

@section('add_class')

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Add New Class</h1>
    <div>
        <a href="https://docs.google.com/spreadsheets/d/1FF9u-QerAjEkbazzJhEtDH9f4pRedrGa/edit?usp=sharing&ouid=109161218020310229957&rtpof=true&sd=true" target="_blank" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Sample Excel File
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
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- Add Class Form --}}
        <div class="col-lg-8 mb-4">
            <div class="card card-custom">
                <div class="card-header">
                    <i class="bi bi-plus-square-fill me-2"></i>Class Details
                </div>
                <div class="card-body p-4">
                    <form action="{{ url('add_class_admin') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="stream" class="form-label">Program <span class="text-danger">*</span></label>
                                <select name="stream" id="stream" class="form-select @error('stream') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('stream') ? '' : 'selected' }}>Select Program</option>
                                    @foreach($programs as $program)
                                    <option value="{{ $program->program_id }}" {{ old('stream') == $program->program_id ? 'selected' : '' }}>{{ $program->name }}</option>
                                    @endforeach
                                </select>
                                @error('stream')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Academic Batch (Year) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('date_from') is-invalid @enderror" name="date_from" id="date_from" min="1900" max="2099" step="1" value="{{ old('date_from', date('Y') - 1 ) }}" placeholder="From" required>
                                    <span class="input-group-text">-</span>
                                    <input type="number" class="form-control @error('date_to') is-invalid @enderror" name="date_to" id="date_to" min="1900" max="2099" step="1" value="{{ old('date_to', date('Y')) }}" placeholder="To" required>
                                </div>
                                @error('date_from')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('date_to')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="semnumber" class="form-label">Semester Number <span class="text-danger">*</span></label>
                                <select name="semnumber" id="semnumber" class="form-select @error('semnumber') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('semnumber') ? '' : 'selected' }}>Select Semester</option>
                                    @for ($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ old('semnumber') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('semnumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="devision" class="form-label">Division <span class="text-danger">*</span></label>
                                <input type="text" id="devision" class="form-control @error('devision') is-invalid @enderror" name="devision" value="{{ old('devision') }}" placeholder="e.g., A, B, Morning" required>
                                @error('devision')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="counselor" class="form-label">Class Counselor <span class="text-danger">*</span></label>
                                <select name="counselor" id="counselor" class="form-select @error('counselor') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('counselor') ? '' : 'selected' }}>Select Counselor</option>
                                    @foreach($datas as $data) {{-- Assuming $datas are the counselors/teachers --}}
                                    <option value="{{ $data->id }}" {{ old('counselor') == $data->id ? 'selected' : '' }}>{{ $data->name }}</option>
                                    @endforeach
                                </select>
                                @error('counselor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-2">
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="bi bi-plus-circle-fill me-2"></i>Add Class
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
                    <i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Bulk Add Classes
                </div>
                <div class="card-body p-4">
                     <p class="small text-muted">
                        Upload an Excel file to add multiple classes at once.
                        Ensure the file follows the format specified in the sample file.
                    </p>
                    <form action="{{ url('add_class_excel_admin') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Excel File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('excel_file') is-invalid @enderror" name="excel_file" id="excel_file" required accept=".xlsx, .xls, .csv">
                            @error('excel_file')
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