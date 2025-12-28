<?php
$pageTitle = "Login - Electro Electronics";
require_once '../includes/header.php';

// Redirect if already logged in (role-based)
if (isLoggedIn()) {
    startSession();
    if (isset($_SESSION['role']) && $_SESSION['role'] === ROLE_ADMIN) {
        header('Location: ../admin/');
    } else {
        header('Location: ../');
    }
    exit();
}

require_once '../controllers/AuthController.php';

$authController = new AuthController();

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $authController->login();
    if ($result['success']) {
        // Role-based redirect
        startSession();
        if (isset($_SESSION['role']) && $_SESSION['role'] === ROLE_ADMIN) {
            // Admin redirects to Admin Dashboard
            header('Location: ../admin/');
        } else {
            // User redirects to User Dashboard (homepage)
            header('Location: ../');
        }
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
                    <h3 class="text-center">Login</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <?php
                    $successMsg = getFlashMessage('success');
                    if ($successMsg):
                    ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>