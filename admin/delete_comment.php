<?php
session_start();
require_once '../config/database.php';

if (!isset($_POST['id'])) {
    header('Location: comments.php');
    exit;
}

$id = $_POST['id'];

$sql = "DELETE FROM comments WHERE comment_id = ?";
$db->query($sql, [$id]);



$_SESSION['alert'] = [
    'type' => 'success',
    'title' => 'Deleted!',
    'text' => 'Comments has been deleted successfully.'
];

header('Location: comments.php');
exit;
