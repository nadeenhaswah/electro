<?php
session_start();
require_once '../config/database.php';

if (!isset($_POST['id'])) {
    header('Location: users.php');
    exit;
}

$id = $_POST['id'];

$sql = "DELETE FROM users WHERE user_id = ?";
$db->query($sql, [$id]);



$_SESSION['alert'] = [
    'type' => 'success',
    'title' => 'Deleted!',
    'text' => 'User has been deleted successfully.'
];

header('Location: users.php');
exit;
