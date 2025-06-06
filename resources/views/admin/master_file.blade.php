@extends('admin.layout')

@section('title', 'Upload Master Data File')
{{-- @section('page_title', 'Master Data File Upload') --}} {{-- Keeping this commented --}}

@section('master_file')

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title"><i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Upload Master Data File</h1>
    <div>
        <a href="https://docs.google.com/spreadsheets/d/1eWttKINWwliAXHagM2fsAlSjK8E1SC8u/edit?usp=sharing&ouid=109161218020310229957&rtpof=true&sd=true" target="_blank" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Download Sample Master Excel File
        </a>
    </div>
</div>

<div class="container-fluid">

    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {!! session('success') !!} {{-- Allow HTML if success message contains details --}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert"> {{-- Corrected to alert-danger --}}
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {!! session('error') !!} {{-- Allow HTML if error message contains details --}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6"> {{-- Centered and slightly narrower column for focused upload form --}}
            <div class="card card-custom">
                <div class="card-header">
                    <i class="bi bi-upload me-2"></i>Upload Master Excel File
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">
                        Use this form to upload the master data file for the system. This file might contain initial setup data for programs, classes, subjects, students, etc.
                        Please ensure your file strictly adheres to the structure and format specified in the
                        <a href="https://docs.google.com/spreadsheets/d/1eWttKINWwliAXHagM2fsAlSjK8E1SC8u/edit?usp=sharing&ouid=109161218020310229957&rtpof=true&sd=true" target="_blank">sample master Excel file</a>.
                    </p>
                    <div class="alert alert-warning small">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Caution:</strong> Uploading a master file can significantly alter system data. Please double-check the file content and structure before proceeding.
                    </div>
                    <hr>
                    <form action="{{ url('master_file') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="master_file_upload" class="form-label">Select Master Excel File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('master') is-invalid @enderror" name="master" id="master_file_upload" required accept=".xlsx, .xls, .csv">
                            @error('master')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Accepted file types: .xlsx, .xls, .csv</div>
                        </div>

                        <div class="d-grid"> {{-- d-grid for full-width button --}}
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="bi bi-cloud-upload-fill me-2"></i>Upload and Process Master File
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection