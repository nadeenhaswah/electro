<?php
$pageTitle = "Shopping Cart - Electro Electronics";
require_once 'includes/header.php';

require_once 'controllers/CartController.php';

$cartController = new CartController();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $result = $cartController->add();
        setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
        header('Location: cart.php');
        exit();
    } elseif ($action === 'update') {
        $result = $cartController->update();
        setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
        header('Location: cart.php');
        exit();
    } elseif ($action === 'remove') {
        $result = $cartController->remove();
        setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
        header('Location: cart.php');
        exit();
    }
}

$data = $cartController->index();
$items = $data['items'] ?? [];
$total = $data['total'] ?? 0;
$cart_id = $data['cart_id'] ?? null;

// Display flash messages
$successMsg = getFlashMessage('success');
$errorMsg = getFlashMessage('error');
?>

<div class="container my-5">
    <h2 class="mb-4">Shopping Cart</h2>

    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <div class="alert alert-info">
            <h4>Your cart is empty</h4>
            <p>Start shopping to add items to your cart.</p>
            <a href="products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <?php foreach ($items as $item): ?>
                    <div class="cart-item">
                        <div class="row">
                            <div class="col-md-2">
                                <?php if (isset($item['product']['main_image']) && $item['product']['main_image']): ?>
                                    <img src="<?php echo $item['product']['main_image']; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" style="width: 100px; height: 100px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/100?text=No+Image" class="img-fluid" alt="No Image" style="width: 100px; height: 100px; object-fit: cover;">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h5><?php echo htmlspecialchars($item['product']['name'] ?? 'Product'); ?></h5>
                                <?php if (!empty($item['product']['description'])): ?>
                                    <p class="text-muted small"><?php echo htmlspecialchars(substr($item['product']['description'], 0, 100)); ?>...</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-2">
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" name="quantity" class="form-control quantity-input" value="<?php echo $item['quantity']; ?>" min="1" onchange="this.form.submit()">
                                </form>
                            </div>
                            <div class="col-md-2 text-end">
                                <p class="fw-bold">$<?php echo number_format($item['price_at_time'] * $item['quantity'], 2); ?></p>
                                <form method="POST" action="cart.php" class="d-inline">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove this item?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span class="fw-bold">$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-primary">$<?php echo number_format($total, 2); ?></strong>
                        </div>
                        <?php if (isLoggedIn()): ?>
                            <?php if (!isAdmin()): ?>
                                <a href="checkout.php" class="btn btn-primary w-100">Proceed to Checkout</a>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Admins cannot place orders. This cart is for viewing only.
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <a href="auth/login.php">Login</a> to checkout
                            </div>
                        <?php endif; ?>
                        <a href="products.php" class="btn btn-outline-secondary w-100 mt-2">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

