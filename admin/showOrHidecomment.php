<?php
session_start();
require_once 'config/database.php';

$db = DatabasePDO::getInstance();
if (isset($_POST['show'])) {

    $id = (int) $_POST['id'];

    $sql = "UPDATE comments SET status = 1 WHERE comment_id = ?";
    $db->query($sql, [$id]);

    $_SESSION['alert_show'] = [
        'type' => 'success',
        'title' => 'Shown!',
        'text' => 'Comment has been shown successfully.'
    ];

    header('Location: comments.php');
    exit;
}

if (isset($_POST['hide'])) {

    $id = (int) $_POST['id'];

    $sql = "UPDATE comments SET status = 0 WHERE comment_id = ?";
    $db->query($sql, [$id]);

    $_SESSION['alert_hide'] = [
        'type' => 'success',
        'title' => 'Hidden!',
        'text' => 'Comment has been hidden successfully.'
    ];

    header('Location: comments.php');
    exit;
}
