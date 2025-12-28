<?php
/**
 * Enhanced Access Control Functions
 * Role-based access control for Admin and User dashboards
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/includes/functions.php';

/**
 * Verify user role from database 
 */
function verifyUserRole($requiredRole = null) {
    startSession();
    
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    // If role is already in session, use it
    if (isset($_SESSION['role'])) {
        if ($requiredRole === null) {
            return true; // Just checking if logged in
        }
        return $_SESSION['role'] === $requiredRole;
    }
    
    // If role not in session, fetch from database
    require_once dirname(__DIR__) . '/classes/User.php';
    $user = new User();
    $userData = $user->getUserById($_SESSION['user_id']);
    
    if ($userData) {
        $_SESSION['role'] = $userData['role'];
        if ($requiredRole === null) {
            return true;
        }
        return $userData['role'] === $requiredRole;
    }
    
    return false;
}

/**
 * Require specific role
 */
function requireRole($role) {
    if (!verifyUserRole($role)) {
        $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
        if (!isLoggedIn()) {
            header('Location: ' . $basePath . 'auth/login.php');
        } else {
            header('Location: ' . $basePath . 'index.php');
            setFlashMessage('error', 'Access denied. You do not have permission to access this page.');
        }
        exit();
    }
}

/**
 * Check if user can access admin area
 */
function canAccessAdmin() {
    return verifyUserRole(ROLE_ADMIN);
}

/**
 * Check if user can access user area
 */
function canAccessUserArea() {
    return verifyUserRole(ROLE_USER) || verifyUserRole(ROLE_ADMIN);
}

/**
 * Require admin role (enhanced)
 */
function requireAdminAccess() {
    if (!canAccessAdmin()) {
        $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
        if (!isLoggedIn()) {
            header('Location: ' . $basePath . 'auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        } else {
            header('Location: ' . $basePath . 'index.php');
            setFlashMessage('error', 'Access denied. Admin privileges required.');
        }
        exit();
    }
}

/**
 * Require user role (regular users only, not admin)
 */
function requireUserAccess() {
    if (!isLoggedIn()) {
        $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
        header('Location: ' . $basePath . 'auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
    
    // If admin tries to access user-only area, allow it (admins can do everything)
    if (canAccessAdmin()) {
        return true;
    }
    
    if (!canAccessUserArea()) {
        $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
        header('Location: ' . $basePath . 'index.php');
        setFlashMessage('error', 'Access denied.');
        exit();
    }
}

/**
 * Get current user role
 */
function getCurrentUserRole() {
    startSession();
    if (isset($_SESSION['role'])) {
        return $_SESSION['role'];
    }
    
    if (isset($_SESSION['user_id'])) {
        require_once dirname(__DIR__) . '/classes/User.php';
        $user = new User();
        $userData = $user->getUserById($_SESSION['user_id']);
        if ($userData) {
            $_SESSION['role'] = $userData['role'];
            return $userData['role'];
        }
    }
    
    return null;
}

