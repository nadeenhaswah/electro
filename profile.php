<?php
$pageTitle = "My Profile - Electro Electronics";
require_once 'includes/access_control.php';

// Require user to be logged in
requireUserAccess();

require_once 'controllers/AuthController.php';

$authController = new AuthController();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $result = $authController->updateProfile();
    if ($result['success']) {
        setFlashMessage('success', $result['message']);
        header('Location: profile.php');
        exit();
    } else {
        $error = $result['message'];
    }
}

$user = $authController->getCurrentUser();

if (!$user) {
    setFlashMessage('error', 'User not found');
    header('Location: index.php');
    exit();
}

// Check if edit mode
$editMode = isset($_GET['edit']) && $_GET['edit'] === '1';

// Display flash messages
$successMsg = getFlashMessage('success');
$errorMsg = getFlashMessage('error');
if (isset($error)) {
    $errorMsg = $error;
}
?>
<?php require_once 'includes/header.php';
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-user"></i> My Profile</h3>
                </div>
                <div class="card-body">
                    <?php if ($successMsg): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
                    <?php endif; ?>
                    <?php if ($errorMsg): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
                    <?php endif; ?>

                    <div class="row mb-4">
                        <div class="col-md-12 text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-circle" style="font-size: 80px; color: #007bff;"></i>
                            </div>
                            <h4><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                            <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>

                    <hr>

                    <?php if (!$editMode): ?>
                        <!-- View Mode -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">Profile Information</h5>
                            <a href="profile.php?edit=1" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong><i class="fas fa-id-card"></i> Full Name:</strong>
                            </div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong><i class="fas fa-envelope"></i> Email Address:</strong>
                            </div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong><i class="fas fa-phone"></i> Mobile Number:</strong>
                            </div>
                            <div class="col-md-8">
                                <?php
                                if (!empty($user['mobile'])) {
                                    echo htmlspecialchars($user['mobile']);
                                } else {
                                    echo '<span class="text-muted">Not provided</span>';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong><i class="fas fa-user-tag"></i> Account Type:</strong>
                            </div>
                            <div class="col-md-8">
                                <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </div>
                        </div>

                        <hr>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php" class="btn btn-secondary">Back to Home</a>
                            <a href="orders.php" class="btn btn-primary">View My Orders</a>
                        </div>
                    <?php else: ?>
                        <!-- Edit Mode -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">Edit Profile Information</h5>
                            <a href="profile.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>

                        <form method="POST" action="profile.php">
                            <input type="hidden" name="action" value="update_profile">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                    <small class="form-text text-muted">Letters only, no numbers or symbols</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                    <small class="form-text text-muted">Letters only, no numbers or symbols</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                <small class="form-text text-muted">Must be unique and valid email format</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mobile Number (Jordanian)</label>
                                <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($user['mobile'] ?? ''); ?>" placeholder="0771234567 or +962771234567">
                                <small class="form-text text-muted">Optional. Must start with 077, 078, or 079</small>
                            </div>

                            <hr>
                            <h6 class="mb-3">Change Password (Optional)</h6>
                            <p class="text-muted small mb-3">Leave blank to keep current password</p>

                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control" minlength="8">
                                <small class="form-text text-muted">Must be at least 8 characters with letters, numbers, and symbols</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirmPassword" class="form-control" minlength="8">
                                <small class="form-text text-muted">Must match new password</small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="profile.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>