<?php
/**
 * Order Controller
 * Handles order creation and management
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/classes/Order.php';
require_once dirname(__DIR__) . '/classes/Cart.php';
require_once dirname(__DIR__) . '/classes/ItemImage.php';

class OrderController {
    private $order;
    private $cart;
    private $itemImage;

    public function __construct() {
        $this->order = new Order();
        $this->cart = new Cart();
        $this->itemImage = new ItemImage();
    }

    /**
     * Get user orders
     */
    public function index() {
        if (!isLoggedIn()) {
            return ['error' => 'You must be logged in to view orders'];
        }

        $orders = $this->order->getUserOrders($_SESSION['user_id']);

        // Enrich orders with items
        foreach ($orders as &$order) {
            $order['items'] = $this->order->getOrderItems($order['order_id']);
            foreach ($order['items'] as &$item) {
                $mainImage = $this->itemImage->getMainImage($item['item_id']);
                $item['main_image'] = $mainImage ? $mainImage['image_path'] : null;
            }
        }

        return ['orders' => $orders];
    }

    /**
     * Show order details
     */
    public function show($order_id) {
        if (!isLoggedIn()) {
            return ['error' => 'You must be logged in'];
        }

        $order = $this->order->getById($order_id);
        
        if (!$order) {
            return ['error' => 'Order not found'];
        }

        // Check if order belongs to user (unless admin)
        if ($order['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
            return ['error' => 'Access denied'];
        }

        $order['items'] = $this->order->getOrderItems($order_id);
        
        foreach ($order['items'] as &$item) {
            $mainImage = $this->itemImage->getMainImage($item['item_id']);
            $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
            $imageBaseUrl = $basePath . UPLOAD_URL;
            if ($mainImage && !empty($mainImage['image_path'])) {
                $imagePath = ltrim($mainImage['image_path'], '/');
                $fullFilePath = dirname(__DIR__) . '/assets/images/products/' . $imagePath;
                if (file_exists($fullFilePath)) {
                    $item['main_image'] = $imageBaseUrl . $imagePath;
                } else {
                    $item['main_image'] = null;
                }
            } else {
                $item['main_image'] = null;
            }
        }

        return ['order' => $order];
    }

    /**
     * Create order from cart
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'You must be logged in to checkout'];
        }

        $cart = $this->cart->getOrCreateCart($_SESSION['user_id'], null);
        if (!$cart) {
            return ['success' => false, 'message' => 'Failed to get cart'];
        }

        return $this->order->createFromCart($_SESSION['user_id'], $cart['cart_id']);
    }
}

