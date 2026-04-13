<?php
class Database {
    // Sá»­ dá»¥ng biáº¿n mÃ´i trÆ°á»ng hoáº·c giÃ¡ trá»‹ máº·c Ä‘á»‹nh cho local
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;
    
    public function __construct() {
        // Sá»­ dá»¥ng hÃ m env() tá»« config.php Ä‘á»ƒ Ä‘á»c file .env
        $this->host = env('DB_HOST', 'localhost');
        $this->db_name = env('DB_NAME', 'booking_restaurant');
        $this->username = env('DB_USER', 'root');
        $this->password = env('DB_PASS', '');
    }
   public function getConnection() {
    $this->conn = null;
    try {
        // Táº¡o káº¿t ná»‘i MySQLi
        $this->conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->db_name
        );

        // Kiá»ƒm tra lá»—i káº¿t ná»‘i
        if ($this->conn->connect_error) {
            throw new Exception("Lá»—i káº¿t ná»‘i: " . $this->conn->connect_error);
        }

        // Set UTF-8 MB4 for full Vietnamese safety
        if (!$this->conn->set_charset("utf8mb4")) {
            throw new Exception("Khong the set charset UTF-8 MB4: " . $this->conn->error);
        }
        if (!$this->conn->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            throw new Exception("Khong the set NAMES utf8mb4: " . $this->conn->error);
        }

    } catch (Exception $exception) {
        echo "Lá»—i káº¿t ná»‘i: " . $exception->getMessage();
    }

    return $this->conn;
}

}
?>
