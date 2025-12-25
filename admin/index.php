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
    <link rel="stylesheet" href="assets/css/admin-style.css">
</head>

<body>
    <!-- Header -->
    <header class="admin-header">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-cogs me-2"></i>Electro<span>Admin</span>
                </a>

                <div class="d-flex align-items-center">



                </div>
            </div>
        </nav>
    </header>

    <!-- Main Container -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users me-2"></i>
                                Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="categories.php">
                                <i class="fas fa-list-alt me-2"></i>
                                Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="items.php">
                                <i class="fas fa-box me-2"></i>
                                Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="item_images.php">
                                <i class="fas fa-images me-2"></i>
                                Product Images
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="comments.php">
                                <i class="fas fa-comments me-2"></i>
                                Comments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="payments.php">
                                <i class="fas fa-credit-card me-2"></i>
                                Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="carts.php">
                                <i class="fas fa-shopping-bag me-2"></i>
                                Carts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="wishlists.php">
                                <i class="fas fa-heart me-2"></i>
                                Wishlists
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <i class="fas fa-cog me-2"></i>
                                Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../login.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>


                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-5">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Admin Dashboard</h1>

                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <?php
                                $sql = "SELECT COUNT(*) AS total_rows FROM users ";
                                $result = $db->query($sql);
                                $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                                if (count($rows) > 0):
                                    foreach ($rows as $row): ?>

                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="text-muted">Total Users</h6>
                                                <h3><?= $row['total_rows'] ?></h3>
                                            </div>
                                    <?php
                                    endforeach;
                                endif;
                                    ?>
                                    <div class="stat-icon">
                                        <i class="fas fa-users text-primary"></i>
                                    </div>
                                        </div>
                                        <div class="progress mt-2">
                                            <div class="progress-bar bg-primary" style="width: 20%"></div>
                                        </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <?php
                                        $sql = "SELECT COUNT(*) AS total_rows FROM items ";
                                        $result = $db->query($sql);
                                        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                                        if (count($rows) > 0):
                                            foreach ($rows as $row): ?>
                                                <h6 class="text-muted">Total Products</h6>
                                                <h3><?= $row['total_rows'] ?></h3>
                                    </div>
                            <?php
                                            endforeach;
                                        endif;
                            ?>
                            <div class="stat-icon">
                                <i class="fas fa-box text-success"></i>
                            </div>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-success" style="width: 10%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <?php
                                        $sql = "SELECT COUNT(*) AS total_rows FROM  orders ";
                                        $result = $db->query($sql);
                                        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                                        if (count($rows) > 0):
                                            foreach ($rows as $row): ?>
                                                <h6 class="text-muted">Total Orders</h6>
                                                <h3><?= $row['total_rows'] ?></h3>
                                    </div>
                            <?php
                                            endforeach;
                                        endif;
                            ?>
                            <div class="stat-icon">
                                <i class="fas fa-shopping-cart text-warning"></i>
                            </div>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-warning" style="width: 4%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <?php
                                        $sql = "SELECT SUM(total_price) AS total_prices FROM orders ";
                                        $result = $db->query($sql);
                                        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                                        if (count($rows) > 0):
                                            foreach ($rows as $row): ?>
                                                <h6 class="text-muted">Revenue</h6>
                                                <h3><?= $row['total_prices'] ?></h3>
                                    </div>
                            <?php
                                            endforeach;
                                        endif;
                            ?>
                            <div class="stat-icon">
                                <i class="fas fa-dollar-sign text-danger"></i>
                            </div>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-danger" style="width: 50%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Recent Orders</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>#ORD-7841</td>
                                                <td>John Doe</td>
                                                <td>2023-12-24</td>
                                                <td>$249.99</td>
                                                <td><span class="badge bg-warning">Processing</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>#ORD-7840</td>
                                                <td>Jane Smith</td>
                                                <td>2023-12-24</td>
                                                <td>$599.99</td>
                                                <td><span class="badge bg-success">Completed</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>#ORD-7839</td>
                                                <td>Robert Johnson</td>
                                                <td>2023-12-23</td>
                                                <td>$129.99</td>
                                                <td><span class="badge bg-primary">Shipped</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>#ORD-7838</td>
                                                <td>Emily Davis</td>
                                                <td>2023-12-23</td>
                                                <td>$899.99</td>
                                                <td><span class="badge bg-danger">Cancelled</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Top Products</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        MacBook Pro 16"
                                        <span class="badge bg-primary rounded-pill">42 sales</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        iPhone 15 Pro Max
                                        <span class="badge bg-primary rounded-pill">38 sales</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Sony WH-1000XM5
                                        <span class="badge bg-primary rounded-pill">31 sales</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Samsung Odyssey G9
                                        <span class="badge bg-primary rounded-pill">24 sales</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Canon EOS R5
                                        <span class="badge bg-primary rounded-pill">18 sales</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Admin JS -->
    <script src="assets/js/admin.js"></script>
</body>

</html>