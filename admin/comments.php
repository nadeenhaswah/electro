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
    <title>Manage Comments - Electro Admin</title>

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
        .comment-card {
            border-left: 4px solid var(--primary-color);
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .comment-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 0.85rem;
            color: var(--grey-medium);
        }

        .product-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .product-link:hover {
            text-decoration: underline;
        }

        .comment-text {
            padding: 10px 0;
            line-height: 1.6;
            color: var(--body-color);
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-visible {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .status-hidden {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }

        .status-pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
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
            font-size: 0.9rem;
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

        .filter-tabs {
            border-bottom: 2px solid var(--grey-light);
            margin-bottom: 20px;
        }

        .filter-tabs .nav-link {
            border: none;
            color: var(--grey-medium);
            font-weight: 500;
            padding: 10px 20px;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }

        .filter-tabs .nav-link:hover {
            color: var(--primary-color);
            background-color: rgba(209, 0, 36, 0.05);
        }

        .filter-tabs .nav-link.active {
            color: var(--primary-color);
            background-color: white;
            border-bottom: 3px solid var(--primary-color);
        }

        .search-container {
            max-width: 300px;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .stats-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1;
        }

        .stats-label {
            font-size: 0.9rem;
            color: var(--grey-medium);
            margin-top: 5px;
        }

        .comment-date {
            font-size: 0.8rem;
            color: var(--grey-medium);
        }

        .bulk-actions {
            background-color: var(--grey-lighter);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .bulk-actions.active {
            display: block;
        }

        .comment-checkbox {
            margin-right: 10px;
        }
    </style>
</head>

<body>
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
                        <h1 class="h2">Manage Comments</h1>
                        <p class="text-muted mb-0"> Hide or delete user comments</p>
                    </div>

                </div>

                <?php
                $sql = "
                    SELECT 
                        COUNT(*) AS total_comments,
                        SUM(status = 1) AS visible_comments,
                        SUM(status = 0) AS hidden_comments
                    FROM comments
                    ";

                $result = $db->query($sql);
                $stats = $result->fetch(PDO::FETCH_ASSOC);

                $TotalComments        = $stats['total_comments'];
                $TotalvisibleComments = $stats['visible_comments'];
                $TotalhiddenComments  = $stats['hidden_comments'];
                ?>
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number"><?= $TotalComments ?></div>
                            <div class="stats-label">Total Comments</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number"><?= $TotalvisibleComments  ?></div>
                            <div class="stats-label">Visible Comments</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number"><?= $TotalhiddenComments ?></div>
                            <div class="stats-label">Hidden Comments</div>
                        </div>
                    </div>

                </div>

                <!-- Filter Tabs -->
                <ul class="nav filter-tabs" id="commentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                            <i class="fas fa-list me-2"></i>All Comments
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="visible-tab" data-bs-toggle="tab" data-bs-target="#visible" type="button">
                            <i class="fas fa-eye me-2"></i>Visible
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="hidden-tab" data-bs-toggle="tab" data-bs-target="#hidden" type="button">
                            <i class="fas fa-eye-slash me-2"></i>Hidden
                        </button>
                    </li>

                </ul>


                <!-- Comments List -->
                <div class="tab-content" id="commentTabsContent">
                    <!-- All Comments Tab -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        <?php

                        $sql3 = "SELECT
                                c.comment_id        AS id,

                                -- اسم المستخدم
                                (
                                    SELECT  CONCAT(u.first_name, ' ', u.last_name)
                                    FROM users u
                                    WHERE u.user_id = c.user_id
                                ) AS user_name,
                                (
                                    SELECT  u.first_name
                                    FROM users u
                                    WHERE u.user_id = c.user_id
                                ) AS first_name,
                                (
                                    SELECT  u.last_name
                                    FROM users u
                                    WHERE u.user_id = c.user_id
                                ) AS last_name,

                                
                                -- اسم المنتج
                                (
                                    SELECT i.name
                                    FROM items i
                                    WHERE i.item_id = c.item_id
                                ) AS product_name,

                                c.item_id           AS product_id,

                                c.comment,

                                -- حالة التعليق
                                CASE
                                    WHEN c.status = 0 THEN 'hidden'
                                    WHEN c.status = 1 THEN 'visible'
                                END AS status,

                                -- التقييم (إن وجد)
                                (
                                    SELECT r.rating
                                    FROM item_ratings r
                                    WHERE r.item_id = c.item_id
                                    AND r.user_id = c.user_id
                                    LIMIT 1
                                ) AS rating,

                                DATE_FORMAT(c.comment_date, '%Y-%m-%d %H:%i') AS date

                            FROM comments c;
                            ";
                        $result3 = $db->query($sql3);
                        $comments = $result3->fetchAll(PDO::FETCH_ASSOC);


                        if (empty($comments)): ?>
                            <div class="empty-state">
                                <i class="fas fa-comments"></i>
                                <h3>No Comments Found</h3>
                                <p class="mb-0">There are no comments to display</p>
                            </div>
                            <?php else:
                            foreach ($comments as $comment):
                                $statusClass = 'status-' . $comment['status'];
                                $statusText = ucfirst($comment['status']);
                                $firstLetters = strtoupper(substr($comment['first_name'], 0, 1) . substr($comment['last_name'], 0, 1));

                            ?>
                                <div class="comment-card" data-comment-id="<?php echo $comment['id']; ?>" data-status="<?php echo $comment['status']; ?>">
                                    <div class="comment-header">
                                        <div class="user-info">
                                            <div class="user-avatar"><?php echo $firstLetters ?></div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($comment['user_name']); ?></strong>
                                                <div class="comment-meta">
                                                    <span>on <a href="item_view.php?id=<?php echo $comment['product_id']; ?>" class="product-link"><?php echo htmlspecialchars($comment['product_name']); ?></a></span>
                                                    <span class="comment-date">
                                                        <i class="far fa-clock me-1"></i>
                                                        <?php echo $comment['date']; ?>
                                                    </span>
                                                    <?php if ($comment['rating'] > 0): ?>
                                                        <span>
                                                            <i class="fas fa-star text-warning"></i>
                                                            <?php echo $comment['rating']; ?>/5
                                                        </span>
                                                    <?php endif; ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start gap-2">
                                            <span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                            <div class="action-buttons">
                                                <?php if ($comment['status'] === 'visible'): ?>
                                                    <button class="btn btn-action btn-outline-secondary btn-hide"
                                                        data-id="<?php echo $comment['id']; ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#hideModal"
                                                        title="Hide Comment">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </button>
                                                <?php elseif ($comment['status'] === 'hidden'): ?>
                                                    <button class="btn btn-action btn-outline-success btn-show"
                                                        data-id="<?php echo $comment['id']; ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#showModal"
                                                        title="Show Comment">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                <?php elseif ($comment['status'] === 'pending'): ?>
                                                    <button class="btn btn-action btn-outline-success btn-approve"
                                                        data-id="<?php echo $comment['id']; ?>"
                                                        title="Approve Comment">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <button
                                                    class="btn btn-sm btn-outline-danger btn-action btn-delete"
                                                    title="Delete"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-id="<?= $comment['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="comment-text">
                                        <?php echo htmlspecialchars($comment['comment']); ?>
                                    </div>
                                </div>
                        <?php endforeach;
                        endif; ?>

                        <!-- Pagination -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
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

                    <!-- Visible Comments Tab -->
                    <div class="tab-pane fade" id="visible" role="tabpanel">

                        <?php
                        $visibleComments = array_filter($comments, function ($c) {
                            return $c['status'] === 'visible';
                        });
                        if (empty($visibleComments)): ?>
                            <div class="empty-state">
                                <i class="fas fa-eye"></i>
                                <h3>No Visible Comments</h3>
                                <p class="mb-0">There are no visible comments</p>
                            </div>
                            <?php else:
                            foreach ($visibleComments as $comment):
                                $firstLetters = strtoupper(substr($comment['first_name'], 0, 1) . substr($comment['last_name'], 0, 1));
                            ?>

                                <div class="comment-card" data-comment-id="<?php echo $comment['id']; ?>">
                                    <div class="comment-header">
                                        <div class="user-info">
                                            <div class="user-avatar"><?php echo $firstLetters ?></div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($comment['user_name']); ?></strong>
                                                <div class="comment-meta">
                                                    <span>on <a href="item_view.php?id=<?php echo $comment['product_id']; ?>" class="product-link"><?php echo htmlspecialchars($comment['product_name']); ?></a></span>
                                                    <span class="comment-date">
                                                        <i class="far fa-clock me-1"></i>
                                                        <?php echo $comment['date']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start gap-2">
                                            <span class="status-badge status-visible">Visible</span>
                                            <div class="action-buttons">
                                                <button class="btn btn-action btn-outline-secondary btn-hide"
                                                    data-id="<?php echo $comment['id']; ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#hideModal"
                                                    title="Hide Comment">
                                                    <i class="fas fa-eye-slash"></i>
                                                </button>
                                                <button
                                                    class="btn btn-sm btn-outline-danger btn-action btn-delete"
                                                    title="Delete"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-id="<?= $comment['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="comment-text">
                                        <?php echo htmlspecialchars($comment['comment']); ?>
                                    </div>
                                </div>
                        <?php endforeach;
                        endif; ?>
                    </div>

                    <!-- Hidden Comments Tab -->
                    <div class="tab-pane fade" id="hidden" role="tabpanel">
                        <?php
                        $hiddenComments = array_filter($comments, function ($c) {
                            return $c['status'] === 'hidden';
                        });
                        if (empty($hiddenComments)): ?>
                            <div class="empty-state">
                                <i class="fas fa-eye-slash"></i>
                                <h3>No Hidden Comments</h3>
                                <p class="mb-0">There are no hidden comments</p>
                            </div>
                            <?php else:
                            foreach ($hiddenComments as $comment):
                                $firstLetters = strtoupper(substr($comment['first_name'], 0, 1) . substr($comment['last_name'], 0, 1));
                            ?>
                                <div class="comment-card" data-comment-id="<?php echo $comment['id']; ?>">
                                    <div class="comment-header">
                                        <div class="user-info">
                                            <div class="user-avatar"><?php echo $firstLetters ?></div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($comment['user_name']); ?></strong>
                                                <div class="comment-meta">
                                                    <span>on <a href="item_view.php?id=<?php echo $comment['product_id']; ?>" class="product-link"><?php echo htmlspecialchars($comment['product_name']); ?></a></span>
                                                    <span class="comment-date">
                                                        <i class="far fa-clock me-1"></i>
                                                        <?php echo $comment['date']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start gap-2">
                                            <span class="status-badge status-hidden">Hidden</span>
                                            <div class="action-buttons">
                                                <button class="btn btn-action btn-outline-success btn-show"
                                                    data-id="<?php echo $comment['id']; ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#showModal"
                                                    title="Show Comment">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button
                                                    class="btn btn-sm btn-outline-danger btn-action btn-delete"
                                                    title="Delete"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-id="<?= $comment['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="comment-text">
                                        <?php echo htmlspecialchars($comment['comment']); ?>
                                    </div>
                                </div>
                        <?php endforeach;
                        endif; ?>
                    </div>


                </div>
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form method="POST" action="delete_comment.php">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center">
                        <input type="hidden" name="id" id="delete-id">
                        <p class="mb-0">Are you sure you want to delete this comment?</p>
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
    <!-- hide Confirmation Modal -->
    <div class="modal fade" id="hideModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form method="POST" action="showOrHidecomment.php">

                    <div class="modal-header bg-secendery text-white">
                        <h5 class="modal-title">Confirm hide</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center">
                        <input type="hidden" name="id" id="hide-id">
                        <p class="mb-0">Are you sure you want to hide this comment?</p>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" name="hide" class="btn btn-danger">
                            Yes
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- show Confirmation Modal -->
    <div class="modal fade" id="showModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form method="POST" action="showOrHidecomment.php">

                    <div class="modal-header bg-secendery text-white">
                        <h5 class="modal-title">Confirm show</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center">
                        <input type="hidden" name="id" id="show-id">
                        <p class="mb-0">Are you sure you want to show this comment?</p>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" name="show" class="btn btn-danger">
                            Yes
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

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
    <?php

    if (isset($_SESSION['alert_show'])):
        $alert = $_SESSION['alert_show'];
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
        unset($_SESSION['alert_show']);
    endif;
    ?>
    <?php

    if (isset($_SESSION['alert_hide'])):
        $alert = $_SESSION['alert_hide'];
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
        unset($_SESSION['alert_hide']);
    endif;
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const showModal = document.getElementById('showModal');

        showModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            document.getElementById('show-id').value = id;
        });
    </script>
    <script>
        const hideModal = document.getElementById('hideModal');

        hideModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            document.getElementById('hide-id').value = id;
        });
    </script>
    <script>
        const deleteModal = document.getElementById('deleteModal');

        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            document.getElementById('delete-id').value = id;
        });
    </script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Admin JS -->
    <script src="assets/js/admin.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Bulk Actions
            const bulkActionsBtn = document.getElementById('bulkActionsBtn');
            const bulkActionsPanel = document.getElementById('bulkActionsPanel');
            const cancelBulkActions = document.getElementById('cancelBulkActions');
            const selectAllComments = document.getElementById('selectAllComments');
            const bulkActionSelect = document.getElementById('bulkActionSelect');
            const applyBulkAction = document.getElementById('applyBulkAction');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            let bulkMode = false;
            let selectedComments = new Set();

            bulkActionsBtn.addEventListener('click', function() {
                bulkMode = !bulkMode;
                if (bulkMode) {
                    bulkActionsPanel.classList.add('active');
                    bulkActionsBtn.innerHTML = '<i class="fas fa-times me-2"></i>Cancel Bulk Actions';
                    bulkActionsBtn.classList.remove('btn-outline-secondary');
                    bulkActionsBtn.classList.add('btn-outline-danger');
                } else {
                    bulkActionsPanel.classList.remove('active');
                    bulkActionsBtn.innerHTML = '<i class="fas fa-tasks me-2"></i>Bulk Actions';
                    bulkActionsBtn.classList.remove('btn-outline-danger');
                    bulkActionsBtn.classList.add('btn-outline-secondary');
                    selectedComments.clear();
                    updateCheckboxes();
                    updateBulkDeleteButton();
                }
            });

            cancelBulkActions.addEventListener('click', function() {
                bulkActionsPanel.classList.remove('active');
                bulkActionsBtn.innerHTML = '<i class="fas fa-tasks me-2"></i>Bulk Actions';
                bulkActionsBtn.classList.remove('btn-outline-danger');
                bulkActionsBtn.classList.add('btn-outline-secondary');
                bulkMode = false;
                selectedComments.clear();
                updateCheckboxes();
                updateBulkDeleteButton();
            });

            selectAllComments.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.comment-checkbox');
                if (this.checked) {
                    checkboxes.forEach(cb => {
                        cb.checked = true;
                        selectedComments.add(cb.getAttribute('data-comment-id'));
                    });
                } else {
                    checkboxes.forEach(cb => {
                        cb.checked = false;
                        selectedComments.delete(cb.getAttribute('data-comment-id'));
                    });
                }
                updateBulkDeleteButton();
            });

            // Handle individual checkbox changes
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('comment-checkbox')) {
                    const commentId = e.target.getAttribute('data-comment-id');
                    if (e.target.checked) {
                        selectedComments.add(commentId);
                    } else {
                        selectedComments.delete(commentId);
                        selectAllComments.checked = false;
                    }
                    updateBulkDeleteButton();
                }
            });

            function updateCheckboxes() {
                const checkboxes = document.querySelectorAll('.comment-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = selectedComments.has(cb.getAttribute('data-comment-id'));
                });
                selectAllComments.checked = selectedComments.size > 0 &&
                    selectedComments.size === document.querySelectorAll('.comment-checkbox').length;
            }

            function updateBulkDeleteButton() {
                if (selectedComments.size > 0) {
                    bulkDeleteBtn.style.display = 'inline-block';
                    bulkDeleteBtn.innerHTML = `<i class="fas fa-trash me-2"></i>Delete Selected (${selectedComments.size})`;
                } else {
                    bulkDeleteBtn.style.display = 'none';
                }
            }

            // Apply bulk action
            applyBulkAction.addEventListener('click', function() {
                const action = bulkActionSelect.value;
                if (!action) {
                    alert('Please select a bulk action.');
                    return;
                }

                if (selectedComments.size === 0) {
                    alert('Please select at least one comment.');
                    return;
                }

                if (confirm(`Are you sure you want to ${action} ${selectedComments.size} comment(s)?`)) {
                    // In real application, send bulk action request to server
                    const commentIds = Array.from(selectedComments);
                    console.log(`Bulk ${action}:`, commentIds);

                    // Update UI
                    commentIds.forEach(commentId => {
                        const commentCard = document.querySelector(`[data-comment-id="${commentId}"]`);
                        if (commentCard) {
                            switch (action) {
                                case 'approve':
                                    updateCommentStatus(commentId, 'visible');
                                    break;
                                case 'hide':
                                    updateCommentStatus(commentId, 'hidden');
                                    break;
                                case 'delete':
                                    commentCard.remove();
                                    break;
                            }
                        }
                    });

                    // Reset bulk mode
                    selectedComments.clear();
                    updateCheckboxes();
                    updateBulkDeleteButton();
                    bulkActionSelect.value = '';

                    alert(`Successfully ${action}d ${commentIds.length} comment(s).`);
                }
            });









            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');

            searchInput.addEventListener('input', function() {
                filterComments();
            });

            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                filterComments();
            });

            // Sort functionality
            const sortSelect = document.getElementById('sortComments');
            sortSelect.addEventListener('change', function() {
                sortComments(this.value);
            });

            // Tab switching - filter comments based on active tab
            const commentTabs = document.querySelectorAll('#commentTabs button');
            commentTabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function() {
                    filterComments();
                });
            });

            // Function to hide comment
            function hideComment(commentId) {
                if (confirm('Are you sure you want to hide this comment?')) {
                    updateCommentStatus(commentId, 'hidden');
                }
            }

            // Function to show comment
            function showComment(commentId) {
                updateCommentStatus(commentId, 'visible');
            }

            // Function to approve comment
            function approveComment(commentId) {
                updateCommentStatus(commentId, 'visible');
            }

            // Function to delete comment
            function deleteComment(commentId) {
                // In real application, send delete request to server
                console.log('Deleting comment:', commentId);

                const commentCard = document.querySelector(`[data-comment-id="${commentId}"]`);
                if (commentCard) {
                    commentCard.remove();
                    alert('Comment deleted successfully.');
                }
            }

            // Function to update comment status
            function updateCommentStatus(commentId, newStatus) {
                // In real application, send update request to server
                console.log(`Updating comment ${commentId} status to ${newStatus}`);

                const commentCard = document.querySelector(`[data-comment-id="${commentId}"]`);
                if (commentCard) {
                    const statusBadge = commentCard.querySelector('.status-badge');
                    const actionButtons = commentCard.querySelector('.action-buttons');

                    // Update status badge
                    commentCard.setAttribute('data-status', newStatus);
                    statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    statusBadge.className = 'status-badge status-' + newStatus;

                    // Update action buttons
                    let newButtons = '';
                    if (newStatus === 'visible') {
                        newButtons = `
                            <button class="btn btn-action btn-outline-secondary btn-hide" 
                                    data-comment-id="${commentId}"
                                    title="Hide Comment">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger btn-delete" 
                                    data-comment-id="${commentId}"
                                    data-comment-user="${commentCard.querySelector('.user-info strong').textContent}"
                                    title="Delete Comment">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    } else if (newStatus === 'hidden') {
                        newButtons = `
                            <button class="btn btn-action btn-outline-success btn-show" 
                                    data-comment-id="${commentId}"
                                    title="Show Comment">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger btn-delete" 
                                    data-comment-id="${commentId}"
                                    data-comment-user="${commentCard.querySelector('.user-info strong').textContent}"
                                    title="Delete Comment">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }

                    actionButtons.innerHTML = newButtons;

                    // Re-attach event listeners to new buttons
                    commentCard.querySelectorAll('.btn-hide, .btn-show, .btn-delete').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const commentId = this.getAttribute('data-comment-id');
                            if (this.classList.contains('btn-hide')) hideComment(commentId);
                            if (this.classList.contains('btn-show')) showComment(commentId);
                            if (this.classList.contains('btn-approve')) approveComment(commentId);
                            if (this.classList.contains('btn-delete')) {
                                const commentUser = this.getAttribute('data-comment-user');
                                document.getElementById('deleteCommentUser').textContent = commentUser;
                                document.getElementById('confirmDeleteCommentBtn').setAttribute('data-comment-id', commentId);
                                new bootstrap.Modal(document.getElementById('deleteCommentModal')).show();
                            }
                        });
                    });

                    alert(`Comment ${newStatus === 'visible' ? 'approved and made visible' : 'hidden'} successfully.`);
                }
            }

            // Function to filter comments
            function filterComments() {
                const searchTerm = searchInput.value.toLowerCase();
                const activeTab = document.querySelector('#commentTabs .nav-link.active').id.replace('-tab', '');

                document.querySelectorAll('.comment-card').forEach(card => {
                    const user = card.querySelector('.user-info strong').textContent.toLowerCase();
                    const product = card.querySelector('.product-link').textContent.toLowerCase();
                    const comment = card.querySelector('.comment-text').textContent.toLowerCase();
                    const status = card.getAttribute('data-status');

                    const matchesSearch = searchTerm === '' ||
                        user.includes(searchTerm) ||
                        product.includes(searchTerm) ||
                        comment.includes(searchTerm);

                    const matchesTab = activeTab === 'all' ||
                        (activeTab === 'visible' && status === 'visible') ||
                        (activeTab === 'hidden' && status === 'hidden') ||
                        (activeTab === 'pending' && status === 'pending');

                    card.style.display = matchesSearch && matchesTab ? '' : 'none';
                });
            }

            // Function to sort comments
            function sortComments(sortType) {
                const commentCards = Array.from(document.querySelectorAll('.comment-card'));
                const container = document.querySelector('.tab-pane.active');

                commentCards.sort((a, b) => {
                    const aDate = new Date(a.querySelector('.comment-date').textContent);
                    const bDate = new Date(b.querySelector('.comment-date').textContent);
                    const aUser = a.querySelector('.user-info strong').textContent;
                    const bUser = b.querySelector('.user-info strong').textContent;
                    const aProduct = a.querySelector('.product-link').textContent;
                    const bProduct = b.querySelector('.product-link').textContent;

                    switch (sortType) {
                        case 'newest':
                            return bDate - aDate;
                        case 'oldest':
                            return aDate - bDate;
                        case 'product':
                            return aProduct.localeCompare(bProduct);
                        case 'user':
                            return aUser.localeCompare(bUser);
                        default:
                            return 0;
                    }
                });

                // Reorder comments
                commentCards.forEach(card => container.appendChild(card));
            }

            // Initialize
            filterComments();
        });
    </script>
</body>

</html>