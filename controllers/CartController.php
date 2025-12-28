<?php
/**
 * Cart Controller
 * Handles shopping cart operations
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/classes/Cart.php';
require_once dirname(__DIR__) . '/classes/Product.php';
require_once dirname(__DIR__) . '/classes/ItemImage.php';

class CartController {
    private $cart;
    private $product;
    private $itemImage;

    public function __construct() {
        $this->cart = new Cart();
        $this->product = new Product();
        $this->itemImage = new ItemImage();
    }

    /**
     * Get or create cart
     */
    private function getCart() {
        startSession();
        
        if (isLoggedIn()) {
            return $this->cart->getOrCreateCart($_SESSION['user_id'], null);
        } else {
            if (!isset($_SESSION['session_id'])) {
                $_SESSION['session_id'] = session_id();
            }
            return $this->cart->getOrCreateCart(null, $_SESSION['session_id']);
        }
    }

    /**
     * View cart
     */
    public function index() {
        $cart = $this->getCart();
        if (!$cart) {
            return ['items' => [], 'total' => 0, 'cart_id' => null];
        }

        // Handle both array and object returns
        $cart_id = is_array($cart) ? ($cart['cart_id'] ?? null) : (isset($cart->cart_id) ? $cart->cart_id : null);
        if (!$cart_id) {
            // Try to get cart_id from the cart array/object directly
            if (is_array($cart) && isset($cart[0]['cart_id'])) {
                $cart_id = $cart[0]['cart_id'];
            } elseif (is_array($cart)) {
                // If cart is an array with numeric keys, get the first element
                $firstCart = reset($cart);
                $cart_id = is_array($firstCart) ? ($firstCart['cart_id'] ?? null) : null;
            }
            
            if (!$cart_id) {
                return ['items' => [], 'total' => 0, 'cart_id' => null];
            }
        }

        $items = $this->cart->getCartItems($cart_id);
        
        // Enrich items with product details
        $enrichedItems = [];
        foreach ($items as $item) {
            $product = $this->product->getById($item['item_id']);
            if ($product) {
                $item['product'] = $product;
                $item['product']['final_price'] = $this->product->getFinalPrice($product);
                $mainImage = $this->itemImage->getMainImage($item['item_id']);
                $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
                $imageBaseUrl = $basePath . UPLOAD_URL;
                if ($mainImage && !empty($mainImage['image_path'])) {
                    $imagePath = ltrim($mainImage['image_path'], '/');
                    $fullFilePath = dirname(__DIR__) . '/assets/images/products/' . $imagePath;
                    if (file_exists($fullFilePath)) {
                        $item['product']['main_image'] = $imageBaseUrl . $imagePath;
                    } else {
                        $item['product']['main_image'] = null;
                    }
                } else {
                    $item['product']['main_image'] = null;
                }
                $enrichedItems[] = $item;
            } else {
                // Product not found, but keep the item with basic info
                $item['product'] = [
                    'name' => $item['name'] ?? 'Product Not Found',
                    'description' => $item['description'] ?? '',
                    'main_image' => null,
                    'final_price' => $item['price_at_time'] ?? 0
                ];
                $enrichedItems[] = $item;
            }
        }
        $items = $enrichedItems;

        $total = $this->cart->getCartTotal($cart_id);

        return [
            'items' => $items,
            'total' => $total,
            'cart_id' => $cart_id
        ];
    }

    /**
     * Add item to cart
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $item_id = (int)($_POST['item_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($quantity <= 0) {
            $quantity = 1;
        }

        $cart = $this->getCart();
        if (!$cart) {
            return ['success' => false, 'message' => 'Failed to get cart'];
        }

        // Handle both array and object returns
        $cart_id = is_array($cart) ? $cart['cart_id'] : (isset($cart->cart_id) ? $cart->cart_id : null);
        if (!$cart_id) {
            return ['success' => false, 'message' => 'Failed to get cart ID'];
        }

        return $this->cart->addItem($cart_id, $item_id, $quantity);
    }

    /**
     * Update cart item quantity
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $cart_item_id = (int)($_POST['cart_item_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($this->cart->updateQuantity($cart_item_id, $quantity)) {
            return ['success' => true, 'message' => 'Cart updated'];
        }

        return ['success' => false, 'message' => 'Failed to update cart'];
    }

    /**
     * Remove item from cart
     */
    public function remove() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $cart_item_id = (int)($_POST['cart_item_id'] ?? 0);

        if ($this->cart->removeItem($cart_item_id)) {
            return ['success' => true, 'message' => 'Item removed from cart'];
        }

        return ['success' => false, 'message' => 'Failed to remove item'];
    }

    /**
     * Get cart count
     */
    public function getCount() {
        $cart = $this->getCart();
        if (!$cart) {
            return 0;
        }

        // Handle both array and object returns
        $cart_id = is_array($cart) ? $cart['cart_id'] : (isset($cart->cart_id) ? $cart->cart_id : null);
        if (!$cart_id) {
            return 0;
        }

        return $this->cart->getCartCount($cart_id);
    }
}

