<?php
$pageTitle = "Register - Electro Electronics";
require_once '../includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ../');
    exit();
}

require_once '../controllers/AuthController.php';

$authController = new AuthController();

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $authController->register();
    if ($result['success']) {
        // After successful registration, redirect to login page
        startSession();
        setFlashMessage('success', 'Registration successful! Please login to continue.');
        header('Location: login.php');
        exit();
    } else {
        $error = $result['message'];
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Register</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="register.php">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mobile (Jordanian)</label>
                            <input type="text" name="mobile" class="form-control" placeholder="0771234567 or +962771234567">
                            <small class="form-text text-muted">Optional. Must start with 077, 078, or 079</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                            <small class="form-text text-muted">Must be at least 8 characters with letters, numbers, and symbols</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password *</label>
                            <input type="password" name="confirmPassword" class="form-control" required minlength="8">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                    <div class="text-center mt-3">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

