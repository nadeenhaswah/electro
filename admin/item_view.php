<?php
require_once '../config/database.php';
session_start();
// في الواقع الحقيقي، يجب التحقق من صلاحية المستخدم
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] != 'admin') {
//     header('Location: login.php');
//     exit;
// }


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Product - Electro Admin</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Montserrat Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin-style.css">

    <style>
        .product-gallery {
            position: relative;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: contain;
            border-radius: 10px;
            background-color: var(--grey-lighter);
        }

        .thumbnail-container {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            overflow-x: auto;
            padding-bottom: 10px;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .thumbnail:hover {
            border-color: var(--primary-color);
        }

        .thumbnail.active {
            border-color: var(--primary-color);
            transform: scale(1.05);
        }

        .product-info-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .product-title {
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--grey-light);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--grey-medium);
            font-weight: 500;
        }

        .info-value {
            font-weight: 600;
            text-align: right;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .stat-card {
            text-align: center;
            padding: 15px;
            background-color: var(--grey-lighter);
            border-radius: 8px;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--grey-medium);
            margin-top: 5px;
        }

        .description-box {
            background-color: var(--grey-lighter);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php
    include('includes/header.php');
    ?>s

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php
            include('includes/sidebar.php');
            ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-5">
                <!-- Product Details -->
                <div class="row mt-4">
                    <?php
                    $id = $_GET['id'];
                    $params[] = $id;

                    $sql = "SELECT 
                                    i.item_id AS id,
                                    i.name,
                                    i.price,
                                    i.description,
                                    i.quantity,
                                    i.discount_value AS discount,
                                    i.quantity AS stock,
                                    i.category_id,
                                    i.discount_start,
                                    i.discount_end,
                                    i.add_date,
                                    i.country_made,
                                    i.status AS item_status,
                                    -- صورة رئيسية
                                    (SELECT GROUP_CONCAT(image_path SEPARATOR ',') 
                                    FROM item_images 
                                    WHERE item_id = i.item_id) AS images,
                                    -- اسم الفئة
                                    (SELECT name 
                                    FROM categories 
                                    WHERE id = i.category_id) AS category,
                                    -- متوسط التقييم (rating)
                                    (SELECT IFNULL(ROUND(AVG(rating),1), 0)
                                    FROM item_ratings 
                                    WHERE item_id = i.item_id) AS rating
                                FROM items i WHERE item_id=?;
                                ";

                    $result = $db->query($sql, $params);
                    $products = $result->fetchAll(PDO::FETCH_ASSOC);
                    if (count($products) > 0):
                        foreach ($products as $product):
                            $images = explode(',', $product['images']);
                            // print_r($images);

                    ?>
                            <div class="col-lg-6">
                                <!-- Product Images -->
                                <div class="product-gallery">

                                    <img src="../uploads//items/<?= $images[0] ?>"
                                        class="main-image" alt="Product" id="mainImage">

                                    <div class="thumbnail-container">
                                        <?php


                                        foreach ($images as $index => $image) {
                                            $imagePath = !empty($image) ? '../uploads/items/' . $image : '../uploads/items/placeholder.png';

                                            $activeClass = $index === 0 ? 'active' : '';
                                        ?>
                                            <img src="<?php echo $imagePath; ?>"
                                                class="thumbnail <?php echo $activeClass; ?>"
                                                alt="Thumbnail <?php echo $index + 1; ?>"
                                                onclick="changeMainImage('<?php echo $imagePath; ?>', this)">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="product-info-card">
                                    <div class="product-title">
                                        <h1 class="h3 mb-2"><?= $product['name'] ?></h1>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php
                                            // --- Stock & Status ---
                                            $stockClass = 'good-stock';
                                            $statusText = 'In Stock';
                                            $statusClass = 'status-instock';

                                            if ($product['stock'] == 0) {
                                                $stockClass = 'empty-stock';
                                                $statusText = 'Out of Stock';
                                                $statusClass = 'status-out';
                                            } elseif ($product['stock'] <= 5) { // أقل من 5 low
                                                $stockClass = 'low-stock';
                                                $statusText = 'Low Stock';
                                                $statusClass = 'status-low';
                                            }

                                            $stockPercent = $product['stock'] > 50 ? 100 : ($product['stock'] / 50 * 100);
                                            ?>
                                            <span class="badge bg-success"><?= $statusText; ?></span>
                                            <span class="badge bg-warning"><?= $product['discount'] ?>% OFF</span>
                                        </div>
                                    </div>

                                    <!-- Basic Information -->
                                    <div class="mb-4">
                                        <h5 class="mb-3">Product Information</h5>
                                        <div class="info-row">
                                            <span class="info-label">Product ID:</span>
                                            <span class="info-value">#<?= $product['id'] ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Category:</span>
                                            <span class="info-value"><?= $product['category'] ?></span>
                                        </div>
                                        <div class="info-row">
                                            <?php
                                            $discount = ($product['price'] * $product['discount']) / 100;
                                            $priceAfterDiscount = $product['price'] - $discount; ?>



                                            <span class="info-label">Price:</span>
                                            <span class="info-value">
                                                <strong class="text-success">$<?= $priceAfterDiscount ?></strong>
                                                <small class="text-muted ms-2"><del>$<?= $product['price']  ?></del></small>
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Discount:</span>
                                            <span class="info-value"><?= $product['discount'] ?>% (Until <?= $product['discount_end'] ?> )</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Stock Quantity:</span>
                                            <span class="info-value">
                                                <span class="badge bg-success"><?= $product['quantity'] ?> units</span>
                                            </span>
                                        </div>
                                        <?php
                                        // --- Rating ---
                                        $fullStars = floor($product['rating']);
                                        $halfStar = ($product['rating'] - $fullStars) >= 0.5;
                                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

                                        $ratingStarsHtml = '';
                                        for ($i = 0; $i < $fullStars; $i++) {
                                            $ratingStarsHtml .= '<i class="fas fa-star"></i>';
                                        }
                                        if ($halfStar) {
                                            $ratingStarsHtml .= '<i class="fas fa-star-half-alt"></i>';
                                        }
                                        for ($i = 0; $i < $emptyStars; $i++) {
                                            $ratingStarsHtml .= '<i class="far fa-star"></i>';
                                        }
                                        ?>
                                        <div class="info-row">
                                            <span class="info-label">Rating:</span>
                                            <span class="info-value">

                                                <span class="ms-2 text-warning"><?= $ratingStarsHtml . "  " . $product['rating']; ?></span>

                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Country of Origin:</span>
                                            <span class="info-value"><?= $product['country_made'] ?></span>
                                        </div>

                                        <div class="info-row">
                                            <span class="info-label">Added Date:</span>
                                            <span class="info-value"><?= $product['add_date'] ?></span>
                                        </div>

                                    </div>


                                </div>
                            </div>
                </div>

                <!-- Product Description -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Product Description</h5>
                            </div>
                            <div class="card-body">
                                        <p><?= $product['description'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>

        <?php endforeach;
                    endif; ?>
            </main>
        </div>
    </div>

    <script>
        function changeMainImage(src, element) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            element.classList.add('active');
        }
    </script>
</body>

</html>