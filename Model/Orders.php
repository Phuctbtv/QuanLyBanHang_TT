<?php 
class Orders{
	public $conn;

	public $id;
	public $code;
	public $order_date;
	public $total_money;
	public $status;
	public $customers_id;
	public $users_id;
	public $created_at;
	public $updated_at;

	public function __construct($db){
		$this->conn = $db;
	}
	//READ
	public function getAll(){
		$query = "SELECT orders.*, 
                     customers.name as customer_name, 
                     users.username as user_name 
              FROM orders
              JOIN customers ON orders.customers_id = customers.id
              JOIN users ON orders.users_id = users.id
              WHERE orders.status = 1
              ORDER BY orders.order_date DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	// đếm tổng số bản ghi
	public function countAll(){
		$query = "SELECT count(*) as total_order
				  FROM orders
				  WHERE status = 1";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	//READ -lấy tất cả nhưng chỉ lấy status=1
	public function getAllstatus($limit = 5, $offset = 0){
		$query = "SELECT orders.*, 
                     customers.name as customer_name, 
                     users.username as user_name 
              FROM orders
              JOIN customers ON orders.customers_id = customers.id
              JOIN users ON orders.users_id = users.id
              WHERE orders.status = 1
              LIMIT :limit OFFSET :offset";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
		$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	//CREAD
	public function create(){
		$query = "INSERT INTO orders
				  SET code = :code, order_date = :order_date, total_money = :total_money, status = :status, customers_id = :customers_id, users_id = :users_id, created_at = :created_at, updated_at = :updated_at";

		$stmt = $this->conn->prepare($query);

		//lay thoi gian hien tai và set mũi giờ theo chuẩn VietNam
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$current_time = date('Y-m-d H:i:s');

	    $status = $this->status ?? 1;
	  	// Đảm bảo order_date có giá trị (nếu không có thì dùng thời gian hiện tại)
    	$order_date = $this->order_date ?? $current_time;

	    $stmt->bindParam(":code", $this->code);
	    $stmt->bindParam(":order_date", $this->order_date); 
	    $stmt->bindParam(":total_money", $this->total_money);
	    $stmt->bindParam(":status", $status);
	    $stmt->bindParam(":customers_id", $this->customers_id);
	    $stmt->bindParam(":users_id", $this->users_id);
	    $stmt->bindParam(":created_at", $current_time);
	    $stmt->bindParam(":updated_at", $current_time);
	    $stmt->execute();
	     return $this->id = $this->conn->lastInsertId();

	}
	//UPDATE
	public function update(){
		$query = "UPDATE orders
				  SET code = :code, order_date = :order_date, total_money = :total_money, status = :status, customers_id = :customers_id, users_id = :users_id, updated_at = :updated_at
				   WHERE id = :id";
		$stmt = $this->conn->prepare($query);
		//lay thoi gian hien tai và set mũi giờ theo chuẩn VietNam
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$current_time = date('Y-m-d H:i:s');
		$status = $this->status ?? 1;
	  	// Đảm bảo order_date có giá trị (nếu không có thì dùng thời gian hiện tại)
    	$order_date = $this->order_date ?? $current_time;

	    $stmt->bindParam(":code", $this->code);
	    $stmt->bindParam(":order_date", $this->order_date); 
	    $stmt->bindParam(":total_money", $this->total_money);
	    $stmt->bindParam(":status", $status);
	    $stmt->bindParam(":customers_id", $this->customers_id);
	    $stmt->bindParam(":users_id", $this->users_id);
	    $stmt->bindParam(":updated_at", $current_time);
	    $stmt->bindParam(":id", $this->id); 
	    return $stmt->execute();
	}
	// READ ONE - Lấy 1 đơn hàng theo ID
    public function getById($id){
        $query = "SELECT orders.*, 
                         customers.name as customer_name, 
                         users.username as user_name 
                  FROM orders
                  JOIN customers ON orders.customers_id = customers.id
                  JOIN users ON orders.users_id = users.id
                  WHERE orders.id = :id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // DELETE - Xóa đơn hàng
    public function delete($id){
        $query = "DELETE FROM orders WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // UPDATE STATUS - Cập nhật trạng thái đơn hàng
    public function updateStatus($id){
        $query = "UPDATE orders 
                  SET status = 0, 
                      updated_at = :updated_at 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $current_time = date('Y-m-d H:i:s');
        
        $stmt->bindParam(":updated_at", $current_time);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }
    // SEARCH - Tìm kiếm đơn hàng
    public function search($keyword){
        $query = "SELECT orders.*, 
                         customers.name as customer_name, 
                         users.username as user_name 
                  FROM orders
                  JOIN customers ON orders.customers_id = customers.id
                  JOIN users ON orders.users_id = users.id
                  WHERE (orders.code LIKE :keyword 
                     OR customers.name LIKE :keyword
                     OR users.username LIKE :keyword) and orders.status = 1
                  ORDER BY orders.order_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $search_keyword = "%" . $keyword . "%";
        $stmt->bindParam(":keyword", $search_keyword);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // hàm thống kê doanh thu bán được
    public function statistical($month, $year) {
	    $query = "SELECT :month as thang,
			    (SELECT COUNT(*) 
			     FROM (SELECT products_id FROM order_details od 
			           JOIN orders o ON od.orders_id = o.id
			           WHERE o.status = 1 AND month(order_date) = :month and year(order_date) = :year
			           GROUP BY products_id) sp
			    ) as tongSP,
			    
			    (SELECT COUNT(*) 
			     FROM orders 
			     WHERE status = 1 AND month(order_date) = :month and year(order_date) = :year
			    ) as tongDH,
			    
			    (SELECT COUNT(*) 
			     FROM (SELECT customers_id FROM orders 
			           WHERE status = 1 AND month(order_date) = :month and year(order_date) = :year
			           GROUP BY customers_id) kh
			    ) as tongKH,
			    
			    (SELECT SUM(total_money) 
			     FROM orders 
			     WHERE status = 1 AND month(order_date) = :month and year(order_date) = :year
			    ) as tongDT";

	    $stmt = $this->conn->prepare($query);
	    $stmt->bindParam(":month", $month);
	    $stmt->bindParam(":year", $year);
	    $stmt->execute();
	    return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function statisticalDay($month, $year) {
		$query = "SELECT o2.order_date ,
				  count(distinct od.products_id) as sumProduct,
				  count(distinct o2.customers_id) as sumCustomer,
				  count(distinct o2.id) as sumOrder,
				  o2.id, total_money	  
			    
			FROM orders o2 join order_details od on o2.id = od.orders_id
			WHERE MONTH(o2.order_date) = :month
			  AND YEAR(o2.order_date) = :year
			GROUP BY o2.id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":month", $month);
	    $stmt->bindParam(":year", $year);
	    $stmt->execute();
	    return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function statisticalUser($month, $year) {
		$query = "SELECT u.username as ten,
    
			    (SELECT COUNT(DISTINCT od.products_id)
			     FROM orders o1 
			     JOIN order_details od ON o1.id = od.orders_id
			     WHERE o1.status = 1 
			       AND MONTH(o1.order_date) = :month 
			       AND YEAR(o1.order_date) = :year
			       AND o1.users_id = u.id
			    ) as tongSP,
			    
			    (SELECT COUNT(*) 
			     FROM orders o1
			     WHERE o1.status = 1 
			       AND MONTH(o1.order_date) = :month 
			       AND YEAR(o1.order_date) = :year
			       AND o1.users_id = u.id
			    ) as tongDH,
			    
			    (SELECT SUM(o1.total_money) 
			     FROM orders o1
			     WHERE o1.status = 1 
			       AND MONTH(o1.order_date) = :month
			       AND YEAR(o1.order_date) = :year
			       AND o1.users_id = u.id
			    ) as tongDT
			    
			FROM orders o2 join users u on o2.users_id = u.id
			WHERE MONTH(o2.order_date) = :month 
			  AND YEAR(o2.order_date) = :year
			  group by ten";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":month", $month);
	    $stmt->bindParam(":year", $year);
	    $stmt->execute();
	    return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
?>