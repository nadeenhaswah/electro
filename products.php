<?php
$pageTitle = "Products - Electro Electronics";
require_once 'includes/header.php';

require_once 'controllers/ProductController.php';
require_once 'controllers/CategoryController.php';

$productController = new ProductController();
$data = $productController->index();

$products = $data['products'];
$categories = $data['categories'];
$filters = $data['filters'];
$page = $data['page'];
?>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="products.php">
                        <div class="mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" placeholder="Search products...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo (isset($filters['category_id']) && $filters['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Min Price</label>
                            <input type="number" name="min_price" class="form-control" value="<?php echo htmlspecialchars($filters['min_price'] ?? ''); ?>" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Max Price</label>
                            <input type="number" name="max_price" class="form-control" value="<?php echo htmlspecialchars($filters['max_price'] ?? ''); ?>" step="0.01">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        <a href="products.php" class="btn btn-secondary w-100 mt-2">Clear</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>All Products</h2>
                <span class="text-muted"><?php echo count($products); ?> products found</span>
            </div>

            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    No products found. Try adjusting your filters.
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
                                    
                                    <!-- Rating Display -->
                                    <?php if (isset($product['rating_average']) && $product['rating_average'] > 0): ?>
                                        <div class="mb-2">
                                            <div class="d-flex align-items-center">
                                                <?php 
                                                $avgRating = $product['rating_average'];
                                                for ($i = 1; $i <= 5; $i++): 
                                                ?>
                                                    <i class="fas fa-star <?php echo $i <= round($avgRating) ? 'text-warning' : 'text-muted'; ?>" style="font-size: 0.9rem;"></i>
                                                <?php endfor; ?>
                                                <span class="ms-2 small text-muted">
                                                    <?php echo number_format($avgRating, 1); ?> 
                                                    (<?php echo $product['rating_total']; ?> <?php echo $product['rating_total'] == 1 ? 'rating' : 'ratings'; ?>)
                                                </span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="mb-2">
                                            <span class="text-muted small">No ratings yet</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <?php if ($product['discount_value'] && $product['discount_value'] > 0): ?>
                                                <span class="product-price-old">$<?php echo number_format($product['price'], 2); ?></span>
                                            <?php endif; ?>
                                            <span class="product-price">$<?php echo number_format($product['final_price'], 2); ?></span>
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="product.php?id=<?php echo $product['item_id'] ?? 0; ?>" class="btn btn-primary">View Details</a>
                                        <?php if (isLoggedIn()): ?>
                                            <?php
                                            require_once 'controllers/WishlistController.php';
                                            $wishlistController = new WishlistController();
                                            $isInWishlist = $wishlistController->isInWishlist($product['item_id'] ?? 0);
                                            ?>
                                            <?php if ($isInWishlist): ?>
                                                <form method="POST" action="wishlist.php" class="mt-2">
                                                    <input type="hidden" name="action" value="remove">
                                                    <input type="hidden" name="item_id" value="<?php echo $product['item_id'] ?? 0; ?>">
                                                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                                                        <i class="fas fa-heart"></i> Remove from Wishlist
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" action="wishlist.php" class="mt-2">
                                                    <input type="hidden" name="action" value="add">
                                                    <input type="hidden" name="item_id" value="<?php echo $product['item_id'] ?? 0; ?>">
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
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

