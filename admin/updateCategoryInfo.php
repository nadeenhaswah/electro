<?php
session_start();
require_once '../config/database.php';

if (isset($_POST['submit'])) {

    // Sanitize
    $id              = htmlspecialchars(trim($_POST['id'])); // ID to update
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

    // Check for duplicate category name (excluding current category)
    $db = DatabasePDO::getInstance();
    $checkSql = "SELECT COUNT(*) as count FROM categories WHERE name = ? AND id != ?";
    $result = $db->query($checkSql, [$name, $id]);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($row['count'] > 0) {
        $errors[] = "This category already exists.";
    }

    // Final Result
    if (empty($errors)) {
        $sql = "UPDATE categories SET
                    name = ?,
                    description = ?,
                    discount_value = ?,
                    discount_start = ?,
                    discount_end = ?,
                    visibility = ?,
                    allow_comments = ?
                WHERE id = ?";

        $params = [
            $name,
            $description,
            $discount_value,
            $discount_start,
            $discount_end,
            $visibility,
            $allow_comments,
            $id
        ];
        $db->query($sql, $params);

        $_SESSION['alert_update'] = [
            'type' => 'success',
            'title' => 'Category Updated Successfully',
            'text' => 'The category has been updated in the system.'
        ];

        header("Location: categories.php");
        exit;
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
