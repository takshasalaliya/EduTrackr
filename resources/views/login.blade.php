<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Attendance Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons (optional, but good for consistency if you add icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #eef2f7; /* Consistent with dashboard background */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Consistent font */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Use min-height for flexibility */
            margin: 0;
            padding: 20px; /* Add padding for smaller screens so container isn't edge-to-edge */
        }

        .login-container {
            background: white;
            padding: 30px 35px; /* Increased padding */
            border-radius: 12px; /* Consistent border-radius */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07); /* Consistent shadow */
            width: 100%;
            max-width: 420px; /* Slightly wider for better spacing */
            text-align: center; /* Center content within the container */
        }

        .login-logo img {
            max-width: 200px; /* Adjust as needed */
            margin-bottom: 25px;
        }

        .login-header h1 {
            font-weight: 600;
            color: #0056b3; /* Darker primary blue */
            margin-bottom: 25px;
            font-size: 1.8rem;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            float: left; /* Align labels to the left */
            margin-bottom: 0.3rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }


        .btn-login { /* Custom class for login button for specific styling */
            background-color: #007bff; /* Primary blue from dashboard */
            border-color: #007bff;
            color: white;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 8px;
            width: 100%;
            margin-top: 15px; /* Space above the button */
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .btn-login:hover {
            background-color: #0056b3; /* Darker shade on hover */
            border-color: #0056b3;
            color: white;
        }

        .alert-danger {
            font-size: 0.9rem;
            text-align: left;
            border-radius: 8px;
        }

        .error-message {
            color: #dc3545; /* Bootstrap danger color */
            font-size: 0.85rem;
            display: block; /* Make sure it takes its own line */
            text-align: left;
            margin-top: 0.25rem;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: #007bff; /* Floating label color on focus/filled */
        }
        .form-floating > label {
            color: #6c757d; /* Default floating label color */
        }

        /* Optional: Add a link back to dashboard or for "forgot password" */
        .login-links {
            margin-top: 20px;
            font-size: 0.9rem;
        }
        .login-links a {
            color: #007bff;
            text-decoration: none;
        }
        .login-links a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="https://i.ibb.co/yFhzNxBJ/3-removebg-preview.png" alt="Semcom Logo">
        </div>
        <div class="login-header">
            <h1>Login</h1>
        </div>

        @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
        @endif

        <form action="loginMatch" method="POST">
            @csrf
            <div class="mb-3 text-start"> <!-- text-start aligns label to left -->
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                @error('email')
                    <span class="invalid-feedback error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-3 text-start"> <!-- text-start aligns label to left -->
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password" required>
                @error('password')
                    <span class="invalid-feedback error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </button>
        </form>

        <div class="login-links mt-3">
            <a href="/">Back to Dashboard</a>
            <!-- You can add a "Forgot Password?" link here if needed -->
            <!-- <span class="mx-2">|</span>
            <a href="/forgot-password">Forgot Password?</a> -->
        </div>
    </div>

    <!-- Bootstrap JS Bundle (Popper.js included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>