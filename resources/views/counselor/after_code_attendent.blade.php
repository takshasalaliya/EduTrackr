@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Attendance Log')
@section('page_title')
    Attendance Log for Code: <span class="text-primary">{{ $code ?? 'N/A' }}</span>
@endsection

@section('after_code_attendent') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <a href="{{ url('select_counselor') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle me-2"></i>Back to Selection
        </a>
    </div>
    @if(isset($code) && $code)
    <button type="button" class="btn btn-danger"
            data-bs-toggle="modal" data-bs-target="#deleteCodeAfterLogModalCounselor"
            data-code-to-delete-log="{{ $code }}">
        <i class="bi bi-trash3-fill me-2"></i>Delete This Code ({{ $code }})
    </button>
    @endif
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
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($students) && is_countable($students) && count($students) > 0)
        <div class="row">
            {{-- Present Students --}}
            <div class="col-md-6 mb-4">
                <div class="card card-custom">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-person-check-fill me-2"></i>Present Students
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Enrollment No.</th>
                                        <th scope="col">Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $presentCount = 0; @endphp
                                    @foreach($students as $student_record)
                                        @if($student_record->attendance == 'present')
                                            @php $presentCount++; @endphp
                                            <tr>
                                                <td>{{ $presentCount }}</td>
                                                <td>{{ $student_record->student->enrollment_number ?? 'N/A' }}</td>
                                                <td>{{ $student_record->student->name ?? 'N/A' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @if($presentCount == 0)
                                        <tr><td colspan="3" class="text-center text-muted p-3">No students marked present.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($presentCount > 0)
                    <div class="card-footer text-end">
                        <strong>Total Present: {{ $presentCount }}</strong>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Absent Students --}}
            <div class="col-md-6 mb-4">
                <div class="card card-custom">
                    <div class="card-header bg-danger text-white">
                        <i class="bi bi-person-x-fill me-2"></i>Absent Students
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Enrollment No.</th>
                                        <th scope="col">Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $absentCount = 0; @endphp
                                    @foreach($students as $student_record)
                                        @if($student_record->attendance == 'absent')
                                            @php $absentCount++; @endphp
                                            <tr>
                                                <td>{{ $absentCount }}</td>
                                                <td>{{ $student_record->student->enrollment_number ?? 'N/A' }}</td>
                                                <td>{{ $student_record->student->name ?? 'N/A' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @if($absentCount == 0)
                                        <tr><td colspan="3" class="text-center text-muted p-3">No students marked absent.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                     @if($absentCount > 0)
                    <div class="card-footer text-end">
                        <strong>Total Absent: {{ $absentCount }}</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Duplicated Attendance Information --}}
        @if(isset($duplicated) && $duplicated->count() > 0)
        <div class="card card-custom mt-4">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Potential Irregular Attendance Marking
            </div>
            <div class="card-body">
                <h5 class="card-title">Instances of students potentially marking attendance for others:</h5>
                <ul class="list-group list-group-flush">
                    @foreach($duplicated as $data)
                    <li class="list-group-item">
                        Student from Class/Device <strong>{{ $data->in_class ?? 'Unknown' }}</strong> marked attendance for student <strong>{{ $data->out_class ?? 'Unknown' }}</strong>.
                    </li>
                    @endforeach
                </ul>
                <p class="mt-3 text-muted small">Please review these instances. This detection is based on [explain your detection logic briefly, e.g., IP address, device ID, rapid succession from different students on same device, etc.].</p>
            </div>
        </div>
        @else
        <div class="card card-custom mt-4">
            <div class="card-header">
                <i class="bi bi-shield-check me-2"></i>Attendance Integrity
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">No instances of students marking attendance for others were detected for this session.</p>
            </div>
        </div>
        @endif

    @elseif(isset($students) && $students == 'no')
        <div class="alert alert-warning text-center py-4">
            <h4 class="alert-heading"><i class="bi bi-info-circle-fill me-2"></i>No Data Found</h4>
            <p>No attendance data is available for the specified code: <strong>{{ $code ?? 'N/A' }}</strong>.</p>
            <p>This might mean the code was deleted before any attendance was logged, or the code is invalid.</p>
        </div>
    @else
        <div class="alert alert-info text-center py-4">
            <p>Loading attendance data or no information to display.</p>
        </div>
    @endif
</div>

<!-- Delete Code Confirmation Modal -->
@if(isset($code) && $code)
<div class="modal fade" id="deleteCodeAfterLogModalCounselor" tabindex="-1" aria-labelledby="deleteCodeAfterLogModalCounselorLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteCodeAfterLogModalCounselorLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Code Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the attendance code: <strong id="codeToDeleteInLogCounselor">{{ $code }}</strong>?
        <p class="text-danger small mt-2">This will prevent further attendance marking with this code. Existing records will be kept.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="#" id="confirmDeleteCodeLinkAfterLogCounselor" class="btn btn-danger">Yes, Delete Code</a>
      </div>
    </div>
  </div>
</div>
@endif

@endsection


@if(isset($code) && $code)
<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteCodeAfterLogModalCounselor');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            // var button = event.relatedTarget; // data-code-to-delete-log is on the button
            // var codeToDelete = button.getAttribute('data-code-to-delete-log');

            var confirmDeleteLink = deleteModal.querySelector('#confirmDeleteCodeLinkAfterLogCounselor');
            // var modalStrong = deleteModal.querySelector('#codeToDeleteInLogCounselor');
            // modalStrong.textContent = codeToDelete; // Already set by Blade

            confirmDeleteLink.href = '/delete_code_counselor/' + '{{ $code }}';
        });
    }
});
</script>
@endif
