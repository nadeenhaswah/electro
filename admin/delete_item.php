<?php
session_start();
require_once '../config/database.php';

if (!isset($_POST['id'])) {
    header('Location: items.php');
    exit;
}

$id = $_POST['id'];

$sql = "DELETE FROM items WHERE item_id = ?";
$db->query($sql, [$id]);



$_SESSION['alert'] = [
    'type' => 'success',
    'title' => 'Deleted!',
    'text' => 'Product has been deleted successfully.'
];

header('Location: items.php');
exit;
