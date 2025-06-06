@extends('counselor.layoutcounselor') {{-- Adjusted path assuming 'counselor' is a directory in views --}}

@section('title', 'Add Students (Bulk Upload)')
@section('page_title', 'Add Students - Bulk Upload via Excel')

@section('add_form') {{-- Keeping this section name as requested --}}

{{-- The page title is now handled by @section('page_title') and the layout's header.
     The "Sample Excel File" button is moved into the new header structure below. --}}

<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div></div> {{-- Empty div for spacing if needed, or remove if title is always left-aligned by layout --}}
    <a href="https://docs.google.com/spreadsheets/d/13x9mSTJKIwwvh4rqK7FR_D7mCiCx7P8v/edit?usp=sharing&ouid=109161218020310229957&rtpof=true&sd=true" target="_blank" class="btn btn-outline-success">
        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Download Sample Excel File
    </a>
</div>


<div class="container-fluid"> {{-- Changed from your inner container to utilize full width of main-content area --}}

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

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card card-custom">
                <div class="card-header">
                    <i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Upload Student Data Excel
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">
                        Use this form to add multiple students to the system by uploading an Excel file.
                        Please ensure your file adheres to the structure and format of the
                        <a href="https://docs.google.com/spreadsheets/d/13x9mSTJKIwwvh4rqK7FR_D7mCiCx7P8v/edit?usp=sharing&ouid=109161218020310229957&rtpof=true&sd=true" target="_blank">sample Excel file</a>.
                    </p>
                    <hr>
                    <form action="{{ url('add_student_excel') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file_upload" class="form-label">Select Excel File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('excel_file') is-invalid @enderror" name="excel_file" id="excel_file_upload" required accept=".xlsx, .xls, .csv">
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Accepted file types: .xlsx, .xls, .csv</div>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="bi bi-upload me-2"></i>Upload and Process File
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection