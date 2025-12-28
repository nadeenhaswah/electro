<?php

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        require_once dirname(__DIR__) . '/config/constants.php';
        
        if (!isset($GLOBALS['db_config'])) {
            $GLOBALS['db_config'] = require dirname(__DIR__) . '/config/database.php';
        }
        $config = $GLOBALS['db_config'];

        try {
            $host = $config['host'];
            $dbname = $config['database'];
            $username = $config['username'];
            $password = $config['password'];
            $socketPath = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';
            
            // Try Unix socket first for macOS/XAMPP, then fallback to TCP
            if (file_exists($socketPath)) {
                try {
                    $dsn = "mysql:unix_socket={$socketPath};dbname={$dbname}";
                    $this->conn = new PDO($dsn, $username, $password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]);
                } catch (PDOException $e) {
                    // If socket fails, try TCP
                    if ($host === '127.0.0.1') {
                        $host = 'localhost';
                    }
                    $dsn = "mysql:host={$host};dbname={$dbname}";
                    $this->conn = new PDO($dsn, $username, $password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]);
                }
            } else {
                // Use TCP connection (port defaults to 3306 if not specified)
                if ($host === '127.0.0.1') {
                    $host = 'localhost';
                }
                $dsn = "mysql:host={$host};dbname={$dbname}";
                $this->conn = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            }
            
            // Set charset after connection
            $charset = $config['charset'] ?? 'utf8';
            $this->conn->exec("SET NAMES '{$charset}'");
            
            // Explicitly select the database
            $this->conn->exec("USE `{$dbname}`");
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}

