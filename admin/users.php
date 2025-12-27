<?php
session_start();
require_once '../config/database.php';
// session_start();
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
    <title>Manage Users - Electro Admin</title>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- Same header and sidebar structure as index.php -->

    <header class="admin-header">
        <!-- Same header as index.php -->
        <?php
        include('includes/header.php');
        ?>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar">
                <?php
                include('includes/sidebar.php');
                ?>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-5">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Users</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">

                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus"></i> Add User
                        </button>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control table-search" name="keyword" placeholder="Search users by name, email..." data-table="usersTable">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Users List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="usersTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $sql = "SELECT * FROM users";
                                    $result = $db->query($sql);
                                    $users = $result->fetchAll(PDO::FETCH_ASSOC);
                                    // print_r($users);
                                    if (count($users) > 0):
                                        foreach ($users as $user) :
                                            $roleBadge = $user['role'] == 'admin' ? 'primary' : 'secondary';
                                    ?>
                                            <tr>
                                                <td>#<?php echo $user['user_id']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo htmlspecialchars($user['mobile']); ?></td>
                                                <td><span class="badge bg-<?php echo $roleBadge; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="update_user.php?id=<?= $user['user_id']; ?>"
                                                            class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>


                                                        <button
                                                            class="btn btn-sm btn-outline-danger btn-action btn-delete"
                                                            title="Delete"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal"
                                                            data-id="<?= $user['user_id']; ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>

                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form class="needs-validation" novalidate method="POST" action="add_user.php">
                    <div class="modal-body">
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
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="number" name="mobile" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="confirmPass" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role *</label>
                            <select class="form-select" name="role" required>
                                <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : ''; ?>>Administrator</option>
                                <option value="user" <?= ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>

                                <option value="user">User</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form method="POST" action="delete_user.php">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center">
                        <input type="hidden" name="id" id="delete-id">
                        <p class="mb-0">Are you sure you want to delete this user?</p>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            Yes, Delete
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <?php

    if (isset($_SESSION['alert'])):
        $alert = $_SESSION['alert'];
    ?>
        <script>
            Swal.fire({
                icon: '<?= $alert['type']; ?>',
                title: '<?= $alert['title']; ?>',
                text: '<?= $alert['text']; ?>',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    <?php
        unset($_SESSION['alert']);
    endif;
    ?>
    <?php

    if (isset($_SESSION['alert_add'])):
        $alert = $_SESSION['alert_add'];
    ?>
        <script>
            Swal.fire({
                icon: '<?= $alert['type']; ?>',
                title: '<?= $alert['title']; ?>',
                text: '<?= $alert['text']; ?>',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    <?php
        unset($_SESSION['alert_add']);
    endif;
    ?>

    <?php

    if (isset($_SESSION['alert_update'])):
        $alert = $_SESSION['alert_update'];
    ?>
        <script>
            Swal.fire({
                icon: '<?= $alert['type']; ?>',
                title: '<?= $alert['title']; ?>',
                text: '<?= $alert['text']; ?>',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    <?php
        unset($_SESSION['alert_update']);
    endif;
    ?>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const deleteModal = document.getElementById('deleteModal');

        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            document.getElementById('delete-id').value = id;
        });
    </script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Admin JS -->
    <script src="assets/js/admin.js"></script>
</body>

</html>