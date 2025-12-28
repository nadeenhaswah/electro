<?php
require_once '../config/constants.php';
require_once '../includes/functions.php';
require_once '../controllers/AuthController.php';

$authController = new AuthController();
$authController->logout();

header('Location: ../');
exit();


