<?php
$pageTitle = "Order Details - Electro Electronics";
require_once 'includes/header.php';
require_once 'includes/access_control.php';

// Require user access (regular users or admins can access)
requireUserAccess();

if (!isset($_GET['id'])) {
    header('Location: orders.php');
    exit();
}

require_once 'controllers/OrderController.php';

$orderController = new OrderController();
$data = $orderController->show((int)$_GET['id']);

if (isset($data['error'])) {
    echo '<div class="container my-5"><div class="alert alert-danger">' . $data['error'] . '</div></div>';
    require_once 'includes/footer.php';
    exit();
}

$order = $data['order'];
?>

<div class="container my-5">
    <h2 class="mb-4">Order #<?php echo $order['order_id']; ?></h2>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <strong>Order Date:</strong> <?php echo date('F d, Y H:i', strtotime($order['created_at'])); ?>
                </div>
                <div class="col-md-6 text-end">
                    <strong>Status:</strong> <span class="badge bg-<?php 
                        echo $order['status'] === 'completed' ? 'success' : 
                            ($order['status'] === 'cancelled' ? 'danger' : 'warning'); 
                    ?>"><?php echo ucfirst($order['status']); ?></span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <h5>Order Items</h5>
            <table class="table">
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
                    require_once 'classes/Order.php';
                    require_once 'classes/Payment.php';
                    $orderCheck = new Order();
                    $paymentCheck = new Payment();
                    $paymentInfo = $paymentCheck->getByOrderId($order['order_id']);
                    $orderItems = isset($order['items']) ? $order['items'] : [];
                    foreach ($orderItems as $item): 
                        // Check if user can review (has purchased and payment is completed)
                        $canReview = false;
                        if ($paymentInfo && $paymentInfo['payment_status'] === 'completed') {
                            $canReview = $orderCheck->hasUserPurchasedItem($_SESSION['user_id'], $item['item_id']);
                        }
                    ?>
                        <tr>
                            <td>
                                <?php if ($item['main_image']): ?>
                                    <img src="<?php echo $item['main_image']; ?>" alt="" style="width: 50px; height: 50px; object-fit: cover;" class="me-2">
                                <?php endif; ?>
                                <?php echo htmlspecialchars($item['name']); ?>
                                <?php if ($canReview): ?>
                                    <br>
                                    <a href="review.php?item_id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="fas fa-star"></i> Add Review
                                    </a>
                                <?php endif; ?>
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

            <div class="mt-3">
                <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
                <a href="invoice.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-primary">View Invoice</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

