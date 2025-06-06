<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Counselor Panel') - Attendance MS</title>
    <!-- Bootstrap CSS (Updated to 5.3.2) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons (Updated to latest) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Counselor Panel Styles (can be same as admin or slightly tweaked) -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef2f7; /* Light background for main content area */
            color: #333;
            overflow-x: hidden; /* Prevent horizontal scrollbar from sidebar transition */
        }

        .counselor-wrapper { /* Renamed for specificity */
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background-color: #0A2540; /* Deep, professional blue - consistent with admin */
            color: #adb5bd;
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1030;
        }

        .sidebar-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .sidebar-header a.brand-link { /* Specific class for the main brand link */
            color: #ffffff;
            font-size: 1.2rem;
            font-weight: 600;
            text-decoration: none;
            display: flex; /* For logo and text alignment */
            align-items: center;
            justify-content: center;
        }
        .sidebar-header img.logo {
            max-width: 30px; /* Adjust as needed */
            margin-right: 10px;
        }
         .sidebar-header .user-info {
            margin-top: 10px;
            font-size: 0.9rem;
            color: #ffffff; /* White for better contrast */
         }
         .sidebar-header .user-info .user-role {
            font-size: 0.8rem;
            color: #adb5bd;
            display: block;
        }


        .sidebar .nav-pills .nav-link {
            color: #adb5bd;
            padding: 0.75rem 1.5rem;
            border-radius: 0;
            transition: background-color 0.2s ease, color 0.2s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-pills .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
            min-width: 20px;
            text-align: center;
        }
        .sidebar .nav-pills .nav-link .ms-1 {
            transition: opacity 0.3s ease-in-out;
        }

        .sidebar .nav-pills .nav-link:hover,
        .sidebar .nav-pills .nav-link.active {
            background-color: #007bff; /* Primary blue for active/hover */
            color: #ffffff;
        }
        .sidebar .nav-pills .nav-link.active i {
            color: #ffffff;
        }

        .sidebar .nav-pills .collapse .nav-link {
            padding-left: 3rem;
            font-size: 0.9rem;
            background-color: rgba(0,0,0,0.1);
        }
        .sidebar .nav-pills .collapse .nav-link:hover {
            background-color: #0069d9;
        }
        .sidebar .nav-pills .nav-link[data-bs-toggle="collapse"]::after {
            content: '\F282'; /* Bootstrap Icon chevron down */
            font-family: 'bootstrap-icons';
            margin-left: auto;
            transition: transform 0.2s ease-in-out;
            font-size: 0.8rem;
        }
        .sidebar .nav-pills .nav-link[data-bs-toggle="collapse"][aria-expanded="true"]::after {
            transform: rotate(180deg);
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 260px;
            transition: margin-left 0.3s ease-in-out;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        .page-header h1.page-header-title { /* More specific selector */
            font-size: 1.75rem;
            color: #0056b3;
            font-weight: 600;
            margin-bottom: 0; /* Remove bottom margin from h1 */
        }

        .menu-toggle-btn {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 0.5rem 0.8rem;
            border-radius: 0.25rem;
            display: none;
        }
         .menu-toggle-btn:hover, .menu-toggle-btn:focus {
            background-color: #0056b3;
            color: white;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1040;
            }
            .sidebar.sidebar-open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .menu-toggle-btn {
                display: inline-block;
            }
            .sidebar .nav-pills .nav-link .ms-1 {
                display: inline !important;
            }
        }

        /* Optional: Sidebar collapsed state for desktop */
        body.sidebar-collapsed .sidebar {
             width: 80px;
        }
        body.sidebar-collapsed .sidebar .sidebar-header a.brand-link span, /* Target text in brand link */
        body.sidebar-collapsed .sidebar .sidebar-header .user-info,
        body.sidebar-collapsed .sidebar .nav-pills .nav-link span,
        body.sidebar-collapsed .sidebar .nav-pills .nav-link[data-bs-toggle="collapse"]::after {
            opacity: 0;
            width: 0;
            overflow: hidden;
            display: none;
        }
         body.sidebar-collapsed .sidebar .sidebar-header img.logo {
            margin-right: 0; /* No margin when text is hidden */
         }
         body.sidebar-collapsed .sidebar .nav-pills .nav-link {
            justify-content: center;
        }
        body.sidebar-collapsed .sidebar .nav-pills .nav-link i {
            margin-right: 0;
        }
        body.sidebar-collapsed .main-content {
            margin-left: 80px;
        }

        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1020;
            display: none;
        }
        .sidebar-open ~ .sidebar-backdrop {
            display: block;
        }
    </style>
</head>
<body class=""> <!-- Add 'sidebar-collapsed' here for desktop collapsed view by default -->
<div class="counselor-wrapper">
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <a href="/counselor" class="brand-link"> <!-- Added class for easier targeting -->
                <img src="https://i.ibb.co/yFhzNxBJ/3-removebg-preview.png" alt="Semcom Logo" class="logo">
                <span>Counselor Panel</span>
            </a>
            @auth <!-- Check if user is authenticated -->
            <div class="user-info">
                {{ Auth::user()->name }}
                <span class="user-role">Role: {{ ucfirst(Auth::user()->role) }}</span>
            </div>
            @endauth
        </div>

        <ul class="nav nav-pills flex-column mb-auto pt-2">
            <li class="nav-item">
                <a href="/counselor" class="nav-link {{ Request::is('counselor') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> <span class="ms-1">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#studentSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('add_student', 'student_list') ? 'active' : '' }}" aria-expanded="{{ Request::is('add_student', 'student_list') ? 'true' : 'false' }}">
                    <i class="bi bi-people-fill"></i> <span class="ms-1">Student</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('add_student', 'student_list') ? 'show' : '' }}" id="studentSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/add_student" class="nav-link {{ Request::is('add_student') ? 'active' : '' }}"><i class="bi bi-person-plus-fill"></i> <span class="ms-1">Add</span></a></li>
                    <li><a href="/student_list" class="nav-link {{ Request::is('student_list') ? 'active' : '' }}"><i class="bi bi-list-ul"></i> <span class="ms-1">View</span></a></li>
                </ul>
            </li>
            <li>
                <a href="#subjectSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('add_subject', 'subject_list') ? 'active' : '' }}" aria-expanded="{{ Request::is('add_subject', 'subject_list') ? 'true' : 'false' }}">
                    <i class="bi bi-book-half"></i> <span class="ms-1">Subject</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('add_subject', 'subject_list') ? 'show' : '' }}" id="subjectSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/add_subject" class="nav-link {{ Request::is('add_subject') ? 'active' : '' }}"><i class="bi bi-journal-plus"></i> <span class="ms-1">Add</span></a></li>
                    <li><a href="/subject_list" class="nav-link {{ Request::is('subject_list') ? 'active' : '' }}"><i class="bi bi-journals"></i> <span class="ms-1">View</span></a></li>
                </ul>
            </li>
            <li>
                <a href="#subjectTeacherMapSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('subjectallocated', 'list_teachingstaff') ? 'active' : '' }}" aria-expanded="{{ Request::is('subjectallocated', 'list_teachingstaff') ? 'true' : 'false' }}">
                    <i class="bi bi-link-45deg"></i> <span class="ms-1">Subject & Teacher Map</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('subjectallocated', 'list_teachingstaff') ? 'show' : '' }}" id="subjectTeacherMapSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/subjectallocated" class="nav-link {{ Request::is('subjectallocated') ? 'active' : '' }}"><i class="bi bi-node-plus-fill"></i> <span class="ms-1">Add</span></a></li>
                    <li><a href="/list_teachingstaff" class="nav-link {{ Request::is('list_teachingstaff') ? 'active' : '' }}"><i class="bi bi-diagram-2-fill"></i> <span class="ms-1">View</span></a></li>
                </ul>
            </li>
            <li>
                <a href="#studentSubjectMapSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('optionalgroup', 'optionallist') ? 'active' : '' }}" aria-expanded="{{ Request::is('optionalgroup', 'optionallist') ? 'true' : 'false' }}">
                    <i class="bi bi-check2-square"></i> <span class="ms-1">Subject & Student Map</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('optionalgroup', 'optionallist') ? 'show' : '' }}" id="studentSubjectMapSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/optionalgroup" class="nav-link {{ Request::is('optionalgroup') ? 'active' : '' }}"><i class="bi bi-ui-checks"></i> <span class="ms-1">Add</span></a></li>
                    <li><a href="/optionallist" class="nav-link {{ Request::is('optionallist') ? 'active' : '' }}"><i class="bi bi-card-checklist"></i> <span class="ms-1">View</span></a></li>
                </ul>
            </li>
            <li>
                <a href="#attendanceSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('select_counselor', 'attendent_list_counselor') ? 'active' : '' }}" aria-expanded="{{ Request::is('select_counselor', 'attendent_list_counselor') ? 'true' : 'false' }}">
                    <i class="bi bi-calendar-check-fill"></i> <span class="ms-1">Take Attendance</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('select_counselor', 'attendent_list_counselor') ? 'show' : '' }}" id="attendanceSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/select_counselor" class="nav-link {{ Request::is('select_counselor') ? 'active' : '' }}"><i class="bi bi-calendar-plus-fill"></i> <span class="ms-1">Add</span></a></li>
                    <li><a href="/attendent_list_counselor" class="nav-link {{ Request::is('attendent_list_counselor') ? 'active' : '' }}"><i class="bi bi-calendar-range-fill"></i> <span class="ms-1">View</span></a></li>
                </ul>
            </li>
            <li>
                <a href="#classAttendanceSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('classattendent') ? 'active' : '' }}" aria-expanded="{{ Request::is('classattendent') ? 'true' : 'false' }}">
                    <i class="bi bi-easel3-fill"></i> <span class="ms-1">Class Attendance</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('classattendent') ? 'show' : '' }}" id="classAttendanceSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/classattendent" class="nav-link {{ Request::is('classattendent') ? 'active' : '' }}"><i class="bi bi-display-fill"></i> <span class="ms-1">View</span></a></li>
                </ul>
            </li>
            {{-- New Annual Leave Section --}}
            <li>
                <a href="#annualLeaveSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('counselor/leaves/create', 'counselor/leaves') ? 'active' : '' }}" aria-expanded="{{ Request::is('counselor/leaves/create', 'counselor/leaves') ? 'true' : 'false' }}">
                    <i class="bi bi-calendar-event-fill"></i> <span class="ms-1">Activity Leave</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('counselor/leaves/create', 'counselor/leaves') ? 'show' : '' }}" id="annualLeaveSubmenu" data-bs-parent="#sidebar">
                    <li><a href="{{ url('counselor/leaves/create') }}" class="nav-link {{ Request::is('counselor/leaves/create') ? 'active' : '' }}"><i class="bi bi-calendar2-plus-fill"></i> <span class="ms-1">Create Leave</span></a></li>
                    <li><a href="{{ url('counselor/leaves') }}" class="nav-link {{ Request::is('counselor/leaves') && !Request::is('counselor/leaves/create') ? 'active' : '' }}"><i class="bi bi-calendar3-week-fill"></i> <span class="ms-1">Show Leaves</span></a></li>
                </ul>
            </li>
        </ul>

        <div class="sidebar-footer">
            <a href="/logout" class="nav-link">
                <i class="bi bi-box-arrow-left"></i> <span class="ms-1">Logout</span>
            </a>
        </div>
    </nav>
    <!-- End Sidebar -->

    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <button id="menu-toggle-btn" class="btn menu-toggle-btn" type="button">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="page-header-title">@yield('page_title', 'Dashboard')</h1> <!-- Dynamic Page Title -->
        </header>

        <!-- Page Content Yield -->
        @yield('content') <!-- Consolidated yield for main content -->
        @section('add_form')
        @show
        @section('student_table')
        @show
        @section('edit_form')
        @show
        @section('add_class')
        @show   
        @section('class_list')
        @show
        @section('edit_class')
        @show
        @section('dashboard')
        @show
        @section('addsubject')
        @show
        @section('subject_table')
        @show
        @section('teachingstaff')
        @show
        @section('editteachingstaff')
        @show
        @section('optional_subject')
        @show
        @section('optional_subject_list')
        @show
        @section('attendent_before')
        @show
        @section('attendent')
        @show
        @section('subject_table_attendent')
        @show
        @section('edit_attendent')
        @show
        @section('pdf')
        @show
        @section('attendent_code')
        @show
        @section('after_code_attendent')
        @show
        @section('classattendent')
        @show
        @section('layoutcounselor')
        @show
    </main>
    <!-- End Main Content -->
</div>
<div class="sidebar-backdrop"></div> <!-- Backdrop for mobile -->


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuToggleBtn = document.getElementById('menu-toggle-btn');
        const sidebar = document.getElementById('sidebar');
        // const body = document.body; // Not strictly needed for this toggle version
        const sidebarBackdrop = document.querySelector('.sidebar-backdrop');

        if (menuToggleBtn) {
            menuToggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('sidebar-open'); // Toggle on sidebar itself
                if (sidebar.classList.contains('sidebar-open') && window.innerWidth < 992) {
                    sidebarBackdrop.style.display = 'block';
                } else {
                    sidebarBackdrop.style.display = 'none';
                }
            });
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', () => {
                sidebar.classList.remove('sidebar-open');
                sidebarBackdrop.style.display = 'none';
            });
        }

        // Optional: Desktop sidebar collapse toggle (if you add a button for it)
        const desktopCollapseToggle = document.getElementById('desktop-sidebar-toggle');
        if (desktopCollapseToggle) {
            desktopCollapseToggle.addEventListener('click', () => {
                document.body.classList.toggle('sidebar-collapsed');
            });
        }
    });
</script>
</body>
</html>