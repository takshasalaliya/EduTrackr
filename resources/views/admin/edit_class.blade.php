@extends('admin.layout')

@section('title', 'Edit Class')
{{-- @section('page_title', 'Edit Class Information') --}} {{-- Keeping this commented --}}

@section('edit_class')

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title">Edit Class Information</h1>
    <div>
        <a href="{{ url('class_list') }}" class="btn btn-outline-secondary"> {{-- Assuming 'class_list' is the route for the class list --}}
            <i class="bi bi-arrow-left-circle me-2"></i>Back to Class List
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
        // Assuming $datas is the class instance being edited. Renaming for clarity.
        $class_instance = $datas;
        // Splitting the year for academic batch
        $year_parts = explode('-', $class_instance->year);
        $year_from = $year_parts[0] ?? (date('Y') - 1);
        $year_to = $year_parts[1] ?? date('Y');
    @endphp

    <div class="card card-custom">
        <div class="card-header">
            <i class="bi bi-pencil-square me-2"></i>Editing Class: {{ $class_instance->program->name ?? 'Class' }} - Sem {{ $class_instance->sem }} Div {{ $class_instance->devision }}
        </div>
        <div class="card-body p-4">
            <form action="{{ url('/edit_class_success/'.$class_instance->id) }}" method="post">
                @csrf
                {{-- Add @method('PUT') or @method('PATCH') if your route expects it --}}

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="stream" class="form-label">Program <span class="text-danger">*</span></label>
                        <select name="stream" id="stream" class="form-select @error('stream') is-invalid @enderror" required>
                            <option value="" disabled>Select Program</option>
                            @foreach($programs as $program)
                            <option value="{{ $program->program_id }}" {{ old('stream', $class_instance->stream) == $program->program_id ? 'selected' : '' }}>{{ $program->name }}</option>
                            @endforeach
                        </select>
                        @error('stream')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Academic Batch (Year) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('date_from') is-invalid @enderror" name="date_from" id="date_from" min="1900" max="2099" step="1" value="{{ old('date_from', $year_from) }}" placeholder="From" required>
                            <span class="input-group-text">-</span>
                            <input type="number" class="form-control @error('date_to') is-invalid @enderror" name="date_to" id="date_to" min="1900" max="2099" step="1" value="{{ old('date_to', $year_to) }}" placeholder="To" required>
                        </div>
                        @error('date_from')
                            <div class="invalid-feedback d-block">{{ $message }}</div> {{-- d-block for input-group errors --}}
                        @enderror
                        @error('date_to')
                            <div class="invalid-feedback d-block">{{ $message }}</div> {{-- d-block for input-group errors --}}
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="semnumber" class="form-label">Semester Number <span class="text-danger">*</span></label>
                        <select name="semnumber" id="semnumber" class="form-select @error('semnumber') is-invalid @enderror" required>
                             <option value="" disabled>Select Semester</option>
                            @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('semnumber', $class_instance->sem) == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                            @endfor
                        </select>
                        @error('semnumber')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="devision" class="form-label">Division <span class="text-danger">*</span></label>
                        <input type="text" id="devision" class="form-control @error('devision') is-invalid @enderror" name="devision" value="{{ old('devision', $class_instance->devision) }}" placeholder="e.g., A, B, Morning" required>
                        @error('devision')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <dsiv class="row">
                    <div class="col-md-6 mb-4">
                        <label for="counselor" class="form-label">Class Counselor <span class="text-danger">*</span></label>
                        <select name="counselor" id="counselor" class="form-select @error('counselor') is-invalid @enderror" required>
                            <option value="" disabled>Select Counselor</option>
                            {{-- Assuming $available_counselors is passed from controller with id and name --}}
                            {{-- And $class_instance->counselor_id holds the current counselor's ID --}}
                            @foreach($classes as $counselor_option)
                            <option value="{{ $counselor_option->id }}" {{$datas->coundelor_id==$counselor_option->id?'selected':''}} >{{ $counselor_option->name }}</option>
                            @endforeach
                        </select>
                        @error('counselor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </dsiv>

                <div class="mt-2 pt-2">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="bi bi-save-fill me-2"></i>Update Class
                    </button>
                    <a href="{{ url('class_list') }}" class="btn btn-outline-secondary btn-lg ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection