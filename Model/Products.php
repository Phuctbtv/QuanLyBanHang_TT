<?php
class Products {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $description;
    public $status;
    public $code;
    public $users_id;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

   // create() method ngắn gọn  
    public function create() {
    $query = "INSERT INTO " . $this->table_name . " 
            SET name = :name, description = :description, status = :status,
                code = :code, users_id = :users_id,
                created_at = :created_at, updated_at = :updated_at, photo = :photo";
    
    $stmt = $this->conn->prepare($query);
    
    // Lấy thời gian hiện tại trong PHP
    $current_time = date('Y-m-d H:i:s');
    
    $name = $this->name ?? '';
    $description = $this->description ?? '';
    $status = $this->status ?? 1;
    $code = $this->code ?? '';
    $users_id = $this->users_id ?? 1;
    
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":description", $description); 
    $stmt->bindParam(":status", $status);
    $stmt->bindParam(":code", $code);
    $stmt->bindParam(":users_id", $users_id);
    $stmt->bindParam(":created_at", $current_time);
    $stmt->bindParam(":updated_at", $current_time);
    $stmt->bindParam(":photo", $this->photo);
    
    return $stmt->execute();
}
    //hàm lấy tổng số bản ghi của product
    public function countAll(){
        $query = "SELECT COUNT(*) as total
                  FROM products
                  WHERE status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function readAll() {
        $query = "SELECT c.*, u.username as user_name
                  FROM " . $this->table_name . " c
                  LEFT JOIN users u ON c.users_id = u.id
                  ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function readOne($id) {
        $query = "SELECT c.*, u.username as user_name
                  FROM " . $this->table_name . " c
                  LEFT JOIN users u ON c.users_id = u.id
                  WHERE c.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Hàm lấy nhiều sản phẩm theo ID
    public function getProductsByIds($ids) {
        // Nếu $ids không phải là mảng, chuyển thành mảng
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        
        // Mảng lưu ID hợp lệ
        $valid_ids = [];
        
        // Lọc ID hợp lệ
        foreach ($ids as $id) {
            if (!empty($id)) {
                $valid_ids[] = $id; // Giữ nguyên
            }
        }
        
        // Nếu không có ID hợp lệ
        if (empty($valid_ids)) {
            return [];
        }
        
        // Tạo chuỗi ID cách nhau bởi dấu phẩy
        $id_string = '';
        $count = count($valid_ids);
        for ($i = 0; $i < $count; $i++) {
            if ($i > 0) {
                $id_string .= ',';
            }
            $id_string .= $valid_ids[$i];
        }
        
        $query = "SELECT * FROM $this->table_name WHERE id IN ($id_string)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        // Lấy tất cả kết quả
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $row;
        }
        
        return $results;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                   SET name = :name, description = :description, status = :status,
                   code = :code, users_id = :users_id,
                   created_at = :created_at, updated_at = :updated_at, photo = :photo
                   WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $current_time=date('Y-m-d H:i:s');
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description); 
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":users_id", $this->users_id);
        $stmt->bindParam(":created_at", $current_time);
        $stmt->bindParam(":updated_at", $current_time);
        $stmt->bindParam(":photo", $this->photo);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
    
    public function updatestatus($id){
        $query="UPDATE " . $this->table_name . "
        SET status= 0, updated_at = :updated_at
        WHERE id = :id";
        $current_time=date('Y-m-d H:i:s');
        $stmt =$this->conn->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":updated_at", $current_time);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
    public function getProducts($limit = 1, $offset = 0){
        $query = "SELECT products.*, users.username FROM " . $this->table_name . " INNER JOIN users on products.users_id = users.id WHERE products.status = 1 LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function search($keyword){
        // tim nam hoac sdt hoac email 
        $query = "SELECT products.*, users.username as user_name FROM " . $this->table_name . " INNER JOIN users on products.users_id = users.id WHERE (name LIKE :keyword 
                                                               or code LIKE :keyword
                                                               or description LIKE :keyword)
                                                               AND products.status = 1 ";
        $keyword='%'.$keyword.'%';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword',$keyword);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>