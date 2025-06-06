@extends('counselor.layoutcounselor') {{-- Adjusted path assuming 'counselor' is a directory in views --}}

@section('title', 'Counselor Dashboard')
@section('page_title', 'Counselor Dashboard')

@section('content')
<style>
    /* Styles for stat cards, similar to admin dashboard but can be tweaked if needed */
    .stat-card {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        display: flex;
        align-items: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        color: #333;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .stat-card .stat-icon {
        font-size: 2.8rem;
        padding: 15px;
        border-radius: 50%;
        margin-right: 20px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70px;
        height: 70px;
    }

    .stat-card .stat-info h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0;
    }

    .stat-card .stat-info p {
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 0;
        text-transform: uppercase;
        font-weight: 500;
    }

    .stat-card.students .stat-icon { background-color: #007bff; }
    .stat-card.students .stat-info h3 { color: #007bff; }

    .stat-card.teachers .stat-icon { background-color: #28a745; }
    .stat-card.teachers .stat-info h3 { color: #28a745; }

    .stat-card.counselors .stat-icon { background-color: #17a2b8; }
    .stat-card.counselors .stat-info h3 { color: #17a2b8; }

    .stat-card.classes .stat-icon { background-color: #ffc107; }
    .stat-card.classes .stat-info h3 { color: #ffc107; }
    .stat-card.classes .stat-icon i { color: #333; } /* Ensure icon color is visible on yellow */

    /* Card and table styles from admin panel for consistency */
    .card-custom {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        border: none;
        background-color: #fff; /* Ensure card background is white */
    }
    .card-custom .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        color: #0056b3; /* Darker blue for header text */
        font-size: 1.1rem;
        padding: 0.75rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .table thead th {
        background-color: #eef2f7; /* Light greyish blue for table header */
        color: #495057; /* Dark grey for text */
        font-weight: 600;
        border-bottom-width: 1px;
        border-top: none;
        text-align: center; /* Center align header text */
    }
    .table tbody tr:hover {
        background-color: #f1f5f9; /* Lighter blue on hover */
    }
    .table td, .table th {
        vertical-align: middle;
        padding: 0.75rem; /* Standard padding */
    }
    .table td:not(:first-child), .table th:not(:first-child) {
        text-align: center; /* Center align numeric data */
    }
    .table tfoot th {
        background-color: #e9ecef; /* Slightly darker for footer */
        font-weight: 700;
    }
</style>

<div class="container-fluid">
    {{-- NEW: Take Attendance Shortcut Button --}}
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ url('/select_counselor') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-calendar-check-fill me-2"></i>Take Attendance
        </a>
    </div>

    {{-- Top Stat Cards --}}
    <div class="row g-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card students">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-info">
                    <h3>{{ $students ?? 0 }}</h3> <p>Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card teachers">
                <div class="stat-icon"><i class="bi bi-person-video3"></i></div>
                <div class="stat-info">
                    <h3>{{ $teachers ?? 0 }}</h3> <p>Total Teachers</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card counselors">
                <div class="stat-icon"><i class="bi bi-chat-left-dots-fill"></i></div>
                <div class="stat-info">
                    <h3>{{ $counselors ?? 0 }}</h3> <p>Total Counselors</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card classes">
                <div class="stat-icon"><i class="bi bi-easel2-fill"></i></div>
                <div class="stat-info">
                    <h3>{{ $classes ?? 0 }}</h3> <p>Total Classes</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Counselor's Assigned Classes --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">
                    <span><i class="bi bi-person-workspace me-2"></i>Your Assigned Classes</span>
                </div>
                <div class="card-body p-0">
                    @php
                        $counselorClassesFound = false;
                    @endphp
                    @if(isset($class_name) && $class_name->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Program</th>
                                    <th>Division</th>
                                    <th>Semester</th>
                                    <th>Batch Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($class_name as $class)
                                    @if(isset(Auth::user()->id) && $class->coundelor_id == Auth::user()->id) {{-- Assuming coundelor_id typo is consistent --}}
                                        @php $counselorClassesFound = true; @endphp
                                        <tr>
                                            <td>{{ $class->id }}</td>
                                            <td>{{ $class->program->name ?? 'N/A' }}</td>
                                            <td>{{ $class->devision }}</td>
                                            <td>{{ $class->sem }}</td>
                                            <td>{{ $class->year }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!$counselorClassesFound)
                                    <tr>
                                        <td colspan="5" class="text-center text-muted p-3">You are not currently assigned as a counselor to any class.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @else
                        <div class="text-center p-4">
                            <p class="text-muted mb-0">No class data available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Total Lecture of Each Unit --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">
                    <span><i class="bi bi-bar-chart-line-fill me-2"></i>Total Lectures Per Unit (for your subjects)</span>
                    @if(isset($subjects) && $subjects->count() > 0 && isset($units) && $units->count() > 0)
                        <button id="exportLecturesBtn" class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export to Excel
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                     @if(isset($subjects) && $subjects->count() > 0 && isset($units))
                        @php
                            // Initialize grand totals
                            $grand_total_unit_lectures = array_fill(1, 6, 0);
                            $grand_total_all_lectures = 0;
                            $hasDataForTable = false; // Flag to check if there's any data to show in table
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0" id="lecturesPerUnitTable">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">Subject / Class Context</th>
                                        <th>Unit 1</th>
                                        <th>Unit 2</th>
                                        <th>Unit 3</th>
                                        <th>Unit 4</th>
                                        <th>Unit 5</th>
                                        <th>Unit 6</th>
                                        <th>Total Lectures</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjects as $subject_item)
                                        @php
                                            // YOUR LOGIC for calculating lecture counts per unit for the current subject
                                            $unit_counts_for_subject = array_fill(1, 6, 0); // [1=>0, 2=>0, ..., 6=>0]
                                            $first_student_id_for_subject = null;

                                            // Find the first student_id associated with this subject_item->id (staff_id) from the $units collection
                                            // This assumes $subject_item->id corresponds to 'staff_id' in the $units table
                                            if (isset($units) && $units->count() > 0) {
                                                foreach ($units as $unit_entry_check) {
                                                    if (isset($unit_entry_check->staff_id) && $subject_item->id == $unit_entry_check->staff_id && isset($unit_entry_check->student_id)) {
                                                        $first_student_id_for_subject = $unit_entry_check->student_id;
                                                        break; // Found the first student for this subject
                                                    }
                                                }
                                            }

                                            // If a representative student_id was found, count lectures for that student and subject combination
                                            if ($first_student_id_for_subject !== null && isset($units) && $units->count() > 0) {
                                                foreach ($units as $unit_entry) {
                                                    if (isset($unit_entry->student_id, $unit_entry->staff_id, $unit_entry->unit) &&
                                                        $unit_entry->student_id == $first_student_id_for_subject &&
                                                        $subject_item->id == $unit_entry->staff_id) {

                                                        // Ensure unit is an integer and within the 1-6 range
                                                        $unit_number = filter_var($unit_entry->unit, FILTER_VALIDATE_INT);
                                                        if ($unit_number !== false && $unit_number >= 1 && $unit_number <= 6) {
                                                            if (isset($unit_counts_for_subject[$unit_number])) { // Check if key exists
                                                                $unit_counts_for_subject[$unit_number]++;
                                                                $hasDataForTable = true; // Mark that we have some data for the table overall
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            // Calculate total lectures for this subject based on the counts derived
                                            $total_lectures_for_current_subject = array_sum($unit_counts_for_subject);
                                        @endphp
                                        <tr>
                                            <td style="text-align: left;">
                                                {{ $subject_item->subject->short_name ?? 'N/A Subject' }} /
                                                {{ $subject_item->subject->student_class->program->name ?? 'N/A Program' }} /
                                                Sem {{ $subject_item->subject->student_class->sem ?? 'N/A' }} /
                                                Div {{ $subject_item->subject->student_class->devision ?? 'N/A' }}
                                                (Batch: {{ $subject_item->subject->student_class->year ?? 'N/A' }})
                                            </td>
                                            @for ($i = 1; $i <= 6; $i++)
                                                <td>{{ $unit_counts_for_subject[$i] ?? 0 }}</td> {{-- Display count, default to 0 if not set --}}
                                                @php
                                                    $grand_total_unit_lectures[$i] += ($unit_counts_for_subject[$i] ?? 0);
                                                @endphp
                                            @endfor
                                            <td><strong>{{ $total_lectures_for_current_subject }}</strong></td>
                                            @php
                                                $grand_total_all_lectures += $total_lectures_for_current_subject;
                                            @endphp
                                        </tr>
                                    @endforeach

                                    @if(!$hasDataForTable && $subjects->count() > 0)
                                        <tr>
                                            <td colspan="8" class="text-center text-muted p-3">
                                                No lecture unit data found for the subjects listed based on the current criteria.
                                                Please ensure lecture units are recorded and meet the conditions.
                                            </td>
                                        </tr>
                                    @elseif($subjects->count() == 0)
                                        <tr>
                                            <td colspan="8" class="text-center text-muted p-3">
                                                You are not assigned to any subjects.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                @if($hasDataForTable) {{-- Only show tfoot if there was data --}}
                                <tfoot>
                                    <tr>
                                        <th style="text-align: right;">Grand Totals:</th>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <th>{{ $grand_total_unit_lectures[$i] }}</th>
                                        @endfor
                                        <th>{{ $grand_total_all_lectures }}</th>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                     @else
                        <div class="text-center p-4">
                            @if(!isset($subjects) || $subjects->count() == 0)
                                <p class="text-muted mb-0">You are not assigned to any subjects.</p>
                            @elseif(!isset($units))
                                <p class="text-muted mb-0">Unit data is not available.</p>
                            @else
                                <p class="text-muted mb-0">No subject or unit data available to display lecture counts.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script for Excel Export --}}
{{-- Make sure to include this library, e.g., via CDN or npm package --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const exportButton = document.getElementById('exportLecturesBtn');
    if (exportButton) {
        exportButton.addEventListener('click', function () {
            const table = document.getElementById('lecturesPerUnitTable');
            if (table) {
                const wb = XLSX.utils.table_to_book(table, { sheet: "LecturesPerUnit" });
                const today = new Date();
                const dateString = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
                const fileName = `LecturesPerUnit_${dateString}.xlsx`;
                XLSX.writeFile(wb, fileName);
            } else {
                console.error("Table with ID 'lecturesPerUnitTable' not found.");
                alert("Could not find the table to export.");
            }
        });
    }
});
</script>
@endsection