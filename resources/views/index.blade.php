<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Attendance Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #eef2f7; /* Lighter, modern background */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Modern font */
            color: #333;
        }

        .header-banner {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); /* Primary blue gradient */
            padding: 30px 0 20px;
            text-align: center;
            margin-bottom: 30px;
        }

        .header-banner img {
            max-width: 300px; /* Control logo size */
            height: auto;
            margin-bottom: 15px;
        }

        .main-content {
            padding: 0 20px 20px; /* Adjusted padding */
        }

        .welcome-title {
            color: #fff; /* White text on gradient */
            font-weight: 300;
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: 600;
            color: #0056b3; /* Darker primary blue */
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff; /* Primary blue underline */
            display: inline-block;
        }

        .about-section {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            margin-bottom: 30px;
        }
        .about-section h3 {
            color: #0056b3;
            margin-bottom: 15px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 25px; /* Ensure spacing on small screens */
            height: 100%; /* Make cards in a row same height */
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Pushes button to bottom */
            flex-grow: 1;
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #007bff; /* Primary blue for icons */
        }

        .card-title {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 10px;
        }

        .card-text {
            color: #555;
            font-size: 0.95rem;
            flex-grow: 1; /* Allows text to take available space before button */
            margin-bottom: 15px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 10px 20px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-danger { /* Admin panel button */
            background-color: #dc3545;
            border-color: #dc3545;
            padding: 10px 20px;
            font-weight: 500;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .login-section {
            padding: 30px 0;
            text-align: center;
        }

        .btn-accent { /* For the main login button */
            background-color: #28a745; /* Green for a positive action */
            border-color: #28a745;
            color: white;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        .btn-accent:hover {
            background-color: #218838;
            border-color: #1e7e34;
            color: white;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .header-banner img {
                max-width: 200px;
            }
            .welcome-title {
                font-size: 1.8rem;
            }
            .card {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <header class="header-banner">
        <img src="https://i.ibb.co/yFhzNxBJ/3-removebg-preview.png" alt="Semcom Logo" class="img-fluid">
        <h1 class="welcome-title">Attendance Management System</h1>
    </header>

    <div class="container-fluid main-content">
        <!-- Welcome Message (moved from under logo to here as a standalone title for content area) -->
        <div class="d-flex justify-content-center flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4">
            <h2 class="h3 text-center" style="color: #0056b3;">Welcome to Your Dashboard</h2>
        </div>

        <!-- About SEMCOM College Section -->
        <div class="row mb-4 justify-content-center">
            <div class="col-md-10 col-lg-8">
                <section class="about-section">
                    <h3 class="text-center"><i class="fas fa-university me-2"></i>About SEMCOM College</h3>
                    <p class="text-muted">SEMCOM College is a premier educational institution offering a diverse range of undergraduate and postgraduate programs. With a focus on academic excellence and student well-being, SEMCOM strives to foster a learning environment that is inclusive, innovative, and forward-thinking.</p>
                </section>
            </div>
        </div>

        <!-- Dashboard Cards Section -->
        <div class="row">
            <!-- Card 1 for Students -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-eye"></i></div>
                        <h5 class="card-title">View Attendance</h5>
                        <p class="card-text">Check your attendance records and details.</p>
                        <a href="login" class="btn btn-primary w-100">View Records</a>
                    </div>
                </div>
            </div>

            <!-- Card 2 for Teachers -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                        <h5 class="card-title">Manage Classes</h5>
                        <p class="card-text">Update and manage attendance for your classes.</p>
                        <a href="login" class="btn btn-primary w-100">Manage Classes</a>
                    </div>
                </div>
            </div>

            <!-- Card 3 for Counselors -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-comments"></i></div>
                        <h5 class="card-title">Student Counseling</h5>
                        <p class="card-text">Access and review counseling sessions and reports.</p>
                        <a href="login" class="btn btn-primary w-100">View Counseling</a>
                    </div>
                </div>
            </div>

            <!-- Card 4 for Admins -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-user-shield"></i></div>
                        <h5 class="card-title">System Administration</h5>
                        <p class="card-text">Manage users, permissions, and settings.</p>
                        <a href="login" class="btn btn-danger w-100">Admin Panel</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Button Section -->
        <div class="row login-section">
            <div class="col-md-12 text-center">
                <p class="mb-3 text-muted">Ready to get started?</p>
                <a href="login" class="btn btn-accent btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Login Now
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle (Popper.js included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>