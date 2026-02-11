<?php
// Model/User.php
class Users {
    private $conn;
    private $table_name = "users";

    // Các thuộc tính theo bảng users của bạn
    public $id;
    public $username;
    public $password;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // READ - Lấy tất cả users (dùng cho dropdown)
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE users.status = 1
                  ORDER BY username";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ - Lấy user theo ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // READ - Lấy user theo username (dùng cho login)
    public function getByUsername($username) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE username = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREATE - Tạo user mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET username = :username, 
                      password = :password, 
                      status = :status,
                      created_at = NOW(),
                      updated_at = NOW(),
                      photo = :photo";
        
        $stmt = $this->conn->prepare($query);
        // Hash password trước khi lưu
        // $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(":username", $this->username);
        // $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":password",md5($this->password));
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":photo", $this->photo);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    // UPDATE - Cập nhật user
public function update() {
    // Kiểm tra id có tồn tại không
    if (empty($this->id)) {
        return false;
    }
    
    // Luôn cập nhật mật khẩu (có thể là chuỗi rỗng nếu không đổi)
    $query = "UPDATE " . $this->table_name . " 
              SET username = :username, 
                  status = :status, 
                  updated_at = :updated_at, photo = :photo 
              WHERE id = :id";

    $stmt = $this->conn->prepare($query);
    
    $stmt->bindParam(":username", $this->username);
    $stmt->bindParam(":status", $this->status);
    $stmt->bindParam(":updated_at", date('Y-m-d H:i:s',time()));
    $stmt->bindParam(":photo", $this->photo);
    $stmt->bindParam(":id", $this->id);
    if($stmt->execute()) {
        return true;
    }
    return false;
}
 //Update cập nhật mật khẩu của user
public function updatePassword(){
    if (empty($this->id)) {
        return false;
    }else{
        $query = "UPDATE users
                  SET password = :password
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":password", md5($this->password));
        $stmt->bindParam(":id", $this->id);
        if($stmt->execute()) {
        return true;
        }
        return false;
    }
}

    // DELETE - Xóa user (cẩn thận vì có khóa ngoại)
    public function delete($id) {
        // Kiểm tra xem user có đang được sử dụng không
        $check_query = "SELECT COUNT(*) as count FROM customers WHERE users_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(1, $id);
        $check_stmt->execute();
        $result = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            // Không cho xóa nếu user đang được sử dụng
            return false;
        }
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // VERIFY - Xác thực login
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }

    

    // Lấy users đang hoạt động
    public function getActiveUsers($limit = 1,$offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE status = 1 ORDER BY username LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //lấy tổng bản ghi
    public function countAll(){
        $query = "SELECT COUNT(*) as total
                  FROM users
                  WHERE status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Tìm kiếm user theo keyword
    public function search($keyword) {
        // Kiểm tra keyword có tồn tại
        if (empty($keyword)) {
            return $this->getAll(); // Nếu không có keyword, trả về tất cả
        }
        
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE (username LIKE :keyword 
                OR id LIKE :keyword) and users.status = 1 
                ORDER BY id DESC";
        
        $stmt = $this->conn->prepare($query);
        
        // Thêm % vào keyword để tìm kiếm phần chuỗi
        $search_keyword = "%" . $keyword . "%";
        $stmt->bindParam(":keyword", $search_keyword);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>