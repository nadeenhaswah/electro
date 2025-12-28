<?php

function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    startSession();
    
    $sessionRole = $_SESSION['role'] ?? null;
    if ($sessionRole === ROLE_ADMIN) {
        return true;
    }
    
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        return false;
    }
    
    require_once dirname(__DIR__) . '/classes/User.php';
    $user = new User();
    $userData = $user->getUserById($userId);
    
    if ($userData && $userData['role'] === ROLE_ADMIN) {
        $_SESSION['role'] = ROLE_ADMIN;
        return true;
    }
    
    return false;
}

function requireLogin() {
    if (!isLoggedIn()) {
        $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
        header('Location: ' . $basePath . 'auth/login.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $basePath = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) ? '../' : '';
        header('Location: ' . $basePath . 'index.php');
        exit();
    }
}

function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function getFlashMessage($key) {
    startSession();
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

function setFlashMessage($key, $message) {
    startSession();
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][$key] = $message;
}



