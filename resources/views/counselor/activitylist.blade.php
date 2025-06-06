@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Logged Activities')
@section('page_title', 'Student Activity Log')

@section('content') {{-- Assuming your layout uses @yield('content') --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <p class="text-muted mb-0">List of logged student activities and participation.</p>
    </div>
    <a href="{{ url('counselor/leaves/create') }}" class="btn btn-primary"> {{-- Link to the "Add Activity" page --}}
        <i class="bi bi-plus-lg me-2"></i>Log New Activity
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
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Optional: Filters for the activity log (e.g., by date range, activity name) --}}


    <div class="card card-custom">
        <div class="card-header">
            <i class="bi bi-list-stars me-2"></i>Logged Activities
        </div>
        <div class="card-body p-0">
            @if(isset($activity_logs) && $activity_logs->count() > 0) {{-- Assuming $activity_logs is passed from controller --}}
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Activity Name</th>
                            <th scope="col" class="text-center">Session No.</th>
                            <th scope="col">From Date</th>
                            <th scope="col">To Date</th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Enrollment No.</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- This assumes $activity_logs is a collection of activity_student pivot records
                             or similar, eager-loading 'activity' and 'student' relations. --}}
                        @foreach($activity_logs as $index => $log_entry)
                        <tr>
                            <td>{{ $index + 1 + ($activity_logs instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($activity_logs->currentPage() - 1) * $activity_logs->perPage() : 0) }}</td>
                            <td>{{ $log_entry->name ?? 'N/A' }}</td>
                            <td class="text-center">{{ $log_entry->session ?? '-' }}</td>
                            <td>{{ isset($log_entry->from_date) ? \Carbon\Carbon::parse($log_entry->from_date)->format('d M Y') : 'N/A' }}</td>
                            <td>{{ isset($log_entry->to_date) ? \Carbon\Carbon::parse($log_entry->to_date)->format('d M Y') : 'N/A' }}</td>
                            <td>{{ $log_entry->student->name ?? 'N/A' }}</td>
                            <td>{{ $log_entry->student->enrollment_number ?? 'N/A' }}</td>
                            <td class="text-center">
                                {{-- Delete button for this specific student's participation in this activity --}}
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Remove Student from Activity"
                                        data-bs-toggle="modal" data-bs-target="#deleteActivityParticipationModal"
                                        data-participation-id="{{ $log_entry->id }}" {{-- Assuming $log_entry->id is the ID of the activity_student pivot record --}}
                                        data-activity-name="{{ $log_entry->activity->name ?? 'Activity' }}"
                                        data-student-name="{{ $log_entry->student->name ?? 'Student' }}">
                                    <i class="bi bi-person-x-fill"></i> Remove
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center p-4">
                <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">No student activities logged yet or matching your filters.</p>
                <p class="small text-muted mt-2">You can <a href="{{ url('counselor/activity/create') }}">log a new activity</a>.</p>
            </div>
            @endif
        </div>
        @if(isset($activity_logs) && $activity_logs instanceof \Illuminate\Pagination\LengthAwarePaginator && $activity_logs->hasPages())
        <div class="card-footer bg-light border-top-0">
            {{ $activity_logs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Activity Participation Confirmation Modal -->
<div class="modal fade" id="deleteActivityParticipationModal" tabindex="-1" aria-labelledby="deleteActivityParticipationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteActivityParticipationModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Removal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to remove student <strong id="studentNameForRemoval"></strong> from activity <strong id="activityNameForRemoval"></strong>?
        <p class="text-danger small mt-2">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteActivityParticipationForm" method="GET" action=""> {{-- Action will be set by JS, method GET for now --}}
            <button type="submit" class="btn btn-danger">Yes, Remove Student</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteActivityParticipationModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var participationId = button.getAttribute('data-participation-id');
            var activityName = button.getAttribute('data-activity-name');
            var studentName = button.getAttribute('data-student-name');

            var modalStudentName = deleteModal.querySelector('#studentNameForRemoval');
            var modalActivityName = deleteModal.querySelector('#activityNameForRemoval');
            var deleteForm = deleteModal.querySelector('#deleteActivityParticipationForm');

            modalStudentName.textContent = studentName;
            modalActivityName.textContent = activityName;

            // Assuming your delete route is GET: counselor/activity/participation/delete/{id}
            deleteForm.action = '/counselor/activity/participation/delete/' + participationId;
            deleteForm.method = 'GET'; // Explicitly set to GET

            // If you change to POST/DELETE method for the route:
            // deleteForm.action = '{{ url("counselor/activity/participation") }}/' + participationId;
            // deleteForm.method = 'POST';
            // Make sure a hidden input for @method('DELETE') is present in the form in that case.
        });
    }
});
</script>
