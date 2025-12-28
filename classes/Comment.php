<?php

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/classes/Database.php';

class Comment {
    private $db;
    private $table = "comments";

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addComment($item_id, $user_id, $comment, $rating = 0) {
        require_once dirname(__DIR__) . '/classes/Order.php';
        $order = new Order();
        
        if (!$order->hasUserPurchasedItem($user_id, $item_id)) {
            return ['success' => false, 'message' => 'You must purchase and complete payment for this product before adding a comment.'];
        }

        if (empty($comment)) {
            return ['success' => false, 'message' => 'Comment cannot be empty'];
        }

        try {
            $status = 1; // Approved - visible to all users immediately
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (item_id, user_id, comment, status) VALUES (:item_id, :user_id, :comment, :status)");
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                return ['success' => true, 'message' => 'Review added successfully! Your review is now visible to all users.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to add comment: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to add comment'];
    }

    public function getProductComments($item_id, $approved_only = true) {
        try {
            $sql = "SELECT c.*, u.first_name, u.last_name 
                    FROM {$this->table} c 
                    LEFT JOIN users u ON c.user_id = u.user_id 
                    WHERE c.item_id = :item_id";
            
            if ($approved_only) {
                // status = 1 means approved (tinyint(1) in database)
                $sql .= " AND c.status = 1";
            }
            
            $sql .= " ORDER BY c.comment_date DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as &$result) {
                $result['status'] = (int)$result['status'];
            }
            return $results;
        } catch (Exception $e) {
            return [];
        }
    }

    public function getAllComments($limit = null, $offset = 0) {
        try {
            $sql = "SELECT c.*, u.first_name, u.last_name, u.email, i.name as item_name FROM {$this->table} c LEFT JOIN users u ON c.user_id = u.user_id LEFT JOIN items i ON c.item_id = i.item_id ORDER BY c.comment_date DESC";
            
            if ($limit) {
                $sql .= " LIMIT :limit OFFSET :offset";
            }

            $stmt = $this->db->prepare($sql);
            if ($limit) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function approveComment($comment_id) {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 1 WHERE comment_id = :comment_id");
            $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                return $result;
            }

            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Reject/Delete comment
     */
    public function deleteComment($comment_id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE comment_id = :comment_id");
            $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
            $result = $stmt->execute();

            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getById($comment_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE comment_id = :comment_id LIMIT 1");
            $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }
}

