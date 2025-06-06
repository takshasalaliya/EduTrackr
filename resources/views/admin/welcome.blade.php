@extends('admin.layout') {{-- Assuming your layout file is in resources/views/admin/layout.blade.php --}}

@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard') {{-- For the header in the main content area --}}

@section('admin_dashboard')
<style>
    .stat-card {
        background-color: #ffffff;
        border-radius: 12px; /* Consistent with other elements */
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07); /* Consistent shadow */
        display: flex;
        align-items: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        color: #333; /* Default text color */
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .stat-card .stat-icon {
        font-size: 2.8rem; /* Larger icon */
        padding: 15px;
        border-radius: 50%; /* Circular background for icon */
        margin-right: 20px;
        color: #fff; /* Icon color will be white */
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70px; /* Fixed width for icon circle */
        height: 70px; /* Fixed height for icon circle */
    }

    .stat-card .stat-info h3 {
        font-size: 2rem; /* Larger number */
        font-weight: 700;
        margin-bottom: 0;
    }

    .stat-card .stat-info p {
        font-size: 0.95rem;
        color: #6c757d; /* Muted text for label */
        margin-bottom: 0;
        text-transform: uppercase;
        font-weight: 500;
    }

    /* Specific colors for stat cards */
    .stat-card.students .stat-icon { background-color: #007bff; } /* Primary Blue */
    .stat-card.students .stat-info h3 { color: #007bff; }

    .stat-card.teachers .stat-icon { background-color: #28a745; } /* Success Green */
    .stat-card.teachers .stat-info h3 { color: #28a745; }

    .stat-card.counselors .stat-icon { background-color: #17a2b8; } /* Info Teal */
    .stat-card.counselors .stat-info h3 { color: #17a2b8; }

    .stat-card.classes .stat-icon { background-color: #ffc107; } /* Warning Yellow */
    .stat-card.classes .stat-info h3 { color: #ffc107; }
    .stat-card.classes .stat-icon i { color: #333; } /* Darker icon for light background */


    .card-custom {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        border: none;
    }
    .card-custom .card-header {
        background-color: #f8f9fa; /* Lighter header */
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        color: #0056b3; /* Darker primary blue for header text */
        font-size: 1.1rem;
        padding: 0.75rem 1.25rem;
    }
    .table thead th {
        background-color: #eef2f7; /* Light background for table header */
        color: #495057;
        font-weight: 600;
        border-bottom-width: 1px;
        border-top: none; /* Remove default top border from bootstrap table */
    }
    .table tbody tr:hover {
        background-color: #f1f5f9;
    }
    .table td, .table th {
        vertical-align: middle;
    }

</style>

<div class="container-fluid">
    <div class="row g-4">
        <!-- Students Stat Card -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card students">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $students ?? 0 }}</h3>
                    <p>Total Students</p>
                </div>
            </div>
        </div>

        <!-- Teachers Stat Card -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card teachers">
                <div class="stat-icon">
                    <i class="bi bi-person-video3"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $teachers ?? 0 }}</h3>
                    <p>Total Teachers</p>
                </div>
            </div>
        </div>

        <!-- Counselors Stat Card -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card counselors">
                <div class="stat-icon">
                    <i class="bi bi-chat-left-dots-fill"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $counselors ?? 0 }}</h3>
                    <p>Total Counselors</p>
                </div>
            </div>
        </div>

        <!-- Classes Stat Card -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card classes">
                <div class="stat-icon">
                    <i class="bi bi-easel2-fill"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $classes ?? 0 }}</h3>
                    <p>Total Classes</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">
                    <i class="bi bi-list-ul me-2"></i>Recently Added Classes
                </div>
                <div class="card-body p-0">
                    @if(isset($class_name) && $class_name->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Program Name</th>
                                    <th>Division</th>
                                    <th>Semester</th>
                                    <th>Batch Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($class_name as $class)
                                <tr>
                                    <td>{{ $class->id }}</td>
                                    <td>{{ $class->program->name ?? 'N/A' }}</td>
                                    <td>{{ $class->devision }}</td>
                                    <td>{{ $class->sem }}</td>
                                    <td>{{ $class->year }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center p-4">
                        <p class="text-muted mb-0">No classes found.</p>
                    </div>
                    @endif
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection