@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Attendance Code Generated')
@section('page_title', 'Attendance Code')

@section('attendent_code') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <a href="{{ url('select_counselor') }}" class="btn btn-outline-secondary"> {{-- Link back to criteria selection --}}
            <i class="bi bi-arrow-left-circle me-2"></i>Back to Selection
        </a>
    </div>
    {{-- No extra buttons needed here as actions are below --}}
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

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7"> {{-- Centered column --}}
            <div class="card card-custom text-center">
                <div class="card-header">
                    <h4 class="mb-0">
                        @if(isset($attend) && $attend == 'yes' && isset($code))
                            <i class="bi bi-qr-code me-2"></i>Your Generated Attendance Code
                        @else
                            <i class="bi bi-exclamation-octagon-fill me-2 text-danger"></i>Attendance Status
                        @endif
                    </h4>
                </div>
                <div class="card-body p-4 p-md-5">
                    @if(isset($attend) && $attend == 'yes' && isset($code))
                        <p class="lead">Share this code with your students to mark their attendance for the current lecture.</p>
                        <div class="my-4">
                            <h1 class="display-1 fw-bold text-primary bg-light py-3 rounded border">{{ $code }}</h1>
                        </div>
                        <p class="text-muted small">This code is active for a limited time or until you close this session/lecture.</p>
                        <hr class="my-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ url('see_attendendent_counselor/'.$code) }}" class="btn btn-info btn-lg">
                                <i class="bi bi-eye-fill me-2"></i>View Attendance Log
                            </a>
                            <button type="button" class="btn btn-danger btn-lg"
                                    data-bs-toggle="modal" data-bs-target="#deleteAttendanceCodeModalCounselor"
                                    data-code-to-delete="{{ $code }}">
                                <i class="bi bi-trash3-fill me-2"></i>Delete This Code
                            </button>
                        </div>
                    @elseif(isset($attend) && $attend == 'no')
                        <div class="alert alert-warning py-4">
                            <h4 class="alert-heading"><i class="bi bi-calendar-x-fill me-2"></i>Attendance Already Finalized</h4>
                            <p>Attendance for the selected lecture criteria has already been taken and finalized, or a code was not generated successfully.</p>
                            <hr>
                            <p class="mb-0">You can try selecting different criteria or view existing attendance records.</p>
                        </div>
                    @else
                         <div class="alert alert-danger py-4">
                            <h4 class="alert-heading"><i class="bi bi-x-octagon-fill me-2"></i>Error</h4>
                            <p>Could not generate or retrieve an attendance code. Please try again or check your selection.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Code Confirmation Modal -->
@if(isset($attend) && $attend == 'yes' && isset($code))
<div class="modal fade" id="deleteAttendanceCodeModalCounselor" tabindex="-1" aria-labelledby="deleteAttendanceCodeModalCounselorLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteAttendanceCodeModalCounselorLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Code Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the attendance code: <strong id="codeToDeleteCounselor">{{ $code }}</strong>?
        <p class="text-danger small mt-2">This will prevent further attendance marking with this code.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        {{-- The form needs to submit to your delete route. Assuming GET for simplicity based on your link. --}}
        <a href="#" id="confirmDeleteCodeLinkCounselor" class="btn btn-danger">Yes, Delete Code</a>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@if(isset($attend) && $attend == 'yes' && isset($code))
<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteAttendanceCodeModalCounselor');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            // var button = event.relatedTarget; // Not strictly needed if code is already in modal
            // var codeToDelete = button.getAttribute('data-code-to-delete'); // data-code-to-delete is on the button

            // var modalBodyStrong = deleteModal.querySelector('#codeToDeleteCounselor'); // Already set by Blade
            var confirmDeleteLink = deleteModal.querySelector('#confirmDeleteCodeLinkCounselor');

            // modalBodyStrong.textContent = codeToDelete; // Already set by Blade
            confirmDeleteLink.href = 'delete_code_counselor/' + '{{ $code }}';
        });
    }
});
</script>
@endif
