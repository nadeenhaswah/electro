<?php
/* Application Constants*/

// Paths
define('ROOT_PATH', dirname(__DIR__) . '/');

define('ASSETS_PATH', 'assets/');

/* المسار الحقيقي على السيرفر */
define('UPLOAD_PATH', ROOT_PATH . 'uploads/items/');

/* المسار المستخدم في المتصفح */
define('UPLOAD_URL', 'uploads/items/');

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour

// Pagination
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// File upload settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');

// Order statuses
define('ORDER_PENDING', 'pending');
define('ORDER_PROCESSING', 'processing');
define('ORDER_SHIPPED', 'shipped');
define('ORDER_COMPLETED', 'completed');
define('ORDER_CANCELLED', 'cancelled');

// Payment statuses
define('PAYMENT_PENDING', 'pending');
define('PAYMENT_COMPLETED', 'completed');
define('PAYMENT_FAILED', 'failed');
