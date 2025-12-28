<?php
/**
 * Authentication Controller
 * Handles login, registration, and logout
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/classes/User.php';
require_once dirname(__DIR__) . '/classes/Cart.php';

class AuthController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    /**
     * Handle registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $email = sanitize($_POST['email'] ?? '');
        $first_name = sanitize($_POST['first_name'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        $mobile = sanitize($_POST['mobile'] ?? '');

        $result = $this->user->register($email, $first_name, $last_name, $password, $confirmPassword, $mobile);

        return $result;
    }

    /**
     * Handle login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $result = $this->user->login($email, $password);
        
        if ($result['success']) {
            // Convert guest cart if exists
            if (isset($_SESSION['session_id'])) {
                $cart = new Cart();
                $cart->convertGuestCart($_SESSION['session_id'], $_SESSION['user_id']);
            }
        }

        return $result;
    }

    /**
     * Handle logout
     */
    public function logout() {
        return $this->user->logout();
    }

    /**
     * Get current user
     */
    public function getCurrentUser() {
        startSession();
        if (isset($_SESSION['user_id'])) {
            return $this->user->getUserById($_SESSION['user_id']);
        }
        return false;
    }

    /**
     * Handle profile update
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        startSession();
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'You must be logged in'];
        }

        $user_id = $_SESSION['user_id'];
        $email = sanitize($_POST['email'] ?? '');
        $first_name = sanitize($_POST['first_name'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $mobile = sanitize($_POST['mobile'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';

        // Validate password match if password is provided
        if (!empty($password)) {
            if ($password !== $confirmPassword) {
                return ['success' => false, 'message' => 'Passwords do not match'];
            }
        }

        // If password is empty
        $passwordToUpdate = !empty($password) ? $password : null;

        return $this->user->updateProfileFull($user_id, $first_name, $last_name, $email, $mobile, $passwordToUpdate);
    }
}

