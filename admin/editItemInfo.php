<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = [];

    // ========================
    // Collect Data
    // ========================
    $item_id        = (int)$_POST['item_id'];
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
    if (!$item_id) $errors[] = "Invalid item.";
    if (empty($name)) $errors[] = "Product name is required.";
    if (!is_numeric($price) || $price <= 0) $errors[] = "Invalid price.";
    if (!is_numeric($quantity) || $quantity < 0) $errors[] = "Invalid quantity.";
    if (empty($category_id)) $errors[] = "Category is required.";

    if (empty($errors)) {

        $db = DatabasePDO::getInstance();
        $db->query("START TRANSACTION");

        try {
            // ========================
            // Update Item
            // ========================
            $sql = "UPDATE items SET
                        name = ?,
                        description = ?,
                        price = ?,
                        discount_value = ?,
                        discount_start = ?,
                        discount_end = ?,
                        quantity = ?,
                        status = ?,
                        country_made = ?,
                        category_id = ?
                    WHERE item_id = ?";

            $params = [
                $name,
                $description,
                $price,
                $discount_value,
                $discount_start,
                $discount_end,
                $quantity,
                $status,
                $country_made,
                $category_id,
                $item_id
            ];

            $db->query($sql, $params);

            // ========================
            // Upload NEW Images (optional)
            // ========================
            if (!empty($_FILES['images']['name'][0])) {

                $uploadDir = "../uploads/items/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
                    if ($_FILES['images']['error'][$index] === 0) {

                        $ext = pathinfo($_FILES['images']['name'][$index], PATHINFO_EXTENSION);
                        $fileName = uniqid('item_') . '.' . $ext;
                        move_uploaded_file($tmpName, $uploadDir . $fileName);

                        $db->query(
                            "INSERT INTO item_images (item_id, image_path, is_main)
                             VALUES (?, ?, 0)",
                            [$item_id, $fileName]
                        );
                    }
                }
            }

            $db->query("COMMIT");

            $_SESSION['alert_edit'] = [
                'type' => 'success',
                'title' => 'Updated',
                'text' => 'Product updated successfully'
            ];

            header("Location: items.php");
            exit;
        } catch (Exception $e) {
            $db->query("ROLLBACK");
            echo "Error: " . $e->getMessage();
        }
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red'>$error</p>";
        }
    }
}
