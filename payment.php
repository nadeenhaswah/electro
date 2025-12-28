<?php
$pageTitle = "Payment - Electro Electronics";
require_once 'includes/header.php';
require_once 'includes/access_control.php';

// Require login - but prevent admins from placing orders
requireLogin();

// Admin cannot place orders (read-only access for orders)
if (isAdmin()) {
    setFlashMessage('error', 'Admins cannot place orders. Please use a regular user account.');
    header('Location: admin/orders/');
    exit();
}

if (!isset($_GET['order_id'])) {
    header('Location: orders.php');
    exit();
}

require_once 'controllers/PaymentController.php';
require_once 'controllers/OrderController.php';

$paymentController = new PaymentController();
$orderController = new OrderController();

// Handle payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $paymentController->process();
    if ($result['success']) {
        header('Location: invoice.php?order_id=' . $_GET['order_id']);
        exit();
    } else {
        setFlashMessage('error', $result['message']);
    }
}

$orderData = $orderController->show((int)$_GET['order_id']);
if (isset($orderData['error'])) {
    echo '<div class="container my-5"><div class="alert alert-danger">' . $orderData['error'] . '</div></div>';
    require_once 'includes/footer.php';
    exit();
}

$order = $orderData['order'];

// Display flash messages
$errorMsg = getFlashMessage('error');
?>

<div class="container my-5">
    <h2 class="mb-4">Payment Information</h2>

    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Order Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Order ID:</strong> #<?php echo $order['order_id']; ?></p>
                    <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Payment Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="payment.php?order_id=<?php echo $order['order_id']; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Cardholder Name *</label>
                            <input type="text" name="cardholder_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Card Number *</label>
                            <input type="text" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" required maxlength="19">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expiry Date *</label>
                                <input type="text" name="expiry_date" class="form-control" placeholder="MM/YYYY" required maxlength="7">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CVV *</label>
                                <input type="text" name="cvv" class="form-control" placeholder="123" required maxlength="4">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Billing Address</label>
                            <textarea name="billing_address" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="credit_card" selected>Credit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> This is a fake payment system. No real payment will be processed.
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">Complete Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

