<?php
$pageTitle = "My Orders - Electro Electronics";
require_once 'includes/header.php';
require_once 'includes/access_control.php';

// Require user access (regular users or admins can access)
requireUserAccess();

require_once 'controllers/OrderController.php';

$orderController = new OrderController();
$data = $orderController->index();
$orders = $data['orders'] ?? [];
?>

<div class="container my-5">
    <h2 class="mb-4">My Orders</h2>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            <h4>No orders yet</h4>
            <p>Start shopping to see your orders here.</p>
            <a href="products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td><?php echo count($order['items'] ?? []); ?> item(s)</td>
                            <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $order['status'] === 'completed' ? 'success' : 
                                        ($order['status'] === 'cancelled' ? 'danger' : 'warning'); 
                                ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="order.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                <?php if ($order['status'] !== 'cancelled'): ?>
                                    <a href="invoice.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-secondary">Invoice</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

