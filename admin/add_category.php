<?php
session_start();
require_once 'config/database.php';

if (isset($_POST['submit'])) {

    // Sanitize
    $name            = htmlspecialchars(trim($_POST['name']));
    $description     = htmlspecialchars(trim($_POST['description']));
    $discount_value  = htmlspecialchars(trim($_POST['discount_value']));
    $discount_start  = htmlspecialchars(trim($_POST['discount_start']));
    $discount_end    = htmlspecialchars(trim($_POST['discount_end']));
    $visibility      = isset($_POST['visibility']) ? 1 : 0;
    $allow_comments  = isset($_POST['allow_comments']) ? 1 : 0;

    $errors = [];

    // Name Validation
    if (empty($name)) {
        $errors[] = "Category name is required.";
    }

    // Discount Validation
    if (!empty($discount_value) && !is_numeric($discount_value)) {
        $errors[] = "Discount value must be a number.";
    }

    // Date Validation
    if (!empty($discount_start) && !strtotime($discount_start)) {
        $errors[] = "Discount start date is invalid.";
    }
    if (!empty($discount_end) && !strtotime($discount_end)) {
        $errors[] = "Discount end date is invalid.";
    }

    // Check for duplicate category name
    $db = DatabasePDO::getInstance();
    $checkSql = "SELECT COUNT(*) as count FROM categories WHERE name = ?";
    $result = $db->query($checkSql, [$name]);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($row['count'] > 0) {
        $errors[] = "This category already exists.";
    }

    // Final Result
    if (empty($errors)) {
        $sql = "INSERT INTO categories 
                (name, description, discount_value, discount_start, discount_end, visibility, allow_comments)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $params = [$name, $description, $discount_value, $discount_start, $discount_end, $visibility, $allow_comments];
        $db->query($sql, $params);

        $_SESSION['alert_add'] = [
            'type' => 'success',
            'title' => 'Category Added Successfully',
            'text' => 'The category has been added to the system.'
        ];

        header("Location: categories.php");
        exit;

    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
