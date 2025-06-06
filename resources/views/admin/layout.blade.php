<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Attendance MS</title>
    <!-- Bootstrap CSS (Updated to 5.3.2) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons (Updated to latest) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Admin Styles -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef2f7; /* Light background for main content area */
            color: #333;
            overflow-x: hidden; /* Prevent horizontal scrollbar from sidebar transition */
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px; /* Slightly wider sidebar */
            background-color: #0A2540; /* Deep, professional blue */
            color: #adb5bd; /* Softer text color for non-active items */
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            position: fixed; /* Fixed sidebar */
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

        .sidebar-header a {
            color: #ffffff;
            font-size: 1.2rem;
            font-weight: 600;
            text-decoration: none;
        }
         .sidebar-header .user-role {
            font-size: 0.8rem;
            color: #adb5bd;
            display: block;
        }


        .sidebar .nav-pills .nav-link {
            color: #adb5bd;
            padding: 0.75rem 1.5rem;
            border-radius: 0; /* Remove default pill radius for full-width effect */
            transition: background-color 0.2s ease, color 0.2s ease;
            white-space: nowrap; /* Prevent text wrapping */
            display: flex;
            align-items: center;
        }
        .sidebar .nav-pills .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.2rem; /* Consistent icon size */
            min-width: 20px; /* Ensure alignment */
            text-align: center;
        }
        .sidebar .nav-pills .nav-link .ms-1 {
            transition: opacity 0.3s ease-in-out;
        }

        .sidebar .nav-pills .nav-link:hover,
        .sidebar .nav-pills .nav-link.active { /* Style for active link */
            background-color: #007bff; /* Primary blue for active/hover */
            color: #ffffff;
        }
        .sidebar .nav-pills .nav-link.active i {
            color: #ffffff;
        }

        /* Submenu styling */
        .sidebar .nav-pills .collapse .nav-link {
            padding-left: 3rem; /* Indent sub-items */
            font-size: 0.9rem;
            background-color: rgba(0,0,0,0.1); /* Slightly different background for submenu */
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
            margin-top: auto; /* Pushes footer to the bottom */
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 260px; /* Same as sidebar width */
            transition: margin-left 0.3s ease-in-out;
        }

        .navbar-toggler-icon { /* For the menu toggle button */
           background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        .page-header h1 {
            font-size: 1.75rem;
            color: #0056b3; /* Darker primary blue */
            font-weight: 600;
        }

        .menu-toggle-btn {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 0.5rem 0.8rem;
            border-radius: 0.25rem;
            display: none; /* Hidden by default, shown on smaller screens */
        }
         .menu-toggle-btn:hover, .menu-toggle-btn:focus {
            background-color: #0056b3;
            color: white;
        }


        /* Responsive adjustments */
        @media (max-width: 991.98px) { /* Medium devices and down */
            .sidebar {
                transform: translateX(-100%);
                z-index: 1040; /* Ensure it's above content but below potential modals */
            }
            .sidebar.sidebar-open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .menu-toggle-btn {
                display: inline-block; /* Show toggle button */
            }
            .sidebar .nav-pills .nav-link .ms-1 {
                display: inline !important; /* Ensure text is always visible on mobile sidebar */
            }
        }

        /* Optional: Sidebar collapsed state (if you implement a toggle for desktop) */
        body.sidebar-collapsed .sidebar {
             width: 80px; /* Collapsed width */
        }
        body.sidebar-collapsed .sidebar .sidebar-header a,
        body.sidebar-collapsed .sidebar .nav-pills .nav-link span,
        body.sidebar-collapsed .sidebar .nav-pills .nav-link[data-bs-toggle="collapse"]::after,
        body.sidebar-collapsed .sidebar .sidebar-header .user-role {
            opacity: 0;
            width: 0;
            overflow: hidden;
            display: none; /* Hide text when collapsed */
        }
         body.sidebar-collapsed .sidebar .nav-pills .nav-link {
            justify-content: center;
        }
        body.sidebar-collapsed .sidebar .nav-pills .nav-link i {
            margin-right: 0;
        }
        body.sidebar-collapsed .main-content {
            margin-left: 80px; /* Adjust content margin when sidebar is collapsed */
        }

        /* Backdrop for mobile sidebar */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1020; /* Below sidebar, above content */
            display: none; /* Hidden by default */
        }
        .sidebar-open ~ .sidebar-backdrop { /* Show backdrop when sidebar is open on mobile */
            display: block;
        }

    </style>
</head>
<body class=""> <!-- Add 'sidebar-collapsed' class here for desktop collapsed view by default -->
<div class="admin-wrapper">
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <a href="/admin">
                SEMCOM Admin
                @auth <!-- Check if user is authenticated -->
                <span class="user-role">{{ Auth::user()->name }} : {{ Auth::user()->role }}</span>
                @endauth
            </a>
        </div>

        <ul class="nav nav-pills flex-column mb-auto pt-2">
            <li class="nav-item">
                <a href="/admin" class="nav-link {{ Request::is('admin') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> <span class="ms-1">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#programSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('field*') ? 'active' : '' }}" aria-expanded="{{ Request::is('field*') ? 'true' : 'false' }}">
                    <i class="bi bi-journal-richtext"></i> <span class="ms-1">Program</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('field*') ? 'show' : '' }}" id="programSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/field" class="nav-link {{ Request::is('field') ? 'active' : '' }}"><i class="bi bi-plus-circle-fill"></i> <span class="ms-1">Add Program</span></a></li>
                    <!-- Add view program link if needed -->
                </ul>
            </li>
            <li>
                <a href="#teacherSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('add_teacher', 'teacher_list') ? 'active' : '' }}" aria-expanded="{{ Request::is('add_teacher', 'teacher_list') ? 'true' : 'false' }}">
                    <i class="bi bi-person-video3"></i> <span class="ms-1">Teacher</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('add_teacher', 'teacher_list') ? 'show' : '' }}" id="teacherSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/add_teacher" class="nav-link {{ Request::is('add_teacher') ? 'active' : '' }}"><i class="bi bi-person-plus-fill"></i> <span class="ms-1">Add Teacher</span></a></li>
                    <li><a href="/teacher_list" class="nav-link {{ Request::is('teacher_list') ? 'active' : '' }}"><i class="bi bi-list-ul"></i> <span class="ms-1">View Teachers</span></a></li>
                </ul>
            </li>
             <li>
                <a href="#classSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('add_class', 'class_list') ? 'active' : '' }}" aria-expanded="{{ Request::is('add_class', 'class_list') ? 'true' : 'false' }}">
                    <i class="bi bi-easel2-fill"></i> <span class="ms-1">Class</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('add_class', 'class_list') ? 'show' : '' }}" id="classSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/add_class" class="nav-link {{ Request::is('add_class') ? 'active' : '' }}"><i class="bi bi-plus-square-fill"></i> <span class="ms-1">Add Class</span></a></li>
                    <li><a href="/class_list" class="nav-link {{ Request::is('class_list') ? 'active' : '' }}"><i class="bi bi-card-list"></i> <span class="ms-1">View Classes</span></a></li>
                </ul>
            </li>
            <li>
                <a href="#studentSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('add_student_admin', 'student_list_admin') ? 'active' : '' }}" aria-expanded="{{ Request::is('add_student_admin', 'student_list_admin') ? 'true' : 'false' }}">
                    <i class="bi bi-people-fill"></i> <span class="ms-1">Student</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('add_student_admin', 'student_list_admin') ? 'show' : '' }}" id="studentSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/add_student_admin" class="nav-link {{ Request::is('add_student_admin') ? 'active' : '' }}"><i class="bi bi-person-add"></i> <span class="ms-1">Add Student</span></a></li>
                    <li><a href="/student_list_admin" class="nav-link {{ Request::is('student_list_admin') ? 'active' : '' }}"><i class="bi bi-person-lines-fill"></i> <span class="ms-1">View Students</span></a></li>
                </ul>
            </li>
            <li>
                <a href="#subjectSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('add_subject_admin', 'subject_list_admin') ? 'active' : '' }}" aria-expanded="{{ Request::is('add_subject_admin', 'subject_list_admin') ? 'true' : 'false' }}">
                    <i class="bi bi-book-half"></i> <span class="ms-1">Subject</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('add_subject_admin', 'subject_list_admin') ? 'show' : '' }}" id="subjectSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/add_subject_admin" class="nav-link {{ Request::is('add_subject_admin') ? 'active' : '' }}"><i class="bi bi-journal-plus"></i> <span class="ms-1">Add Subject</span></a></li>
                    <li><a href="/subject_list_admin" class="nav-link {{ Request::is('subject_list_admin') ? 'active' : '' }}"><i class="bi bi-journals"></i> <span class="ms-1">View Subjects</span></a></li>
                </ul>
            </li>
            <li>
                <a href="#subjectTeacherMapSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('subjectallocated_admin', 'list_teachingstaff_admin') ? 'active' : '' }}" aria-expanded="{{ Request::is('subjectallocated_admin', 'list_teachingstaff_admin') ? 'true' : 'false' }}">
                    <i class="bi bi-link-45deg"></i> <span class="ms-1">Subject & Teacher Map</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('subjectallocated_admin', 'list_teachingstaff_admin') ? 'show' : '' }}" id="subjectTeacherMapSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/subjectallocated_admin" class="nav-link {{ Request::is('subjectallocated_admin') ? 'active' : '' }}"><i class="bi bi-node-plus-fill"></i> <span class="ms-1">Assign Subject</span></a></li>
                    <li><a href="/list_teachingstaff_admin" class="nav-link {{ Request::is('list_teachingstaff_admin') ? 'active' : '' }}"><i class="bi bi-diagram-2-fill"></i> <span class="ms-1">View Assignments</span></a></li>
                </ul>
            </li>
             <li>
                <a href="#studentSubjectMapSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('optionalgroup_admin', 'optionallist_admin') ? 'active' : '' }}" aria-expanded="{{ Request::is('optionalgroup_admin', 'optionallist_admin') ? 'true' : 'false' }}">
                    <i class="bi bi-check2-square"></i> <span class="ms-1">Student & Subject Map</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('optionalgroup_admin', 'optionallist_admin') ? 'show' : '' }}" id="studentSubjectMapSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/optionalgroup_admin" class="nav-link {{ Request::is('optionalgroup_admin') ? 'active' : '' }}"><i class="bi bi-ui-checks"></i> <span class="ms-1">Assign Optional</span></a></li>
                    <li><a href="/optionallist_admin" class="nav-link {{ Request::is('optionallist_admin') ? 'active' : '' }}"><i class="bi bi-card-checklist"></i> <span class="ms-1">View Optional</span></a></li>
                </ul>
            </li>
            <li>
                <a href="#whatsappSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('whatsapp*') ? 'active' : '' }}" aria-expanded="{{ Request::is('whatsapp*') ? 'true' : 'false' }}">
                    <i class="bi bi-whatsapp"></i> <span class="ms-1">WhatsApp Message</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('whatsapp*') ? 'show' : '' }}" id="whatsappSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/whatsapp" class="nav-link {{ Request::is('whatsapp') ? 'active' : '' }}"><i class="bi bi-send-fill"></i> <span class="ms-1">Management</span></a></li>
                </ul>
            </li>
            <li>
                <a href="#masterFileSubmenu" data-bs-toggle="collapse" class="nav-link {{ Request::is('master_file*') ? 'active' : '' }}" aria-expanded="{{ Request::is('master_file*') ? 'true' : 'false' }}">
                    <i class="bi bi-file-earmark-zip-fill"></i> <span class="ms-1">Master File</span>
                </a>
                <ul class="collapse nav flex-column ms-1 {{ Request::is('master_file*') ? 'show' : '' }}" id="masterFileSubmenu" data-bs-parent="#sidebar">
                    <li><a href="/master_file" class="nav-link {{ Request::is('master_file') ? 'active' : '' }}"><i class="bi bi-file-earmark-plus-fill"></i> <span class="ms-1">Add</span></a></li>
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
            <h1>@yield('page_title', 'Dashboard')</h1> <!-- Dynamic Page Title -->
        </header>

        <!-- Page Content Yield -->
        @yield('admin_dashboard') <!-- Consolidated yield for main content -->
        
        <!-- Original Yields (can be consolidated into @yield('content') in child views) -->
        @section('add_teacher') @show
        @section('teacher') @show
        @section('edit_teacher') @show
        @section('dashboard') @show
        @section('edit_class') @show
        @section('add_field') @show
        @section('addsubject') @show
        @section('subject_table') @show
        @section('teachingstaff') @show
        @section('editteachingstaff') @show
        @section('optional_subject') @show
        @section('optional_subject_list') @show
        @section('add_form') @show
        @section('student_table') @show
        @section('edit_form') @show
        @section('add_class') @show
        @section('class_list') @show
        @section('hour') @show
        @section('timetable') @show
        @section('master_file') @show
    </main>
    <!-- End Main Content -->
</div>
<div class="sidebar-backdrop"></div> <!-- Backdrop for mobile -->


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuToggleBtn = document.getElementById('menu-toggle-btn');
        const sidebar = document.getElementById('sidebar');
        const body = document.body;
        const sidebarBackdrop = document.querySelector('.sidebar-backdrop');

        if (menuToggleBtn) {
            menuToggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('sidebar-open');
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

        // Optional: Desktop sidebar collapse toggle (if you want a button for it)
        // const desktopCollapseToggle = document.getElementById('desktop-sidebar-toggle');
        if (desktopCollapseToggle) {
            desktopCollapseToggle.addEventListener('click', () => {
                body.classList.toggle('sidebar-collapsed');
            });
        }

        // Auto active and open parent for submenus based on Laravel Request::is
        // (This is handled by adding 'active' and 'show' classes directly in the Blade template)
    });
</script>
</body>
</html>