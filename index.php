<?php
$pageTitle = "Home - Electro Electronics";
require_once 'includes/header.php';

require_once 'controllers/ProductController.php';
require_once 'controllers/CategoryController.php';

$productController = new ProductController();
$categoryController = new CategoryController();

// Get featured products
$featuredProducts = $productController->index();
$categories = $categoryController->index();
?>

<!-- Hero Slider -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div style="height: 400px; display: flex; align-items: center; justify-content: center; 
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('assets/images/products/product03.png'); 
            background-size: contain; 
            background-repeat: no-repeat ; 
            background-position: center; 
            color: white; text-align: center; padding: 2rem;">
                <div>
                    <h1 class="display-4">Laptop Collection</h1>
                    <p class="lead">Shop now and get up to 50% OFF</p>
                    <a href="products.php?category_id=1" class="btn btn-light btn-lg">Shop Now</a>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div style="height: 400px; display: flex; align-items: center; justify-content: center; 
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('assets/images/products/ايفون-15-بلس-زهري-300x300.png'); 
            background-size: contain; 
            background-repeat: no-repeat ; 
            background-position: center; 
            color: white; text-align: center; padding: 2rem;">
                <div>
                    <h1 class="display-4">Phones Collection</h1>
                    <p class="lead">Latest phones at best prices</p>
                    <a href="products.php" class="btn btn-light btn-lg">Shop Now</a>
                </div>
            </div>


        </div>
        <div class="carousel-item">
            <div style="height: 400px; display: flex; align-items: center; justify-content: center; 
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('assets/images/products/vintage-camera-with-lens-isolated-white-background.jpg'); 
            background-size: contain; 
            background-repeat: no-repeat ; 
            background-position: center; 
            color: white; text-align: center; padding: 2rem;">
                <div>
                    <h1 class="display-4">Cameras Collection</h1>
                    <p class="lead">Capture your moments</p>
                    <a href="products.php" class="btn btn-light btn-lg">Shop Now</a>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>



<!-- Discount Section -->
<div class="container my-5">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2 class="display-5">Hot Deals This Week</h2>
            <p class="text-muted">New Collection Up to 50% OFF</p>
        </div>
    </div>
    <div class="row">
        <?php
        $products = isset($featuredProducts['products']) ? $featuredProducts['products'] : [];
        foreach (array_slice($products, 0, 3) as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card product-card">
                    <?php if (!empty($product['main_image'])): ?>
                        <img src="<?php echo $product['main_image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" style="height: 250px; object-fit: cover;">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/300x250?text=No+Image" class="card-img-top" alt="No Image" style="height: 250px; object-fit: cover;">
                    <?php endif; ?>
                    <?php if (!empty($product['discount_value']) && $product['discount_value'] > 0): ?>
                        <span class="discount-badge">-<?php echo $product['discount_value']; ?>%</span>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name'] ?? 'Product'); ?></h5>
                        <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 100)); ?><?php echo !empty($product['description']) && strlen($product['description']) > 100 ? '...' : ''; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <?php if (!empty($product['discount_value']) && $product['discount_value'] > 0): ?>
                                    <span class="product-price-old">$<?php echo number_format($product['price'] ?? 0, 2); ?></span>
                                <?php endif; ?>
                                <span class="product-price">$<?php echo number_format($product['final_price'] ?? 0, 2); ?></span>
                            </div>
                            <a href="product.php?id=<?php echo $product['item_id'] ?? 0; ?>" class="btn btn-primary btn-sm">View</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- New Products Section -->
<div class="container my-5">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2 class="display-5">New Products</h2>
        </div>
    </div>
    <div class="row">
        <?php
        $products = isset($featuredProducts['products']) ? $featuredProducts['products'] : [];
        foreach (array_slice($products, 0, 6) as $product): ?>
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="card product-card">
                    <?php if (!empty($product['main_image'])): ?>
                        <img src="<?php echo $product['main_image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/300x250?text=No+Image" class="card-img-top" alt="No Image" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h6 class="card-title"><?php echo htmlspecialchars($product['name'] ?? 'Product'); ?></h6>
                        <p class="product-price mb-2">$<?php echo number_format($product['final_price'] ?? 0, 2); ?></p>
                        <a href="product.php?id=<?php echo $product['item_id'] ?? 0; ?>" class="btn btn-sm btn-outline-primary w-100">View</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>