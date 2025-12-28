<?php

/**
 * Product Controller
 * Handles product listing, details, search, and filtering
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/classes/Product.php';
require_once dirname(__DIR__) . '/classes/Category.php';
require_once dirname(__DIR__) . '/classes/Comment.php';
require_once dirname(__DIR__) . '/classes/ItemImage.php';
require_once dirname(__DIR__) . '/classes/Order.php';
require_once dirname(__DIR__) . '/classes/ItemRating.php';

class ProductController
{
    private $product;
    private $category;
    private $comment;
    private $itemImage;
    private $order;
    private $itemRating;

    public function __construct()
    {
        $this->product = new Product();
        $this->category = new Category();
        $this->comment = new Comment();
        $this->itemImage = new ItemImage();
        $this->order = new Order();
        $this->itemRating = new ItemRating();
    }

    /**
     * List products with filters
     */
    public function index()
    {
        $filters = [];

        if (isset($_GET['search'])) {
            $filters['search'] = sanitize($_GET['search']);
        }

        if (isset($_GET['category_id']) && $_GET['category_id']) {
            $filters['category_id'] = (int)$_GET['category_id'];
        }

        if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
            $filters['min_price'] = (float)$_GET['min_price'];
        }

        if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
            $filters['max_price'] = (float)$_GET['max_price'];
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $products = $this->product->getAll($filters, $limit, $offset);
        $categories = $this->category->getAll(true);

        $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
        $imageBaseUrl = $basePath . UPLOAD_URL;

        foreach ($products as &$product) {
            $product['final_price'] = $this->product->getFinalPrice($product);

            $mainImage = $this->itemImage->getMainImage($product['item_id']);
            if (!$mainImage) {
                $allImages = $this->itemImage->getItemImages($product['item_id']);
                if (!empty($allImages)) {
                    $mainImage = $allImages[0];
                }
            }
            if ($mainImage && !empty($mainImage['image_path'])) {
                $imagePath = ltrim($mainImage['image_path'], '/');
                $product['main_image'] = $imageBaseUrl . $imagePath;
            } else {
                $product['main_image'] = null;
            }

            $ratingData = $this->itemRating->getAverageRating($product['item_id']);
            $product['rating_average'] = $ratingData['average'];
            $product['rating_total'] = $ratingData['total'];
        }

        return [
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'page' => $page
        ];
    }

    public function show($item_id)
    {
        $product = $this->product->getById($item_id);

        if (!$product) {
            return ['error' => 'Product not found'];
        }

        $product['final_price'] = $this->product->getFinalPrice($product);
        $images = $this->itemImage->getItemImages($item_id);
        $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
        $imageBaseUrl = $basePath . UPLOAD_URL;
        $images = $this->itemImage->getItemImages($item_id);

        foreach ($images as &$img) {
            if (!empty($img['image_path'])) {
                $img['image_url'] = UPLOAD_URL . $img['image_path'];
            } else {
                $img['image_url'] = null;
            }
        }

        // $product['images'] = $images;

        $product['images'] = $images;
        $product['comments'] = $this->comment->getProductComments($item_id, true);

        $ratingData = $this->itemRating->getAverageRating($item_id);
        $product['rating_average'] = $ratingData['average'];
        $product['rating_total'] = $ratingData['total'];

        $product['user_rating'] = null;
        if (isLoggedIn()) {
            $userRating = $this->itemRating->getUserRating($item_id, $_SESSION['user_id']);
            if ($userRating) {
                $product['user_rating'] = $userRating['rating'];
            }
        }

        $canComment = false;
        if (isLoggedIn()) {
            $canComment = $this->order->hasUserPurchasedItem($_SESSION['user_id'], $item_id);
        }

        return [
            'product' => $product,
            'canComment' => $canComment
        ];
    }

    /**
     * Add comment and rating
     */
    public function addComment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'You must be logged in to comment'];
        }

        $item_id = (int)($_POST['item_id'] ?? 0);
        $comment_text = sanitize($_POST['comment'] ?? '');
        $rating = (int)($_POST['rating'] ?? 0);

        // Validate rating (required, 1-5)
        if ($rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Please select a valid rating (1-5 stars)'];
        }

        // Add comment
        $commentResult = $this->comment->addComment($item_id, $_SESSION['user_id'], $comment_text);

        if (!$commentResult['success']) {
            return $commentResult;
        }

        // Add rating
        $ratingResult = $this->itemRating->addRating($item_id, $_SESSION['user_id'], $rating);

        if (!$ratingResult['success']) {
            // Comment was added but rating failed - still return success but with warning
            return ['success' => true, 'message' => 'Review added successfully, but rating could not be saved: ' . $ratingResult['message']];
        }

        return ['success' => true, 'message' => 'Review and rating added successfully!'];
    }
}
