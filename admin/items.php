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
    <title>Manage Products - Electro Admin</title>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-image-lg {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }

        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .image-preview {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 24px;
            height: 24px;
            background-color: rgba(220, 53, 69, 0.9);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-available {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .status-outofstock {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .status-lowstock {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .discount-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .rating-stars {
            color: #ffc107;
            font-size: 0.9rem;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .search-container {
            max-width: 300px;
        }

        .filter-dropdown {
            min-width: 200px;
        }

        .form-section {
            border-bottom: 1px solid var(--grey-light);
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title {
            color: var(--headers-color);
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--primary-color);
        }

        .image-dropzone {
            border: 2px dashed var(--grey-light);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .image-dropzone:hover {
            border-color: var(--primary-color);
            background-color: rgba(209, 0, 36, 0.05);
        }

        .image-dropzone i {
            font-size: 3rem;
            color: var(--grey-medium);
            margin-bottom: 15px;
        }

        .stock-indicator {
            height: 8px;
            border-radius: 4px;
            background-color: var(--grey-light);
            overflow: hidden;
        }

        .stock-progress {
            height: 100%;
            transition: width 0.3s ease;
        }

        .low-stock {
            background-color: #ffc107;
        }

        .good-stock {
            background-color: #28a745;
        }

        .empty-stock {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php
    include('includes/header.php');
    ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php
            include('includes/sidebar.php');
            ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-5">
                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">Manage Products</h1>
                        <p class="text-muted mb-0">View, add, edit, or delete products</p>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="fas fa-plus me-2"></i>Add New Product
                        </button>
                    </div>
                </div>

                <!-- Search and Filter Bar -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="input-group search-container">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search products by name, SKU, or description..." id="searchInput">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                                <div class="dropdown-menu filter-dropdown">
                                    <h6 class="dropdown-header">Filter by Category</h6>
                                    <div class="px-3 py-2">
                                        <select class="form-select form-select-sm" id="categoryFilter">
                                            <option value="">All Categories</option>
                                            <option value="1">Laptops</option>
                                            <option value="2">Smartphones</option>
                                            <option value="3">Cameras</option>
                                            <option value="4">Accessories</option>
                                        </select>
                                    </div>
                                    <h6 class="dropdown-header">Filter by Status</h6>
                                    <div class="px-3 py-2">
                                        <select class="form-select form-select-sm" id="statusFilter">
                                            <option value="">All Status</option>
                                            <option value="available">Available</option>
                                            <option value="outofstock">Out of Stock</option>
                                            <option value="lowstock">Low Stock</option>
                                        </select>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <button class="dropdown-item text-danger" id="clearFilters">
                                        <i class="fas fa-times me-2"></i>Clear All Filters
                                    </button>
                                </div>
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-sort me-2"></i>Sort
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item sort-option" href="#" data-sort="name-asc">
                                        <i class="fas fa-sort-alpha-down me-2"></i>Name A-Z
                                    </a>
                                    <a class="dropdown-item sort-option" href="#" data-sort="name-desc">
                                        <i class="fas fa-sort-alpha-down-alt me-2"></i>Name Z-A
                                    </a>
                                    <a class="dropdown-item sort-option" href="#" data-sort="price-asc">
                                        <i class="fas fa-sort-numeric-down me-2"></i>Price Low-High
                                    </a>
                                    <a class="dropdown-item sort-option" href="#" data-sort="price-desc">
                                        <i class="fas fa-sort-numeric-down-alt me-2"></i>Price High-Low
                                    </a>
                                    <a class="dropdown-item sort-option" href="#" data-sort="date-new">
                                        <i class="fas fa-calendar-alt me-2"></i>Newest First
                                    </a>
                                    <a class="dropdown-item sort-option" href="#" data-sort="date-old">
                                        <i class="fas fa-calendar me-2"></i>Oldest First
                                    </a>
                                </div>
                            </div>

                            <button class="btn btn-outline-secondary" id="exportBtn">
                                <i class="fas fa-download me-2"></i>Export
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Products List</h5>
                        <span class="badge bg-primary" id="productCount">Total: 42 products</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="productsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">Pic</th>
                                        <th>Product Name</th>
                                        <th>Discount</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Rating</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php


                                    $sql = "SELECT 
                                    i.item_id AS id,
                                    i.name,
                                    i.price,
                                    i.discount_value AS discount,
                                    i.quantity AS stock,
                                    i.category_id,
                                    i.status AS item_status,
                                    -- صورة رئيسية
                                    (SELECT image_path 
                                    FROM item_images 
                                    WHERE item_id = i.item_id AND is_main = 1 
                                    LIMIT 1) AS image,
                                    -- اسم الفئة
                                    (SELECT name 
                                    FROM categories 
                                    WHERE id = i.category_id) AS category,
                                    -- متوسط التقييم (rating)
                                    (SELECT IFNULL(ROUND(AVG(rating),1), 0)
                                    FROM item_ratings 
                                    WHERE item_id = i.item_id) AS rating
                                FROM items i;
                                ";

                                    $result = $db->query($sql);
                                    $products = $result->fetchAll(PDO::FETCH_ASSOC);
                                    // print_r($products);
                                    if (count($products) > 0):
                                        foreach ($products as $product) :
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

                                            // --- Discount Badge ---
                                            $discountBadge = '';
                                            if ($product['discount'] > 0) {
                                                $discountBadge = '<span class="badge bg-success ms-1">-' . $product['discount'] . '%</span>';
                                            }

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

                                            // --- Image path ---
                                            $imagePath = !empty($product['image']) ? '../uploads/items/' . $product['image'] : '../uploads/items/placeholder.png';
                                    ?>
                                            <tr data-product-id="<?php echo $product['id']; ?>">
                                                <td>
                                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                                </td>
                                                <td>
                                                    <code><?php echo $discountBadge; ?></code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($product['category']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <?php
                                                        $discount = ($product['price'] * $product['discount']) / 100;
                                                        $priceAfterDiscount = $product['price'] - $discount; ?>
                                                        <strong class="<?php echo $product['discount'] > 0 ? 'text-success' : ''; ?>">
                                                            $<?php echo number_format($priceAfterDiscount, 2); ?>
                                                        </strong>

                                                        <?php if ($product['discount'] > 0): ?>
                                                            <small class="text-muted">
                                                                <del>$<?php echo $product['price'] ?></del>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span><?php echo $product['stock']; ?></span>
                                                        <div class="stock-indicator" style="width: 60px;">
                                                            <div class="stock-progress <?php echo $stockClass; ?>" style="width: <?php echo $stockPercent; ?>%;"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                                </td>
                                                <td>
                                                    <div class="rating-stars">
                                                        <?php echo $ratingStarsHtml; ?>
                                                        <small class="text-muted ms-1">(<?php echo $product['rating']; ?>)</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="item_view.php?id=<?php echo $product['id']; ?>" class="btn btn-action btn-outline-primary" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit_item.php?id=<?php echo $product['id']; ?>" class="btn btn-action btn-outline-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button
                                                            class="btn btn-sm btn-outline-danger btn-action btn-delete"
                                                            title="Delete"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal"
                                                            data-id="<?= $product['id']; ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php
                                        endforeach;
                                    endif;


                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing 1 to 6 of 42 products
                                </div>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination mb-0">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#">Previous</a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="#">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addItemForm" class="needs-validation" method="POST" action="add_item.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Left Column: Basic Information -->
                            <div class="col-lg-8">
                                <!-- Basic Information -->
                                <div class="form-section">
                                    <h6 class="section-title">Basic Information</h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" required placeholder="Enter product name">
                                            <div class="invalid-feedback">
                                                Please enter product name.
                                            </div>
                                        </div>


                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="4" placeholder="Enter product description"></textarea>
                                    </div>
                                </div>

                                <!-- Pricing -->
                                <div class="form-section">
                                    <h6 class="section-title">Pricing</h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Price <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" name="price" required min="0" step="0.01" placeholder="0.00">
                                            </div>
                                            <div class="invalid-feedback">
                                                Please enter price.
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Discount (%)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="discount_value" min="0" max="100" step="0.01" placeholder="0.00">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Discount Start Date</label>
                                            <input type="date" class="form-control" name="discount_start">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Discount End Date</label>
                                            <input type="date" class="form-control" name="discount_end">
                                        </div>
                                    </div>
                                </div>

                                <!-- Inventory -->
                                <div class="form-section">
                                    <h6 class="section-title">Inventory</h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="quantity" required min="0" placeholder="0">
                                            <div class="invalid-feedback">
                                                Please enter stock quantity.
                                            </div>
                                        </div>


                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status" required>
                                                <option value="available" selected>Available</option>
                                                <option value="outofstock">Out of Stock</option>
                                                <option value="lowstock">Low Stock</option>
                                                <option value="instock">In Stock</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Country of Origin</label>
                                            <input type="text" class="form-control" name="country_made" placeholder="e.g., USA, China">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Category & Images -->
                            <div class="col-lg-4">
                                <!-- Category -->
                                <div class="form-section">
                                    <h6 class="section-title">Category</h6>

                                    <div class="mb-3">
                                        <label class="form-label">Main Category <span class="text-danger">*</span></label>
                                        <select class="form-select" name="category_id" required>
                                            <?php
                                            $sql = "SELECT id, name FROM categories WHERE visibility = 1";
                                            $result = $db->query($sql);
                                            $categories = $result->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($categories as $category): ?>
                                                <option value="<?= $category['id']; ?>">
                                                    <?= htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select category.
                                        </div>
                                    </div>


                                </div>

                                <!-- Product Images -->
                                <div class="form-section">
                                    <h6 class="section-title">Product Images</h6>

                                    <div class="mb-3">
                                        <label class="form-label">Upload Images <span class="text-danger">*</span></label>
                                        <div class="image-dropzone" id="imageDropzone">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p class="mb-2">Drag & drop images here</p>
                                            <p class="text-muted small mb-0">or click to browse</p>
                                            <input type="file" class="d-none" id="imageUpload" name="images[]" multiple accept="image/*">
                                        </div>
                                        <div class="form-text">
                                            Upload multiple images. First image will be main.
                                        </div>
                                    </div>

                                    <div class="image-preview-container" id="imagePreviewContainer">
                                        <!-- Image previews will be added here -->
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form method="POST" action="delete_item.php">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center">
                        <input type="hidden" name="id" id="delete-id">
                        <p class="mb-0">Are you sure you want to delete this user?</p>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            Yes, Delete
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Admin JS -->
    <script src="assets/js/admin.js"></script>


    <?php

    if (isset($_SESSION['alert_add'])):
        $alert = $_SESSION['alert_add'];
    ?>
        <script>
            Swal.fire({
                icon: '<?= $alert['type']; ?>',
                title: '<?= $alert['title']; ?>',
                text: '<?= $alert['text']; ?>',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    <?php
        unset($_SESSION['alert_add']);
    endif;
    ?>
    <?php

    if (isset($_SESSION['alert_edit'])):
        $alert = $_SESSION['alert_edit'];
    ?>
        <script>
            Swal.fire({
                icon: '<?= $alert['type']; ?>',
                title: '<?= $alert['title']; ?>',
                text: '<?= $alert['text']; ?>',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    <?php
        unset($_SESSION['alert_edit']);
    endif;
    ?>
    <?php

    if (isset($_SESSION['alert'])):
        $alert = $_SESSION['alert'];
    ?>
        <script>
            Swal.fire({
                icon: '<?= $alert['type']; ?>',
                title: '<?= $alert['title']; ?>',
                text: '<?= $alert['text']; ?>',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    <?php
        unset($_SESSION['alert']);
    endif;
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image Upload Functionality
            const imageUpload = document.getElementById('imageUpload');
            const imageDropzone = document.getElementById('imageDropzone');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            let uploadedImages = [];

            // Open file browser on dropzone click
            imageDropzone.addEventListener('click', function() {
                imageUpload.click();
            });

            // Handle file selection
            imageUpload.addEventListener('change', function(e) {
                handleFiles(e.target.files);
            });

            // Handle drag and drop
            imageDropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.style.borderColor = 'var(--primary-color)';
                this.style.backgroundColor = 'rgba(209, 0, 36, 0.05)';
            });

            imageDropzone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.style.borderColor = '';
                this.style.backgroundColor = '';
            });

            imageDropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.style.borderColor = '';
                this.style.backgroundColor = '';

                const files = e.dataTransfer.files;
                handleFiles(files);
            });

            function handleFiles(files) {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (!file.type.startsWith('image/')) continue;

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        addImagePreview(e.target.result, file.name);
                    };
                    reader.readAsDataURL(file);
                }
            }

            function addImagePreview(src, filename) {
                const previewId = 'preview-' + Date.now();
                const preview = document.createElement('div');
                preview.className = 'image-preview';
                preview.id = previewId;
                preview.innerHTML = `
                    <img src="${src}" alt="${filename}">
                    <div class="remove-btn" onclick="removeImagePreview('${previewId}')">
                        <i class="fas fa-times"></i>
                    </div>
                `;
                imagePreviewContainer.appendChild(preview);
            }

            window.removeImagePreview = function(previewId) {
                const preview = document.getElementById(previewId);
                if (preview) {
                    preview.remove();

                }
            };

            // Delete Product
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const productName = this.getAttribute('data-product-name');

                    document.getElementById('deleteProductName').textContent = productName;
                    document.getElementById('confirmDeleteProductBtn').setAttribute('data-product-id', productId);

                    const modal = new bootstrap.Modal(document.getElementById('deleteProductModal'));
                    modal.show();
                });
            });

            // Confirm Delete
            document.getElementById('confirmDeleteProductBtn').addEventListener('click', function() {
                if (!document.getElementById('confirmProductDelete').checked) {
                    return;
                }

                const productId = this.getAttribute('data-product-id');

                // In real application, send delete request to server
                console.log(`Deleting product ${productId}`);

                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteProductModal'));
                modal.hide();

                // Remove product row from table
                const productRow = document.querySelector(`[data-product-id="${productId}"]`);
                if (productRow) {
                    productRow.remove();
                    updateProductCount();
                }
            });

            // Search Functionality
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');

            searchInput.addEventListener('input', function() {
                filterProducts();
            });

            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                filterProducts();
            });

            // Filter Functionality
            const categoryFilter = document.getElementById('categoryFilter');
            const statusFilter = document.getElementById('statusFilter');
            const clearFilters = document.getElementById('clearFilters');

            categoryFilter.addEventListener('change', filterProducts);
            statusFilter.addEventListener('change', filterProducts);

            clearFilters.addEventListener('click', function() {
                categoryFilter.value = '';
                statusFilter.value = '';
                filterProducts();
            });

            // Sort Functionality
            document.querySelectorAll('.sort-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sortType = this.getAttribute('data-sort');
                    sortProducts(sortType);

                    // Update active sort indicator
                    document.querySelectorAll('.sort-option').forEach(opt => {
                        opt.classList.remove('active');
                    });
                    this.classList.add('active');
                });
            });




            function filterProducts() {
                const searchTerm = searchInput.value.toLowerCase();
                const category = categoryFilter.value;
                const status = statusFilter.value;

                document.querySelectorAll('#productsTable tbody tr').forEach(row => {
                    const name = row.cells[1].textContent.toLowerCase();
                    const sku = row.cells[2].textContent.toLowerCase();
                    const rowCategory = row.cells[3].textContent;
                    const rowStatus = row.cells[6].textContent.toLowerCase();

                    const matchesSearch = searchTerm === '' ||
                        name.includes(searchTerm) ||
                        sku.includes(searchTerm);

                    const matchesCategory = category === '' ||
                        rowCategory.toLowerCase().includes(category.toLowerCase());

                    const matchesStatus = status === '' ||
                        rowStatus.includes(status.toLowerCase());

                    row.style.display = matchesSearch && matchesCategory && matchesStatus ? '' : 'none';
                });

                updateProductCount();
            }

            function sortProducts(sortType) {
                const rows = Array.from(document.querySelectorAll('#productsTable tbody tr'));
                const tbody = document.querySelector('#productsTable tbody');

                rows.sort((a, b) => {
                    const aName = a.cells[1].textContent;
                    const bName = b.cells[1].textContent;
                    const aPrice = parseFloat(a.cells[4].querySelector('strong').textContent.replace('$', ''));
                    const bPrice = parseFloat(b.cells[4].querySelector('strong').textContent.replace('$', ''));
                    const aStock = parseInt(a.cells[5].querySelector('span').textContent);
                    const bStock = parseInt(b.cells[5].querySelector('span').textContent);

                    switch (sortType) {
                        case 'name-asc':
                            return aName.localeCompare(bName);
                        case 'name-desc':
                            return bName.localeCompare(aName);
                        case 'price-asc':
                            return aPrice - bPrice;
                        case 'price-desc':
                            return bPrice - aPrice;
                        case 'date-new':
                            // For demo, using ID as date proxy
                            return parseInt(b.getAttribute('data-product-id')) - parseInt(a.getAttribute('data-product-id'));
                        case 'date-old':
                            return parseInt(a.getAttribute('data-product-id')) - parseInt(b.getAttribute('data-product-id'));
                        default:
                            return 0;
                    }
                });

                // Reorder rows
                rows.forEach(row => tbody.appendChild(row));
            }

            function updateProductCount() {
                const visibleRows = document.querySelectorAll('#productsTable tbody tr[style=""]').length;
                const totalRows = document.querySelectorAll('#productsTable tbody tr').length;
                document.getElementById('productCount').textContent = `Showing: ${visibleRows} of ${totalRows} products`;
            }



            // Initialize
            updateProductCount();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const deleteModal = document.getElementById('deleteModal');

        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            document.getElementById('delete-id').value = id;
        });
    </script>
</body>

</html>