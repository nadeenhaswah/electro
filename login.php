<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electro - Login & Signup</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Montserrat Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --body-color: #333;
            --headers-color: #2B2D42;
            --primary-color: #D10024;
            --dark-color: #15161D;
            --dark-alt: #1E1F29;
            --grey-light: #E4E7ED;
            --grey-lighter: #FBFBFC;
            --grey-medium: #8D99AE;
            --grey-dark: #B9BABC;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            color: var(--body-color);
            background-color: var(--grey-lighter);
            line-height: 1.6;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: var(--headers-color);
            font-weight: 700;
        }

        a {
            text-decoration: none;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        a:hover {
            color: var(--dark-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(209, 0, 36, 0.25);
        }

        /* Header Styles */
        .site-header {
            background-color: var(--dark-color);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .site-logo {
            font-size: 28px;
            font-weight: 700;
            color: white;
        }

        .site-logo span {
            color: var(--primary-color);
        }

        .nav-links a {
            color: var(--grey-light);
            margin-left: 20px;
            font-weight: 500;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .nav-links .active {
            color: var(--primary-color);
        }

        /* Auth Container */
        .auth-container {
            max-width: 800px;
            margin: 40px auto;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .auth-image {
            background: linear-gradient(rgba(21, 22, 29, 0.8), rgba(21, 22, 29, 0.9)), url('https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-image h2 {
            color: white;
            margin-bottom: 20px;
        }

        .auth-image p {
            color: var(--grey-light);
            margin-bottom: 30px;
        }

        .auth-form {
            padding: 40px;
        }

        .auth-form h2 {
            margin-bottom: 10px;
        }

        .auth-form p {
            color: var(--grey-medium);
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-text {
            color: var(--grey-medium);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--grey-light);
        }

        .divider span {
            padding: 0 15px;
            color: var(--grey-medium);
            font-size: 14px;
        }

        .social-login {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .social-btn {
            flex: 1;
            padding: 10px;
            border: 1px solid var(--grey-light);
            border-radius: 5px;
            background-color: white;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            background-color: var(--grey-lighter);
        }

        .social-btn.facebook {
            color: #1877F2;
        }

        .social-btn.google {
            color: #DB4437;
        }

        .login-link,
        .signup-link {
            text-align: center;
            margin-top: 20px;
            color: var(--grey-medium);
        }

        .login-link a,
        .signup-link a {
            font-weight: 600;
        }

        /* Footer */
        .site-footer {
            background-color: var(--dark-color);
            color: var(--grey-light);
            padding: 30px 0;
            margin-top: 50px;
            text-align: center;
        }

        .copyright {
            color: var(--grey-medium);
            font-size: 14px;
        }

        /* Tabs for switching between login/signup */
        .auth-tabs {
            display: none;
        }

        @media (max-width: 768px) {
            .auth-image {
                display: none;
            }

            .auth-tabs {
                display: flex;
                background-color: var(--dark-alt);
                padding: 0;
            }

            .auth-tab {
                flex: 1;
                text-align: center;
                padding: 15px;
                color: var(--grey-light);
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .auth-tab.active {
                background-color: var(--primary-color);
                color: white;
            }

            .auth-content {
                display: none;
            }

            .auth-content.active {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="site-logo">Electro<span>.</span></div>
                <div class="nav-links">
                    <a href="index.php">Home</a>

                    <a href="login.php" class="active">Login</a>
                    <a href="signup.php">Sign Up</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Login Page -->
        <section id="loginPage" class="login-page">
            <div class="container">
                <div class="auth-container">
                    <!-- Tabs for mobile view -->
                    <div class="auth-tabs">
                        <div class="auth-tab active" onclick="showAuthTab('login')">Login</div>
                        <div class="auth-tab" onclick="showAuthTab('signup')">Sign Up</div>
                    </div>

                    <div class="row g-0">
                        <!-- Left side with image -->
                        <div class="col-md-6 auth-image d-none d-md-block">
                            <h2>Welcome Back to Electro</h2>
                            <p>Login to your account to access exclusive deals, track your orders, and manage your profile.</p>
                            <div class="mt-4">
                                <h4>New Customer?</h4>
                                <p>Create an account to enjoy faster checkout, save multiple shipping addresses, and view order history.</p>
                                <a href="signup.php" class="btn btn-outline-light mt-3">Create Account</a>
                            </div>
                        </div>

                        <!-- Right side with login form -->
                        <div class="col-md-6 auth-content active" id="loginContent">
                            <div class="auth-form">
                                <h2>Login to Your Account</h2>


                                <div class="divider">
                                    <span> Login with email</span>
                                </div>

                                <!-- Login form -->
                                <form id="loginForm">
                                    <div class="mb-3">
                                        <label for="loginEmail" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="loginEmail" placeholder="Enter your email" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="loginPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="loginPassword" placeholder="Enter your password" required>
                                        <div class="form-text">
                                            <a href="forgot-password.html">Forgot your password?</a>
                                        </div>
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="rememberMe">
                                        <label class="form-check-label" for="rememberMe">Remember me</label>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Login</button>
                                </form>

                                <div class="signup-link">
                                    Don't have an account? <a href="signup.php">Create one here</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="site-logo mb-3">Electro<span>.</span></div>
            <p class="mb-3">Your one-stop shop for electronics, laptops, smartphones and accessories</p>
            <div class="copyright">
                &copy; 2023 Electro. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            if (email && password) {
                alert('Login successful! Redirecting to dashboard...');
                // In a real application, you would submit the form to a server here
            } else {
                alert('Please fill in all required fields.');
            }
        });

        // Function to switch between login and signup tabs on mobile
        function showAuthTab(tab) {
            // Update tabs
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            if (tab === 'login') {
                document.querySelectorAll('.auth-tab')[0].classList.add('active');
            } else {
                document.querySelectorAll('.auth-tab')[1].classList.add('active');
            }

            // Update content visibility
            document.querySelectorAll('.auth-content').forEach(c => c.classList.remove('active'));
            if (tab === 'login') {
                document.getElementById('loginContent').classList.add('active');
            } else {
                // For mobile, we would redirect to signup page
                window.location.href = 'signup.php';
            }
        }
    </script>
</body>

</html>