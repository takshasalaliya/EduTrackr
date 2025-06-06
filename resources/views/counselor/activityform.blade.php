@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Add New Activity')
@section('page_title', 'Log New Student Activity')

@section('content') {{-- Assuming your layout uses @yield('content') --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <p class="text-muted mb-0">Log details of a new student activity or event.</p>
    </div>
    {{-- Optional: Link to view existing activities --}}
    {{-- <a href="{{ url('counselor/activities') }}" class="btn btn-outline-secondary">
        <i class="bi bi-list-task me-2"></i>View Activities
    </a> --}}
</div>

<div class="container-fluid"> {{-- Use container-fluid for full width --}}

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

    <form action="{{ url('/activity') }}" method="post">
        @csrf
        <div class="row">
            {{-- Activity Details Column --}}
            <div class="col-lg-7 mb-4">
                <div class="card card-custom">
                    <div class="card-header">
                        <i class="bi bi-calendar-event-fill me-2"></i>Activity Details
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="activity_name" class="form-label">Activity Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('activity_name') is-invalid @enderror" id="activity_name" name="activity_name" value="{{ old('activity_name') }}" placeholder="e.g., Seminar on Career Development" required>
                                @error('activity_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="date_from" class="form-label">From Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_from') is-invalid @enderror" id="date_from" name="date_from" value="{{ old('date_from', date('Y-m-d')) }}" required>
                                @error('date_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="date_to" class="form-label">To Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_to') is-invalid @enderror" id="date_to" name="date_to" value="{{ old('date_to', date('Y-m-d')) }}" required>
                                @error('date_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="session_number" class="form-label">Session Number (if applicable)</label>
                                <input type="number" class="form-control @error('session_number') is-invalid @enderror" id="session_number" name="session_number" value="{{ old('session_number') }}" placeholder="e.g., 1, 2" min="1">
                                @error('session_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Student Selection Column --}}
            <div class="col-lg-5 mb-4">
                <div class="card card-custom">
                    <div class="card-header">
                        <i class="bi bi-people-fill me-2"></i>Select Participating Students <span class="text-danger">*</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="studentSearchInput" class="form-control" placeholder="Search by Name or Enrollment No...">
                            </div>
                        </div>

                        <div id="studentListContainer" class="border p-2 rounded" style="max-height: 350px; overflow-y: auto;">
                            @if(isset($students) && $students->count() > 0) {{-- Assuming $students are passed from controller (all students in counselor's classes) --}}
                                @foreach($students as $student)
                                <div class="form-check student-list-item mb-1">
                                    <input class="form-check-input student-checkbox @error('selected_students') is-invalid @enderror" type="checkbox" name="selected_students[]" value="{{ $student->student_id }}" id="student_{{ $student->student_id }}">
                                    <label class="form-check-label" for="student_{{ $student->student_id }}">
                                        <span class="student-name">{{ $student->name }}</span>
                                        <small class="text-muted">(Enroll: {{ $student->enrollment_number }}) - {{ $student->class->program->name ?? 'N/A' }} Sem {{ $student->class->sem ?? 'N/A' }}</small>
                                    </label>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted m-0">No students found. Please ensure students are added to your classes.</p>
                            @endif
                        </div>
                        @error('selected_students')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text mt-2">Select all students who participated in this activity.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center mt-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-lg me-2"></i>Log Activity
                </button>
            </div>
        </div>
    </form>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('studentSearchInput');
    const studentListContainer = document.getElementById('studentListContainer');
    const studentItems = studentListContainer ? studentListContainer.querySelectorAll('.student-list-item') : [];

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const filter = this.value.toLowerCase().trim();

            studentItems.forEach(function (item) {
                const studentName = item.querySelector('.student-name').textContent.toLowerCase();
                const labelText = item.querySelector('label').textContent.toLowerCase(); // Search entire label text

                if (labelText.includes(filter)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
