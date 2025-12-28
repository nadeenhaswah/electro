<?php

/**
 * Payment Controller
 * Handles fake payment processing
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/classes/Payment.php';
require_once dirname(__DIR__) . '/classes/Order.php';
require_once dirname(__DIR__) . '/classes/ItemImage.php';

class PaymentController
{
    private $payment;
    private $order;
    private $itemImage;

    public function __construct()
    {
        $this->payment = new Payment();
        $this->order = new Order();
        $this->itemImage = new ItemImage();
    }

    /**
     * Process payment
     */
    public function maskCardNumber($cardNumber)
    {
        $last4 = substr($cardNumber, -4);
        return str_repeat('*', strlen($cardNumber) - 4) . $last4;
    }
    public function process()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'You must be logged in'];
        }

        $order_id = (int)($_POST['order_id'] ?? 0);
        $cardholder_name = sanitize($_POST['cardholder_name'] ?? '');
        $card_number = sanitize($_POST['card_number'] ?? '');
        $expiry_date = sanitize($_POST['expiry_date'] ?? '');
        $cvv = sanitize($_POST['cvv'] ?? '');
        $billing_address = sanitize($_POST['billing_address'] ?? '');
        $payment_method = sanitize($_POST['payment_method'] ?? 'credit_card');

        // Validate required fields
        if (empty($cardholder_name) || empty($card_number) || empty($expiry_date) || empty($cvv)) {
            return ['success' => false, 'message' => 'All payment fields are required'];
        }

        // Get order
        $order = $this->order->getById($order_id);
        if (!$order) {
            return ['success' => false, 'message' => 'Order not found'];
        }

        // Verify order belongs to user
        if ($order['user_id'] != $_SESSION['user_id']) {
            return ['success' => false, 'message' => 'Access denied'];
        }

        // Process payment
        $result = $this->payment->processPayment(
            $order_id,
            $cardholder_name,
            $card_number,
            $expiry_date,
            $cvv,
            $billing_address,
            $order['total_price'],
            $payment_method
        );

        return $result;
    }

    /**
     * Get payment invoice
     */
    public function invoice($order_id)
    {
        if (!isLoggedIn()) {
            return ['error' => 'You must be logged in'];
        }

        $order = $this->order->getById($order_id);

        if (!$order) {
            return ['error' => 'Order not found'];
        }

        // Verify order belongs to user (unless admin)
        if ($order['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
            return ['error' => 'Access denied'];
        }

        $payment = $this->payment->getByOrderId($order_id);
        $order['items'] = $this->order->getOrderItems($order_id);

        foreach ($order['items'] as &$item) {
            $mainImage = $this->itemImage->getMainImage($item['item_id']);
            $item['main_image'] = $mainImage ? $mainImage['image_path'] : null;
        }

        return [
            'order' => $order,
            'payment' => $payment
        ];
    }
}
