<?php
$pageTitle = "Category - Electro Electronics";
require_once 'includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit();
}

require_once 'controllers/CategoryController.php';

$categoryController = new CategoryController();
$data = $categoryController->show((int)$_GET['id']);

if (isset($data['error'])) {
    echo '<div class="container my-5"><div class="alert alert-danger">' . $data['error'] . '</div></div>';
    require_once 'includes/footer.php';
    exit();
}

$category = $data['category'];
$products = $data['products'];
$page = $data['page'];
?>

<div class="container my-5">
    <h2 class="mb-4"><?php echo htmlspecialchars($category['name']); ?></h2>
    
    <?php if ($category['description']): ?>
        <p class="text-muted mb-4"><?php echo htmlspecialchars($category['description']); ?></p>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <div class="alert alert-info">
            No products found in this category.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        <?php if ($product['main_image']): ?>
                            <img src="<?php echo $product['main_image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height: 250px; object-fit: cover;">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x250?text=No+Image" class="card-img-top" alt="No Image" style="height: 250px; object-fit: cover;">
                        <?php endif; ?>
                        <?php if ($product['discount_value'] && $product['discount_value'] > 0): ?>
                            <span class="discount-badge">-<?php echo $product['discount_value']; ?>%</span>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name'] ?? 'Product'); ?></h5>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 100)); ?><?php echo !empty($product['description']) && strlen($product['description']) > 100 ? '...' : ''; ?></p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <?php if ($product['discount_value'] && $product['discount_value'] > 0): ?>
                                        <span class="product-price-old">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php endif; ?>
                                    <span class="product-price">$<?php echo number_format($product['final_price'], 2); ?></span>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="product.php?id=<?php echo $product['item_id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                                <?php if (isLoggedIn()): ?>
                                    <?php
                                    require_once 'controllers/WishlistController.php';
                                    $wishlistController = new WishlistController();
                                    $isInWishlist = $wishlistController->isInWishlist($product['item_id']);
                                    ?>
                                    <?php if ($isInWishlist): ?>
                                        <form method="POST" action="wishlist.php" class="mt-2">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="item_id" value="<?php echo $product['item_id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                                                <i class="fas fa-heart"></i> Remove from Wishlist
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="wishlist.php" class="mt-2">
                                            <input type="hidden" name="action" value="add">
                                            <input type="hidden" name="item_id" value="<?php echo $product['item_id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                                                <i class="far fa-heart"></i> Add to Wishlist
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

