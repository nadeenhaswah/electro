<?php

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/classes/Database.php';

class User {
    private $db;
    private $table = "users";

    public $user_id;
    public $email;
    public $first_name;
    public $last_name;
    public $mobile;
    public $password;
    public $role;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    private function validateName($name) {
        if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            return ['valid' => false, 'message' => 'Name must contain letters only. No numbers or symbols allowed.'];
        }
        return ['valid' => true];
    }

    private function validateJordanMobile($mobile) {
        if (empty($mobile)) {
            return ['valid' => true];
        }

        $mobile = preg_replace('/[\s\-]/', '', $mobile);
        
        $countryCode = '+962';
        $countryCodeLength = 4;
        if (substr($mobile, 0, $countryCodeLength) === $countryCode) {
            $mobile = '0' . substr($mobile, $countryCodeLength);
        }
        
        $validPattern = '/^07[789]\d{7}$/';
        if (preg_match($validPattern, $mobile)) {
            return ['valid' => true];
        }
        
        return ['valid' => false, 'message' => 'Invalid Jordanian mobile number. Must start with 077, 078, or 079 and be 10 digits (e.g., 0771234567)'];
    }

    private function validatePassword($password) {
        $minLength = 8;
        
        if (strlen($password) < $minLength) {
            return ['valid' => false, 'message' => 'Password must be at least ' . $minLength . ' characters long'];
        }
        
        $hasLetter = preg_match('/[a-zA-Z]/', $password);
        if (!$hasLetter) {
            return ['valid' => false, 'message' => 'Password must contain at least one letter'];
        }
        
        $hasNumber = preg_match('/[0-9]/', $password);
        if (!$hasNumber) {
            return ['valid' => false, 'message' => 'Password must contain at least one number'];
        }
        
        $hasSymbol = preg_match('/[^a-zA-Z0-9]/', $password);
        if (!$hasSymbol) {
            return ['valid' => false, 'message' => 'Password must contain at least one symbol (@ # $ % ! * etc.)'];
        }
        
        return ['valid' => true];
    }

    /*  Register a new user     */
    public function register($email, $first_name, $last_name, $password, $confirmPassword, $mobile = null) {
        // Validate input
        if (empty($email) || empty($first_name) || empty($last_name) || empty($password) || empty($confirmPassword)) {
            return ['success' => false, 'message' => 'All required fields must be filled'];
        }

        // Validate first name (letters only)
        $nameValidation = $this->validateName($first_name);
        if (!$nameValidation['valid']) {
            return ['success' => false, 'message' => 'First Name: ' . $nameValidation['message']];
        }

        // Validate last name (letters only)
        $nameValidation = $this->validateName($last_name);
        if (!$nameValidation['valid']) {
            return ['success' => false, 'message' => 'Last Name: ' . $nameValidation['message']];
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Check if email already exists
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        if (!empty($mobile)) {
            $normalizedMobile = preg_replace('/[\s\-]/', '', $mobile);
            
            $countryCode = '+962';
            $countryCodeLength = 4;
            if (substr($normalizedMobile, 0, $countryCodeLength) === $countryCode) {
                $normalizedMobile = '0' . substr($normalizedMobile, $countryCodeLength);
            }
            
            $mobileValidation = $this->validateJordanMobile($normalizedMobile);
            if (!$mobileValidation['valid']) {
                return ['success' => false, 'message' => $mobileValidation['message']];
            }
            
            $mobile = $normalizedMobile;
        }

        // Validate password strength
        $passwordValidation = $this->validatePassword($password);
        if (!$passwordValidation['valid']) {
            return ['success' => false, 'message' => $passwordValidation['message']];
        }

        // Validate that passwords match confirmPassword
        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Passwords do not match'];
        }

        // Hash password 
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (email, first_name, last_name, password, mobile, role) VALUES (:email, :first_name, :last_name, :password, :mobile, :role)");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':mobile', $mobile);
            $role = ROLE_USER;
            $stmt->bindParam(':role', $role);
            $result = $stmt->execute();

            if ($result) {
                return ['success' => true, 'message' => 'Registration successful'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Registration failed'];
    }

    /**
     * Login user
     */
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        $user = $this->getUserByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            // Start session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $user['role'];
            
            return ['success' => true, 'message' => 'Login successful', 'user' => $user];
        }

        return ['success' => false, 'message' => 'Invalid email or password'];
    }

    /**
     * Logout user
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        return true;
    }

    /* Get user by email*/
    public function getUserByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    /*Get user by ID*/
    public function getUserById($user_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if email exists
     */
    public function emailExists($email) {
        $user = $this->getUserByEmail($email);
        return $user !== false;
    }

    /**
     * Check if email exists (excluding current user)
     */
    private function emailExistsExcludingUser($email, $exclude_user_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email AND user_id != :exclude_user_id LIMIT 1");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':exclude_user_id', $exclude_user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch() !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Update user profile 
     */
    public function updateProfileFull($user_id, $first_name, $last_name, $email, $mobile = null, $password = null) {
        // Validate input
        if (empty($email) || empty($first_name) || empty($last_name)) {
            return ['success' => false, 'message' => 'First name, last name, and email are required'];
        }

        // Validate first name (letters only)
        $nameValidation = $this->validateName($first_name);
        if (!$nameValidation['valid']) {
            return ['success' => false, 'message' => 'First Name: ' . $nameValidation['message']];
        }

        // Validate last name (letters only)
        $nameValidation = $this->validateName($last_name);
        if (!$nameValidation['valid']) {
            return ['success' => false, 'message' => 'Last Name: ' . $nameValidation['message']];
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Check if email already exists (excluding current user)
        if ($this->emailExistsExcludingUser($email, $user_id)) {
            return ['success' => false, 'message' => 'Email already registered by another user'];
        }

        // Validate mobile 
            if (!empty($mobile)) {
                $normalizedMobile = preg_replace('/[\s\-]/', '', $mobile);
                
                $countryCode = '+962';
                $countryCodeLength = 4;
                if (substr($normalizedMobile, 0, $countryCodeLength) === $countryCode) {
                    $normalizedMobile = '0' . substr($normalizedMobile, $countryCodeLength);
                }
                
                $mobileValidation = $this->validateJordanMobile($normalizedMobile);
                if (!$mobileValidation['valid']) {
                    return ['success' => false, 'message' => $mobileValidation['message']];
                }
                
                $mobile = $normalizedMobile;
            }

        // Validate password if provided
        if (!empty($password)) {
            $passwordValidation = $this->validatePassword($password);
            if (!$passwordValidation['valid']) {
                return ['success' => false, 'message' => $passwordValidation['message']];
            }
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        }

        // Update user
        try {
            if (!empty($password)) {
                // Update with password
                $stmt = $this->db->prepare("UPDATE {$this->table} SET first_name = :first_name, last_name = :last_name, email = :email, mobile = :mobile, password = :password WHERE user_id = :user_id");
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':mobile', $mobile);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $result = $stmt->execute();
            } else {
                // Update without password
                $stmt = $this->db->prepare("UPDATE {$this->table} SET first_name = :first_name, last_name = :last_name, email = :email, mobile = :mobile WHERE user_id = :user_id");
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':mobile', $mobile);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $result = $stmt->execute();
            }

            if ($result) {
                // Update session if email or name changed
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['email'] = $email;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                
                return ['success' => true, 'message' => 'Profile updated successfully'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update profile: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to update profile'];
    }

    /**
     * Get all users (for admin)
     */
    public function getAllUsers($limit = null, $offset = 0) {
        try {
            $sql = "SELECT * FROM {$this->table}";
            if ($limit) {
                $sql .= " LIMIT :limit OFFSET :offset";
            }

            $stmt = $this->db->prepare($sql);
            if ($limit) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Count total users
     */
    public function countUsers() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
            $result = $stmt->fetch();
            return $result['count'];
        } catch (Exception $e) {
            return 0;
        }
    }
}

