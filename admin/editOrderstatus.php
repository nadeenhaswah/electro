<?php
session_start();
require_once '../config/database.php';

$db = DatabasePDO::getInstance();
if (isset($_POST['submit'])) {

    $id =  $_POST['order_id'];
    $status =  $_POST['status'];

    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $db->query($sql, [$status, $id]);

    $_SESSION['alert'] = [
        'type' => 'success',
        'title' => 'Updated!',
        'text' => 'Order status has been updated successfully.'
    ];

    header('Location: viewOrder.php?id=' . $id);
    exit;
}
