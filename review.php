<?php
$pageTitle = "Add Review - Electro Electronics";
require_once 'includes/access_control.php';

// Require user to be logged in
requireUserAccess();

if (!isset($_GET['item_id'])) {
    header('Location: orders.php');
    exit();
}

require_once 'controllers/ProductController.php';
require_once 'classes/Order.php';
require_once 'classes/ItemRating.php';

$productController = new ProductController();
$order = new Order();
$itemRating = new ItemRating();

$item_id = (int)$_GET['item_id'];
$user_id = $_SESSION['user_id'];

// Verify user has purchased and paid for this product
if (!$order->hasUserPurchasedItem($user_id, $item_id)) {
    setFlashMessage('error', 'You must purchase and complete payment for this product before adding a review.');
    header('Location: orders.php');
    exit();
}

// Get product details
$data = $productController->show($item_id);
if (isset($data['error'])) {
    setFlashMessage('error', $data['error']);
    header('Location: orders.php');
    exit();
}

$product = $data['product'];

// Handle review submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
    $result = $productController->addComment();
    if ($result['success']) {
        setFlashMessage('success', $result['message']);
        header('Location: review.php?item_id=' . $item_id . '&success=1');
        exit();
    } else {
        $error = $result['message'];
    }
}

// Check if review was just submitted
$successMsg = getFlashMessage('success');
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMsg = $successMsg ?: 'Review submitted successfully! Your review is now visible to all users.';
}

// Display flash messages
$errorMsg = getFlashMessage('error');
?>
<?php require_once 'includes/header.php';
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-star"></i> Write a Review</h3>
                </div>
                <div class="card-body">
                    <?php if ($successMsg): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMsg); ?>
                        </div>
                        <div class="text-center mb-4">
                            <a href="product.php?id=<?php echo $item_id; ?>" class="btn btn-primary">View Product</a>
                            <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
                        </div>
                    <?php else: ?>
                        <!-- Product Info -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <div class="row align-items-center">
                                <?php if (!empty($product['images'])): ?>
                                    <div class="col-auto">
                                        <img src="<?php echo $product['images'][0]['image_url']; ?>"
                                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                                            style="width: 80px; height: 80px; object-fit: cover;"
                                            class="rounded">
                                    </div>
                                <?php endif; ?>
                                <div class="col">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($product['description']); ?></p>
                                </div>
                            </div>
                        </div>

                        <?php if ($error || $errorMsg): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error ?: $errorMsg); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="review.php?item_id=<?php echo $item_id; ?>" id="reviewForm">
                            <input type="hidden" name="action" value="add_review">
                            <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">

                            <div class="mb-4">
                                <label class="form-label">Your Rating *</label>
                                <div class="rating-input">
                                    <input type="hidden" name="rating" id="ratingValue" required>
                                    <div class="d-flex align-items-center">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <i class="fas fa-star rating-star" data-rating="<?php echo $i; ?>" style="font-size: 2rem; color: #ddd; cursor: pointer; margin-right: 5px;" onmouseover="highlightStars(<?php echo $i; ?>)" onmouseout="resetStars()" onclick="setRating(<?php echo $i; ?>)"></i>
                                        <?php endfor; ?>
                                        <span class="ms-3 text-muted" id="ratingText">Click to rate</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Select a rating from 1 to 5 stars (required)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Your Review *</label>
                                <textarea name="comment" class="form-control" rows="5" required placeholder="Share your experience with this product..."></textarea>
                                <small class="form-text text-muted">Your review will be visible to all users.</small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="orders.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit Review
                                </button>
                            </div>
                        </form>

                        <script>
                            let selectedRating = 0;

                            function highlightStars(rating) {
                                const stars = document.querySelectorAll('.rating-star');
                                stars.forEach((star, index) => {
                                    const starRating = parseInt(star.getAttribute('data-rating'));
                                    if (starRating <= rating) {
                                        star.style.color = '#ffc107';
                                    } else {
                                        star.style.color = '#ddd';
                                    }
                                });
                                updateRatingText(rating);
                            }

                            function resetStars() {
                                if (selectedRating === 0) {
                                    const stars = document.querySelectorAll('.rating-star');
                                    stars.forEach(star => {
                                        star.style.color = '#ddd';
                                    });
                                    document.getElementById('ratingText').textContent = 'Click to rate';
                                } else {
                                    highlightStars(selectedRating);
                                }
                            }

                            function setRating(rating) {
                                selectedRating = rating;
                                document.getElementById('ratingValue').value = rating;
                                highlightStars(rating);
                                document.getElementById('ratingText').textContent = rating + ' out of 5 stars';
                            }

                            function updateRatingText(rating) {
                                const texts = {
                                    1: '1 star - Poor',
                                    2: '2 stars - Fair',
                                    3: '3 stars - Good',
                                    4: '4 stars - Very Good',
                                    5: '5 stars - Excellent'
                                };
                                document.getElementById('ratingText').textContent = texts[rating] || 'Click to rate';
                            }

                            // Validate form submission
                            document.getElementById('reviewForm').addEventListener('submit', function(e) {
                                if (!document.getElementById('ratingValue').value || document.getElementById('ratingValue').value < 1) {
                                    e.preventDefault();
                                    alert('Please select a rating before submitting your review.');
                                    return false;
                                }
                            });
                        </script>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>