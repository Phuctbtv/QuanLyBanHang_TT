<?php 
class Categories{
	public $conn;

	public $id;
	public $name;
	public $status;
	public $created_at;
	public $updated_at;

	public function __construct($db){
		$this->conn = $db;
	}
	//READ
	public function getAll($limit = 1, $offset = 0){
		$query = "SELECT categories.*
				  FROM categories
				  WHERE status = 1 LIMIT :limit OFFSET :offset";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
		$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function readAll(){
		$query = "SELECT categories.*
				  FROM categories
				  WHERE status = 1 ";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	//CREAD
	public function create(){
		$query = "INSERT INTO categories
				  SET name = :name, status = :status, created_at = :created_at, updated_at = :updated_at";
		$stmt = $this->conn->prepare($query);
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$stmt->bindParam(":name", $this->name);
		$stmt->bindParam(":status", $this->status);
		$stmt->bindParam(":created_at",date("Y-m-d H:i:s"));
		$stmt->bindParam(":updated_at",date("Y-m-d H:i:s"));
		return $stmt->execute();
	}
	//UPDATE
	public function update(){
		$query = "UPDATE categories
				  SET name = :name, status = :status, updated_at = :updated_at
				  WHERE id = :id";
		$stmt = $this->conn->prepare($query);
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		// $current_time = date("Y-m-d H:i:s");
		$stmt->bindParam(":name", $this->name);
		$stmt->bindParam(":status", $this->status);
		$stmt->bindParam(":updated_at", date("Y-m-d H:i:s"));
		$stmt->bindParam(":id", $this->id);
		return $stmt->execute();
	}
	//DELETE
	public function delete($id){
		$query = "DELETE FROM categories
				  WHERE categories.id = :id";
		$stmt =$this->conn->prepare($query);
		$stmt->bindParam(":id",$id);
		return $stmt->execute();
	}
	//UPDATE status =0
	public function updatestatus($id){
		$query = "UPDATE categories SET categories.status = 0 
				  WHERE categories.id = :id";
		$stmt =$this->conn->prepare($query);
		$stmt->bindParam(":id",$id);
		return $stmt->execute();
	}
	//lấy 1 danh mục sản phẩm 
	public function getById($id){
    $query = "SELECT categories.* FROM categories WHERE categories.id = ? LIMIT 1";
    $stmt = $this->conn->prepare($query);
    
    
    $stmt->bindParam(1, $id);
    
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
	//Lấy tổng bản ghi của categories
	public function countAll(){
		$query = "SELECT COUNT(*) as total
				  FROM categories
				  WHERE status = 1";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	 public function search($keyword){
        // tim nam hoac sdt hoac email 
        $query = "SELECT * FROM categories WHERE (name LIKE :keyword )
                                                               AND status = 1 ";
        $keyword='%'.$keyword.'%';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword',$keyword);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>