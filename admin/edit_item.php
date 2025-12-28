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
    <title>Edit Product - Electro Admin</title>

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

    <!-- نفس الأنماط من صفحة الإضافة -->
    <style>
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
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

        .existing-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .existing-image {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
        }

        .existing-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .existing-image .main-badge {
            position: absolute;
            top: 5px;
            left: 5px;
            background-color: var(--primary-color);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .existing-image .remove-btn {
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
                        <h1 class="h2">Edit Product</h1>
                        <p class="text-muted mb-0">Update product information</p>
                    </div>

                </div>

                <!-- Edit Product Form -->

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
                        <form id="editItemForm" method="POST" action="editItemInfo.php" class="needs-validation" enctype="multipart/form-data">
                            <div class="row">
                                <!-- Left Column: Basic Information -->
                                <div class="col-lg-8">
                                    <!-- Basic Information -->
                                    <div class="form-section">
                                        <h6 class="section-title">Basic Information</h6>
                                        <input type="hidden" name="item_id" value="<?= $product['id'] ?>">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="<?= $product['name'] ?>" name="name" required>
                                                <div class=" invalid-feedback">
                                                    Please enter product name.
                                                </div>
                                            </div>

                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
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
                                                    <input type="number" class="form-control" value="<?= $product['price'] ?>" name="price" required min="0" step="0.01" value="1299.99">
                                                </div>
                                                <div class="invalid-feedback">
                                                    Please enter price.
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Discount (%)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" value="<?= $product['discount'] ?>"
                                                        name="discount_value" min="0" max="100" step="0.01" value="10.00">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Discount Start Date</label>
                                                <input type="date" class="form-control" name="discount_start" value="<?= $product['discount_start'] ?>">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Discount End Date</label>
                                                <input type="date" class="form-control" name="discount_end" value="<?= $product['discount_end'] ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Inventory -->
                                    <div class="form-section">
                                        <h6 class="section-title">Inventory</h6>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" value="<?= $product['stock'] ?>" name="quantity" required min="0" value="15">
                                                <div class="invalid-feedback">
                                                    Please enter stock quantity.
                                                </div>
                                            </div>


                                        </div>
                                        <?php
                                        // --- Stock & Status ---
                                        if ($product['stock'] == 0) {
                                            $item_status = 'outofstock';
                                        } elseif ($product['stock'] <= 5) {
                                            $item_status = 'lowstock';
                                        } else {
                                            $item_status = 'instock';
                                        }

                                        ?>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="status">
                                                    <option <?= ($item_status == 'outofstock') ? 'selected' : '' ?>>Out of Stock</option>
                                                    <option <?= ($item_status == 'lowstock') ? 'selected' : '' ?>>Low Stock</option>
                                                    <option <?= ($item_status == 'instock') ? 'selected' : '' ?>>In Stock</option>
                                                </select>

                                            </div>




                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Country of Origin</label>
                                                <input type="text" class="form-control" vlalue="<?= $product['country_made'] ?>" name="country_made" value="USA">
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
                                                    <option value="<?= $category['id']; ?>" <?= ($category['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($category['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please select category.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Existing Product Images -->
                                    <div class="form-section">
                                        <h6 class="section-title">Current Images</h6>

                                        <div class="existing-images">
                                            <?php

                                            // print_r($images);

                                            foreach ($images as $index => $image) {
                                            ?>
                                                <div class="existing-image">
                                                    <img src="../uploads/items/<?php echo $image ?>" alt="Product Image">
                                                    <?php if ($image == $images[0]): ?>
                                                        <span class="main-badge">Main</span>
                                                    <?php endif; ?>
                                                    <div class="remove-btn" onclick="removeExistingImage(<?php echo $index; ?>)">
                                                        <i class="fas fa-times"></i>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!-- Add New Images -->
                                    <div class="form-section">
                                        <h6 class="section-title">Add New Images</h6>

                                        <div class="mb-3">
                                            <label class="form-label">Upload Additional Images</label>
                                            <div class="image-dropzone" id="imageDropzone">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <p class="mb-2">Drag & drop images here</p>
                                                <p class="text-muted small mb-0">or click to browse</p>
                                                <input type="file" id="imageUpload" name="images[]" multiple accept="image/*" style="display: none;">
                                            </div>
                                            <div class="form-text">
                                                Add more images to the product
                                            </div>
                                        </div>
                                <?php
                            endforeach;
                        endif;
                                ?>
                                <div class="image-preview-container" id="imagePreviewContainer">
                                    <!-- New image previews will be added here -->
                                </div>
                                    </div>


                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                                    Cancel
                                </button>
                                <button type="submit" name="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning: This action cannot be undone!</strong>
                    </div>
                    <p>Are you sure you want to delete this product?</p>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmProductDelete">
                        <label class="form-check-label" for="confirmProductDelete">
                            I understand this action cannot be undone
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image Upload Functionality (same as add page)
            const imageUpload = document.getElementById('imageUpload');
            const imageDropzone = document.getElementById('imageDropzone');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            let newUploadedImages = [];

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
                    newUploadedImages.push(file);
                }
            }

            function addImagePreview(src, filename) {
                const previewId = 'preview-' + Date.now();
                const preview = document.createElement('div');
                preview.className = 'image-preview';
                preview.id = previewId;
                preview.innerHTML = `
                    <img src="${src}" alt="${filename}">
                    <div class="remove-btn" onclick="removeNewImagePreview('${previewId}')">
                        <i class="fas fa-times"></i>
                    </div>
                `;
                imagePreviewContainer.appendChild(preview);
            }

            window.removeNewImagePreview = function(previewId) {
                const preview = document.getElementById(previewId);
                if (preview) {
                    preview.remove();
                    // Remove from newUploadedImages array
                    const index = newUploadedImages.findIndex(img => img.name === preview.querySelector('img').alt);
                    if (index > -1) {
                        newUploadedImages.splice(index, 1);
                    }
                }
            };

            window.removeExistingImage = function(imageId) {
                // In real application, send delete request to server
                console.log(`Removing existing image ${imageId}`);
                // Remove image element
                const imageElement = document.querySelector(`.existing-image img[src*="${imageId}"]`)?.closest('.existing-image');
                if (imageElement) {
                    imageElement.remove();
                }

            };

            // Delete Product
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                if (!document.getElementById('confirmProductDelete').checked) {
                    alert('Please confirm that you understand this action cannot be undone.');
                    return;
                }

                // In real application, send delete request to server
                alert('Product deleted successfully!');
                window.location.href = 'items.php';
            });

            // Form Validation
            const editItemForm = document.getElementById('editItemForm');
            editItemForm.addEventListener('submit', function(event) {
                if (!editItemForm.checkValidity()) {
                    event.stopPropagation();
                } else {
                    event.preventDefault();
                    // In real application, submit form via AJAX

                    // Collect form data
                    const formData = new FormData(editItemForm);
                    newUploadedImages.forEach((image, index) => {
                        formData.append('new_images[]', image);
                    });

                    // Simulate form submission
                    console.log('Updating product:', Object.fromEntries(formData));
                    alert('Product updated successfully!');
                    window.location.href = 'item_view.php?id=1';
                }
                editItemForm.classList.add('was-validated');
            });
        });
    </script> -->
</body>

</html>