<?php
require_once 'Model/Orders.php';
require_once 'config/Database.php';
require_once 'Model/Customers.php';
require_once 'Model/Users.php';
require_once 'Model/Products.php';
require_once 'Model/Ordersdetail.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
class OrdersController{
	public function index(){
		$database = new Database();
		$db = $database->connect();
		$limit = 5;
		$orders = new Orders($db);
		$totalRecord = $orders->countAll();

		if (empty($totalRecord)){
			$totalRecordnew = 0;
		}else {
			$totalRecordnew = $totalRecord[0]['total_order'];
		}
		$page = ceil($totalRecordnew / $limit);
		if (empty($_GET['page'])){
			$_GET['page'] = 1;
		}
		$offset = ($_GET['page'] - 1) * $limit;
		$order = $orders->getAllstatus($limit,$offset);
		// var_dump($order);
		// die;
		$check = "orders";


		require_once 'View/layout/orders/indexOrders.php';
	}
	//hien thi form tao moi
	public function create(){
		$check = "orders";
		$database = new Database();
		$db = $database->connect();

		//lay danh sach users de hien thi dropdown
		$customers = new Customer($db);
		$customer = $customers->getCustomers();

		$products = new Products($db);
		$product = $products->getProducts();

		$users = new Users($db);
		$user = $users->getAll();
		//lay danh sach 
		require_once 'View/layout/orders/create.php';
	}
	// làm hoàn thiện check validate
   public function checkValidateCreate() {
    $errors = [];

    if (empty($_POST['order_date'])){
    	$errors['order_date'] = "Bạn không được bỏ trống trường này";
    }else{
    	// check định dạng ngày/tháng/năm
    	//nếu không định dạng đúng ngày / tháng /năm thì thông báo bạn nhập sai định dạng ngày tháng năm
    	// Tạo đối tượng DateTime từ format
    	$dateTime = DateTime::createFromFormat('Y-m-d',$_POST['order_date']);
    	if (empty($dateTime)){
    		$errors['order_date'] = "Nhập sai định dạng ngày/tháng/năm";
    	}
    }

    if (empty($_POST['customers_id'])){
    	$errors['customers_id'] = "Bạn không được bỏ trống trường này";
    }else{
    	// check giữa trường nhập người nhập so sánh với dữ liệu trong csdl 
    	// nếu khác thì in ra lỗi là không có khách hàng này trong csdl 
    	// B1 kết nối đến csdl 
    	$database = new Database();
		$db = $database->connect();
    	// B2 truy xuất đến trường id trong bảng Customers 
    	$customers = new Customer($db);
		$customer = $customers->readOne($_POST['customers_id']);
    	// B3 kiểm tra trường nhập với dữ liệu của cột customer_id 
    	if (empty($customer)){
    		$errors['customers_id'] = "Không có khách hàng này";
    	}
    	//B4 nếu lỗi thì báo không có khách hàng này trong csdl
    }

    if (empty($_POST['users_id'])){
    	$errors['users_id'] = "Bạn không được bỏ trống trường này";
    }else{
    	$database = new Database();
    	$db = $database->connect();
    	//check giữa trường nhập người nhập so sánh với dữ liệu trong csdl
    	$users = new Users($db);
    	$user = $users->getById($_POST['users_id']);
    	if (empty($user)){
    		$errors['users_id'] = "Không có người dùng này";
    	}
    	//nếu khác thì in ra lỗi không có người dùng này trong csdl
    }

    if (empty($_POST['product_id'])){
    	$errors['product_id'] = "Bạn không được bỏ trống trường này";
    }else{
    	$database =  new Database();
    	$db = $database->connect();

    	$products = new Products($db);
    	$product = $products->getProductsByIds($_POST['product_id']);
    	if (empty($product)){
    		$errors['product_id'] = "Không có sản phẩm này ";
    	}
    	//check giữa trường nhập so sánh với dữ liệu trong csdl
    	//nếu khác thì in ra lỗi không có sản phẩm này trong csdl
    }

    if (empty($_POST['total_money'])){
    	$errors['total_money'] = "Bạn không được bỏ trống trường này";
    }else{
    	// kiểm tra dữ liệu người nhập có đúng định dạng số hay không hoặc số người nhập <0
    	if (!is_numeric($_POST['total_money'])){
    		$errors['total_money'] = "Bạn nhập số tiền sai định dạng";
    	}
    	if ($_POST['total_money'] < 0){
    		$errors['total_money'] = "Số tiền tối thiểu là 0";
    	}
    	// nếu không phải thì báo lỗi bạn phải nhập đúng định dạng số
    }

    return $errors;
}

	// Hiển thị form sửa đơn hàng
public function edit() {
    // Kết nối database
    $database = new Database();
    $db = $database->connect();
    
    // Lấy ID từ URL
    if (empty($_GET['id'])) {
        die("Không lấy được id");
    }
    
    $id = $_GET['id'];
    
    // Khởi tạo model Orders
    $orders = new Orders($db);
    
    // Lấy thông tin đơn hàng cần sửa
    $orders_data = $orders->getById($id);
    
    if (!$orders_data) {
       die("Không tìm thấy đơn hàng!");
    }
  
    // Lấy danh sách khách hàng, người dùng, sản phẩm để hiển thị dropdown
    $customerModel = new Customer($db);
    $customers = $customerModel->getCustomers();
    
    $userModel = new Users($db);
    $users = $userModel->getAll();
    
    $orderDetail = new Ordersdetail($db);
    $orderDetails = $orderDetail->getbyID($id);

    
    $productModel = new Products($db);
    $products = $productModel->getProducts();
    
    
    
    // Truyền dữ liệu sang view
    require_once 'View/layout/orders/edit.php';
}
// Xử lý cập nhật đơn hàng
public function update() {
    // Kết nối database
    $database = new Database();
    $db = $database->connect();
    
    // Validate dữ liệu
    $errors = $this->checkValidateCreate();
    
    // Lấy ID từ form
    if (empty($_POST['id'])){
    	die("Không có id này");
    }
    $id = $_POST['id'];
    
    if (!empty($errors)) {
        // Nếu có lỗi, lấy lại dữ liệu để hiển thị form
        $order_data = $_POST;
        
        // Lấy lại danh sách dropdown
        $customerModel = new Customer($db);
        $customers = $customerModel->getCustomers();
        
        $userModel = new Users($db);
        $users = $userModel->getAll();
        
        $productModel = new Products($db);
        $products = $productModel->getProducts();
        
        // Truyền sang view
        require_once 'View/layout/orders/edit.php';
        return;
    }
    
    // Khởi tạo model Orders
    $orders = new Orders($db);
    
    // Set dữ liệu từ form
    $orders->id = $id;
    $orders->code = time();
    $orders->order_date = $_POST['order_date'] . ' ' . date('H:i:s');
    $orders->total_money = $_POST['total_money'];
    $orders->status = $_POST['status'] ?? 1;
    $orders->customers_id = $_POST['customers_id'];
    $orders->users_id = $_POST['users_id'];
    
    // Cập nhật đơn hàng
    if ($orders->update()) {
        // Xóa chi tiết đơn hàng cũ
        $orderDetailModel = new Ordersdetail($db);
        $orderDetailModel->delete($id);
        
        
            
            foreach ($_POST['product_id'] as $product_id) {
                $orderDetail = new OrderDetail($db);
                $orderDetail->orders_id = date("Y-m-d H:i:s",time());
                $orderDetail->products_id = $product_id;
                $orderDetail->quantity = 1; // Cần lấy từ form
                $orderDetail->price = 0; // Cần lấy từ database
                $orderDetail->create();
            }
        
        echo "Tạo đơn hàng thất bại";
        header('Location: index.php?controller=Orders&action=index');
        exit();
    } else {
     	echo "Tạo đơn hàng thành công";
        header('Location: index.php?controller=Orders&action=index');
        exit();
    }
}
	//Xóa đơn hàng là set đơn hàng đó là trạng thái không hoạt động
	public function destroy() {
		$id = $_GET['id'] ?? die('Thiếu id');
		//kết nối database
		$database = new Database();
		$db = $database->connect();

		$orders = new Orders($db);

		if($orders->updateStatus($id)) {
			header('Location: index.php?controller=Orders&action=index');
			exit();
		} else {
			echo "Lỗi khi xóa đơn hàng";
		}
	}
	//hàm xuất export excel
	public function export() {
		$database = new Database();
        $db = $database->connect();
        $order = new Orders($db);
        $orders = $order->getAll();

        // khởi tạo 1 file excel
        $spreadsheet = new Spreadsheet();
        // lấy trang tính để bắt đầu ghi file
        $sheet = $spreadsheet->getActiveSheet();
        // tạo tiêu đề trang
        $sheet->setTitle('Danh sách sản phẩm');
        // tạo tiêu đề cột
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Code');
        $sheet->setCellValue('C1', 'order_date');
        $sheet->setCellValue('D1', 'total_money');
        $sheet->setCellValue('E1', 'Trạng thái');
        $sheet->setCellValue('F1', 'Tên khách hàng');
        $sheet->setCellValue('G1', 'Người tạo');
        $sheet->setCellValue('H1', 'Ngày tạo');

        //đổ dữ liệu vào bắt đầu từ hàng 2
         $row = 2;
         foreach ($orders as $item) {
            if ($item['status'] == 1) {
                $item['status'] = "Hoạt động";
            } else {
                $item['status'] = "Bị khóa";
            }
             $sheet->setCellValue('A' . $row, $item['id']);
             $sheet->setCellValue('B' . $row, $item['code']);
             $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($item['order_date'])));
             $sheet->setCellValue('D' .$row, $item['total_money']);
             $sheet->setCellValue('E' .$row, $item['status']);
             $sheet->setCellValue('F' .$row, $item['customer_name']);
             $sheet->setCellValue('G' .$row, $item['user_name']);
             $sheet->setCellValue('H' .$row, date('d/m/Y', strtotime($item['created_at'])));
             $row++;
         }
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $end = 'H' . ($row - 1);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $end)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getStyle('A1:H' . $row)
        ->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER) // Căn giữa ngang
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);   // Căn giữa dọc
        foreach (range('A', 'H') as $columnID) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Danhsachkhachhang.xlsx"');
        //xóa mọi thứ trong bộ đệm file
        ob_end_clean();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
	}
	// hàm validate import excel
	public function validateImport() {
		$errors = [];
		if (empty($_FILES['filePush']['name'])) {
			$errors['file'] = "Bạn chưa nhập file";
			return $errors;
		}
		$extension = pathinfo($_FILES['filePush']['name'], PATHINFO_EXTENSION);
        if ($extension != 'xlsx' && $extension != 'csv' && $extension != 'xls') {
            $errors['format'] = "File không đúng định dạng";
        }
        if ($_FILES['filePush']['size'] > 5000000) {
        	$errors['size'] = "File tải lên vượt quá quy định cho phép";
        }
        if (!empty($errors)) {
        	return $errors;
        }
        $filePath = $_FILES['filePush']['tmp_name'];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $data = $spreadsheet->getActiveSheet()->toArray('', true, true, true);

        $db = (new Database())->connect();
        $customer = new Customer($db);
        $customers = $customer->readAll(); 

        $user = new Users($db);
        $users = $user->getAll();

        $order = new Orders($db);
        $orders = $order->getAll();

        $customerMap = [];
        foreach ($customers as $c) {
            $customerMap[$c['name']] = $c['id'];
        }
        $userMap = [];
        foreach ($users as $u) {
        	$userMap[$u['username']] = $u['id'];
        }

        // 3. Duyệt dữ liệu
        foreach ($data as $rowNumber => $row) {
            if ($rowNumber == 1) continue; 

            if (empty($row['B'])) {
                $errors[$rowNumber]['code'] = "Code không được để trống";
            } else {
            	foreach ($orders as $u) {
                if ($u['code'] == $row['B']) {
                    $errors[$rowNumber]['code'] = "SĐT này đã tồn tại";
                }
            }
        	}
            if (empty($row['C'])) {
                $errors[$rowNumber]['order_date'] = "Trường này không được để trống";
            }
            if (empty($row['D'])) {
            	$errors[$rowNumber]['total_money'] = "Tổng tiền không được để trống";
            } else {
            	if (!is_numeric($row['D'])) {
            		$errors[$rowNumber]['total_money'] = "Tổng tiền phải là kiểu số";
            	}
            }
            if (empty($row['F'])) {
            	$errors[$rowNumber]['customers_id'] = "Tên khách hàng không được để trống";
            } else {
            	if (empty($customerMap[$row['F']])) {
            		$errors[$rowNumber]['customers_id'] = "Khách hàng này không tồn tại";
            	}
            }
            
            // Validate Người tạo
            if (empty($row['G'])) {
                $errors[$rowNumber]['users_id'] = "Người tạo không được để trống";
            } elseif (empty($userMap[$row['G']])) {
                $errors[$rowNumber]['users_id'] = "Người tạo này không tồn tại trong hệ thống";
            }

            // Validate ngày tạo
            if (empty($row['H'])) {
                $errors[$rowNumber]['created_at'] = "Bạn chưa nhập ngày tạo";
            } else {
                // đọc định dạng Ngày/Tháng/Năm từ Excel
                $dateObj = DateTime::createFromFormat('d/m/Y', $row['H']);

                // Kiểm tra xem có đọc thành công không
                if (!$dateObj) {
                    $errors[$rowNumber]['created_at'] = "Nhập sai định dạng ngày/tháng/năm";
                }
            }
            if (!empty($errors[$rowNumber])) {
                $errorsFail[] = $rowNumber; // gán số vào mảng
                $errors['fail'] = $errorsFail; // gán mảng vào key là fail
            }
        }
        return $errors;
	}
	// hiển thị file import
	public function importshow() {
		// Truyền dữ liệu sang view
    require_once 'View/layout/orders/import.php';
	}
	// hàm để nhập file excel
	public function import() {
		$errors = $this->validateImport();
		if (!empty($errors['fail'])) {
			$failRows = $errors['fail'];
		} else {
			$failRows = [];
		}
		if (!empty($errors)) {
            require_once __DIR__ . '/../View/layout/orders/import.php';
        } 
		$filePath = $_FILES['filePush']['tmp_name']; 
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray('', true, true, true);
        $database = new Database();
        $db = $database->connect();

        $userModel = new Users($db);
        $allUsers = $userModel->getAll(); 
        $userMap = [];
        foreach ($allUsers as $u) {
            $userMap[($u['username'])] = $u['id'];
        }
        $customer = new Customer($db);
        $customers = $customer->readAll();
        $customerMap = [];
        foreach ($customers as $c) {
        	$customerMap[$c['name']] = $c['id'];
        }
        $orders = new Orders($db);
        foreach ($data as $rowNumber => $row) {
            if ($rowNumber > 1) {
                if (!in_array($rowNumber, $failRows)) {
                    if ($row['E'] == "Hoạt động") {
                        $status = 1;
                    } else {
                        $status = 0;
                    }

                    // đọc định dạng Ngày/Tháng/Năm từ Excel
                    $dateObj = DateTime::createFromFormat('d/m/Y', $row['H']);

                    // Kiểm tra xem có đọc thành công không
                    if ($dateObj) {
                        $created_at = $dateObj->format('Y-m-d H:i:s');
                    } else {
                        $created_at = date('Y-m-d H:i:s');
                    }


                    // đọc định dạng Ngày/Tháng/Năm từ Excel
                    $dateObj1 = DateTime::createFromFormat('d/m/Y', $row['C']);

                    // Kiểm tra xem có đọc thành công không
                    if ($dateObj1) {
                        $order_date = $dateObj1->format('Y-m-d H:i:s');
                    } else {
                        $order_date = date('Y-m-d H:i:s');
                    }

                    // Lấy tên từ Excel 
                    $userNameFromExcel = $row['G'];
                    
                    $users_id = $userMap[$userNameFromExcel]; 

                    $customerNameFromExcel = $row['F'];
                    $customers_id = $customerMap[$customerNameFromExcel];

   
                    $orders->code = $row['B'];
                    $orders->order_date = $order_date;
                    $orders->total_money = $row['D'];
                    $orders->status = $status;
                    $orders->customers_id = $customers_id;
                    $orders->users_id = $users_id;
                    $orders->created_at = $created_at;

                    $orders->create();
                    
                } 
            } 
        }

        header("Location: index.php?controller=Orders&action=index&msg=success");
        exit;
	}

    public function search(){
        $check="orders";
        $page = 0;
        $database = new Database();
        $db = $database->connect();
        $orders = new Orders($db);
        if(!empty($_POST['keyword'])){
            $keyword=$_POST['keyword'];
            $order=$orders->search($keyword);
            // session_start();
            // $_SESSION['data_export'] = $customers;
        }else{
           $keyword="vui long nhap tu khoa tim kiem";
           $order = $orders->getAll();
            // echo $keyword;
        }


        require_once 'View/layout/orders/indexOrders.php';
    }

    public function exportSearch(){
        if (!empty($_GET['keyword'])) {
            $keyword = $_GET['keyword'];
        }
        $database = new Database();
        $db = $database->connect();
        $order = new Orders($db);
        $orders = $order->search($keyword);

        // khởi tạo 1 file excel
        $spreadsheet = new Spreadsheet();
        // lấy trang tính để bắt đầu ghi file
        $sheet = $spreadsheet->getActiveSheet();
        // tạo tiêu đề trang
        $sheet->setTitle('Danh sách sản phẩm');
        // tạo tiêu đề cột
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Code');
        $sheet->setCellValue('C1', 'order_date');
        $sheet->setCellValue('D1', 'total_money');
        $sheet->setCellValue('E1', 'Trạng thái');
        $sheet->setCellValue('F1', 'Tên khách hàng');
        $sheet->setCellValue('G1', 'Người tạo');
        $sheet->setCellValue('H1', 'Ngày tạo');

        //đổ dữ liệu vào bắt đầu từ hàng 2
         $row = 2;
         foreach ($orders as $item) {
            if ($item['status'] == 1) {
                $item['status'] = "Hoạt động";
            } else {
                $item['status'] = "Bị khóa";
            }
             $sheet->setCellValue('A' . $row, $item['id']);
             $sheet->setCellValue('B' . $row, $item['code']);
             $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($item['order_date'])));
             $sheet->setCellValue('D' .$row, $item['total_money']);
             $sheet->setCellValue('E' .$row, $item['status']);
             $sheet->setCellValue('F' .$row, $item['customer_name']);
             $sheet->setCellValue('G' .$row, $item['user_name']);
             $sheet->setCellValue('H' .$row, date('d/m/Y', strtotime($item['created_at'])));
             $row++;
         }
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $end = 'H' . ($row - 1);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $end)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getStyle('A1:H' . $row)
        ->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER) // Căn giữa ngang
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);   // Căn giữa dọc
        foreach (range('A', 'H') as $columnID) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Danhsachkhachhang.xlsx"');
        //xóa mọi thứ trong bộ đệm file
        ob_end_clean();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }   

	public function store(){
		$database = new Database();
		$db = $database->connect();
		$errors = $this->checkValidateCreate();
		if (!empty($errors)){
			//Nếu có lỗi thì phải ghi lại các trường nhập đó vào trong 1 biến
			// lấy lại danh sách khách hàng , người dùng , sản phẩm do form phải render lấy lại những giá trị đó
			//lay danh sach users de hien thi dropdown
			$customers = new Customer($db);
			$customer = $customers->getCustomers();

			$products = new Products($db);
			$product = $products->getProducts();

			$users = new Users($db);
			$user = $users->getAll();

			require_once 'View/layout/orders/create.php';
		}else{
			//Nếu không có lỗi B1đầu tiên ta phải khởi tạo đối tượng mới
			$orders = new Orders($db);
			//Sau đó lưu dữ liệu đó vào csdl
			$orders->code = time(); 
			// Xử lý order_date: chuyển từ Y-m-d sang DATETIME
	        $orders->order_date = date('Y-m-d H:i:s',strtotime($_POST['order_date']));
			$orders->total_money = $_POST['total_money'];
			$orders->status = 1;
			$orders->customers_id = $_POST['customers_id'];
			$orders->users_id = $_POST['users_id'];
			//Sau đó chuyển hướng đến trang index
			if($orders->create()) {
				//b1: lấy ra id sau khi insert ở bảng order
				 // $order_id = $db->lastInsertId();
				 //tìm hiểu lấy id sau khi thêm sau biến vừa tạo
				//b2: lấy ra trường product_id 
				 //giá trị đẩy lên của multiple là kiểu giá trị mảng
				 	foreach ($_POST['product_id'] as $product_id) {
		            // Kết nối csdl, khởi tạo đối tượng orders_details
		            $orders_details = new Ordersdetail($db);
		            
		            // Set các trường
		            $orders_details->orders_id = $orders->id;
		            $orders_details->products_id = $product_id;
		            $orders_details->quantity = 0; 
		            $orders_details->price = 0; 
		            
		            // B5: Insert vào bảng orders_details
		            $orders_details->create();
				        }
				//b3 : set cho 2 trường quantity với trường price = 0;
				//b4: set 2 trường create_at và update_at = date(,);
				//b4': kết nối csdl, khởi tạo đối tượng orders_details trong thư mục model
				//b5: insert vào bảng orders_details
				//tạo thêm 1 bảng categories bao gồm tên và name và status ,2 trường created_at,updated_at
            header('Location: index.php?controller=Orders&action=index');
            exit();
	        } else {
	            echo "Tạo đơn hàng thất bại!";
	        }

		}
	}
}
?>