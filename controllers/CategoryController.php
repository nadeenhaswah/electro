<?php
/**
 * Category Controller
 * Handles category listing and category products
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/classes/Category.php';
require_once dirname(__DIR__) . '/classes/Product.php';
require_once dirname(__DIR__) . '/classes/ItemImage.php';

class CategoryController {
    private $category;
    private $product;
    private $itemImage;

    public function __construct() {
        $this->category = new Category();
        $this->product = new Product();
        $this->itemImage = new ItemImage();
    }

    /**
     * List all categories
     */
    public function index() {
        $categories = $this->category->getAll(true);
        return ['categories' => $categories];
    }

    /**
     * Show category products
     */
    public function show($category_id) {
        $category = $this->category->getById($category_id);
        
        if (!$category) {
            return ['error' => 'Category not found'];
        }

        $filters = ['category_id' => $category_id];
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $products = $this->product->getAll($filters, $limit, $offset);

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
        }

        return [
            'category' => $category,
            'products' => $products,
            'page' => $page
        ];
    }
}

