<?php
class Database {
    private $host = "localhost";
    private $db_name = "store";  // ĐÚNG TÊN DATABASE CỦA BẠN?
    private $username = "root";
    private $password = "";      // MẬT KHẨU CỦA XAMPP
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
            echo "<script>console.log('Kết nối database thành công!')</script>";
        } catch(PDOException $e) {
            die("Lỗi kết nối database: " . $e->getMessage());
        }
        return $this->conn;
    }
}