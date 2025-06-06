@extends('counselor.layoutcounselor') {{-- Adjusted path --}}

@section('title', 'Class Attendance Summary')
@section('page_title', 'Class Attendance Summary')

@section('classattendent') {{-- Keeping this section name as requested --}}

{{-- Page-specific action buttons or info --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- The H1 title is now in the layout via @yield('page_title') --}}
    <div>
        <p class="text-muted mb-0">Summary of student attendance for the selected class/period.</p>
        {{-- Add filter display here if filters are active, e.g., "For Class: BCA Sem 3 Div A (2023-2024)" --}}
        @if(isset($from) && isset($to))
            <p class="small text-muted mb-0">
                Displaying data from: <strong>{{ \Carbon\Carbon::parse($from)->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('d M Y') }}</strong>
            </p>
        @endif
    </div>
    <div class="btn-group" role="group" aria-label="Export Actions">
    {{-- Ensure $to and $from are available and not null before creating these links --}}
    @if(isset($to) && isset($from))
        <a href="{{ url('/class_pdf/'.$to.'/'.$from) }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf-fill me-2"></i>Download PDF
        </a>
        <a href="{{ url('generate_excel_studentclass/'.$to.'/'.$from) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel-fill me-2"></i>Download Excel
        </a>
    @else
        <button class="btn btn-danger" disabled><i class="bi bi-file-earmark-pdf-fill me-2"></i>Download PDF</button>
        <button class="btn btn-success" disabled><i class="bi bi-file-earmark-excel-fill me-2"></i>Download Excel</button>
        <small class="ms-2 text-muted align-self-center">(Filter by date to enable exports)</small>
    @endif
    </div>
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

    {{-- Filter Form --}}
    <div class="card card-custom mb-4">
        <div class="card-body pb-2">
            <form action="/filter/date" method="get" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label for="date_from_summary" class="form-label">From Date</label>
                    <input type="date" name="date_from" id="date_from_summary" class="form-control form-control-sm" value="{{ request('date_from', $from ?? '') }}" required>
                </div>
                <div class="col-md-4">
                    <label for="date_to_summary" class="form-label">To Date</label>
                    <input type="date" name="date_to" id="date_to_summary" class="form-control form-control-sm" value="{{ request('date_to', $to ?? '') }}" required>
                </div>
                <div class="col-md-auto d-flex align-items-end"> {{-- Changed to col-md-auto for better alignment --}}
                    <button type="submit" class="btn btn-sm btn-primary w-100 mt-3 mt-md-0">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>


    <div class="card card-custom">
        <div class="card-header">
            <i class="bi bi-table me-2"></i>Attendance Data
        </div>
        <div class="card-body p-0">
            @if(isset($datas) && count($datas) > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0"> {{-- Added table-striped for better readability --}}
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" id="enrollmentSortHeader" style="cursor: pointer;">
                                Enrollment Number <i class="bi bi-arrow-down-up sort-icon ms-1"></i>
                            </th>
                            <th scope="col">Student Name</th>
                            <th scope="col">From Date</th>
                            <th scope="col">To Date</th>
                            <th scope="col" class="text-center">Total Lectures</th>
                            <th scope="col" class="text-center">Present Lectures</th>
                            <th scope="col" class="text-center">Attendance %</th>
                            <th scope="col" class="text-center">Actions</th> {{-- New Header for Actions --}}
                        </tr>
                    </thead>
                    <tbody id="attendanceTableBody"> {{-- ID for JS targeting --}}
                        @foreach($datas as $index => $data_string)
                            @php
                                // Data parsing should ideally be in the controller
                                $record = explode('&', $data_string);
                                $enrollment = $record[0] ?? 'N/A';
                                $name = $record[1] ?? 'N/A';
                                // Use original date strings from $record for detail link if needed,
                                // or rely on global $from and $to as currently implemented.
                                $record_from_date_str = $record[2] ?? null;
                                $record_to_date_str = $record[3] ?? null;

                                $from_date = $record_from_date_str ? \Carbon\Carbon::parse($record_from_date_str)->format('d M Y') : 'N/A';
                                $to_date_display = $record_to_date_str ? \Carbon\Carbon::parse($record_to_date_str)->format('d M Y') : 'N/A';
                                // Note: $to variable (global filter) might be different from $to_date_display (record specific)
                                // The detail link below uses global $from and $to

                                $total_class = $record[4] ?? 0;
                                $present = $record[5] ?? 0;
                                $percentage = $record[6] ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $enrollment }}</td>
                                <td>{{ $name }}</td>
                                <td>{{ $from_date }}</td>
                                <td>{{ $to_date_display }}</td>
                                <td class="text-center">{{ $total_class }}</td>
                                <td class="text-center">{{ $present }}</td>
                                <td class="text-center fw-bold
                                    @if($percentage >= 75) text-success
                                    @elseif($percentage >= 50) text-warning
                                    @else text-danger @endif">
                                    {{ rtrim(rtrim(sprintf('%.2f', $percentage), '0'), '.') }}%
                                </td>
                                <td class="text-center">
                                    @if(isset($from) && isset($to) && $enrollment !== 'N/A')
                                    {{-- This link assumes $from and $to are the global filter dates --}}
                                    <a href="{{ url('counselor/student-attendance-detail/' . $enrollment ) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="View Details for {{ $name }} within {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}">
                                        <i class="bi bi-eye"></i> Details
                                    </a>
                                    @else
                                    <button class="btn btn-sm btn-outline-primary" title="Details unavailable without date filter or valid enrollment" disabled>
                                        <i class="bi bi-eye"></i> Details
                                    </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center p-4">
                <i class="bi bi-bar-chart-steps display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">No attendance summary data found.</p>
                @if(!isset($from) || !isset($to))
                <p class="small text-muted mt-2">Please select a date range and click "Filter" to view data.</p>
                @else
                <p class="small text-muted mt-2">This might be because no attendance has been recorded for the selected criteria or class.</p>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

{{-- JavaScript for sorting --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortHeader = document.getElementById('enrollmentSortHeader');
    const tableBody = document.getElementById('attendanceTableBody');
    let sortOrder = 'none'; // 'none', 'asc', 'desc'

    if (sortHeader && tableBody) {
        sortHeader.addEventListener('click', function() {
            const icon = sortHeader.querySelector('.sort-icon');
            const rows = Array.from(tableBody.querySelectorAll('tr'));
            const enrollmentColIndex = 1; // Enrollment Number is the 2nd column (index 1)

            // Determine new sort order
            if (sortOrder === 'asc') {
                sortOrder = 'desc'; // Toggle to descending
            } else {
                sortOrder = 'asc'; // Toggle to ascending (or from 'none' to 'asc')
            }

            rows.sort((rowA, rowB) => {
                const cellA = rowA.querySelectorAll('td')[enrollmentColIndex].textContent.trim();
                const cellB = rowB.querySelectorAll('td')[enrollmentColIndex].textContent.trim();
                
                const comparison = cellA.localeCompare(cellB, undefined, { numeric: true, sensitivity: 'base' });
                
                return sortOrder === 'asc' ? comparison : -comparison;
            });

            // Update icon based on the new sortOrder
            if (sortOrder === 'asc') {
                icon.classList.remove('bi-arrow-down-up', 'bi-sort-alpha-up');
                icon.classList.add('bi-sort-alpha-down');
            } else { // sortOrder === 'desc'
                icon.classList.remove('bi-arrow-down-up', 'bi-sort-alpha-down');
                icon.classList.add('bi-sort-alpha-up');
            }

            // Re-append sorted rows
            tableBody.innerHTML = ''; // Clear existing rows to re-append in order
            rows.forEach(row => tableBody.appendChild(row));

            // Update # column (serial number)
            const firstColumnCells = tableBody.querySelectorAll('tr > td:first-child');
            firstColumnCells.forEach((cell, index) => {
                cell.textContent = index + 1;
            });
        });
    }
});
</script>
@endsection