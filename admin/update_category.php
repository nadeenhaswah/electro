<?php
require_once 'config/database.php';
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
    <title>Edit Categories - Electro Admin</title>

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
        .category-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            border-radius: 10px;
            color: white;
        }

        .category-card {
            transition: all 0.3s ease;
            border: 1px solid var(--grey-light);
            border-radius: 10px;
            overflow: hidden;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
        }

        .category-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .status-inactive {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary-color);
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .product-count {
            font-size: 0.9rem;
            color: var(--grey-medium);
        }

        .category-actions {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .category-card:hover .category-actions {
            opacity: 1;
        }

        .form-section-title {
            border-left: 4px solid var(--primary-color);
            padding-left: 15px;
            margin-bottom: 20px;
            margin-top: 30px;
        }

        .icon-picker {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .icon-option {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background-color: var(--grey-lighter);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .icon-option:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .icon-option.selected {
            background-color: var(--primary-color);
            color: white;
        }

        .color-picker {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .color-option {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .color-option:hover {
            transform: scale(1.1);
        }

        .color-option.selected {
            border-color: var(--dark-color);
            transform: scale(1.1);
        }

        .discount-period {
            background-color: var(--grey-lighter);
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--grey-medium);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--grey-light);
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php
    include('includes/header.php');
    ?>
    <!-- Main Container -->




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
                        <h1 class="h2">Edit Categories</h1>
                        <p class="text-muted mb-0">Manage product categories and their settings</p>
                    </div>

                </div>

                <form id="addCategoryForm" class="needs-validation" method="POST" action="updateCategoryInfo.php">
                    <div class="modal-body">
                        <!-- Basic Information -->
                        <h5 class="form-section-title">Basic Information</h5>
                        <?php

                        $id = $_GET['id'];
                        $params[] = $id;
                        $sql = "SELECT * FROM categories WHERE id =?";
                        $result = $db->query($sql, $params);
                        $categories = $result->fetchAll(PDO::FETCH_ASSOC);
                        // print_r($users);
                        if (count($categories) > 0):
                            foreach ($categories as $category) :

                        ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <input type="hidden" class="form-control" name="id"
                                            value="<?= $category['id'] ?>" required>

                                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            value="<?= $category['name'] ?>" required>

                                    </div>

                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3" placeholder="Brief description of this category"><?= $category['description'] ?>
                                </textarea>
                                </div>



                                <!-- Settings -->
                                <h5 class="form-section-title">Category Settings</h5>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="visibility" id="visibilitySwitch"
                                                <?= $category['visibility'] ? 'checked' : '' ?>> <label class="form-check-label" for="visibilitySwitch">
                                                Visible to customers
                                            </label>
                                        </div>
                                        <div class="form-text">If disabled, category will be hidden from store</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="allow_comments" id="commentsSwitch"
                                                <?= $category['allow_comments'] ? 'checked' : '' ?>> <label class="form-check-label" for="commentsSwitch">
                                                Allow product comments
                                            </label>
                                        </div>
                                        <div class="form-text">Enable/disable comments for products in this category</div>
                                    </div>
                                </div>

                                <!-- Discount Settings -->
                                <h5 class="form-section-title">Discount Settings (Optional)</h5>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Discount Value</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" value="<?= $category['discount_value'] ?>"
                                                name="discount_value" min="0" max="100" step="0.01" placeholder="0.00">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <div class="form-text">Percentage discount for all products in this category</div>
                                    </div>


                                </div>

                                <div class="discount-period">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Discount Start Date</label>
                                            <input type="date" class="form-control" value="<?= $category['discount_start'] ?>"
                                                name="discount_start">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Discount End Date</label>
                                            <input type="date" class="form-control" name="discount_end" value="<?= $category['discount_end'] ?>">
                                        </div>
                                    </div>

                                </div>
                    </div>
                    <div class="modal-footer">
                        <a href="categories.php" type="button" class="btn btn-secondary mx-3" >Cancel</a>
                        <button type="submit" name="submit" class="btn btn-primary">Create Category</button>
                    </div>
            <?php
                            endforeach;
                        endif;
            ?>
                </form>
            </main>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCategoryForm" class="needs-validation" novalidate>
                    <div class="modal-body">
                        <!-- Basic Information -->
                        <h5 class="form-section-title">Basic Information</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required placeholder="e.g., Laptops, Smartphones">
                                <div class="invalid-feedback">
                                    Please enter category name.
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug (URL)</label>
                                <input type="text" class="form-control" name="slug" placeholder="e.g., laptops-computers">
                                <div class="form-text">Leave blank to auto-generate from name</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Brief description of this category"></textarea>
                        </div>

                        <!-- Icon & Color -->
                        <h5 class="form-section-title">Icon & Color</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Select Icon</label>
                                <div class="icon-picker">
                                    <?php
                                    $icons = [
                                        'fa-laptop',
                                        'fa-mobile-alt',
                                        'fa-camera',
                                        'fa-headphones',
                                        'fa-gamepad',
                                        'fa-clock',
                                        'fa-tv',
                                        'fa-tablet-alt',
                                        'fa-keyboard',
                                        'fa-mouse',
                                        'fa-hdd',
                                        'fa-server'
                                    ];
                                    foreach ($icons as $icon) {
                                    ?>
                                        <div class="icon-option" data-icon="<?php echo $icon; ?>">
                                            <i class="fas <?php echo $icon; ?>"></i>
                                        </div>
                                    <?php } ?>
                                </div>
                                <input type="hidden" name="icon" value="fa-laptop">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Select Color</label>
                                <div class="color-picker">
                                    <?php
                                    $colors = [
                                        '#3498db',
                                        '#2ecc71',
                                        '#e74c3c',
                                        '#f39c12',
                                        '#9b59b6',
                                        '#1abc9c',
                                        '#34495e',
                                        '#d35400',
                                        '#c0392b',
                                        '#8e44ad',
                                        '#16a085',
                                        '#27ae60'
                                    ];
                                    foreach ($colors as $color) {
                                    ?>
                                        <div class="color-option" style="background-color: <?php echo $color; ?>" data-color="<?php echo $color; ?>"></div>
                                    <?php } ?>
                                </div>
                                <input type="hidden" name="color" value="#3498db">
                            </div>
                        </div>

                        <!-- Settings -->
                        <h5 class="form-section-title">Category Settings</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="visibility" id="visibilitySwitch" checked>
                                    <label class="form-check-label" for="visibilitySwitch">
                                        Visible to customers
                                    </label>
                                </div>
                                <div class="form-text">If disabled, category will be hidden from store</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="allow_comments" id="commentsSwitch" checked>
                                    <label class="form-check-label" for="commentsSwitch">
                                        Allow product comments
                                    </label>
                                </div>
                                <div class="form-text">Enable/disable comments for products in this category</div>
                            </div>
                        </div>

                        <!-- Discount Settings -->
                        <h5 class="form-section-title">Discount Settings (Optional)</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Value</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="discount_value" min="0" max="100" step="0.01" placeholder="0.00">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="form-text">Percentage discount for all products in this category</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Type</label>
                                <select class="form-select" name="discount_type">
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed">Fixed Amount</option>
                                </select>
                            </div>
                        </div>

                        <div class="discount-period">
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
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enableDiscount">
                                <label class="form-check-label" for="enableDiscount">
                                    Enable discount for this category
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>





    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Admin JS -->
    <script src="assets/js/admin.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Icon Picker
            document.querySelectorAll('.icon-option').forEach(icon => {
                icon.addEventListener('click', function() {
                    const iconPicker = this.closest('.icon-picker');
                    iconPicker.querySelectorAll('.icon-option').forEach(i => i.classList.remove('selected'));
                    this.classList.add('selected');

                    const iconName = this.getAttribute('data-icon');
                    const hiddenInput = iconPicker.parentElement.querySelector('input[type="hidden"]');
                    hiddenInput.value = iconName;
                });
            });

            // Color Picker
            document.querySelectorAll('.color-option').forEach(color => {
                color.addEventListener('click', function() {
                    const colorPicker = this.closest('.color-picker');
                    colorPicker.querySelectorAll('.color-option').forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');

                    const colorValue = this.getAttribute('data-color');
                    const hiddenInput = colorPicker.parentElement.querySelector('input[type="hidden"]');
                    hiddenInput.value = colorValue;
                });
            });

            // Enable Discount Toggle
            document.getElementById('enableDiscount').addEventListener('change', function() {
                const discountInputs = document.querySelectorAll('#addCategoryForm input[name^="discount"], #addCategoryForm select[name="discount_type"]');
                discountInputs.forEach(input => {
                    input.disabled = !this.checked;
                });
            });

            document.getElementById('editEnableDiscount').addEventListener('change', function() {
                const discountInputs = document.querySelectorAll('#editCategoryForm input[name^="discount"], #editCategoryForm select[name="discount_type"]');
                discountInputs.forEach(input => {
                    input.disabled = !this.checked;
                });
            });

            // Delete Action Toggle
            document.getElementById('deleteAction').addEventListener('change', function() {
                const moveDiv = document.getElementById('moveToCategoryDiv');
                moveDiv.style.display = this.value === 'move' ? 'block' : 'none';
            });

            // Delete Category
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.getAttribute('data-category-id');
                    const categoryName = this.getAttribute('data-category-name');

                    document.getElementById('deleteCategoryName').textContent = categoryName;
                    document.getElementById('confirmDeleteCategoryBtn').setAttribute('data-category-id', categoryId);

                    const modal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
                    modal.show();
                });
            });

            // Confirm Delete
            document.getElementById('confirmDeleteCategoryBtn').addEventListener('click', function() {
                if (!document.getElementById('confirmDelete').checked) {
                    alert('Please confirm that you understand this action cannot be undone.');
                    return;
                }

                const categoryId = this.getAttribute('data-category-id');
                const deleteAction = document.getElementById('deleteAction').value;
                const moveToCategory = document.getElementById('moveToCategory').value;

                if (deleteAction === 'move' && !moveToCategory) {
                    alert('Please select a category to move products to.');
                    return;
                }

                // In real application, send delete request to server
                console.log(`Deleting category ${categoryId}, action: ${deleteAction}, move to: ${moveToCategory}`);
                alert(`Category deleted successfully. Products ${deleteAction === 'delete' ? 'deleted' : 'moved to another category'}.`);

                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal'));
                modal.hide();

                // Reload or remove category card
                const categoryCard = document.querySelector(`[data-category-id="${categoryId}"]`).closest('.col-md-6');
                categoryCard.remove();
            });

            // Form Validation
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        event.preventDefault();
                        // In real application, submit form via AJAX
                        const formId = form.id;
                        if (formId === 'addCategoryForm') {
                            alert('Category created successfully!');
                            const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
                            modal.hide();
                            form.reset();
                        } else if (formId === 'editCategoryForm') {
                            alert('Category updated successfully!');
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
                            modal.hide();
                        }
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            // Set default selected icon and color
            document.querySelector('.icon-option[data-icon="fa-laptop"]').classList.add('selected');
            document.querySelector('.color-option[data-color="#3498db"]').classList.add('selected');
        });

        // Load category data for editing
        function loadCategoryData(categoryId) {
            // In real application, fetch category data via AJAX
            // For demo, using sample data
            const sampleData = {
                1: {
                    name: 'Laptops',
                    slug: 'laptops-computers',
                    description: 'Computers, notebooks, and laptops for work and gaming',
                    icon: 'fa-laptop',
                    color: '#3498db',
                    visibility: true,
                    allow_comments: true,
                    discount_value: '10.00',
                    discount_type: 'percentage',
                    discount_start: '2023-12-01',
                    discount_end: '2023-12-31'
                }
            };

            const data = sampleData[categoryId] || {
                name: '',
                slug: '',
                description: '',
                icon: 'fa-laptop',
                color: '#3498db',
                visibility: true,
                allow_comments: true,
                discount_value: '',
                discount_type: 'percentage',
                discount_start: '',
                discount_end: ''
            };

            // Populate form
            document.getElementById('editCategoryId').value = categoryId;
            document.getElementById('editName').value = data.name;
            document.getElementById('editSlug').value = data.slug;
            document.getElementById('editDescription').value = data.description;
            document.getElementById('editIcon').value = data.icon;
            document.getElementById('editColor').value = data.color;
            document.getElementById('editVisibilitySwitch').checked = data.visibility;
            document.getElementById('editCommentsSwitch').checked = data.allow_comments;
            document.getElementById('editDiscountValue').value = data.discount_value;
            document.getElementById('editDiscountType').value = data.discount_type;
            document.getElementById('editDiscountStart').value = data.discount_start;
            document.getElementById('editDiscountEnd').value = data.discount_end;
            document.getElementById('editEnableDiscount').checked = !!data.discount_value;

            // Update icon and color pickers
            document.querySelectorAll('#editIconPicker .icon-option').forEach(icon => {
                icon.classList.remove('selected');
                if (icon.getAttribute('data-icon') === data.icon) {
                    icon.classList.add('selected');
                }
            });

            document.querySelectorAll('#editColorPicker .color-option').forEach(color => {
                color.classList.remove('selected');
                if (color.getAttribute('data-color') === data.color) {
                    color.classList.add('selected');
                }
            });

            // Enable/disable discount fields
            const discountInputs = document.querySelectorAll('#editCategoryForm input[name^="discount"], #editCategoryForm select[name="discount_type"]');
            discountInputs.forEach(input => {
                input.disabled = !data.discount_value;
            });
        }
    </script>
</body>

</html>