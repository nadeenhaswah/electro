<?php
require_once 'config.php';

class DatabasePDO
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $this->conn = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DatabasePDO();
        }
        return self::$instance;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    public function getAllUsers()
    {
        return $this->query("SELECT * FROM users")->fetchAll();
    }
}



$db = DatabasePDO::getInstance();
