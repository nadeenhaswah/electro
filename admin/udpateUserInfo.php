
<?php
session_start();
require_once 'config/database.php';

if (isset($_POST['submit'])) {

    // Sanitize
    $user_id        = htmlspecialchars(trim($_POST['user_id']));
    $first_name        = htmlspecialchars(trim($_POST['first_name']));
    $last_name        = htmlspecialchars(trim($_POST['last_name']));
    $email        = htmlspecialchars(trim($_POST['email']));
    $mobile       = htmlspecialchars(trim($_POST['mobile']));

    $role  = htmlspecialchars(trim($_POST['role']));
    $errors = [];


    //Email Validation

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
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


    //Final Result

    if (empty($errors)) {

        $db = DatabasePDO::getInstance();
        $sql = "UPDATE users 
        SET email = ?, 
            first_name = ?, 
            last_name = ?, 
            mobile = ?, 
            role = ?
        WHERE user_id = ?";

        $params = [
            $email,
            $first_name,
            $last_name,
            $mobile,
            $role,
            $user_id
        ];

        $db->query($sql, $params);


        $_SESSION['alert_update'] = [
            'type' => 'success',
            'title' => 'User Updated Successfully',
            'text' => 'The user has been Updated .'
        ];
        header("Location: users.php");

        exit;

        exit;
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
