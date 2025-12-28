<?php
/**
 * Wishlist Model
 * Handles wishlist operations
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/classes/Database.php';

class Wishlist {
    private $db;
    private $table = "wishlists";
    private $itemsTable = "wishlist_items";

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get or create wishlist for user
     */
    public function getOrCreateWishlist($user_id) {
        if (!$user_id) {
            return false;
        }

        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $wishlist = $stmt->fetch();
            } else {
                $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ? LIMIT 1");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $wishlist = $result->fetch_assoc();
                $stmt->close();
            }

            if ($wishlist) {
                return $wishlist;
            }

            // Create new wishlist
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id) VALUES (:user_id)");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $wishlist_id = $this->db->lastInsertId();
            } else {
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id) VALUES (?)");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $wishlist_id = $this->db->insert_id;
                $stmt->close();
            }

            return ['wishlist_id' => $wishlist_id, 'user_id' => $user_id];
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Add item to wishlist
     */
    public function addItem($user_id, $item_id) {
        $wishlist = $this->getOrCreateWishlist($user_id);
        if (!$wishlist) {
            return ['success' => false, 'message' => 'Failed to get wishlist'];
        }

        // Check if item already in wishlist
        if ($this->isInWishlist($user_id, $item_id)) {
            return ['success' => false, 'message' => 'Item already in wishlist'];
        }

        try {
            // Extract wishlist_id from array or object
            $wishlist_id = is_array($wishlist) ? ($wishlist['wishlist_id'] ?? null) : (isset($wishlist->wishlist_id) ? $wishlist->wishlist_id : null);
            if (!$wishlist_id) {
                return ['success' => false, 'message' => 'Invalid wishlist'];
            }

            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("INSERT INTO {$this->itemsTable} (wishlist_id, item_id) VALUES (:wishlist_id, :item_id)");
                $stmt->bindParam(':wishlist_id', $wishlist_id, PDO::PARAM_INT);
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $result = $stmt->execute();
            } else {
                $stmt = $this->db->prepare("INSERT INTO {$this->itemsTable} (wishlist_id, item_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $wishlist_id, $item_id);
                $result = $stmt->execute();
                $stmt->close();
            }

            if ($result) {
                return ['success' => true, 'message' => 'Item added to wishlist'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to add item: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to add item'];
    }

    /**
     * Remove item from wishlist
     */
    public function removeItem($user_id, $item_id) {
        $wishlist = $this->getOrCreateWishlist($user_id);
        if (!$wishlist) {
            return ['success' => false, 'message' => 'Failed to get wishlist'];
        }

        // Extract wishlist_id from array or object
        $wishlist_id = is_array($wishlist) ? ($wishlist['wishlist_id'] ?? null) : (isset($wishlist->wishlist_id) ? $wishlist->wishlist_id : null);
        if (!$wishlist_id) {
            return ['success' => false, 'message' => 'Invalid wishlist'];
        }

        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("DELETE FROM {$this->itemsTable} WHERE wishlist_id = :wishlist_id AND item_id = :item_id");
                $stmt->bindParam(':wishlist_id', $wishlist_id, PDO::PARAM_INT);
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $result = $stmt->execute();
            } else {
                $stmt = $this->db->prepare("DELETE FROM {$this->itemsTable} WHERE wishlist_id = ? AND item_id = ?");
                $stmt->bind_param("ii", $wishlist_id, $item_id);
                $result = $stmt->execute();
                $stmt->close();
            }

            if ($result) {
                return ['success' => true, 'message' => 'Item removed from wishlist'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to remove item: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to remove item'];
    }

    /**
     * Check if item is in wishlist
     */
    public function isInWishlist($user_id, $item_id) {
        $wishlist = $this->getOrCreateWishlist($user_id);
        if (!$wishlist) {
            return false;
        }

        // Extract wishlist_id from array or object
        $wishlist_id = is_array($wishlist) ? ($wishlist['wishlist_id'] ?? null) : (isset($wishlist->wishlist_id) ? $wishlist->wishlist_id : null);
        if (!$wishlist_id) {
            return false;
        }

        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT * FROM {$this->itemsTable} WHERE wishlist_id = :wishlist_id AND item_id = :item_id LIMIT 1");
                $stmt->bindParam(':wishlist_id', $wishlist_id, PDO::PARAM_INT);
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetch() !== false;
            } else {
                $stmt = $this->db->prepare("SELECT * FROM {$this->itemsTable} WHERE wishlist_id = ? AND item_id = ? LIMIT 1");
                $stmt->bind_param("ii", $wishlist_id, $item_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                return $result->num_rows > 0;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get all wishlist items
     */
    public function getWishlistItems($user_id) {
        $wishlist = $this->getOrCreateWishlist($user_id);
        if (!$wishlist) {
            return [];
        }

        // Extract wishlist_id from array or object
        $wishlist_id = is_array($wishlist) ? ($wishlist['wishlist_id'] ?? null) : (isset($wishlist->wishlist_id) ? $wishlist->wishlist_id : null);
        if (!$wishlist_id) {
            return [];
        }

        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT wi.*, i.*, c.name as category_name FROM {$this->itemsTable} wi LEFT JOIN items i ON wi.item_id = i.item_id LEFT JOIN categories c ON i.category_id = c.id WHERE wi.wishlist_id = :wishlist_id");
                $stmt->bindParam(':wishlist_id', $wishlist_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll();
            } else {
                $stmt = $this->db->prepare("SELECT wi.*, i.*, c.name as category_name FROM {$this->itemsTable} wi LEFT JOIN items i ON wi.item_id = i.item_id LEFT JOIN categories c ON i.category_id = c.id WHERE wi.wishlist_id = ?");
                $stmt->bind_param("i", $wishlist_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                return $result->fetch_all(MYSQLI_ASSOC);
            }
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get wishlist count
     */
    public function getWishlistCount($user_id) {
        $wishlist = $this->getOrCreateWishlist($user_id);
        if (!$wishlist) {
            return 0;
        }

        // Extract wishlist_id from array or object
        $wishlist_id = is_array($wishlist) ? ($wishlist['wishlist_id'] ?? null) : (isset($wishlist->wishlist_id) ? $wishlist->wishlist_id : null);
        if (!$wishlist_id) {
            return 0;
        }

        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->itemsTable} WHERE wishlist_id = :wishlist_id");
                $stmt->bindParam(':wishlist_id', $wishlist_id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch();
                return $result['count'];
            } else {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->itemsTable} WHERE wishlist_id = ?");
                $stmt->bind_param("i", $wishlist_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();
                return $row['count'];
            }
        } catch (Exception $e) {
            return 0;
        }
    }
}

