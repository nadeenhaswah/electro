    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>About Us</h5>
                    <p>Your trusted source for electronics. We offer the latest laptops, smartphones, cameras, and accessories.</p>
                    <p>
                        <i class="fas fa-map-marker-alt"></i> 1734 Stonecoal Road<br>
                        <i class="fas fa-phone"></i> +021-95-51-84<br>
                        <i class="fas fa-envelope"></i> email@email.com
                    </p>
                </div>
                <div class="col-md-2 mb-4">
                    <h5>Categories</h5>
                    <ul class="list-unstyled">
                        <?php
                        $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
                        require_once dirname(__DIR__) . '/classes/Category.php';
                        $category = new Category();
                        $categories = $category->getAll(true);
                        foreach (array_slice($categories, 0, 5) as $cat): ?>
                            <li><a href="<?php echo $basePath; ?>category.php?id=<?php echo $cat['id']; ?>" class="text-white-50"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Information</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50">About Us</a></li>
                        <li><a href="#" class="text-white-50">Contact Us</a></li>
                        <li><a href="#" class="text-white-50">Privacy Policy</a></li>
                        <li><a href="#" class="text-white-50">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Service</h5>
                    <ul class="list-unstyled">
                        <?php 
                        $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
                        if (isLoggedIn()): ?>
                            <li><a href="<?php echo $basePath; ?>orders.php" class="text-white-50">My Orders</a></li>
                            <li><a href="<?php echo $basePath; ?>wishlist.php" class="text-white-50">Wishlist</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo $basePath; ?>cart.php" class="text-white-50">View Cart</a></li>
                        <li><a href="#" class="text-white-50">Help</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-white">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Electro. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php 
    $assetsPath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../assets/' : 'assets/';
    ?>
    <script src="<?php echo $assetsPath; ?>js/main.js"></script>
</body>
</html>


