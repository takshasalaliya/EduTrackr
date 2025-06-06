<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>@yield('title')</title>
    <style>
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }

        .sidebar-collapsed {
            transform: translateX(-100%);
        }

        .menu-toggle {
            display: none;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                z-index: 1050;
                transform: translateX(-100%);
                width: 240px;
            }

            .sidebar-open .sidebar {
                transform: translateX(0);
            }

            .sidebar ul.nav li a span {
                display: inline !important; /* Ensure text is visible */
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <!-- Sidebar -->
        <div id="sidebar" class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark sidebar">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5">{{Auth::user()->name}}:{{Auth::user()->role}}</span>
                </a>
                <ul class="nav nav-pills flex-column mb-sm-auto mb-0" id="menu">
                    <li class="nav-item">
                        <a href="/reader" class="nav-link align-middle px-0">
                            <i class="fs-4 bi-house"></i> <span class="ms-1">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#student" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                            <i class="fs-4 bi-speedometer2"></i> <span class="ms-1">Attendance</span>
                        </a>
                        <ul class="collapse nav flex-column ms-1" id="student" data-bs-parent="#menu">
                            <li class="w-100">
                                <a href="select" class="nav-link px-0">
                                    <i class="bi bi-file-plus-fill"></i> <span class="ms-1">Add</span>
                                </a>
                            </li>
                            <li>
                                <a href="/attendent_list" class="nav-link px-0">
                                    <i class="bi bi-eye-fill"></i> <span class="ms-1">View</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="/logout" class="nav-link px-0 align-middle">
                            <i class="fs-4 bi bi-box-arrow-left"></i> <span class="ms-1">Logout</span>
                        </a>
                    </li>
                </ul>
                <hr>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col py-3">
            <!-- Toggle Button -->
            <button id="menu-toggle" class="btn btn-primary menu-toggle d-md-none">
                <i class="bi bi-list"></i> Menu
            </button>
            <!-- Page Content -->
            @section('dashboard')
            @show
            @section('attendent_before')
            @show
            @section('attendent')
            @show
            @section('subject_table')
            @show
            @section('edit_attendent')
            @show
            @section('pdf')
            @show
            @section('attendent_code')
            @show
            @section('after_code_attendent')
            @show
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script>
    // Sidebar toggle script
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const body = document.body;

    menuToggle.addEventListener('click', () => {
        body.classList.toggle('sidebar-open');
    });
</script>
</body>
</html>
