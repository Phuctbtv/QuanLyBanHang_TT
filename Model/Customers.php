<?php
class Customer {
    private $conn;
    private $table_name = "customers";

    public $id;
    public $name;
    public $phone;
    public $email;
    public $users_id;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

   // create() method ngắn gọn  
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET name = :name, phone = :phone, email = :email, 
                    users_id = :users_id, status = :status,
                    created_at = :created_at, updated_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        $name = $this->name ?? '';
        $phone = $this->phone ?? '';
        $email = $this->email ?? '';
        $users_id = $this->users_id ?? 1;
        $status = $this->status ?? 1;
        $created_at = $this->created_at ?? date('Y-m-d H:i:s');

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":users_id", $users_id);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":created_at", $created_at);
        
        return $stmt->execute();
    }
    public function countAll(){
        $query = "SELECT COUNT(*) as total
                  FROM customers
                  WHERE status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function readAll() {
        $query = "SELECT c.*, u.username as user_name
                  FROM " . $this->table_name . " c
                  JOIN users u ON c.users_id = u.id
                  WHERE c.status = 1
                  ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function readOne($id) {
        $query = "SELECT c.*, u.username as user_name
                  FROM " . $this->table_name . " c
                  JOIN users u ON c.users_id = u.id
                  WHERE c.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, phone = :phone, email = :email, 
                      users_id = :users_id, status = :status,
                      updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":users_id", $this->users_id);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
    public function updatestatus($id){
        $query="UPDATE " . $this->table_name . "
        SET status= 0,updated_at =NOW()
        WHERE id = :id";
        $stmt =$this->conn->prepare($query);

        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
    public function getCustomers($limit = 1, $offset = 0){
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 1 LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam("offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function search($keyword){
        // tim nam hoac sdt hoac email 
        $query = "SELECT * FROM " . $this->table_name . " WHERE (name LIKE :keyword 
                                                               or email LIKE :keyword
                                                               or phone LIKE :keyword)
                                                               AND status = 1 ";
        $keyword='%'.$keyword.'%';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword',$keyword);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>