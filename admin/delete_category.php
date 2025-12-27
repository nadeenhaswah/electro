<?php
session_start();
require_once '../config/database.php';

if (!isset($_POST['id'])) {
    header('Location: categories.php');
    exit;
}

$id = $_POST['id'];

$sql = "DELETE FROM categories WHERE id = ?";
$db->query($sql, [$id]);



$_SESSION['alert'] = [
    'type' => 'success',
    'title' => 'Deleted!',
    'text' => 'category has been deleted successfully.'
];

header('Location: categories.php');
exit;
