<?php
class Ordersdetail{
	public $conn;

	public $id;
	public $orders_id;
	public $quantity;
	public $price;
	public $products_id;
	public $created_at;
	public $updated_at;

	public function __construct($db) {
        $this->conn = $db;
    }
    // Hàm lấy tất cả chi tiết đơn hàng
    public function getAll() {
        $query = "SELECT * FROM order_details ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getbyID($id){
        $query = "SELECT order_details.products_id
                  FROM order_details 
                  WHERE order_details.orders_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Hàm tạo mới chi tiết đơn hàng
    public function create() {
        $query = "INSERT INTO order_details 
                  SET orders_id = :orders_id, 
                      quantity = :quantity, 
                      price = :price, 
                      products_id = :products_id, 
                      created_at = :created_at, 
                      updated_at = :updated_at";
        
        $stmt = $this->conn->prepare($query);

        // Lấy thời gian hiện tại và set múi giờ theo VietNam
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $current_time = date('Y-m-d H:i:s');
        
        // Bind các tham số
        $stmt->bindParam(":orders_id", $this->orders_id);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":products_id", $this->products_id);
        $stmt->bindParam(":created_at", $current_time);
        $stmt->bindParam(":updated_at", $current_time);

        return $stmt->execute();
    }
    public function delete($id) {
        $query = "DELETE FROM order_details WHERE order_details.orders_id = :orders_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":orders_id", $id);

        return $stmt->execute();
    }
}
?>