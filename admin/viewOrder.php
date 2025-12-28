<?php
require_once 'config/database.php';
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
    <title>Order Details - Electro Admin</title>

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


    <style>
        .order-timeline {
            position: relative;
            padding-left: 30px;
        }

        .order-timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: var(--grey-light);
        }

        .timeline-step {
            position: relative;
            margin-bottom: 25px;
        }

        .timeline-step::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: var(--grey-light);
            border: 3px solid white;
            box-shadow: 0 0 0 3px var(--grey-light);
        }

        .timeline-step.active::before {
            background-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(209, 0, 36, 0.2);
        }

        .timeline-step.completed::before {
            background-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(209, 0, 36, 0.2);
        }

        .order-status-badge {
            padding: 8px 16px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .product-img-sm {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .invoice-header {
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .payment-card {
            background: linear-gradient(135deg, #2B2D42, #15161D);
            color: white;
            border-radius: 10px;
            padding: 20px;
        }

        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(209, 0, 36, 0.3);
        }
    </style>
</head>

<body>
    <?php
    include('includes/header.php');
    ?>

    <div class="container-fluid">
        <div class="row">
            <?php
            include('includes/sidebar.php');
            ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-5">
                <!-- Order Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <?php
                        $id = $_GET['id'];
                        $params[] = $id;

                        $sql1 = "SELECT * FROM payments WHERE order_id = ?";
                        $result1 = $db->query($sql1, $params);
                        $rows1 =  $result1->fetchAll(PDO::FETCH_ASSOC);
                        if (count($rows1) > 0):
                            foreach ($rows1 as $row) :
                                $paymentDate = $row['payment_date'];
                                $paymentStatus = $row['payment_status'];
                            endforeach;
                        endif;
                        ?>

                        <?php
                        $sql = "SELECT * FROM orders WHERE order_id  = ?";
                        $result = $db->query($sql, $params);
                        $rows =  $result->fetchAll(PDO::FETCH_ASSOC);
                        // print_r($rows);
                        if (count($rows) > 0):
                            foreach ($rows as $row) :
                        ?>

                                <h1 class="h2">Order #<?= $id; ?></h1>
                                <p class="text-muted mb-0">Placed on <?= $row['created_at'] ?></p>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">

                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-id="<?= $row['order_id'] ?>" data-bs-target="#updateStatusModal">
                            <i class="fas fa-edit"></i> Update Status
                        </button>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column: Order Items & Timeline -->
                    <div class="col-lg-8">
                        <!-- Order Timeline -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Order Status Timeline</h5>
                            </div>
                            <div class="card-body">
                                <div class="order-timeline">
                                    <div class="timeline-step completed">
                                        <h6 class="mb-1">Order Placed</h6>
                                        <p class="text-muted mb-0"><?= $row['created_at'] ?></p>
                                    </div>

                                    <div class="timeline-step completed">
                                        <h6 class="mb-1">Payment Confirmed</h6>
                                        <p class="text-muted mb-0"><?= $paymentDate ?></p>
                                        <small class="text-success"><?= $paymentStatus  ?></small>
                                    </div>

                                    <div class="timeline-step active">
                                        <h6 class="mb-1">Processing</h6>
                                        <p class="text-muted mb-0">Current Status</p>
                                        <small class="text-success"><?= $row['status'] ?></small>
                                    </div>


                            <?php endforeach;
                        endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        $sql = "SELECT * FROM orders WHERE order_id  = ?";
                        $result = $db->query($sql, $params);
                        $rows =  $result->fetchAll(PDO::FETCH_ASSOC);
                        // print_r($rows);
                        if (count($rows) > 0):
                            foreach ($rows as $row) :
                                $total = $row['total_price'];
                            endforeach;
                        endif;
                        ?>

                        <!-- Order Items -->
                        <?php
                        $sql = "
                            SELECT 
                                i.*
                            FROM order_items oi
                            INNER JOIN items i ON oi.item_id = i.item_id
                            WHERE oi.order_id = ?
                        ";

                        $result = $db->query($sql, $params);
                        $itemdata = $result->fetchAll(PDO::FETCH_ASSOC);

                        // print_r($itemdata);
                        if (count($itemdata) > 0):
                            foreach ($itemdata as $item) :

                            endforeach;
                        endif;
                        ?>

                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">

                                <h5 class="mb-0">Order Items (<?= count($itemdata) ?>)</h5>
                                <span class="badge bg-primary">Total: JOD <?= $total ?></span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Item 1 -->


                                            <?php
                                            $sql = "
                                        SELECT 
                                            i.*,
                                            oi.quantity,
                                            oi.price AS price_at_order
                                        FROM order_items oi
                                        INNER JOIN items i ON oi.item_id = i.item_id
                                        WHERE oi.order_id = ?
                                    ";

                                            $result = $db->query($sql, $params);
                                            $itemdata = $result->fetchAll(PDO::FETCH_ASSOC);

                                            // print_r($itemdata);
                                            if (count($itemdata) > 0):
                                                foreach ($itemdata as $item) :
                                            ?>
                                                    <tr>

                                                        <td>
                                                            <h6 class="mb-1"><?= $item['name'] ?></h6>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <?php
                                                                $discount = ($item['price'] * $item['discount_value']) / 100;
                                                                $priceAfterDiscount = $item['price'] - $discount;
                                                                ?>
                                                                <span class="fw-bold">JOD <?= $priceAfterDiscount ?> </span>
                                                                <small class="text-success">
                                                                    <?= !empty($item['discount_value']) ? $item['discount_value'] . '% discount applied' : '' ?>
                                                                </small>
                                                                <small><del>JOD <?= $item['price'] ?></del></small>
                                                            </div>
                                                        </td>


                                                        <td>
                                                            <span class="badge bg-secondary"><?= $item['quantity']; ?></span>
                                                        </td>
                                                        <td>

                                                            <strong>JOD <?= $priceAfterDiscount * $item['quantity'] ?></strong>
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

                    <!-- Right Column: Customer & Payment Info -->
                    <div class="col-lg-4">
                        <!-- Customer Information -->
                        <div class="card mb-4">
                            <?php
                            $sql = "SELECT user_id FROM orders WHERE order_id  = ?";
                            $result = $db->query($sql, $params);
                            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                            // print_r($rows);
                            if (count($rows) > 0) :
                                foreach ($rows as $row) :
                                    $Id = $row['user_id'];
                                endforeach;
                            endif;

                            $getUser = "SELECT * FROM users WHERE user_id  = ?";
                            $userId[] = $Id;
                            // print_r( $userId);

                            $userInfo = $db->query($getUser, $userId);
                            $allData = $userInfo->fetchAll(PDO::FETCH_ASSOC);
                            // print_r($allData);
                            if (count($allData) > 0) :
                                foreach ($allData as $data) :
                                    $id = $data['user_id'];
                                    $name = $data['first_name'] . " " . $data['last_name'];
                                    $email = $data['email'];
                                    $phone = $data['mobile'];
                                endforeach;
                            endif;

                            ?>
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Customer Information</h5>
                                <a href="viewUser.php?id=45" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div>
                                        <h5 class="mb-0"><?= $name ?></h5>
                                        <p class="text-muted mb-0">Customer ID: #<?= $id ?></p>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-4">
                                        <small class="text-muted">Email:</small>
                                    </div>
                                    <div class="col-8">
                                        <small><?= $email ?></small>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-4">
                                        <small class="text-muted">Phone:</small>
                                    </div>
                                    <div class="col-8">
                                        <small><?= $phone ?></small>
                                    </div>
                                </div>




                            </div>
                        </div>



                        <!-- Payment Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Payment Information</h5>
                            </div>
                            <?php
                            // $id = $_GET['id'];
                            // $params[] = $id;

                            $sql1 = "SELECT * FROM payments WHERE order_id = ?";
                            $result1 = $db->query($sql1, $params);
                            $rows1 =  $result1->fetchAll(PDO::FETCH_ASSOC);
                            if (count($rows1) > 0):
                                foreach ($rows1 as $row) :
                                    $paymentDate = $row['payment_date'];
                                    $paymentStatus = $row['payment_status'];

                            ?>
                                    <div class="card-body">
                                        <div class="payment-card mb-3">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h6 class="mb-1 text-white">VISA</h6>
                                                    <small class="opacity-75"><?= $row['payment_method'] ?></small>
                                                </div>
                                                <i class="fas fa-credit-card fa-2x"></i>
                                            </div>

                                            <div class="mb-3">
                                                <small class="">Card Number</small>
                                                <h4 class="mb-0"><?= $row['card_number'] ?></h4>
                                            </div>

                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="opacity-75">Cardholder</small>
                                                    <p class="mb-0"><?= $row['cardholder_name'] ?></p>
                                                </div>
                                                <div class="col-6">
                                                    <small class="opacity-75">Expires</small>
                                                    <p class="mb-0"><?= $row['expiry_date'] ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Payment ID:</small>
                                                    <p class="mb-0">#<?= $row['payment_id'] ?></p>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Date:</small>
                                                    <p class="mb-0"><?= $row['payment_date'] ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>Payment Status:</strong> <?= $row['payment_status'] ?>
                                        </div>


                                    </div>
                            <?php
                                endforeach;
                            endif; ?>
                        </div>

                        <!-- Order Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Order Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#shipOrderModal">
                                        <i class="fas fa-shipping-fast me-1"></i> Mark as Shipped
                                    </button>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateTrackingModal">
                                        <i class="fas fa-truck me-1"></i> Update Tracking
                                    </button>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                                        <i class="fas fa-times me-1"></i> Cancel Order
                                    </button>
                                    <button class="btn btn-info btn-sm">
                                        <i class="fas fa-file-invoice me-1"></i> Generate Invoice
                                    </button>
                                    <button class="btn btn-secondary btn-sm">
                                        <i class="fas fa-redo me-1"></i> Refund/Exchange
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </main>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="editOrderstatus.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" id="order_id" name="order_id">
                            <label class="form-label">New Status</label>
                            <select class="form-select" name="status" required>
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const updateStatusModal = document.getElementById('updateStatusModal');

        updateStatusModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            document.getElementById('order_id').value = id;
        });
    </script>

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

    <!-- Payment Details Modal -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Details #PAY-7841</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><small class="text-muted">Payment ID:</small></td>
                                    <td><strong>#PAY-7841</strong></td>
                                </tr>
                                <tr>
                                    <td><small class="text-muted">Amount:</small></td>
                                    <td><strong class="text-success">$1,649.97</strong></td>
                                </tr>
                                <tr>
                                    <td><small class="text-muted">Method:</small></td>
                                    <td>Credit Card (VISA)</td>
                                </tr>
                                <tr>
                                    <td><small class="text-muted">Status:</small></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td><small class="text-muted">Transaction ID:</small></td>
                                    <td>TXN_789456123</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Billing Address</h6>
                            <address>
                                John Doe<br>
                                123 Main Street, Apt 4B<br>
                                New York, NY 10001<br>
                                United States
                            </address>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Payment was processed successfully via Stripe. Transaction fee: $49.50 (3%)
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Refund Payment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Tracking Modal -->
    <div class="modal fade" id="updateTrackingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Tracking Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Shipping Carrier</label>
                            <select class="form-select" required>
                                <option value="">Select Carrier</option>
                                <option value="fedex">FedEx</option>
                                <option value="ups">UPS</option>
                                <option value="usps">USPS</option>
                                <option value="dhl">DHL</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tracking Number</label>
                            <input type="text" class="form-control" placeholder="Enter tracking number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estimated Delivery</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sendTrackingEmail" checked>
                            <label class="form-check-label" for="sendTrackingEmail">
                                Send tracking information to customer
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Tracking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Admin JS -->
    <script src="assets/js/admin.js"></script>

    <script>
        // // Timeline interaction
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Timeline steps clickable for demo
        //     const timelineSteps = document.querySelectorAll('.timeline-step');
        //     timelineSteps.forEach(step => {
        //         step.addEventListener('click', function() {
        //             if (!this.classList.contains('active') && !this.classList.contains('completed')) {
        //                 alert('This status is not yet active. You can update the order status using the "Update Status" button.');
        //             }
        //         });
        //     });


        // });
    </script>
</body>

</html>