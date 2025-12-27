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
                                    <div class="progress-bar bg-danger" style="width: 30%"></div>
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
                                            <?php
                                            $sql = "SELECT 
                                                o.order_id,
                                                CONCAT(u.first_name, ' ', u.last_name) AS customer,
                                                o.created_at AS order_date,
                                                o.total_price AS amount,
                                                o.status
                                            FROM orders o
                                            INNER JOIN users u ON o.user_id = u.user_id
                                            ORDER BY o.created_at DESC";
                                            $result = $db->query($sql);
                                            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                                            if (count($rows) > 0):
                                                foreach ($rows as $row): ?>

                                                    <tr>
                                                        <td><?= $row['order_id'] ?></td>
                                                        <td><?= $row['customer'] ?></td>
                                                        <td><?= $row['order_date'] ?></td>
                                                        <td><?= $row['amount'] ?></td>
                                                        <?php
                                                        if ($row['status'] == 'processing') {
                                                            $statusBadge = 'primary';
                                                        } elseif ($row['status'] == 'completed') {
                                                            $statusBadge = 'success';
                                                        } elseif ($row['status'] == 'cancelled') {
                                                            $statusBadge = 'danger';
                                                        } else {
                                                            $statusBadge = 'secondary';
                                                        }
                                                        ?>

                                                        <td><span class="badge bg-<?= $statusBadge;  ?>"><?= $row['status'] ?></span></td>
                                                        <td>
                                                            <a href="viewOrder.php?id=<?= $row['order_id']; ?>" class="btn btn-sm btn-primary">
                                                                View
                                                            </a>

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
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Top Products</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php
                                    $sql = "SELECT 
                                        i.item_id,
                                        i.name,
                                        SUM(oi.quantity) AS total_sales
                                    FROM order_items oi
                                    INNER JOIN items i ON oi.item_id = i.item_id
                                    INNER JOIN orders o ON oi.order_id = o.order_id
                                    WHERE o.status IN ('completed', 'shipped')
                                    GROUP BY i.item_id, i.name
                                    ORDER BY total_sales DESC
                                    LIMIT 5;
                                    ";
                                    $result = $db->query($sql);
                                    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                                    if (count($rows) > 0):
                                        foreach ($rows as $row): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?= htmlspecialchars($row['name']); ?>
                                                <span class="badge bg-primary rounded-pill"> <?= $row['total_sales']; ?> sales</span>
                                            </li>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>



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