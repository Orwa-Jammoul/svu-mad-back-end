<?php
// config/database_simple.php - Minimal version
class Database {
    private $host = "localhost";
    private $db_name = "DishDrop";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            // Try different DSN formats
            $dsnOptions = [
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                "mysql:host={$this->host};dbname={$this->db_name}",
                "mysql:host={$this->host};port=3306;dbname={$this->db_name}"
            ];
            
            foreach ($dsnOptions as $dsn) {
                try {
                    $this->conn = new PDO($dsn, $this->username, $this->password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_TIMEOUT => 5
                    ]);
                    
                    // Test the connection
                    $this->conn->query("SELECT 1");
                    break; // Connection successful, exit loop
                    
                } catch (PDOException $e) {
                    // Try next DSN format
                    continue;
                }
            }
            
            if ($this->conn === null) {
                throw new Exception("All connection attempts failed");
            }
            
        } catch (Exception $exception) {
            // For InfinityFree, provide specific troubleshooting
            $errorMessage = "Connection error: " . $exception->getMessage() . "<br><br>";
            $errorMessage .= "<strong>InfinityFree Troubleshooting:</strong><br>";
            $errorMessage .= "1. Check if database '{$this->db_name}' exists in InfinityFree control panel<br>";
            $errorMessage .= "2. Verify your MySQL credentials in InfinityFree<br>";
            $errorMessage .= "3. Ensure your hosting plan includes MySQL databases<br>";
            $errorMessage .= "4. Try accessing phpMyAdmin from InfinityFree control panel<br>";
            
            if ($this->isLocalhost()) {
                echo $errorMessage;
            } else {
                error_log("Database Error: " . $exception->getMessage());
                echo "Database connection failed. Please try again later.";
            }
        }
        
        return $this->conn;
    }
    
    private function isLocalhost() {
        return in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || 
               strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;
    }
}
?>