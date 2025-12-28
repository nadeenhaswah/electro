<?php
$pageTitle = "Payment Invoice - Electro Electronics";
require_once 'includes/header.php';
require_once 'includes/access_control.php';

// Require user access (regular users or admins can access)
requireUserAccess();

if (!isset($_GET['order_id'])) {
    header('Location: orders.php');
    exit();
}

require_once 'controllers/PaymentController.php';
require_once 'controllers/OrderController.php';

$paymentController = new PaymentController();
$orderController = new OrderController();

$data = $paymentController->invoice((int)$_GET['order_id']);
if (isset($data['error'])) {
    echo '<div class="container my-5"><div class="alert alert-danger">' . $data['error'] . '</div></div>';
    require_once 'includes/footer.php';
    exit();
}

$order = $data['order'];
$payment = $data['payment'];
?>

<div class="container my-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0"><i class="fas fa-check-circle"></i> Payment Successful</h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Order Information</h5>
                    <p><strong>Order ID:</strong> #<?php echo $order['order_id']; ?></p>
                    <p><strong>Order Date:</strong> <?php echo date('F d, Y H:i', strtotime($order['created_at'])); ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-info"><?php echo ucfirst($order['status']); ?></span></p>
                </div>
                <div class="col-md-6">
                    <h5>Customer Information</h5>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                </div>
            </div>

            <hr>

            <h5>Order Items</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // On invoice page, show review button for all items since payment was successful
                    // This is the invoice page, so payment was already completed
                    $orderItems = isset($order['items']) ? $order['items'] : [];
                    foreach ($orderItems as $item): 
                    ?>
                        <tr>
                            <td>
                                <div>
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                </div>
                                <div class="mt-2">
                                    <a href="review.php?item_id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-star"></i> Add Review
                                    </a>
                                </div>
                            </td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th>$<?php echo number_format($order['total_price'], 2); ?></th>
                    </tr>
                </tfoot>
            </table>

            <?php if ($payment): ?>
                <hr>
                <h5>Payment Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Payment Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></p>
                        <p><strong>Card Number:</strong> <?php echo $paymentController->maskCardNumber($payment['card_number']); ?></p>
                        <p><strong>Cardholder:</strong> <?php echo htmlspecialchars($payment['cardholder_name']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Payment Status:</strong> <span class="badge bg-success"><?php echo ucfirst($payment['payment_status']); ?></span></p>
                        <p><strong>Payment Date:</strong> <?php echo date('F d, Y H:i', strtotime($payment['payment_date'])); ?></p>
                        <?php if ($payment['billing_address']): ?>
                            <p><strong>Billing Address:</strong> <?php echo htmlspecialchars($payment['billing_address']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <a href="orders.php" class="btn btn-primary">View All Orders</a>
                <button onclick="window.print()" class="btn btn-secondary">Print Invoice</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

