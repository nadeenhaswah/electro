<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Electro</title>

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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Products</h1>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>

                <!-- Products Grid -->
                <div class="row">
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card product-card">
                                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Product">
                                <div class="card-body">
                                    <h5 class="card-title">Product Name <?php echo $i; ?></h5>
                                    <p class="card-text text-muted">Category: Laptops</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="price">$999.99</span>
                                        <span class="badge bg-success">In Stock</span>
                                    </div>
                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-outline-primary w-100 mb-2">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger w-100">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </main>
</body>

</html>