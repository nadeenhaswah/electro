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
    <title>Admin Dashboard - Electro</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Montserrat Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Admin CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="assets/css/admin-style.css">
</head>

<body>
    <!-- Header -->
    <?php
    include('includes/header.php');
    ?>
    <!-- Main Container -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php
            include('includes/sidebar.php');
            ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-5">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Categories</h1>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCategory">
                        <i class="fas fa-plus"></i> Add Category
                    </button>
                </div>

                <!-- Categories Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Categories List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Discount Value</th>
                                        <th>Discount Start</th>
                                        <th>Discount End</th>
                                        <th>Visibility</th>
                                        <th>Comments</th>
                                        <th>Products</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM categories";
                                    $result = $db->query($sql);
                                    $categories = $result->fetchAll(PDO::FETCH_ASSOC);
                                    // print_r($users);
                                    if (count($categories) > 0):
                                        foreach ($categories as $category) :

                                    ?>

                                            <tr>
                                                <td>#<?= $category['id'] ?></td>
                                                <td><strong></strong><?= $category['name'] ?></td>
                                                <td><?= $category['description'] ?></td>
                                                <td>
                                                    <?= !empty($category['discount_value']) ? $category['discount_value'] . '%' : 'No Discount'; ?>
                                                </td>
                                                <td>
                                                    <?= !empty($category['discount_start']) ? $category['discount_start'] . '' : 'No Discount'; ?>
                                                </td>
                                                <td>
                                                    <?= !empty($category['discount_end']) ? $category['discount_end'] . '' : 'No Discount'; ?>
                                                </td>

                                                <td>
                                                    <span class="badge <?= $category['visibility'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?= $category['visibility'] ? 'Visible' : 'Hidden'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $category['allow_comments'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?= $category['allow_comments'] ? 'Allowed' : 'Not Allowed'; ?>
                                                    </span>
                                                </td>
                                                <td>42</td>
                                                <td>
                                                    <a href="update_category.php?id=<?= $category['id']; ?>"
                                                        class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>


                                                    <button
                                                        class="btn btn-sm btn-outline-danger btn-action btn-delete"
                                                        title="Delete"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal"
                                                        data-id="<?= $category['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>


                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </main>

            <!-- Add / Edit Category Modal -->
            <div class="modal fade" id="addCategory" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white"><?= isset($category) ? 'Edit Category' : 'Add New Category'; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form class="needs-validation" novalidate method="POST" action="add_category.php">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Category Name *</label>
                                    <input type="text" name="name" class="form-control" required value="<?= $category['name'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control"><?= $category['description'] ?? ''; ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Discount Value (%)</label>
                                        <input type="number" step="0.01" name="discount_value" class="form-control" value="<?= $category['discount_value'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Discount Start</label>
                                        <input type="datetime-local" name="discount_start" class="form-control" value="<?= isset($category['discount_start']) ? date('Y-m-d\TH:i', strtotime($category['discount_start'])) : ''; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Discount End</label>
                                        <input type="datetime-local" name="discount_end" class="form-control" value="<?= isset($category['discount_end']) ? date('Y-m-d\TH:i', strtotime($category['discount_end'])) : ''; ?>">
                                    </div>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="visibility" value="1" <?= isset($category['visibility']) && $category['visibility'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label">Visible</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="allow_comments" value="1" <?= isset($category['allow_comments']) && $category['allow_comments'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label">Allow Comments</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="id" value="<?= $category['id'] ?? ''; ?>">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="submit" class="btn btn-primary"><?= isset($category) ? 'Update Category' : 'Add Category'; ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <form method="POST" action="delete_category.php">

                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Confirm Delete</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body text-center">
                                <input type="hidden" name="id" id="delete-id">
                                <p class="mb-0">Are you sure you want to delete this Category?</p>
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



</body>

</html>