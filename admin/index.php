<?php
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
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="notificationsDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="badge bg-danger">3</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header">Notifications</h6>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-shopping-cart text-primary"></i> 5 new orders
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-comment text-success"></i> 12 new comments
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user text-warning"></i> 3 new users
                            </a>
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <div class="avatar me-2">A</div>
                            <span>Admin User</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a>
                            <a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                        </div>
                    </div>
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
                    </ul>

                    <div class="sidebar-footer mt-4">
                        <div class="card bg-dark text-white">
                            <div class="card-body">
                                <h6><i class="fas fa-chart-line me-2"></i>Quick Stats</h6>
                                <small class="text-muted">Today: $4,250</small><br>
                                <small class="text-muted">Orders: 42</small>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-5">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Admin Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Total Users</h6>
                                        <h3>1,254</h3>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-users text-primary"></i>
                                    </div>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-primary" style="width: 75%"></div>
                                </div>
                                <small class="text-muted">+12% from last month</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Total Products</h6>
                                        <h3>568</h3>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-box text-success"></i>
                                    </div>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-success" style="width: 60%"></div>
                                </div>
                                <small class="text-muted">+8% from last month</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Total Orders</h6>
                                        <h3>2,845</h3>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-shopping-cart text-warning"></i>
                                    </div>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-warning" style="width: 85%"></div>
                                </div>
                                <small class="text-muted">+25% from last month</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Revenue</h6>
                                        <h3>$45,250</h3>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-dollar-sign text-danger"></i>
                                    </div>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-danger" style="width: 90%"></div>
                                </div>
                                <small class="text-muted">+18% from last month</small>
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