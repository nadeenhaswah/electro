<?php
/**
 * Item Rating Model
 * Handles product ratings in item_ratings table
 * Users can only rate products they have purchased and paid for
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/classes/Database.php';

class ItemRating {
    private $db;
    private $table = "item_ratings";

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Add or update rating for a product
     * User can only rate once per product (enforced by unique constraint)
     */
    public function addRating($item_id, $user_id, $rating) {
        // Validate rating (1-5)
        if ($rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Rating must be between 1 and 5'];
        }

        // Verify user has purchased and paid for the item
        require_once dirname(__DIR__) . '/classes/Order.php';
        $order = new Order();
        
        if (!$order->hasUserPurchasedItem($user_id, $item_id)) {
            return ['success' => false, 'message' => 'You must purchase and complete payment for this product before rating it.'];
        }

        // Check if user already rated this product
        $existingRating = $this->getUserRating($item_id, $user_id);
        
        try {
            if ($existingRating) {
                // Update existing rating
                $stmt = $this->db->prepare("UPDATE {$this->table} SET rating = :rating WHERE item_id = :item_id AND user_id = :user_id");
                $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $result = $stmt->execute();
                
                if ($result) {
                    return ['success' => true, 'message' => 'Rating updated successfully'];
                }
            } else {
                // Insert new rating
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (item_id, user_id, rating) VALUES (:item_id, :user_id, :rating)");
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
                $result = $stmt->execute();
                
                if ($result) {
                    return ['success' => true, 'message' => 'Rating added successfully'];
                }
            }
        } catch (Exception $e) {
            // Check if it's a duplicate key error (user already rated)
            if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'unique_item_user') !== false) {
                return ['success' => false, 'message' => 'You have already rated this product'];
            }
            return ['success' => false, 'message' => 'Failed to add rating: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to add rating'];
    }

    /**
     * Get user's rating for a product
     */
    public function getUserRating($item_id, $user_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE item_id = :item_id AND user_id = :user_id LIMIT 1");
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get average rating for a product
     */
    public function getAverageRating($item_id) {
        try {
            $stmt = $this->db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM {$this->table} WHERE item_id = :item_id");
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'average' => $result['avg_rating'] ? round((float)$result['avg_rating'], 1) : 0,
                'total' => (int)$result['total_ratings']
            ];
        } catch (Exception $e) {
            return ['average' => 0, 'total' => 0];
        }
    }

    /**
     * Check if user can rate a product
     */
    public function canUserRate($item_id, $user_id) {
        // Verify user has purchased and paid for the item
        require_once dirname(__DIR__) . '/classes/Order.php';
        $order = new Order();
        
        return $order->hasUserPurchasedItem($user_id, $item_id);
    }
}


