<?php
require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/includes/functions.php';
startSession();

// Get cart and wishlist counts
$cartCount = 0;
$wishlistCount = 0;

if (isLoggedIn()) {
    require_once dirname(__DIR__) . '/controllers/CartController.php';
    require_once dirname(__DIR__) . '/controllers/WishlistController.php';
    $cartController = new CartController();
    $wishlistController = new WishlistController();
    $cartCount = $cartController->getCount();
    $wishlistCount = $wishlistController->getCount();
} else {
    // Guest cart - ensure session_id is set
    if (!isset($_SESSION['session_id'])) {
        $_SESSION['session_id'] = session_id();
    }
    require_once dirname(__DIR__) . '/classes/Cart.php';
    $cart = new Cart();
    $guestCart = $cart->getOrCreateCart(null, $_SESSION['session_id']);
    if ($guestCart) {
        $cartCount = $cart->getCartCount($guestCart['cart_id']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Electro - Electronics E-commerce'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php 
    $assetsPath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../assets/' : 'assets/';
    ?>
    <link rel="stylesheet" href="<?php echo $assetsPath; ?>css/style.css">
</head>
<body>
    <!-- Top Header -->
    <div class="bg-dark text-white py-2">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <span><i class="fas fa-phone"></i> +021-95-51-84</span>
                    <span class="ms-3"><i class="fas fa-envelope"></i> email@email.com</span>
                </div>
                <div class="col-md-6 text-end">
                    <?php 
                    $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
                    if (isLoggedIn()): ?>
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</span>
                        <a href="<?php echo $basePath; ?>profile.php" class="text-white ms-3">Profile</a>
                        <a href="<?php echo $basePath; ?>auth/logout.php" class="text-white ms-3">Logout</a>
                        <?php if (isAdmin()): ?>
                            <a href="<?php echo $basePath; ?>admin/" class="text-white ms-3">Admin Panel</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?php echo $basePath; ?>auth/login.php" class="text-white">Login</a>
                        <a href="<?php echo $basePath; ?>auth/register.php" class="text-white ms-3">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <?php 
            $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
            ?>
            <a class="navbar-brand fw-bold" href="<?php echo $basePath; ?>index.php">ELECTRO</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>products.php">Products</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            Categories
                        </a>
                        <ul class="dropdown-menu">
                            <?php
                            require_once dirname(__DIR__) . '/classes/Category.php';
                            $category = new Category();
                            $categories = $category->getAll(true);
                            foreach ($categories as $cat): ?>
                                <li><a class="dropdown-item" href="<?php echo $basePath; ?>category.php?id=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>profile.php">
                                <i class="fas fa-user"></i> Profile
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>wishlist.php">
                            <i class="fas fa-heart"></i> Wishlist
                            <?php if ($wishlistCount > 0): ?>
                                <span class="badge bg-danger"><?php echo $wishlistCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <?php if ($cartCount > 0): ?>
                                <span class="badge bg-danger"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

