<?php
$pageTitle = "Wishlist - Electro Electronics";
require_once 'includes/access_control.php';

// Require user access (regular users or admins can access)
requireUserAccess();

require_once 'controllers/WishlistController.php';

$wishlistController = new WishlistController();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $result = $wishlistController->add();
        setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
        header('Location:' . ($_SERVER['HTTP_REFERER'] ?? 'wishlist.php'));
        exit();
    } elseif ($action === 'remove') {
        $result = $wishlistController->remove();
        setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
        // header('Location: wishlist.php');
        exit();
    }
}

$data = $wishlistController->index();
$items = $data['items'] ?? [];

// Display flash messages
$successMsg = getFlashMessage('success');
$errorMsg = getFlashMessage('error');
?>
<?php require_once 'includes/header.php';
?>
<div class="container my-5">
    <h2 class="mb-4">My Wishlist</h2>

    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <div class="alert alert-info">
            <h4>Your wishlist is empty</h4>
            <p>Start adding products to your wishlist.</p>
            <a href="products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($items as $item): ?>
                <div class="col-md-3 mb-4">
                    <div class="card product-card">
                        <?php if ($item['main_image']): ?>
                            <img src="<?php echo $item['main_image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>" style="height: 250px; object-fit: cover;">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x250?text=No+Image" class="card-img-top" alt="No Image" style="height: 250px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="product-price">$<?php echo number_format($item['final_price'], 2); ?></p>
                            <div class="d-grid gap-2">
                                <a href="product.php?id=<?php echo $item['item_id']; ?>" class="btn btn-primary btn-sm">View</a>
                                <form method="POST" action="wishlist.php">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        <i class="fas fa-heart-broken"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>