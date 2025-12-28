<?php
/**
 * Product Model (Items)
 * Handles product CRUD operations, search, and filtering
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/classes/Database.php';

class Product {
    private $db;
    private $table = "items";

    public $item_id;
    public $name;
    public $description;
    public $price;
    public $discount_value;
    public $discount_start;
    public $discount_end;
    public $country_made;
    public $status;
    public $category_id;
    public $quantity;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new product
     */
    public function create($name, $description, $price, $category_id, $quantity = 0, $discount_value = null, $discount_start = null, $discount_end = null, $country_made = null, $status = 'active') {
        if (empty($name) || empty($price) || empty($category_id)) {
            return ['success' => false, 'message' => 'Required fields are missing'];
        }

        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, description, price, category_id, quantity, discount_value, discount_start, discount_end, country_made, status) VALUES (:name, :description, :price, :category_id, :quantity, :discount_value, :discount_start, :discount_end, :country_made, :status)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
                $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                $stmt->bindParam(':discount_value', $discount_value);
                $stmt->bindParam(':discount_start', $discount_start);
                $stmt->bindParam(':discount_end', $discount_end);
                $stmt->bindParam(':country_made', $country_made);
                $stmt->bindParam(':status', $status);
                $result = $stmt->execute();
                $id = $this->db->lastInsertId();
            } else {
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, description, price, category_id, quantity, discount_value, discount_start, discount_end, country_made, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdiisssss", $name, $description, $price, $category_id, $quantity, $discount_value, $discount_start, $discount_end, $country_made, $status);
                $result = $stmt->execute();
                $id = $this->db->insert_id;
                $stmt->close();
            }

            if ($result) {
                return ['success' => true, 'message' => 'Product created successfully', 'id' => $id];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to create product: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to create product'];
    }

    /**
     * Get product by ID
     */
    public function getById($item_id) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT i.*, c.name as category_name FROM {$this->table} i LEFT JOIN categories c ON i.category_id = c.id WHERE i.item_id = :item_id LIMIT 1");
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetch();
            } else {
                $stmt = $this->db->prepare("SELECT i.*, c.name as category_name FROM {$this->table} i LEFT JOIN categories c ON i.category_id = c.id WHERE i.item_id = ? LIMIT 1");
                $stmt->bind_param("i", $item_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                return $result->fetch_assoc();
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get all products with optional filters
     */
    public function getAll($filters = [], $limit = null, $offset = 0) {
        try {
            $sql = "SELECT i.*, c.name as category_name FROM {$this->table} i LEFT JOIN categories c ON i.category_id = c.id WHERE 1=1";
            $params = [];
            $types = "";

            // Apply filters
            if (isset($filters['category_id']) && $filters['category_id']) {
                $sql .= " AND i.category_id = ?";
                $params[] = $filters['category_id'];
                $types .= "i";
            }

            if (isset($filters['search']) && $filters['search']) {
                $sql .= " AND (i.name LIKE ? OR i.description LIKE ?)";
                $search = "%{$filters['search']}%";
                $params[] = $search;
                $params[] = $search;
                $types .= "ss";
            }

            if (isset($filters['min_price']) && $filters['min_price'] !== '') {
                $sql .= " AND i.price >= ?";
                $params[] = $filters['min_price'];
                $types .= "d";
            }

            if (isset($filters['max_price']) && $filters['max_price'] !== '') {
                $sql .= " AND i.price <= ?";
                $params[] = $filters['max_price'];
                $types .= "d";
            }

            

            $sql .= " ORDER BY i.add_date DESC";

            if ($limit) {
                $sql .= " LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;
                $types .= "ii";
            }

            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare($sql);
                foreach ($params as $index => $param) {
                    $stmt->bindValue($index + 1, $param);
                }
                $stmt->execute();
                return $stmt->fetchAll();
            } else {
                $stmt = $this->db->prepare($sql);
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                return $result->fetch_all(MYSQLI_ASSOC);
                // print_r($result);
            }
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Update product
     */
    public function update($item_id, $name, $description, $price, $category_id, $quantity = null, $discount_value = null, $discount_start = null, $discount_end = null, $country_made = null, $status = 'active') {
        if (empty($name) || empty($price) || empty($category_id)) {
            return ['success' => false, 'message' => 'Required fields are missing'];
        }

        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("UPDATE {$this->table} SET name = :name, description = :description, price = :price, category_id = :category_id, quantity = :quantity, discount_value = :discount_value, discount_start = :discount_start, discount_end = :discount_end, country_made = :country_made, status = :status WHERE item_id = :item_id");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
                $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                $stmt->bindParam(':discount_value', $discount_value);
                $stmt->bindParam(':discount_start', $discount_start);
                $stmt->bindParam(':discount_end', $discount_end);
                $stmt->bindParam(':country_made', $country_made);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $result = $stmt->execute();
            } else {
                $stmt = $this->db->prepare("UPDATE {$this->table} SET name = ?, description = ?, price = ?, category_id = ?, quantity = ?, discount_value = ?, discount_start = ?, discount_end = ?, country_made = ?, status = ? WHERE item_id = ?");
                $stmt->bind_param("ssdiisssssi", $name, $description, $price, $category_id, $quantity, $discount_value, $discount_start, $discount_end, $country_made, $status, $item_id);
                $result = $stmt->execute();
                $stmt->close();
            }

            if ($result) {
                return ['success' => true, 'message' => 'Product updated successfully'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update product: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to update product'];
    }

    /**
     * Delete product
     */
    public function delete($item_id) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE item_id = :item_id");
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                return $stmt->execute();
            } else {
                $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE item_id = ?");
                $stmt->bind_param("i", $item_id);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Calculate final price with discount
     */
    public function getFinalPrice($product) {
        $price = $product['price'];
        $discountValue = $product['discount_value'] ?? 0;
        
        if ($discountValue <= 0) {
            return $price;
        }
        
        $now = date('Y-m-d H:i:s');
        $discountStart = $product['discount_start'] ?? null;
        $discountEnd = $product['discount_end'] ?? null;
        
        $isDiscountActive = true;
        if ($discountStart && $now < $discountStart) {
            $isDiscountActive = false;
        }
        if ($discountEnd && $now > $discountEnd) {
            $isDiscountActive = false;
        }
        
        if ($isDiscountActive) {
            $discountAmount = $price * ($discountValue / 100);
            $price = $price - $discountAmount;
        }
        
        return $price;
    }

    /**
     * Get product quantity (inventory)
     */
    public function getQuantity($item_id) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT quantity FROM {$this->table} WHERE item_id = :item_id LIMIT 1");
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch();
                return $result ? (int)$result['quantity'] : 0;
            } else {
                $stmt = $this->db->prepare("SELECT quantity FROM {$this->table} WHERE item_id = ? LIMIT 1");
                $stmt->bind_param("i", $item_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();
                return $row ? (int)$row['quantity'] : 0;
            }
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Check if product is in stock
     */
    public function isInStock($item_id, $quantity_needed = 1) {
        $available_quantity = $this->getQuantity($item_id);
        return $available_quantity >= $quantity_needed;
    }

    /**
     * Update product quantity (inventory management)
     */
    public function updateQuantity($item_id, $quantity) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("UPDATE {$this->table} SET quantity = :quantity WHERE item_id = :item_id");
                $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                return $stmt->execute();
            } else {
                $stmt = $this->db->prepare("UPDATE {$this->table} SET quantity = ? WHERE item_id = ?");
                $stmt->bind_param("ii", $quantity, $item_id);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Decrease product quantity (when item is purchased)
     */
    public function decreaseQuantity($item_id, $quantity_to_decrease) {
        $current_quantity = $this->getQuantity($item_id);
        $new_quantity = max(0, $current_quantity - $quantity_to_decrease);
        return $this->updateQuantity($item_id, $new_quantity);
    }

    /**
     * Increase product quantity (restock)
     */
    public function increaseQuantity($item_id, $quantity_to_increase) {
        $current_quantity = $this->getQuantity($item_id);
        $new_quantity = $current_quantity + $quantity_to_increase;
        return $this->updateQuantity($item_id, $new_quantity);
    }
}


