<?php
/**
 * Category Model
 * Handles category CRUD operations
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/classes/Database.php';

class Category {
    private $db;
    private $table = "categories";

    public $id;
    public $name;
    public $description;
    public $discount_value;
    public $discount_start;
    public $discount_end;
    public $visibility;
    public $allow_comments;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new category
     */
    public function create($name, $description = null, $discount_value = null, $discount_start = null, $discount_end = null, $visibility = 1, $allow_comments = 1) {
        if (empty($name)) {
            return ['success' => false, 'message' => 'Category name is required'];
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, description, discount_value, discount_start, discount_end, visibility, allow_comments) VALUES (:name, :description, :discount_value, :discount_start, :discount_end, :visibility, :allow_comments)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':discount_value', $discount_value);
            $stmt->bindParam(':discount_start', $discount_start);
            $stmt->bindParam(':discount_end', $discount_end);
            $stmt->bindParam(':visibility', $visibility, PDO::PARAM_INT);
            $stmt->bindParam(':allow_comments', $allow_comments, PDO::PARAM_INT);
            $result = $stmt->execute();
            $id = $this->db->lastInsertId();

            if ($result) {
                return ['success' => true, 'message' => 'Category created successfully', 'id' => $id];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to create category: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to create category'];
    }

    /**
     * Get category by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get all categories
     */
    public function getAll($visible_only = false) {
        try {
            $sql = "SELECT * FROM {$this->table}";
            if ($visible_only) {
                $sql .= " WHERE visibility = 1";
            }
            $sql .= " ORDER BY name ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Update category
     */
    public function update($id, $name, $description = null, $discount_value = null, $discount_start = null, $discount_end = null, $visibility = 1, $allow_comments = 1) {
        if (empty($name)) {
            return ['success' => false, 'message' => 'Category name is required'];
        }

        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET name = :name, description = :description, discount_value = :discount_value, discount_start = :discount_start, discount_end = :discount_end, visibility = :visibility, allow_comments = :allow_comments WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':discount_value', $discount_value);
            $stmt->bindParam(':discount_start', $discount_start);
            $stmt->bindParam(':discount_end', $discount_end);
            $stmt->bindParam(':visibility', $visibility, PDO::PARAM_INT);
            $stmt->bindParam(':allow_comments', $allow_comments, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                return ['success' => true, 'message' => 'Category updated successfully'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update category: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to update category'];
    }

    /**
     * Delete category
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Count total categories
     */
    public function countCategories() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
            $result = $stmt->fetch();
            return $result['count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Check if category has active discount
     */
    public function hasActiveDiscount($category_id) {
        $category = $this->getById($category_id);
        if (!$category || !$category['discount_value']) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        if ($category['discount_start'] && $now < $category['discount_start']) {
            return false;
        }
        if ($category['discount_end'] && $now > $category['discount_end']) {
            return false;
        }

        return true;
    }
}


