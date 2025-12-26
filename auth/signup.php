<?php
require_once './config/database.php';

if (isset($_POST['submit'])) {

    // Sanitize
    $first_name        = htmlspecialchars(trim($_POST['first_name']));
    $last_name        = htmlspecialchars(trim($_POST['last_name']));
    $email        = htmlspecialchars(trim($_POST['email']));
    $mobile       = htmlspecialchars(trim($_POST['mobile']));
    $password     = htmlspecialchars(trim($_POST['password']));
    $confirmPass  = htmlspecialchars(trim($_POST['confirmPass']));
    $errors = [];


    //Email Validation

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    // Check if email already exists
    $db = DatabasePDO::getInstance();
    $checkEmailSql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
    $result = $db->query($checkEmailSql, [$email]);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if ($row['count'] > 0) {
        $errors[] = "This email is already registered. Please use another one or login.";
    }

    // Mobile Validation
    if (!empty($mobile) && !preg_match('/^\d{10}$/', $mobile)) {
        $errors[] = "Mobile number must be exactly 10 digits";
    }



    //Full Name Validation

    if (
        !preg_match('/^[a-zA-Z]+$/', $first_name) ||
        !preg_match('/^[a-zA-Z]+$/', $last_name)

    ) {
        $errors[] = "All name fields must contain letters only";
    }

    //Password Validation
    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W_]/', $password) ||
        preg_match('/\s/', $password)
    ) {
        $errors[] = "Password does not meet security requirements";
    }

    if ($password !== $confirmPass) {
        $errors[] = "Passwords do not match";
    }

    //Final Result

    if (empty($errors)) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user'; // by default
        $db = DatabasePDO::getInstance();
        $sql = "INSERT INTO users (email,first_name ,last_name, mobile, password)
                VALUES (?, ?, ?, ?, ?)";

        $params = [$email, $first_name, $last_name, $mobile, $hashedPassword];
        $db->query($sql, $params);

        header("Location: ../login.php");
        exit;
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
