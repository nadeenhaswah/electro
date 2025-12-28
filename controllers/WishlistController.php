<?php

/**
 * Wishlist Controller
 * Handles wishlist operations
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/classes/Wishlist.php';
require_once dirname(__DIR__) . '/classes/Product.php';
require_once dirname(__DIR__) . '/classes/ItemImage.php';

class WishlistController
{
    private $wishlist;
    private $product;
    private $itemImage;

    public function __construct()
    {
        $this->wishlist = new Wishlist();
        $this->product = new Product();
        $this->itemImage = new ItemImage();
    }

    /**
     * View wishlist
     */
    public function index()
    {
        if (!isLoggedIn()) {
            return ['error' => 'You must be logged in to view your wishlist'];
        }

        $items = $this->wishlist->getWishlistItems($_SESSION['user_id']);

        // Enrich items with product details
        foreach ($items as &$item) {
            $item['final_price'] = $this->product->getFinalPrice($item);
            $mainImage = $this->itemImage->getMainImage($item['item_id']);
            $mainImage = $this->itemImage->getMainImage($item['item_id']);

            if ($mainImage && !empty($mainImage['image_path'])) {
                $item['main_image'] = UPLOAD_URL . $mainImage['image_path'];
            } else {
                $item['main_image'] = null;
            }
        }

        return ['items' => $items];
    }

    /**
     * Add item to wishlist
     */
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'You must be logged in to add items to wishlist'];
        }

        $item_id = (int)($_POST['item_id'] ?? 0);

        return $this->wishlist->addItem($_SESSION['user_id'], $item_id);
    }

    /**
     * Remove item from wishlist
     */
    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'You must be logged in'];
        }

        $item_id = (int)($_POST['item_id'] ?? 0);

        return $this->wishlist->removeItem($_SESSION['user_id'], $item_id);
    }

    /**
     * Check if item is in wishlist
     */
    public function isInWishlist($item_id)
    {
        if (!isLoggedIn()) {
            return false;
        }

        return $this->wishlist->isInWishlist($_SESSION['user_id'], $item_id);
    }

    /**
     * Get wishlist count
     */
    public function getCount()
    {
        if (!isLoggedIn()) {
            return 0;
        }

        return $this->wishlist->getWishlistCount($_SESSION['user_id']);
    }
}
