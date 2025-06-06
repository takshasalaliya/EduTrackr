<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details - {{ $data->name ?? 'User' }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- html2pdf.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        body {
            background-color: #eef2f7;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            padding-top: 20px;
            padding-bottom: 40px;
        }
        .logo-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-header img {
            max-width: 300px;
            height: auto;
        }
        .dashboard-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            margin-bottom: 25px;
        }
        .card-header-custom {
            background-color: #0A2540;
            color: #ffffff;
            font-size: 1.25rem;
            font-weight: 600;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            padding: 1rem 1.5rem;
        }
        .profile-details .row > div {
            padding-bottom: 0.75rem;
        }
        .profile-details dt {
            font-weight: 600;
            color: #555;
        }
        .profile-details dd {
            margin-bottom: 0;
            color: #333;
        }
        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        .table tfoot th, .table tfoot td {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .attendance-percentage.high { color: #198754; }
        .attendance-percentage.medium { color: #ffc107; }
        .attendance-percentage.low { color: #dc3545; }

        .chart-container { /* General container for charts */
            position: relative;
            margin: auto;
            width: 100%;
            margin-bottom: 25px;
        }
        .doughnut-chart-container {
            height: 300px; /* Specific height for doughnut */
            max-width: 400px; /* Max width for doughnut */
        }
        .bar-chart-container {
            height: 350px; /* Specific height for bar chart */
        }

        .action-buttons {
            display: flex; /* Use flexbox for alignment */
            justify-content: flex-end; /* Align buttons to the right */
            gap: 10px; /* Space between buttons */
            margin-bottom: 20px;
        }


        @media (max-width: 767.98px) {
            .logo-header img {
                max-width: 200px;
            }
            .doughnut-chart-container {
                height: 250px;
            }
            .bar-chart-container {
                height: 300px; /* Adjusted for smaller screens */
            }
            .action-buttons {
                justify-content: center; /* Center buttons on smaller screens */
                flex-wrap: wrap; /* Allow buttons to wrap if needed */
            }
        }
        @media (max-width: 575.98px) { /* Extra small devices */
            .bar-chart-container {
                height: 350px; /* Potentially more height if many subjects and labels wrap */
            }
        }
         @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding-top: 0;
                background-color: #fff; /* Ensure plain background for printing */
            }
            .dashboard-card {
                box-shadow: none;
                border: 1px solid #dee2e6; /* Add a light border for structure */
                margin-bottom: 0;
                width: 100% !important; /* Ensure card uses full printable width */
            }
            .container { /* Ensure container uses full width in print */
                max-width: 100% !important;
                width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            .card-header-custom { /* Ensure header prints well */
                background-color: #f8f9fa !important;
                color: #000 !important;
                border-bottom: 1px solid #dee2e6;
            }
            /* Chart specific print styles */
            .chart-container canvas {
                max-width: 100% !important;
                height: auto !important;
            }
            .bar-chart-container { /* Allow bar chart to use available width */
                 width: 100% !important;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">

                <div class="logo-header no-print">
                    <img src="https://i.ibb.co/yFhzNxBJ/3-removebg-preview.png" alt="Semcom Logo">
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons no-print">
                    <a href="{{ url('/classattendent') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left-circle me-2"></i>Back
                    </a>
                    <button id="downloadPdfBtn" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i>Download PDF
                    </button>
                </div>

                <!-- Session Messages -->
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div id="contentToPrint" class="dashboard-card">
                    <div class="card-header-custom text-center">
                        Student Attendance & Activity Details
                    </div>
                    <div class="card-body p-4 p-md-5">

                        <h4 class="mb-3"><i class="bi bi-person-lines-fill me-2"></i>Viewing Details for: {{ $data->name ?? 'Student' }}</h4>

                        {{-- Student Information Section --}}
                        <div class="profile-details mb-4 p-3 bg-light border rounded">
                            <h5 class="mb-3 border-bottom pb-2">Student Profile</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Enrollment No.:</dt>
                                        <dd class="col-sm-7">{{ $data->enrollment_number ?? 'N/A' }}</dd>
                                        <dt class="col-sm-5">Name:</dt>
                                        <dd class="col-sm-7">{{ $data->name ?? 'N/A' }}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Class/Program:</dt>
                                        <dd class="col-sm-7">{{ $data->class->program->name ?? 'N/A' }}</dd>
                                        <dt class="col-sm-5">Semester:</dt>
                                        <dd class="col-sm-7">Sem {{ $data->class->sem ?? 'N/A' }}</dd>
                                        <dt class="col-sm-5">Batch:</dt>
                                        <dd class="col-sm-7">{{ $data->class->year ?? 'N/A' }}</dd>
                                        <dt class="col-sm-5">Division:</dt>
                                        <dd class="col-sm-7">{{ $data->class->devision ?? 'N/A' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Prepare Data for both Table and Charts --}}
                        @php
                            $processedAttendData = [];
                            if (isset($attend) && (is_array($attend) || $attend instanceof \Illuminate\Support\Collection)) {
                                foreach($attend as $atte_string) {
                                    $parts = explode('@', $atte_string);
                                    // Assuming string format: SubjectName@Attended@PercentageStr@TotalLectures@ShortName
                                    $processedAttendData[] = (object)[
                                        'subject_name' => $parts[0] ?? 'N/A',
                                        'lectures_attended' => (int)($parts[1] ?? 0),
                                        'percentage_string' => $parts[2] ?? '0%',
                                        'total_lectures' => (int)($parts[3] ?? 0),
                                        'subject_short_name' => $parts[4] ?? ($parts[0] ?? 'N/A'), // Use short name (index 4), fallback to full name
                                        'percentage_numeric' => (float)rtrim($parts[2] ?? '0', '%')
                                    ];
                                }
                            }
                            // Data for Overall Doughnut Chart
                            $overall_lectures_attended_count = $present ?? 0;
                            $overall_total_lecture_slots = $lecture ?? 0;
                            $overall_lectures_missed_count = max(0, $overall_total_lecture_slots - $overall_lectures_attended_count);
                            
                            $temp_total_activity_sessions_attended = 0;
                            if (isset($activity_participation) && $activity_participation->count() > 0) {
                                foreach($activity_participation as $participation) {
                                    $temp_total_activity_sessions_attended += (int)($participation->session ?? 0);
                                }
                            }
                            $overall_activities_attended_count = $temp_total_activity_sessions_attended;

                        @endphp

                        {{-- Attendance Visualizations Section --}}
                        <div class="attendance-charts mb-5">
                             <h5 class="mb-3 border-bottom pb-2"><i class="bi bi-pie-chart-fill me-2"></i>Overall Engagement</h5>
                             @if($overall_total_lecture_slots > 0 || $overall_activities_attended_count > 0)
                                <div class="chart-container doughnut-chart-container p-3 border rounded bg-white shadow-sm">
                                    <canvas id="overallAttendanceDoughnutChart"></canvas>
                                </div>
                             @else
                                <p class="text-muted">No overall engagement data to display yet.</p>
                             @endif

                             <h5 class="mt-5 mb-3 border-bottom pb-2"><i class="bi bi-bar-chart-line-fill me-2"></i>Subject-wise Lecture Attendance</h5>
                             @if(count($processedAttendData) > 0)
                                <div class="chart-container bar-chart-container p-3 border rounded bg-white shadow-sm">
                                    <canvas id="combinedSubjectBarChart"></canvas>
                                </div>
                             @else
                                <p class="text-muted">No subject-specific lecture attendance to display.</p>
                             @endif
                        </div>


                        {{-- Lecture Attendance Summary Table --}}
                        <div class="attendance-summary mb-4">
                            <h5 class="mb-3 border-bottom pb-2"><i class="bi bi-card-checklist me-2"></i>Lecture Attendance Details</h5>
                            @if(count($processedAttendData) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Subject (Short Name)</th> {{-- Changed for consistency with chart --}}
                                            <th scope="col" class="text-center">Total Lectures</th>
                                            <th scope="col" class="text-center">Lectures Attended</th>
                                            <th scope="col" class="text-center">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($processedAttendData as $summary)
                                        <tr>
                                            <td>{{ $summary->subject_name }} ({{ $summary->subject_short_name }})</td> {{-- Display both for clarity in table --}}
                                            <td class="text-center">{{ $summary->total_lectures }}</td>
                                            <td class="text-center">{{ $summary->lectures_attended }}</td>
                                            <td class="text-center attendance-percentage
                                                @if($summary->percentage_numeric >= 75) high
                                                @elseif($summary->percentage_numeric >= 50) medium
                                                @else low @endif">
                                                <strong>{{ $summary->percentage_string }}</strong>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    @if(isset($lecture) && isset($present))
                                    <tfoot class="table-group-divider">
                                        <tr>
                                            <th>Overall Lecture Total</th>
                                            <td class="text-center">{{ $lecture }}</td>
                                            <td class="text-center">{{ $present }}</td>
                                            @php
                                                $overall_lecture_percentage_numeric = ($lecture > 0) ? (($present / $lecture) * 100) : 0;
                                            @endphp
                                            <td class="text-center attendance-percentage
                                                @if($overall_lecture_percentage_numeric >= 75) high
                                                @elseif($overall_lecture_percentage_numeric >= 50) medium
                                                @else low @endif">
                                                <strong>{{ number_format($overall_lecture_percentage_numeric, 2) }}%</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                            @else
                            <p class="text-muted">No lecture attendance summary available at the moment.</p>
                            @endif
                        </div>

                        {{-- Activity Log Table --}}
                        <div class="activity-log">
                            <h5 class="mb-3 border-bottom pb-2"><i class="bi bi-calendar-event me-2"></i>Activity Log</h5>
                            @if(isset($activity_participation) && $activity_participation->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Activity Name</th>
                                            <th scope="col" class="text-center">Session No.</th>
                                            <th scope="col">From Date</th>
                                            <th scope="col">To Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total_activity_sessions_attended = 0;
                                        @endphp
                                        @foreach($activity_participation as $index => $participation)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $participation->activity->name ?? ($participation->name ?? 'N/A') }}</td>
                                            <td class="text-center">{{ $participation->session }}</td>
                                            <td>{{ isset($participation->activity->date_from) ? \Carbon\Carbon::parse($participation->activity->date_from)->format('d M Y') : (isset($participation->from_date) ? \Carbon\Carbon::parse($participation->from_date)->format('d M Y') : 'N/A') }}</td>
                                            <td>{{ isset($participation->activity->date_to) ? \Carbon\Carbon::parse($participation->activity->date_to)->format('d M Y') : (isset($participation->to_date) ? \Carbon\Carbon::parse($participation->to_date)->format('d M Y') : 'N/A') }}</td>
                                            @php
                                                $total_activity_sessions_attended += (int)($participation->session ?? 0);
                                            @endphp
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-group-divider">
                                        <tr>
                                            <th colspan="2" class="text-start">Total Distinct Activities:</th>
                                            <th class="text-center">{{ $activity_participation->count() }}</th>
                                            <th class="text-end">Total Sessions Attended:</th>
                                            <th class="text-center">{{ $total_activity_sessions_attended }}</th>
                                        </tr>
                                        @php
                                            $total_attended_events = ($present ?? 0) + $total_activity_sessions_attended;
                                            $total_lecture_slots_available = ($lecture ?? 0);
                                            
                                            $overall_combined_percentage = 0.00; // Initialize
                                            if ($total_lecture_slots_available > 0) {
                                                $calculated_percentage = ($total_attended_events / $total_lecture_slots_available) * 100;
                                                $overall_combined_percentage = min($calculated_percentage, 100.00); // Cap at 100%
                                            } elseif ($total_attended_events > 0 && $total_lecture_slots_available == 0) {
                                                // If activities attended but no lecture slots (e.g., vacation or activity-only tracking)
                                                $overall_combined_percentage = 100.00;
                                            }
                                            // If both $total_attended_events and $total_lecture_slots_available are 0, it remains 0.00
                                        @endphp
                                        <tr>
                                            <th colspan="3" class="text-start">Combined Score (Lectures + Activities vs Lecture Slots):</th>
                                            <td colspan="2" class="text-center attendance-percentage
                                                @if($overall_combined_percentage >= 75) high
                                                @elseif($overall_combined_percentage >= 50) medium
                                                @else low @endif">
                                                <strong>{{ number_format($overall_combined_percentage, 2) }}%</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            @else
                            <p class="text-muted">This student has not participated in any logged activities yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data from PHP for charts
            const processedAttendDataJS = @json($processedAttendData);
            const overallLecturesAttended = {{ $overall_lectures_attended_count }};
            const overallActivitiesAttended = {{ $overall_activities_attended_count }};
            const overallLecturesMissed = {{ $overall_lectures_missed_count }};
            const overallTotalLectureSlots = {{ $overall_total_lecture_slots }};


            // 1. Overall Attendance Doughnut Chart
            const overallDoughnutCtx = document.getElementById('overallAttendanceDoughnutChart');
            if (overallDoughnutCtx && (overallTotalLectureSlots > 0 || overallActivitiesAttended > 0)) {
                new Chart(overallDoughnutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Lectures Attended', 'Activities Attended', 'Lectures Missed'],
                        datasets: [{
                            label: 'Overall Engagement',
                            data: [overallLecturesAttended, overallActivitiesAttended, overallLecturesMissed],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 99, 132, 0.7)'
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: false, // Disable animation for better PDF rendering
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Overall Engagement Breakdown'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed;
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 2. Combined Subject Attendance Bar Chart (using short names)
            const combinedBarCtx = document.getElementById('combinedSubjectBarChart');
            if (combinedBarCtx && processedAttendDataJS && processedAttendDataJS.length > 0) {
                const subjectLabels = processedAttendDataJS.map(d => d.subject_short_name || d.subject_name);
                const attendedCounts = processedAttendDataJS.map(d => d.lectures_attended);
                const absentCounts = processedAttendDataJS.map(d => Math.max(0, d.total_lectures - d.lectures_attended));

                new Chart(combinedBarCtx, {
                    type: 'bar',
                    data: {
                        labels: subjectLabels,
                        datasets: [
                            {
                                label: 'Lectures Attended',
                                data: attendedCounts,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Lectures Absent',
                                data: absentCounts,
                                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Allows chart to fill container height/width based on CSS
                        animation: false,
                        scales: {
                            x: {
                                ticks: {
                                    autoSkip: false, // Show all labels
                                    maxRotation: 45,
                                    minRotation: 45 // Keep rotation consistent for readability
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Subject-wise Lecture Attendance Counts'
                            }
                        }
                    }
                });
            }

            // PDF Download Functionality
            const downloadPdfBtn = document.getElementById('downloadPdfBtn');
            if (downloadPdfBtn) {
                downloadPdfBtn.addEventListener('click', function() {
                    const elementToPrint = document.getElementById('contentToPrint');
                    const studentName = "{{ $data->name ?? 'Student' }}";
                    const enrollmentNumber = "{{ $data->enrollment_number ?? 'UnknownEnrollment' }}";
                    const filename = `student_details_${enrollmentNumber}_${studentName.replace(/\s+/g, '_')}.pdf`;

                    downloadPdfBtn.disabled = true;
                    downloadPdfBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating PDF...';

                    const opt = {
                        margin:       [0.5, 0.5, 0.5, 0.5], // inches [top, left, bottom, right]
                        filename:     filename,
                        image:        { type: 'jpeg', quality: 0.98 },
                        html2canvas:  {
                                        scale: 2,
                                        useCORS: true,
                                        logging: false, // Can be true for debugging
                                        letterRendering: true,
                                        scrollX: 0, // Ensure capture starts from the top-left
                                        scrollY: 0,
                                        windowWidth: document.documentElement.scrollWidth, // Capture as if viewport is wide enough for all content
                                        windowHeight: document.documentElement.scrollHeight // Capture as if viewport is tall enough
                                      },
                        jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }, // Changed to landscape
                        pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
                    };

                    html2pdf().from(elementToPrint).set(opt).save().then(function() {
                         downloadPdfBtn.disabled = false;
                         downloadPdfBtn.innerHTML = '<i class="bi bi-file-earmark-pdf-fill me-2"></i>Download PDF';
                    }).catch(function(error) {
                        console.error("Error generating PDF:", error);
                        downloadPdfBtn.disabled = false;
                        downloadPdfBtn.innerHTML = '<i class="bi bi-file-earmark-pdf-fill me-2"></i>Download PDF';
                        alert("Sorry, an error occurred while generating the PDF. Check the console for details.");
                    });
                });
            }
        });
    </script>
</body>
</html>