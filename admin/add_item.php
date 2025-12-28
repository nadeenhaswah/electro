<?php
session_start();
require_once 'config/database.php';

if (isset($_POST['submit'])) {

    $errors = [];

    // ========================
    // Sanitize & Collect Data
    // ========================
    $name           = trim($_POST['name']);
    $description    = trim($_POST['description']);
    $price          = $_POST['price'];
    $discount_value = $_POST['discount_value'] ?: null;
    $discount_start = $_POST['discount_start'] ?: null;
    $discount_end   = $_POST['discount_end'] ?: null;
    $quantity       = $_POST['quantity'];
    $status         = $_POST['status'];
    $country_made   = trim($_POST['country_made']);
    $category_id    = $_POST['category_id'];

    // ========================
    // Validation
    // ========================
    if (empty($name)) {
        $errors[] = "Product name is required.";
    }

    if (!is_numeric($price) || $price <= 0) {
        $errors[] = "Invalid product price.";
    }

    if (!is_numeric($quantity) || $quantity < 0) {
        $errors[] = "Invalid quantity.";
    }

    if (empty($category_id)) {
        $errors[] = "Category is required.";
    }

    if (empty($_FILES['images']['name'][0])) {
        $errors[] = "At least one product image is required.";
    }

    // ========================
    // If No Errors → Insert
    // ========================
    if (empty($errors)) {

        $db = DatabasePDO::getInstance();

        // 🔒 Transaction (مهم جدًا)
        $db->query("START TRANSACTION");

        try {
            // ========================
            // Insert Item
            // ========================
            $sql = "INSERT INTO items 
                    (name, description, price, discount_value, discount_start, discount_end,
                     country_made, status, category_id, quantity)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $name,
                $description,
                $price,
                $discount_value,
                $discount_start,
                $discount_end,
                $country_made,
                $status,
                $category_id,
                $quantity
            ];

            $db->query($sql, $params);

            // Get inserted item id
            $item_id = $db->query("SELECT LAST_INSERT_ID()")->fetchColumn();

            // ========================
            // Upload Images
            // ========================
            $uploadDir = "../uploads/items/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {

                if ($_FILES['images']['error'][$index] === 0) {

                    $ext = pathinfo($_FILES['images']['name'][$index], PATHINFO_EXTENSION);
                    $fileName = uniqid('item_') . '.' . $ext;
                    $filePath = $uploadDir . $fileName;

                    move_uploaded_file($tmpName, $filePath);

                    // First image = main
                    $is_main = ($index === 0) ? 1 : 0;

                    $db->query(
                        "INSERT INTO item_images (item_id, image_path, is_main)
                        VALUES (?, ?, ?)",
                        [$item_id, $fileName, $is_main]
                    );
                }
            }

            // ✅ Commit
            $db->query("COMMIT");

            $_SESSION['alert_add'] = [
                'type' => 'success',
                'title' => 'Product Added',
                'text' => 'Product added successfully with images.'
            ];

            header("Location: items.php");
            exit;
        } catch (Exception $e) {
            // ❌ Rollback if error
            $db->query("ROLLBACK");
            echo "Error: " . $e->getMessage();
        }
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
