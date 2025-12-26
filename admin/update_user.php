<?php
require_once '../config/database.php';
session_start();
// في الواقع الحقيقي، يجب التحقق من صلاحية المستخدم
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] != 'admin') {
//     header('Location: login.php');
//     exit;
// }



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User Information - Electro Admin</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Montserrat Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin-style.css">

    <style>
        .user-profile-header {
            background: linear-gradient(135deg, #2B2D42 0%, #15161D 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .avatar-xl {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: bold;
            border: 5px solid rgba(255, 255, 255, 0.2);
        }

        .avatar-upload {
            position: relative;
            cursor: pointer;
        }

        .avatar-upload input {
            display: none;
        }

        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .avatar-upload:hover .avatar-overlay {
            opacity: 1;
        }

        .info-card {
            border-left: 4px solid var(--primary-color);
            padding-left: 15px;
            margin-bottom: 20px;
        }

        .tab-content {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--grey-medium);
            font-weight: 500;
            padding: 12px 25px;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary-color);
            background-color: rgba(209, 0, 36, 0.05);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background-color: white;
            border-bottom: 3px solid var(--primary-color);
        }

        .activity-log {
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            padding: 15px;
            border-left: 3px solid var(--primary-color);
            background-color: var(--grey-lighter);
            margin-bottom: 10px;
            border-radius: 0 5px 5px 0;
        }

        .stats-card {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .stats-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1;
        }

        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 5px;
            background-color: #e9ecef;
        }

        .password-strength-bar {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .password-requirements {
            font-size: 0.85rem;
            color: var(--grey-medium);
        }

        .password-requirements li {
            margin-bottom: 5px;
        }

        .password-requirements li.valid {
            color: #28a745;
        }

        .password-requirements li.valid::before {
            content: "✓ ";
            font-weight: bold;
        }

        .form-section {
            border-bottom: 1px solid var(--grey-light);
            padding-bottom: 25px;
            margin-bottom: 25px;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-indicator.online {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .status-indicator.offline {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .verification-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            background-color: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <?php
    include('includes/header.php');
    ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php
            include('includes/sidebar.php');
            ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-5">
                <!-- User Profile Header -->
                <div class="user-profile-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <?php
                                $id = $_GET['id'];
                                $params1[] = $id;
                                $sql = "SELECT * FROM users WHERE user_id = ?";
                                $result = $db->query($sql, $params1);
                                $users = $result->fetchAll(PDO::FETCH_ASSOC);
                                // print_r($users);
                                if (count($users) > 0):
                                    foreach ($users as $user) :

                                        $firstLetters = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
                                        // echo $firstLetters
                                ?>
                                        <div class="avatar-upload me-4">
                                            <div class="avatar-xl bg-primary"><?= $firstLetters ?></div>
                                            <div class="avatar-overlay">
                                                <i class="fas fa-camera text-white fa-2x"></i>
                                            </div>
                                            <input type="file" id="avatarInput" accept="image/*">
                                        </div>
                                        <div>
                                            <h1 class="h2 mb-1"><?= ucwords($user['first_name']) . " " . $user['last_name'];  ?></h1>
                                            <p class="mb-2">

                                                <span class="verification-badge">
                                                    <i class="fas fa-check-circle"></i> Email Verified
                                                </span>
                                            </p>
                                            <p class="mb-0 text-light">
                                                <i class="fas fa-envelope me-1"></i> <?= $user['email'] ?>
                                                <i class="fas fa-phone ms-3 me-1"></i> <?= $user['mobile'] ?>
                                            </p>
                                        </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <span class="badge bg-primary fs-6 p-2 mb-2">Customer ID: #<?= $user['user_id'] ?></span>
                        </div>
                    </div>
                </div>


                <!-- Main Content Tabs -->
                <ul class="nav nav-tabs" id="userTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button">
                            <i class="fas fa-shield-alt me-2"></i>Security
                        </button>
                    </li>

                </ul>

                <div class="tab-content mt-4" id="userTabsContent">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile" role="tabpanel">
                        <form id="updateUserForm" class="needs-validation" method="POST" action="udpateUserInfo.php">
                            <!-- Personal Information -->
                            <div class="form-section">
                                <h4 class="mb-4">
                                    <i class="fas fa-id-card text-primary me-2"></i>Personal Information
                                </h4>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <input type="hidden" class="form-control" name="user_id" value="<?= $user['user_id'] ?>" required>
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name" value="<?= $user['first_name'] ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter first name.
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="last_name" value="<?= $user['last_name'] ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter last name.
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>" required>
                                            <span class="input-group-text">
                                                <i class="fas fa-check-circle text-success"></i>
                                            </span>
                                        </div>
                                        <div class="invalid-feedback">
                                            Please enter a valid email address.
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mobile Number</label>
                                        <input type="tel" class="form-control" name="mobile" value="<?= $user['mobile'] ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">User Role <span class="text-danger">*</span></label>
                                        <select class="form-select" name="role" required>
                                            <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : ''; ?>>Administrator</option>
                                            <option value="user" <?= ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>

                                        </select>
                                        <div class="form-text">
                                            Administrator has full access to the system
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between align-items-center pt-4 border-top">

                                <div>
                                    <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                                        Cancel
                                    </button>
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
            <?php endforeach;
                                endif;    ?>
            <!-- Security Tab -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <form id="securityForm" class="needs-validation" novalidate>
                    <!-- Current Password -->
                    <div class="form-section">
                        <h4 class="mb-4">
                            <i class="fas fa-key text-primary me-2"></i>Change Password
                        </h4>

                        <div class="mb-4">
                            <label class="form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="currentPassword" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                Please enter your current password.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="newPassword" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Password must be at least 8 characters.
                                </div>

                                <div class="password-strength mt-2">
                                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                                </div>

                                <div class="password-requirements mt-2">
                                    <p class="mb-2"><small>Password must contain:</small></p>
                                    <ul class="list-unstyled mb-0">
                                        <li id="reqLength">At least 8 characters</li>
                                        <li id="reqUppercase">One uppercase letter</li>
                                        <li id="reqLowercase">One lowercase letter</li>
                                        <li id="reqNumber">One number</li>
                                        <li id="reqSpecial">One special character</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Passwords do not match.
                                </div>
                                <div id="passwordMatch" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            After changing your password, you will be logged out of all devices except this one.
                        </div>
                    </div>

                    <!-- Two-Factor Authentication -->
                    <div class="form-section">
                        <h4 class="mb-4">
                            <i class="fas fa-shield-alt text-primary me-2"></i>Two-Factor Authentication
                        </h4>

                        <div class="alert alert-warning">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>2FA is currently disabled</strong>
                                    <p class="mb-0 mt-1">Add an extra layer of security to your account</p>
                                </div>
                                <button type="button" class="btn btn-primary" id="enable2FABtn">
                                    Enable 2FA
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                                        <h5>SMS Authentication</h5>
                                        <p class="text-muted">Receive codes via SMS</p>
                                        <button type="button" class="btn btn-outline-primary btn-sm">Setup</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-qrcode fa-3x text-primary mb-3"></i>
                                        <h5>Authenticator App</h5>
                                        <p class="text-muted">Use Google Authenticator</p>
                                        <button type="button" class="btn btn-outline-primary btn-sm">Setup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Questions -->
                    <div class="form-section">
                        <h4 class="mb-4">
                            <i class="fas fa-question-circle text-primary me-2"></i>Security Questions
                        </h4>

                        <div class="mb-3">
                            <label class="form-label">Security Question 1</label>
                            <select class="form-select mb-2">
                                <option selected>What is your mother's maiden name?</option>
                                <option>What was your first pet's name?</option>
                                <option>What elementary school did you attend?</option>
                            </select>
                            <input type="text" class="form-control" placeholder="Your answer">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Security Question 2</label>
                            <select class="form-select mb-2">
                                <option selected>What city were you born in?</option>
                                <option>What is your favorite book?</option>
                                <option>What was your first car?</option>
                            </select>
                            <input type="text" class="form-control" placeholder="Your answer">
                        </div>
                    </div>

                    <!-- Active Sessions -->
                    <div class="form-section">
                        <h4 class="mb-4">
                            <i class="fas fa-laptop text-primary me-2"></i>Active Sessions
                        </h4>

                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-desktop text-primary me-2"></i>
                                    <strong>Windows 10 • Chrome</strong>
                                    <small class="d-block text-muted">New York, USA • Current Session</small>
                                    <small class="text-muted">Last active: Just now</small>
                                </div>
                                <span class="badge bg-success">Active</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-mobile-alt text-primary me-2"></i>
                                    <strong>iPhone 13 • Safari</strong>
                                    <small class="d-block text-muted">Los Angeles, USA</small>
                                    <small class="text-muted">Last active: 2 hours ago</small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger">Revoke</button>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-tablet-alt text-primary me-2"></i>
                                    <strong>iPad Air • Safari</strong>
                                    <small class="d-block text-muted">Chicago, USA</small>
                                    <small class="text-muted">Last active: 1 day ago</small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger">Revoke</button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-danger" id="revokeAllSessionsBtn">
                                <i class="fas fa-sign-out-alt me-2"></i>Revoke All Other Sessions
                            </button>
                        </div>
                    </div>

                    <!-- Security Actions -->
                    <div class="d-flex justify-content-end pt-4 border-top">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Security Settings
                        </button>
                    </div>
                </form>
            </div>

                </div>
            </main>
        </div>
    </div>



    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Admin JS -->
    <script src="assets/js/admin.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form Validation
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            // Avatar Upload
            document.getElementById('avatarInput').addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    const avatar = document.querySelector('.avatar-xl');

                    reader.onload = function(e) {
                        avatar.style.backgroundImage = `url(${e.target.result})`;
                        avatar.style.backgroundSize = 'cover';
                        avatar.style.backgroundPosition = 'center';
                        avatar.textContent = '';
                    }

                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Password Toggle
            const toggleButtons = {
                currentPassword: document.getElementById('toggleCurrentPassword'),
                newPassword: document.getElementById('toggleNewPassword'),
                confirmPassword: document.getElementById('toggleConfirmPassword')
            };

            Object.keys(toggleButtons).forEach(key => {
                toggleButtons[key].addEventListener('click', function() {
                    const input = document.getElementById(key);
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            // Password Strength Checker
            const newPasswordInput = document.getElementById('newPassword');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const strengthBar = document.getElementById('passwordStrengthBar');
            const requirements = {
                length: document.getElementById('reqLength'),
                uppercase: document.getElementById('reqUppercase'),
                lowercase: document.getElementById('reqLowercase'),
                number: document.getElementById('reqNumber'),
                special: document.getElementById('reqSpecial')
            };

            newPasswordInput.addEventListener('input', function() {
                const password = this.value;

                // Check requirements
                const hasLength = password.length >= 8;
                const hasUppercase = /[A-Z]/.test(password);
                const hasLowercase = /[a-z]/.test(password);
                const hasNumber = /[0-9]/.test(password);
                const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

                // Update requirement indicators
                updateRequirement(requirements.length, hasLength);
                updateRequirement(requirements.uppercase, hasUppercase);
                updateRequirement(requirements.lowercase, hasLowercase);
                updateRequirement(requirements.number, hasNumber);
                updateRequirement(requirements.special, hasSpecial);

                // Calculate strength
                let strength = 0;
                if (hasLength) strength += 20;
                if (hasUppercase) strength += 20;
                if (hasLowercase) strength += 20;
                if (hasNumber) strength += 20;
                if (hasSpecial) strength += 20;

                // Update strength bar
                strengthBar.style.width = `${strength}%`;

                // Update color
                if (strength < 40) {
                    strengthBar.style.backgroundColor = '#dc3545';
                } else if (strength < 80) {
                    strengthBar.style.backgroundColor = '#ffc107';
                } else {
                    strengthBar.style.backgroundColor = '#28a745';
                }

                // Check password match
                checkPasswordMatch();
            });

            confirmPasswordInput.addEventListener('input', checkPasswordMatch);

            function checkPasswordMatch() {
                const newPass = newPasswordInput.value;
                const confirmPass = confirmPasswordInput.value;
                const matchDiv = document.getElementById('passwordMatch');

                if (confirmPass === '') {
                    matchDiv.innerHTML = '';
                    return;
                }

                if (newPass === confirmPass) {
                    matchDiv.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Passwords match</span>';
                    confirmPasswordInput.setCustomValidity('');
                } else {
                    matchDiv.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Passwords do not match</span>';
                    confirmPasswordInput.setCustomValidity('Passwords do not match');
                }
            }

            function updateRequirement(element, isValid) {
                if (isValid) {
                    element.classList.add('valid');
                    element.classList.remove('text-muted');
                } else {
                    element.classList.remove('valid');
                    element.classList.add('text-muted');
                }
            }

            // Delete Account
            document.getElementById('deleteAccountBtn').addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
                modal.show();
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                const confirmInput = document.getElementById('deleteConfirm');
                if (confirmInput.value === 'DELETE') {
                    // In real application, send delete request to server
                    alert('Account deletion request sent. Redirecting to users page...');
                    window.location.href = 'users.php';
                } else {
                    alert('Please type "DELETE" to confirm deletion.');
                }
            });

            // Enable 2FA
            document.getElementById('enable2FABtn').addEventListener('click', function() {
                // In real application, this would show 2FA setup modal
                alert('2FA setup would open here. This is a demo.');
            });

            // Revoke All Sessions
            document.getElementById('revokeAllSessionsBtn').addEventListener('click', function() {
                if (confirm('Are you sure you want to revoke all other sessions? You will stay logged in on this device only.')) {
                    // In real application, send request to server
                    alert('All other sessions have been revoked.');
                }
            });

            // Charts
            const orderCtx = document.getElementById('orderChart').getContext('2d');
            const spendingCtx = document.getElementById('spendingChart').getContext('2d');

            new Chart(orderCtx, {
                type: 'line',
                data: {
                    labels: ['Nov', 'Dec'],
                    datasets: [{
                        label: 'Orders',
                        data: [8, 12],
                        borderColor: '#D10024',
                        backgroundColor: 'rgba(209, 0, 36, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 2
                            }
                        }
                    }
                }
            });

            new Chart(spendingCtx, {
                type: 'bar',
                data: {
                    labels: ['Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Spending ($)',
                        data: [3450, 5120, 12450],
                        backgroundColor: '#2B2D42',
                        borderColor: '#15161D',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            });

            // Tab Persistence
            const userTabs = document.getElementById('userTabs');
            userTabs.addEventListener('shown.bs.tab', function(event) {
                localStorage.setItem('lastUserTab', event.target.id);
            });

            // Restore last active tab
            const lastTab = localStorage.getItem('lastUserTab');
            if (lastTab) {
                const tab = new bootstrap.Tab(document.querySelector('#' + lastTab));
                tab.show();
            }
        });
    </script>
</body>

</html>