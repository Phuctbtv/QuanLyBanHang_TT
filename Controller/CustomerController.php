<?php
// Controller/CustomerController.php
require_once 'Model/Customers.php';
require_once 'config/Database.php';
require_once 'Model/Users.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
class CustomerController {
    public function index() {
        $database = new Database();
        $db = $database->connect();
        // gán biến limit
        $limit = 2;
        $customer = new Customer($db);
        //lấy tổng số bản ghi của khách hàng
        $totalRecord = $customer->countAll();
        if (empty($totalRecord)){
            $totalRecords = 0;
        } else {
            $totalRecords = $totalRecord[0]['total'];
        }
        //lấy tổng số trang
        $page = $totalRecords / $limit;
        //kiểm tra biến GET có tồn tại không để lấy giá trị trang hiện tại
        if (empty($_GET['page'])){
            $currentPage = 1;
        } else {
            $currentPage = $_GET['page'];
        }
        //set offset
        $offset = ($currentPage - 1) * $limit;
        //truyền biến limit và offset để cho model xử lý
        $customers = $customer->getCustomers($limit, $offset); // Lấy tất cả
        $check="customer";
        require_once 'View/layout/customer/index.php';
    }
    //ham hien thi 1 khach hang theo id duoc chon
    public function show($id) {
        $database = new Database();
        $db = $database->connect();

        $customer = new Customer($db);
        $data = $customer->readOne($id); // Lấy 1 khách hàng

    }
    //hàm export excel
    public function export(){
        $database = new Database();
        $db = $database->connect();
        $customer = new Customer($db);
        $customers = $customer->readAll();
        // sau khi lấy được thông tin khách hàng của trang ta khởi tạo excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách khách hàng');
        // Tạo tiêu đề cột
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Tên khách hàng');
        $sheet->setCellValue('C1', 'SĐT');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'Trạng thái');
        $sheet->setCellValue('F1', 'Ngày tạo');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        // Đổ dữ liệu vào các hàng trong excel
        $row = 2;
        foreach ($customers as $item) {
            if ($item['status'] == 1) {
            $item['status'] = "Hoạt động";
            } else {
            $item['status'] = "Bị khóa";
            }
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['name']);
            $sheet->setCellValue('C' . $row, $item['phone']);
            $sheet->setCellValue('D' . $row, $item['email']);
            $sheet->setCellValue('E' . $row, $item['status']);
            $sheet->setCellValue('F' . $row, date('d/m/Y', strtotime($item['created_at'])));
            $row++;
        }
        $end = 'F' . ($row - 1);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $end)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $end)->getAlignment()->setHorizontal('center');
        foreach (range('A', 'F') as $columnID) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        $filename = 'DanhsachKH - ' . date('d-m-Y') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename='. $filename);
        //xóa mọi thứ trong bộ đệm file
        ob_end_clean();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;

    }

    // validate import
   public function validateImport() {
    $errors = [];
    
    // 1. Kiểm tra file cơ bản (Ngoại vi)
    if (empty($_FILES['filePush']['name'])) {
        return ["file" => "Vui lòng chọn file"];
    }

    $extension = pathinfo($_FILES['filePush']['name'], PATHINFO_EXTENSION);
    if ($extension != 'xlsx' && $extension != 'csv' && $extension != 'xls') {
        $errors['format'] = "File không đúng định dạng";
    }

    if ($_FILES['filePush']['size'] > 5000000) { 
        $errors['size'] = "File tải lên tối đa là 5MB";
    }

    if (!empty($errors)) return $errors; 

    // 2. Khởi tạo dữ liệu (NGOÀI vòng lặp)
    $filePath = $_FILES['filePush']['tmp_name'];
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $data = $spreadsheet->getActiveSheet()->toArray('', true, true, true);

    $db = (new Database())->connect();
    $userModel = new Users($db);
    $allUsersInDB = $userModel->getAll(); 

    $customer = new Customer($db);
    $customers = $customer->readAll();

    $userMap = [];
    foreach ($allUsersInDB as $u) {
        $userMap[$u['username']] = $u['id'];
    }

    // 3. Duyệt dữ liệu
    foreach ($data as $rowNumber => $row) {
        if ($rowNumber == 1) continue; 

        if (empty($row['B'])) {
            $errors[$rowNumber]['name'] = "Tên không được để trống";
        }

        // Validate Phone & Unique
        if (empty($row['C'])) {
            $errors[$rowNumber]['phone'] = "SĐT không được để trống";
        } else {
            foreach ($customers as $u) {
                if ($u['phone'] == $row['C']) {
                    $errors[$rowNumber]['phone'] = "SĐT này đã tồn tại";
                }
            }
        }
        // Validate ngày tạo
        if (empty($row['F'])) {
            $errors[$rowNumber]['created_at'] = "Bạn chưa nhập ngày tạo";
        } else {
            // đọc định dạng Ngày/Tháng/Năm từ Excel
            $dateObj = DateTime::createFromFormat('d/m/Y', $row['F']);

            // Kiểm tra xem có đọc thành công không
            if (!$dateObj) {
                $errors[$rowNumber]['created_at'] = "Nhập sai định dạng ngày/tháng/năm";
            }
        }
        // Validate email & Unique
        if (empty($row['D'])) {
            $errors[$rowNumber]['email'] = "Email không được để trống ";
        } else {
            foreach ($customers as $u) {
                if ($u['email'] == $row['D']) {
                    $errors[$rowNumber]['email'] = "Email này đã tồn tại";
                }
            }
        }

        // Validate Người tạo
        if (empty($row['G'])) {
            $errors[$rowNumber]['users_id'] = "Người tạo không được để trống";
        } elseif (empty($userMap[$row['G']])) {
            $errors[$rowNumber]['users_id'] = "Người tạo này không tồn tại trong hệ thống";
        }
        if (!empty($errors[$rowNumber])) {
            $errorsFail[] = $rowNumber; // gán chuỗi vào mảng
        }
    }
    if (!empty($errorsFail)) {
        $errors['fail'] = $errorsFail; // gán mảng vào key là fail
    }
    return $errors;
}
    // hàm show lên form nhập
    public function importshow(){
        // Truyền dữ liệu sang view
        require_once 'View/layout/customer/import.php';
    }


    // hàm xử lý nhập excel lên
    public function import() {
        $errors = $this->validateImport();
        if (!empty($errors['fail'])) {
            $failRows = $errors['fail'];
        } else {
            $failRows = [];
        }
        if (!empty($errors)) {
            require_once __DIR__ . '/../View/layout/customer/import.php';
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
        foreach ($data as $rowNumber => $row) {
            if ($rowNumber > 1) {
                if (!in_array($rowNumber, $failRows)) {
                    if ($row['E'] == "Hoạt động") {
                        $status = 1;
                    } else {
                        $status = 0;
                    }

                    // đọc định dạng Ngày/Tháng/Năm từ Excel
                    $dateObj = DateTime::createFromFormat('d/m/Y', $row['F']);

                    // Kiểm tra xem có đọc thành công không
                    if ($dateObj) {
                        // Nếu đúng định dạng d/m/Y, ta chuyển sang định dạng Năm-Tháng-Ngày cho CSDL
                        $created_at = $dateObj->format('Y-m-d H:i:s');
                    } else {
                        // Nếu người dùng nhập sai (ví dụ: 32/01/2026 hoặc nhập chữ)
                        // Ta lấy ngày giờ hiện tại để không bị lỗi CSDL
                        $created_at = date('Y-m-d H:i:s');
                    }

                    // Lấy tên từ Excel 
                    $userNameFromExcel = $row['G'];
                    
                    $users_id = $userMap[$userNameFromExcel]; 

                    // Chỉ lưu nếu dòng đó không bị trống id và phone và email và users_id
                    if (!empty($row['C']) && !empty($row['D']) && !empty($row['G'])) {
                        $customer->name = $row['B'];
                        $customer->phone = $row['C'];
                        $customer->email = $row['D'];
                        $customer->status = $status;
                        $customer->created_at = $created_at;
                        $customer->users_id = $users_id;

                        $customer->create();
                    }
                } 
            } 
        }

        header("Location: index.php?controller=Customer&action=index&msg=success");
        exit;
    }

    //edit
    // Hiển thị form sửa
    public function edit() {
        // Lấy ID từ URL
        $check="customer";
        $id = isset($_GET['id']) ? $_GET['id'] : die('Thiếu ID');
        $database = new Database();
        $db = $database->connect();
        // Lấy thông tin khách hàng cần sửa
        $customer = new Customer($db);
        $customer_data = $customer->readOne($id);

        $user = new Users($db);
        $users = $user->getAll();
        if (!$customer_data) {
            die("Không tìm thấy khách hàng!");
        }

        // Truyền dữ liệu sang view
        require_once 'View/layout/customer/edit.php';
    }

    public function checkValidate(){
        $x=[];
        if(isset($_POST['name']) && $_POST['name'] == ""){
            $x['name']="ban phai nhap truong nay";
        }
         else if( !empty($_POST['name']) && strlen($_POST['name'])>45){
            $x['name']="Username khong duoc vuot qua 45 ky ty";
         }
        if(isset($_POST['email']) && $_POST['name'] == ""){
            $x['email']="ban phai nhap truong nay";
        }
        else if(!empty($_POST['email']) &&!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
             $x['email']="Email khong dung dinh dang";
        }
        if(isset($_POST['phone']) && $_POST['phone'] == ""){
            $x['phone']="ban phai nhap truong nay";
        }
        else if(!empty($_POST['phone']) && strlen($_POST['phone'])>10){
           $x['phone']="Phone khong dung dinh dang";
        }
         if(isset($_POST['users_id']) && $_POST['users_id'] == ""){
            $x['users_id']="ban phai nhap truong nay";
        }
        else {
            $database = new Database();
            $db = $database->connect();

            $users = new Users($db);
            if(isset($_POST['users_id'])){
                    $data = $users->getById($_POST['users_id']);
                    if(!$data){
                        $x['users_id']="khong co id nay trong bang users";
                    }
            }else{
                 $x['users_id']="ban phai nhap truong nay";
            }
        }
        return $x;

    }

    //cập nhật dữ liệu sau khi lấy thông tin từ form
    public function update() {
    // 1. Kiểm tra validation
    $errors = $this->checkValidate();

    // 2. Nếu CÓ LỖI: hiển thị lại form
    if (!empty($errors)) {
        $id = $_POST['id'] ?? die('Thiếu ID');

        $database = new Database();
        $db = $database->connect();

        // Lấy dữ liệu từ form (không từ database)
        $customer_data = $_POST;

        // Lấy danh sách users cho dropdown
        $user = new Users($db);
        $users = $user->getAll();

        // Truyền $errors sang view để hiển thị lỗi
        require_once __DIR__ . '/../View/layout/customer/edit.php';
        exit();
    }

    // 3. Nếu KHÔNG CÓ LỖI: cập nhật database
    $id = $_POST['id'] ?? die('Thiếu ID');

    $database = new Database();
    $db = $database->connect();

    $customer = new Customer($db);

    $customer->id = $id;
    $customer->name = $_POST['name'] ?? '';
    $customer->phone = $_POST['phone'] ?? '';
    $customer->email = $_POST['email'] ?? '';
    $customer->users_id = $_POST['users_id'] ?? 1;
    $customer->status = $_POST['status'] ?? 1;

    if($customer->update()) {
        header('Location: index.php?controller=customer&action=index');
        exit();
    }
}
    // phương thức này để hiển thị form để người dùng nhập dữ liệu
        public function create() {
            $check="customer";
            $database = new Database();
            $db = $database->connect();

            // LẤY DANH SÁCH USERS ĐỂ HIỂN THỊ DROPDOWN
            $user = new Users($db);
            $users = $user->getAll();
            // TRUYỀN BIẾN $users SANG VIEW
            require_once __DIR__ . '/../View/layout/customer/create.php';
            }
    //hàm này để xử lý dữ liệu khi người dùng submit form
  // store() method ngắn gọn
    public function store() {
    $database = new Database();
    $db = $database->connect();
    $customer = new Customer($db);

    // 1. PHẢI GỌI HÀM CHECK TRƯỚC để lấy mảng lỗi
    $errors = $this->checkValidate();

    // 2. KIỂM TRA MẢNG LỖI: Nếu mảng $errors có dữ liệu (!empty)
    if (!empty($errors)) {

        $customer_data = $_POST; // Giữ lại dữ liệu đã nhập để hiện lên form

        // Lấy danh sách users để nạp lại dropdown chọn người phụ trách
        $user = new Users($db);
        $users = $user->getAll();

        // Nạp View create (View thêm mới)
        require_once __DIR__ . '/../View/layout/customer/create.php';
        exit();
    }
    // 3. NẾU KHÔNG CÓ LỖI (MẢNG RỖNG)
    else {
        $customer->name = $_POST['name'] ?? '';
        $customer->phone = $_POST['phone'] ?? '';
        $customer->email = $_POST['email'] ?? '';
        $customer->users_id = $_POST['users_id'] ?? 1;
        $customer->status = $_POST['status'] ?? 1;

        if($customer->create()) {
            header('Location: index.php?controller=customer&action=index');
            exit();
        }
    }
}

    public function destroy() {  // XÓA $id từ tham số
    // Lấy ID từ URL
    $id = $_GET['id'] ?? die('Thiếu ID');
    $database = new Database();
    $db = $database->connect();
    $customer = new Customer($db);

    if($customer->updatestatus($id)) {
        // Thành công
        header('Location: index.php?controller=customer&action=index');
        exit();
    } else {
        echo "Lỗi khi xóa khách hàng!";
    }

}
public function search(){
        $check="customer";
        $page = 0;
        $database = new Database();
        $db = $database->connect();
        $customer = new Customer($db);
        if(!empty($_POST['keyword'])){
            $keyword=$_POST['keyword'];
            $customers=$customer->search($keyword);
            // session_start();
            // $_SESSION['data_export'] = $customers;
        }else{
           $keyword="vui long nhap tu khoa tim kiem";
           $customers = $customer->readAll();
            // echo $keyword;
        }


        require_once 'View/layout/customer/index.php';
    }
    // hàm export thứ 2 để cho tìm kiếm 
    public function exportSearch(){
        $database = new Database();
        $db = $database->connect();
        if (!empty($_GET['keyword'])){
            $keyword = $_GET['keyword'];
        }
        $customer = new Customer($db);
        $customers = $customer->search($keyword);
        // session_start();
        // $customers = $_SESSION['data_export'];

        // sau khi lấy được thông tin khách hàng của trang ta khởi tạo excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách khách hàng');
        // Tạo tiêu đề cột
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Tên khách hàng');
        $sheet->setCellValue('C1', 'SĐT');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'Trạng thái');
        $sheet->setCellValue('F1', 'Ngày tạo');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        // Đổ dữ liệu vào các hàng trong excel
        $row = 2;
        foreach ($customers as $item) {
            if ($item['status'] == 1) {
            $item['status'] = "Hoạt động";
            } else {
            $item['status'] = "Bị khóa";
            }
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['name']);
            $sheet->setCellValue('C' . $row, $item['phone']);
            $sheet->setCellValue('D' . $row, $item['email']);
            $sheet->setCellValue('E' . $row, $item['status']);
            $sheet->setCellValue('F' . $row, date('d/m/Y', strtotime($item['created_at'])));
            $row++;
        }
        $end = 'F' . ($row - 1);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $end)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $end)->getAlignment()->setHorizontal('center');
        foreach (range('A', 'F') as $columnID) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        $filename = 'DanhsachKH - ' . date('d-m-Y') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename='. $filename);
        //xóa mọi thứ trong bộ đệm file
        ob_end_clean();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        // unset($_SESSION['data_export']);
        exit;

    }
}
?>