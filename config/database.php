<?php
// Database configuration
// For local development, use local MySQL settings
// For production, use the provided credentials

// Check if we're in local development environment
$isLocal = ($_SERVER['HTTP_HOST'] ?? '') === 'localhost:8000' ||
           ($_SERVER['SERVER_NAME'] ?? '') === 'localhost';

if ($isLocal) {
    // Local development settings
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'wishluv_buildcon');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_CHARSET', 'utf8mb4');
} else {
    // Production settings
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u743570205_wishluvbuild');
    define('DB_USER', 'u743570205_wishluvbuild');
    define('DB_PASS', 'Wishluv@2025');
    define('DB_CHARSET', 'utf8mb4');
}

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// Create database connection instance
function getDBConnection() {
    $database = new Database();
    return $database->getConnection();
}
?>
