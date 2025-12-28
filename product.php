<?php
$pageTitle = "Product Details - Electro Electronics";
require_once 'includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit();
}

require_once 'controllers/ProductController.php';
require_once 'controllers/CartController.php';
require_once 'controllers/WishlistController.php';

$productController = new ProductController();
$cartController = new CartController();
$wishlistController = new WishlistController();

$data = $productController->show((int)$_GET['id']);

if (isset($data['error'])) {
    echo '<div class="container my-5"><div class="alert alert-danger">' . $data['error'] . '</div></div>';
    require_once 'includes/footer.php';
    exit();
}

$product = $data['product'];
$canComment = $data['canComment'];
$isInWishlist = isLoggedIn() ? $wishlistController->isInWishlist($product['item_id']) : false;
?>

<div class="container my-5">
    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6">
            <?php if (!empty($product['images'])): ?>
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($product['images'] as $index => $image): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="<?php echo $image['image_url']; ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-height: 500px; object-fit: contain;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($product['images']) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <img src="https://via.placeholder.com/500x500?text=No+Image" class="img-fluid" alt="No Image">
            <?php endif; ?>
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="mb-3">
                <?php if ($product['discount_value'] && $product['discount_value'] > 0): ?>
                    <span class="text-muted text-decoration-line-through me-2">$<?php echo number_format($product['price'], 2); ?></span>
                    <span class="badge bg-danger">-<?php echo $product['discount_value']; ?>%</span>
                <?php endif; ?>
                <h3 class="text-primary">$<?php echo number_format($product['final_price'], 2); ?></h3>
            </div>

            <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>

            <div class="mb-3">
                <strong>Category:</strong> <?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?><br>
                <?php if ($product['country_made']): ?>
                    <strong>Made in:</strong> <?php echo htmlspecialchars($product['country_made']); ?><br>
                <?php endif; ?>
                <?php if (isset($product['quantity'])): ?>
                    <strong>Stock:</strong> 
                    <?php if ($product['quantity'] > 0): ?>
                        <span class="badge bg-success"><?php echo $product['quantity']; ?> in stock</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Out of stock</span>
                    <?php endif; ?>
                    <br>
                <?php endif; ?>
                
                <!-- Average Rating Display -->
                <?php if (isset($product['rating_average']) && $product['rating_average'] > 0): ?>
                    <div class="mt-2">
                        <strong>Rating:</strong>
                        <div class="d-flex align-items-center">
                            <?php 
                            $avgRating = $product['rating_average'];
                            for ($i = 1; $i <= 5; $i++): 
                            ?>
                                <i class="fas fa-star <?php echo $i <= round($avgRating) ? 'text-warning' : 'text-muted'; ?>" style="font-size: 1.2rem;"></i>
                            <?php endfor; ?>
                            <span class="ms-2">
                                <strong><?php echo number_format($avgRating, 1); ?></strong> 
                                (<?php echo $product['rating_total']; ?> <?php echo $product['rating_total'] == 1 ? 'rating' : 'ratings'; ?>)
                            </span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-2">
                        <strong>Rating:</strong> <span class="text-muted">No ratings yet</span>
                    </div>
                <?php endif; ?>
            </div>

            <form method="POST" action="cart.php" class="mb-3 d-inline-block">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="item_id" value="<?php echo $product['item_id']; ?>">
                <div class="row mb-3">
                    <div class="col-4">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control quantity-input" value="1" min="1">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg me-2">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </form>
            <?php if (isLoggedIn()): ?>
                <?php if ($isInWishlist): ?>
                    <form method="POST" action="wishlist.php" class="d-inline">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="item_id" value="<?php echo $product['item_id']; ?>">
                        <button type="submit" class="btn btn-outline-danger btn-lg">
                            <i class="fas fa-heart"></i> Remove from Wishlist
                        </button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="wishlist.php" class="d-inline">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="item_id" value="<?php echo $product['item_id']; ?>">
                        <button type="submit" class="btn btn-outline-danger btn-lg">
                            <i class="far fa-heart"></i> Add to Wishlist
                        </button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="row mt-5">
        <div class="col-12">
            <h3>Reviews</h3>
            <hr>

            <?php if (isLoggedIn()): ?>
                <?php if ($canComment): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5>Write a Review</h5>
                            <form method="POST" action="product.php?id=<?php echo $product['item_id']; ?>" id="reviewFormProduct">
                                <input type="hidden" name="action" value="add_comment">
                                <input type="hidden" name="item_id" value="<?php echo $product['item_id']; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Your Rating *</label>
                                    <div class="rating-input">
                                        <input type="hidden" name="rating" id="ratingValueProduct" required>
                                        <div class="d-flex align-items-center">
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                <i class="fas fa-star rating-star-product" data-rating="<?php echo $i; ?>" style="font-size: 2rem; color: #ddd; cursor: pointer; margin-right: 5px;" onmouseover="highlightStarsProduct(<?php echo $i; ?>)" onmouseout="resetStarsProduct()" onclick="setRatingProduct(<?php echo $i; ?>)"></i>
                                            <?php endfor; ?>
                                            <span class="ms-3 text-muted" id="ratingTextProduct">Click to rate</span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Select a rating from 1 to 5 stars (required)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Your Review *</label>
                                    <textarea name="comment" class="form-control" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </form>
                            
                            <script>
                            let selectedRatingProduct = 0;
                            
                            function highlightStarsProduct(rating) {
                                const stars = document.querySelectorAll('.rating-star-product');
                                stars.forEach((star) => {
                                    const starRating = parseInt(star.getAttribute('data-rating'));
                                    if (starRating <= rating) {
                                        star.style.color = '#ffc107';
                                    } else {
                                        star.style.color = '#ddd';
                                    }
                                });
                                updateRatingTextProduct(rating);
                            }
                            
                            function resetStarsProduct() {
                                if (selectedRatingProduct === 0) {
                                    const stars = document.querySelectorAll('.rating-star-product');
                                    stars.forEach(star => {
                                        star.style.color = '#ddd';
                                    });
                                    document.getElementById('ratingTextProduct').textContent = 'Click to rate';
                                } else {
                                    highlightStarsProduct(selectedRatingProduct);
                                }
                            }
                            
                            function setRatingProduct(rating) {
                                selectedRatingProduct = rating;
                                document.getElementById('ratingValueProduct').value = rating;
                                highlightStarsProduct(rating);
                                document.getElementById('ratingTextProduct').textContent = rating + ' out of 5 stars';
                            }
                            
                            function updateRatingTextProduct(rating) {
                                const texts = {
                                    1: '1 star - Poor',
                                    2: '2 stars - Fair',
                                    3: '3 stars - Good',
                                    4: '4 stars - Very Good',
                                    5: '5 stars - Excellent'
                                };
                                document.getElementById('ratingTextProduct').textContent = texts[rating] || 'Click to rate';
                            }
                            
                            // Validate form submission
                            document.getElementById('reviewFormProduct').addEventListener('submit', function(e) {
                                if (!document.getElementById('ratingValueProduct').value || document.getElementById('ratingValueProduct').value < 1) {
                                    e.preventDefault();
                                    alert('Please select a rating before submitting your review.');
                                    return false;
                                }
                            });
                            </script>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> You must purchase and complete payment for this product before adding a comment.
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    Please <a href="auth/login.php">login</a> to write a review.
                </div>
            <?php endif; ?>

            <!-- Customer Reviews - Always Visible to All Users -->
            <div class="comments-list mt-4">
                <h5 class="mb-3"><i class="fas fa-comments"></i> Customer Reviews</h5>
                <?php 
                // Comments are always visible to all users (logged in or not)
                // Only approved comments (status = 1) are shown
                // Filter out any empty or invalid comments
                $approvedComments = [];
                if (!empty($product['comments']) && is_array($product['comments'])) {
                    foreach ($product['comments'] as $comment) {
                        // Only show comments that have content and are approved (status = 1)
                        if (!empty($comment['comment']) && isset($comment['status']) && $comment['status'] == 1) {
                            $approvedComments[] = $comment;
                        }
                    }
                }
                
                if (empty($approvedComments)): ?>
                    <p class="text-muted">No reviews yet. Be the first to review!</p>
                <?php else: ?>
                    <p class="text-muted mb-3"><?php echo count($approvedComments); ?> review(s)</p>
                    <?php foreach ($approvedComments as $comment): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong class="d-block">
                                            <?php 
                                            if (!empty($comment['first_name']) || !empty($comment['last_name'])) {
                                                echo htmlspecialchars(trim(($comment['first_name'] ?? '') . ' ' . ($comment['last_name'] ?? '')));
                                            } else {
                                                echo 'Anonymous';
                                            }
                                            ?>
                                        </strong>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> 
                                            <?php 
                                            if (!empty($comment['comment_date'])) {
                                                echo date('F d, Y', strtotime($comment['comment_date']));
                                            }
                                            ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $result = $productController->addComment();
    if ($result['success']) {
        setFlashMessage('success', $result['message']);
    } else {
        setFlashMessage('error', $result['message']);
    }
    header('Location: product.php?id=' . $product['item_id']);
    exit();
}

// Display flash messages
$successMsg = getFlashMessage('success');
$errorMsg = getFlashMessage('error');
if ($successMsg || $errorMsg):
?>
    <div class="container mt-3">
        <?php if ($successMsg): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>

