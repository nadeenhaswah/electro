<?php
/**
 * Payment Model
 * Handles fake payment processing
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/classes/Database.php';

class Payment {
    private $db;
    private $table = "payments";

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Process payment (fake)
     */
    public function processPayment($order_id, $cardholder_name, $card_number, $expiry_date, $cvv, $billing_address, $amount, $payment_method = 'credit_card') {
        // Fake payment - always succeeds
        $payment_status = PAYMENT_COMPLETED;

        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (order_id, cardholder_name, card_number, expiry_date, cvv, billing_address, amount, payment_method, payment_status) VALUES (:order_id, :cardholder_name, :card_number, :expiry_date, :cvv, :billing_address, :amount, :payment_method, :payment_status)");
            $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $stmt->bindParam(':cardholder_name', $cardholder_name);
            $stmt->bindParam(':card_number', $card_number);
            $stmt->bindParam(':expiry_date', $expiry_date);
            $stmt->bindParam(':cvv', $cvv);
            $stmt->bindParam(':billing_address', $billing_address);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':payment_method', $payment_method);
            $stmt->bindParam(':payment_status', $payment_status);
            $result = $stmt->execute();
            $payment_id = $this->db->lastInsertId();

            if ($result) {
                // Update order status to processing
                require_once dirname(__DIR__) . '/classes/Order.php';
                $order = new Order();
                $order->updateStatus($order_id, ORDER_PROCESSING);

                return ['success' => true, 'message' => 'Payment processed successfully', 'payment_id' => $payment_id];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Payment failed: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Payment failed'];
    }

    /**
     * Get payment by order ID
     */
    public function getByOrderId($order_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE order_id = :order_id LIMIT 1");
            $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get payment by ID
     */
    public function getById($payment_id) {
        try {
            $stmt = $this->db->prepare("SELECT p.*, o.*, u.first_name, u.last_name, u.email FROM {$this->table} p LEFT JOIN orders o ON p.order_id = o.order_id LEFT JOIN users u ON o.user_id = u.user_id WHERE p.payment_id = :payment_id LIMIT 1");
            $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Mask card number for display
     */
    public function maskCardNumber($card_number) {
        if (strlen($card_number) <= 4) {
            return $card_number;
        }
        return str_repeat('*', strlen($card_number) - 4) . substr($card_number, -4);
    }
}


