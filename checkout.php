<?php
$pageTitle = "Checkout - Electro Electronics";
require_once 'includes/access_control.php';

// Require login - but prevent admins from placing orders
requireLogin();

// Admin cannot place orders (read-only access for orders)
if (isAdmin()) {
    setFlashMessage('error', 'Admins cannot place orders. Please use a regular user account.');
    header('Location: admin/orders/');
    exit();
}

require_once 'controllers/CartController.php';
require_once 'controllers/OrderController.php';

$cartController = new CartController();
$orderController = new OrderController();

// Handle order creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_order') {
    $result = $orderController->create();
    if ($result['success']) {
        header('Location: payment.php?order_id=' . $result['order_id']);
        exit();
    } else {
        setFlashMessage('error', $result['message']);
    }
}

$cartData = $cartController->index();
$items = $cartData['items'];
$total = $cartData['total'];

if (empty($items)) {
    header('Location: cart.php');
    exit();
}

// Display flash messages
$errorMsg = getFlashMessage('error');
?>
<?php require_once 'includes/header.php';
?>
<div class="container my-5">
    <h2 class="mb-4">Checkout</h2>

    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Order Summary</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo htmlspecialchars($item['product']['name']); ?> x <?php echo $item['quantity']; ?></span>
                            <span>$<?php echo number_format($item['price_at_time'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong class="text-primary">$<?php echo number_format($total, 2); ?></strong>
                    </div>
                </div>
            </div>

            <form method="POST" action="checkout.php">
                <input type="hidden" name="action" value="create_order">
                <button type="submit" class="btn btn-primary btn-lg">Proceed to Payment</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>