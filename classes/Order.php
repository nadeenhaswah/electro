<?php
/**
 * Order Model
 * Handles order creation and management
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/classes/Database.php';

class Order {
    private $db;
    private $table = "orders";
    private $itemsTable = "order_items";

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create order from cart
     */
    public function createFromCart($user_id, $cart_id) {
        require_once dirname(__DIR__) . '/classes/Cart.php';
        $cart = new Cart();
        
        $cartItems = $cart->getCartItems($cart_id);
        if (empty($cartItems)) {
            return ['success' => false, 'message' => 'Cart is empty'];
        }

        $total = $cart->getCartTotal($cart_id);

        try {
            // Start transaction
            if ($this->db instanceof PDO) {
                $this->db->beginTransaction();
            } else {
                $this->db->autocommit(false);
            }

            // Create order
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, total_price, status) VALUES (:user_id, :total_price, :status)");
                $status = ORDER_PENDING;
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':total_price', $total);
                $stmt->bindParam(':status', $status);
                $stmt->execute();
                $order_id = $this->db->lastInsertId();
            } else {
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, total_price, status) VALUES (?, ?, ?)");
                $status = ORDER_PENDING;
                $stmt->bind_param("ids", $user_id, $total, $status);
                $stmt->execute();
                $order_id = $this->db->insert_id;
                $stmt->close();
            }

            // Add order items
            foreach ($cartItems as $item) {
                if ($this->db instanceof PDO) {
                    $stmt = $this->db->prepare("INSERT INTO {$this->itemsTable} (order_id, item_id, quantity, price) VALUES (:order_id, :item_id, :quantity, :price)");
                    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                    $stmt->bindParam(':item_id', $item['item_id'], PDO::PARAM_INT);
                    $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                    $stmt->bindParam(':price', $item['price_at_time']);
                    $stmt->execute();
                } else {
                    $stmt = $this->db->prepare("INSERT INTO {$this->itemsTable} (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiid", $order_id, $item['item_id'], $item['quantity'], $item['price_at_time']);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            // Clear cart
            $cart->clearCart($cart_id);

            // Mark cart as converted
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("UPDATE carts SET status = 'converted' WHERE cart_id = :cart_id");
                $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $this->db->prepare("UPDATE carts SET status = 'converted' WHERE cart_id = ?");
                $stmt->bind_param("i", $cart_id);
                $stmt->execute();
                $stmt->close();
            }

            // Commit transaction
            if ($this->db instanceof PDO) {
                $this->db->commit();
            } else {
                $this->db->commit();
                $this->db->autocommit(true);
            }

            return ['success' => true, 'message' => 'Order created successfully', 'order_id' => $order_id];
        } catch (Exception $e) {
            // Rollback transaction
            if ($this->db instanceof PDO) {
                $this->db->rollBack();
            } else {
                $this->db->rollback();
                $this->db->autocommit(true);
            }
            return ['success' => false, 'message' => 'Failed to create order: ' . $e->getMessage()];
        }
    }

    /**
     * Get order by ID
     */
    public function getById($order_id) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT o.*, u.first_name, u.last_name, u.email FROM {$this->table} o LEFT JOIN users u ON o.user_id = u.user_id WHERE o.order_id = :order_id LIMIT 1");
                $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetch();
            } else {
                $stmt = $this->db->prepare("SELECT o.*, u.first_name, u.last_name, u.email FROM {$this->table} o LEFT JOIN users u ON o.user_id = u.user_id WHERE o.order_id = ? LIMIT 1");
                $stmt->bind_param("i", $order_id);
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
     * Get order items
     */
    public function getOrderItems($order_id) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT oi.*, i.name, i.description FROM {$this->itemsTable} oi LEFT JOIN items i ON oi.item_id = i.item_id WHERE oi.order_id = :order_id");
                $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll();
            } else {
                $stmt = $this->db->prepare("SELECT oi.*, i.name, i.description FROM {$this->itemsTable} oi LEFT JOIN items i ON oi.item_id = i.item_id WHERE oi.order_id = ?");
                $stmt->bind_param("i", $order_id);
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
     * Get user orders
     */
    public function getUserOrders($user_id) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll();
            } else {
                $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->bind_param("i", $user_id);
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
     * Get all orders (admin)
     */
    public function getAllOrders($limit = null, $offset = 0) {
        try {
            $sql = "SELECT o.*, u.first_name, u.last_name, u.email FROM {$this->table} o LEFT JOIN users u ON o.user_id = u.user_id ORDER BY o.created_at DESC";
            
            if ($limit) {
                $sql .= " LIMIT ? OFFSET ?";
            }

            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare($sql);
                if ($limit) {
                    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
                    $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
                }
                $stmt->execute();
                return $stmt->fetchAll();
            } else {
                if ($limit) {
                    $stmt = $this->db->prepare($sql);
                    $stmt->bind_param("ii", $limit, $offset);
                } else {
                    $stmt = $this->db->prepare($sql);
                }
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
     * Update order status
     */
    public function updateStatus($order_id, $status) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("UPDATE {$this->table} SET status = :status WHERE order_id = :order_id");
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                return $stmt->execute();
            } else {
                $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE order_id = ?");
                $stmt->bind_param("si", $status, $order_id);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if user has purchased item (and completed payment)
     * Comments are only allowed after payment completion
     */
    public function hasUserPurchasedItem($user_id, $item_id) {
        try {
            // Check if user has order with this item AND payment is completed
            // Must verify: order exists, contains the item, and payment_status = 'completed'
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count 
                    FROM {$this->itemsTable} oi 
                    INNER JOIN {$this->table} o ON oi.order_id = o.order_id 
                    INNER JOIN payments p ON o.order_id = p.order_id
                    WHERE o.user_id = :user_id 
                    AND oi.item_id = :item_id 
                    AND p.payment_status = 'completed'");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch();
                return $result['count'] > 0;
            } else {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count 
                    FROM {$this->itemsTable} oi 
                    INNER JOIN {$this->table} o ON oi.order_id = o.order_id 
                    INNER JOIN payments p ON o.order_id = p.order_id
                    WHERE o.user_id = ? 
                    AND oi.item_id = ? 
                    AND p.payment_status = 'completed'");
                $stmt->bind_param("ii", $user_id, $item_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();
                return $row['count'] > 0;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get total income
     */
    public function getTotalIncome() {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->query("SELECT SUM(total_price) as total FROM {$this->table} WHERE status IN ('processing', 'shipped', 'completed')");
                $result = $stmt->fetch();
                return $result['total'] ? $result['total'] : 0;
            } else {
                $result = $this->db->query("SELECT SUM(total_price) as total FROM {$this->table} WHERE status IN ('processing', 'shipped', 'completed')");
                $row = $result->fetch_assoc();
                return $row['total'] ? $row['total'] : 0;
            }
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get daily revenue
     */
    public function getDailyRevenue($days = 7) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT DATE(created_at) as date, SUM(total_price) as revenue FROM {$this->table} WHERE status IN ('processing', 'shipped', 'completed') AND created_at >= DATE_SUB(NOW(), INTERVAL :days DAY) GROUP BY DATE(created_at) ORDER BY date DESC");
                $stmt->bindValue(':days', $days, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll();
            } else {
                $stmt = $this->db->prepare("SELECT DATE(created_at) as date, SUM(total_price) as revenue FROM {$this->table} WHERE status IN ('processing', 'shipped', 'completed') AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) GROUP BY DATE(created_at) ORDER BY date DESC");
                $stmt->bind_param("i", $days);
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
     * Count total orders
     */
    public function countOrders() {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
                $result = $stmt->fetch();
                return $result['count'];
            } else {
                $result = $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
                $row = $result->fetch_assoc();
                return $row['count'];
            }
        } catch (Exception $e) {
            return 0;
        }
    }
}

