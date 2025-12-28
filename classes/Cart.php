<?php

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/classes/Database.php';

class Cart {
    private $db;
    private $table = "carts";
    private $itemsTable = "cart_items";

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getOrCreateCart($user_id = null, $session_id = null) {
        if (!$user_id && !$session_id) {
            return false;
        }

        try {
            $guestUserId = 0;
            $cart_user_id = $user_id ? $user_id : $guestUserId;
            
            if ($user_id) {
                $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND status = 'active' LIMIT 1";
                $param = $user_id;
                $type = "i";
            } else {
                $sql = "SELECT * FROM {$this->table} WHERE session_id = ? AND user_id = ? AND status = 'active' LIMIT 1";
                $param = $session_id;
                $type = "si";
            }

            $stmt = $this->db->prepare($sql);
            if ($user_id) {
                $stmt->bindParam(1, $param, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(1, $param, PDO::PARAM_STR);
                $stmt->bindParam(2, $guestUserId, PDO::PARAM_INT);
            }
            $stmt->execute();
            $cart = $stmt->fetch();

            if ($cart) {
                return $cart;
            }

            $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, session_id, status) VALUES (:user_id, :session_id, 'active')");
            $stmt->bindParam(':user_id', $cart_user_id, PDO::PARAM_INT);
            $stmt->bindParam(':session_id', $session_id);
            $stmt->execute();
            $cart_id = $this->db->lastInsertId();

            return ['cart_id' => $cart_id, 'user_id' => $cart_user_id, 'session_id' => $session_id, 'status' => 'active'];
        } catch (Exception $e) {
            return false;
        }
    }

    public function addItem($cart_id, $item_id, $quantity = 1) {
        require_once dirname(__DIR__) . '/classes/Product.php';
        $product = new Product();
        $item = $product->getById($item_id);
        
        if (!$item) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        $price = $product->getFinalPrice($item);
        $existing = $this->getCartItem($cart_id, $item_id);
        
        try {
            if ($existing) {
                // Update quantity
                $newQuantity = $existing['quantity'] + $quantity;
                $stmt = $this->db->prepare("UPDATE {$this->itemsTable} SET quantity = :quantity WHERE id = :id");
                $stmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
                $stmt->bindParam(':id', $existing['id'], PDO::PARAM_INT);
                $result = $stmt->execute();
            } else {
                // Add new item
                $stmt = $this->db->prepare("INSERT INTO {$this->itemsTable} (cart_id, item_id, quantity, price_at_time) VALUES (:cart_id, :item_id, :quantity, :price_at_time)");
                $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                $stmt->bindParam(':price_at_time', $price);
                $result = $stmt->execute();
            }

            if ($result) {
                return ['success' => true, 'message' => 'Item added to cart'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to add item: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to add item'];
    }

    public function getCartItem($cart_id, $item_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->itemsTable} WHERE cart_id = :cart_id AND item_id = :item_id LIMIT 1");
            $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getCartItems($cart_id) {
        try {
            $stmt = $this->db->prepare("SELECT ci.*, i.name, i.description FROM {$this->itemsTable} ci LEFT JOIN items i ON ci.item_id = i.item_id WHERE ci.cart_id = :cart_id");
            $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity($cart_item_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($cart_item_id);
        }

        try {
            $stmt = $this->db->prepare("UPDATE {$this->itemsTable} SET quantity = :quantity WHERE id = :id");
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':id', $cart_item_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    public function removeItem($cart_item_id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->itemsTable} WHERE id = :id");
            $stmt->bindParam(':id', $cart_item_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getCartTotal($cart_id) {
        $items = $this->getCartItems($cart_id);
        $total = 0;

        foreach ($items as $item) {
            $total += $item['price_at_time'] * $item['quantity'];
        }

        return $total;
    }

    public function clearCart($cart_id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->itemsTable} WHERE cart_id = :cart_id");
            $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Convert guest cart to user cart
     */
    public function convertGuestCart($session_id, $user_id) {
        try {
            // Get guest cart
            $guestCart = $this->getOrCreateCart(null, $session_id);
            if (!$guestCart) {
                return true;
            }

            // Get or create user cart
            $userCart = $this->getOrCreateCart($user_id, null);
            
            // Move items from guest cart to user cart
            $guestItems = $this->getCartItems($guestCart['cart_id']);
            
            foreach ($guestItems as $item) {
                $this->addItem($userCart['cart_id'], $item['item_id'], $item['quantity']);
            }

            // Mark guest cart as converted
            $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'converted' WHERE cart_id = :cart_id");
            $stmt->bindParam(':cart_id', $guestCart['cart_id'], PDO::PARAM_INT);
            $stmt->execute();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getCartCount($cart_id) {
        try {
            $stmt = $this->db->prepare("SELECT SUM(quantity) as count FROM {$this->itemsTable} WHERE cart_id = :cart_id");
            $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ? $result['count'] : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
}

