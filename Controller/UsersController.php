<?php
require_once 'Model/Users.php';
require_once 'config/Database.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
class UsersController {
    public function index() {
        $database = new Database();
        $db = $database->connect();
        //khởi tạo và set limit 
        $limit = 1;
        $user = new Users($db);
        //lấy tổng số bản ghi của users
        $totalRecord = $user->countAll();
        //kiểm tra xem có lấy được bản ghi không
        if (empty($totalRecord)){
            $totalRecords = 0;
        } else {
            $totalRecords = $totalRecord[0]['total'];
        }
        $totalpage = $totalRecords / $limit;
        //lấy được biến GET để lấy trang hiện tại đang ở trang bao nhiêu
        if (empty($_GET['page'])){
            $currentPage = 1;
        } else {
            $currentPage = $_GET['page'];
        }
        $offset = ($currentPage - 1) * $limit;
        $users = $user->getActiveUsers($limit, $offset);
        $check = "users";
        require_once 'View/layout/users/indexUsers.php';
    }

    // Hiển thị form sửa
    public function edit() {
        $id = isset($_GET['id']) ? $_GET['id'] : die('Thiếu ID');
        
        $database = new Database();
        $db = $database->connect();
        
        $user = new Users($db);
        $user_data = $user->getById($id);
        
        if (!$user_data) {
            die("Không tìm thấy người dùng!");
        }
        $check = "users";
        require_once 'View/layout/users/edit.php';
    }

    // Hiển thị form sửa password
    public function editPassword() {
        $id = isset($_GET['id']) ? $_GET['id'] : die('Thiếu ID');
        
        $database = new Database();
        $db = $database->connect();
        
        $user = new Users($db);
        $user_data = $user->getById($id);
        
        if (!$user_data) {
            die("Không tìm thấy người dùng!");
        }
        $check = "users";
        require_once 'View/layout/users/EditPassword.php';
    }

    public function checkValidate() {
        $errors = [];
        
        // Validate username
        if (isset($_POST['username']) && trim($_POST['username']) == "") {
            $errors['username'] = "Bạn phải nhập tài khoản";
        } else if (isset($_POST['username']) && strlen($_POST['username']) > 45) {
            $errors['username'] = "Tên tài khoản không được vượt quá 45 ký tự";
        }
        
        if(empty($_POST['password'])){
            $errors['password'] = "Bạn chưa nhập mật khẩu";
        }elseif (isset($_POST['password']) && trim($_POST['password']) != "" && strlen($_POST['password']) > 45) {
            $errors['password'] = "Mật khẩu không được vượt quá 45 ký tự";
        }elseif (!empty($_POST['password']) && strlen($_POST['password']) < 6){
            $errors['password'] = "Mật khẩu không được ít hơn 6 ký tự";
        }elseif (isset($_POST['password']) && trim($_POST['password']) != "" && isset($_POST['confirm_password']) && $_POST['password'] != $_POST['confirm_password']) {
            $errors['password'] = "Mật khẩu xác nhận không khớp";
        }

        if (isset($_POST['status']) && !in_array($_POST['status'], ['0', '1'])) {
            $errors['status'] = "Trạng thái không hợp lệ";
        }
        
        return $errors;
    }

    public function checkValidateUpdate(){
        $errors = [];
        // Validate username
        if (isset($_POST['username']) && trim($_POST['username']) == "") {
            $errors['username'] = "Bạn phải nhập tài khoản";
        } else if (isset($_POST['username']) && strlen($_POST['username']) > 45) {
            $errors['username'] = "Tên tài khoản không được vượt quá 45 ký tự";
        }

        if (isset($_POST['status']) && !in_array($_POST['status'], ['0', '1'])) {
            $errors['status'] = "Trạng thái không hợp lệ";
        }

        return $errors;
    }

    public function checkValidateUpdatepassword(){
        $errors = [];
        if(empty($_POST['password'])){
            $errors['password'] = "Bạn chưa nhập mật khẩu";
        }elseif (isset($_POST['password']) && trim($_POST['password']) != "" && strlen($_POST['password']) > 45) {
            $errors['password'] = "Mật khẩu không được vượt quá 45 ký tự";
        }elseif (!empty($_POST['password']) && strlen($_POST['password']) < 6){
            $errors['password'] = "Mật khẩu không được ít hơn 6 ký tự";
        }elseif (isset($_POST['password']) && trim($_POST['password']) != "" && isset($_POST['confirm_password']) && $_POST['password'] != $_POST['confirm_password']) {
            $errors['confirm_password'] = "Mật khẩu xác nhận không khớp";
        }
        return $errors;
    }

    public function updatePassword(){
         // 1. Kiểm tra validation
        $errors = $this->checkValidateUpdatepassword();
        // 2. Nếu CÓ LỖI: hiển thị lại form edit
        if (!empty($errors)) {
            $id = $_POST['id'] ?? die('Thiếu ID');
            
            $database = new Database();
            $db = $database->connect();
            
            // Lấy dữ liệu từ POST để giữ lại thông tin đã nhập
            $user_data = $_POST;
            
            // Truyền $errors và $user_data sang view
            require_once 'View/layout/users/EditPassword.php';
            exit();
        }
        // 3. Nếu KHÔNG CÓ LỖI: cập nhật database
        $id = $_POST['id'] ?? die('Thiếu ID');
        
        $database = new Database();
        $db = $database->connect();
        $user = new Users($db);
        
        // Gán dữ liệu
        $user->id = $id;
        $user->password = $_POST['password'] ?? '';
        $user->updated_at = date('Y-m-d H:i:s'); // Cập nhật thời gian sửa
        if ($user->updatePassword()) {
            header('Location: index.php?controller=Users&action=index');
            exit();
        } else {
            echo "Cập nhật mật khẩu thất bại!";
        }

    }

    // Cập nhật dữ liệu
    public function update() {
        // 1. Kiểm tra validation
        $errors = $this->checkValidateUpdate();
        
        // 2. Nếu CÓ LỖI: hiển thị lại form edit
        if (!empty($errors)) {
            $id = $_POST['id'] ?? die('Thiếu ID');
            
            $database = new Database();
            $db = $database->connect();
            
            // Lấy dữ liệu từ POST để giữ lại thông tin đã nhập
            $user_data = $_POST;
            
            // Truyền $errors và $user_data sang view
            require_once 'View/layout/users/edit.php';
            exit();
        }
        // 3. Nếu KHÔNG CÓ LỖI: cập nhật database
            $id = $_POST['id'] ?? die('Thiếu ID');

            $database = new Database();
            $db = $database->connect();
            $users = new Users($db); 

            // Lấy dữ liệu cũ để lấy tên ảnh hiện tại
            $userData = $users->getById($id); 

            // Gán ảnh cũ cho đối tượng 
            // $users->photo = $userData['photo'];
            // Kiểm tra nếu người dùng CÓ chọn file mới
            if (!empty($_FILES['photo']['name']) && empty($_FILES['photo']['error'])) {

                $linkNew = dirname(__DIR__) . "/View/layout/upload/" . $_FILES['photo']['name'];                
                // B1: Di chuyển ảnh mới vào thư mục thành công thì mới làm bước tiếp theo
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $linkNew)) {
                    
                    // B2: XÓA ẢNH CŨ (Chỉ xóa khi đã có ảnh mới thay thế thành công)
                    if (!empty($userData['photo'])) {
                        $linkOld = dirname(__DIR__) . "/View/layout/upload/" . $userData['photo'];
                            unlink($linkOld);
                    }
                    
                    // B3: Cập nhật tên ảnh mới vào đối tượng để lưu DB
                    $users->photo = $_FILES['photo']['name'];
                }
            } else {// nếu không chọn file thì lấy yên cái cũ
                    $users->photo = $userData['photo'];
            }

            // Gán các dữ liệu khác
            $users->id = $id;
            $users->username = $_POST['username'] ?? '';
            $users->status = $_POST['status'] ?? 1;
            $users->updated_at = date('Y-m-d H:i:s');

            if ($users->update()) {
                header('Location: index.php?controller=Users&action=index');
                exit();
            } else {
                echo "Cập nhật người dùng thất bại!";
            }
        }
    
    // Hiển thị form tạo mới
    public function create() {
        $check = "users";
        require_once 'View/layout/users/create.php';
        
    }
    
    // Xử lý tạo mới
    public function store() {
        // 1. Kiểm tra validation
        $errors = $this->checkValidate();
        
        // 2. Thêm validation riêng cho tạo mới
        if (isset($_POST['password']) && trim($_POST['password']) == "") {
            $errors['password'] = "Bạn phải nhập mật khẩu";
        }
        
        // Nếu CÓ LỖI: hiển thị lại form create
        if (!empty($errors)) {
            // Giữ lại dữ liệu đã nhập
            $user_data = $_POST;
            require_once 'View/layout/users/create.php';
            exit();
        }
        
        // 3. Nếu KHÔNG CÓ LỖI: tạo mới
        $database = new Database();
        $db = $database->connect();
        $user = new Users($db);
        //B1:tạo đường dẫn để chuyển file ảnh đến đó
        $linkFile = dirname(__DIR__) . "/View/layout/upload/" . $_FILES['photo']['name'];  
        //B2:kiểm tra tên file và empty error để xem có tồn tại file đó không
        if (!empty($_FILES['photo']['name']) && empty($_FILES['photo']['error'])) {
            //B3: Nếu tồn tại thì di chuyển từ file tmp đó sang file cần chuyển ảnh vào đó
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $linkFile)){
                $user->photo = $_FILES['photo']['name'];
            }
        }

                 // Gán dữ liệu
        $user->username = $_POST['username'] ?? '';
        $user->password = md5($_POST['password']);
        $user->status = $_POST['status'] ?? 1;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');
        
        if ($user->create()) {
            header('Location: index.php?controller=Users&action=index');
            exit();
        } else {
            echo "Tạo người dùng thất bại!";
        }
    }
    
    // Xóa người dùng (update status)
    public function destroy() {
        $id = $_GET['id'] ?? die('Thiếu ID');
        
        $database = new Database();
        $db = $database->connect();
        
        $user = new Users($db);
        
        if ($user->updateStatus($id, 0)) { 
            header('Location: index.php?controller=Users&action=index');
            exit();
        } else {
            echo "Lỗi khi xóa người dùng!";
        }
    }

    public function export() {
        $database = new Database();
        $db = $database->connect();
        $users = new Users($db);
        $user = $users->getAll();

        // khởi tạo 1 file excel
        $spreadsheet = new Spreadsheet();
        // lấy trang tính để bắt đầu ghi file
        $sheet = $spreadsheet->getActiveSheet();
        // tạo tiêu đề trang
        $sheet->setTitle('Danh sách sản phẩm');
        // tạo tiêu đề cột
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Tên tài khoản');
        $sheet->setCellValue('C1', 'Hình ảnh');
        $sheet->setCellValue('D1', 'Ngày tạo');
        $sheet->setCellValue('E1', 'Trạng thái');

        //đổ dữ liệu vào bắt đầu từ hàng 2
         $row = 2;
         foreach ($user as $item) {
            if ($item['status'] == 1) {
                $item['status'] = "Hoạt động";
            } else {
                $item['status'] = "Bị khóa";
            }
             $sheet->setCellValue('A' . $row, $item['id']);
             $sheet->setCellValue('B' . $row, $item['username']);
             $sheet->setCellValue('D' .$row, date('d/m/Y', strtotime($item['created_at'])));
             $sheet->setCellValue('E' .$row, $item['status']);
             // kiểm tra xem có tồn tại file ảnh hay không và có đường dẫn ảnh trong project không
             if (!empty($item['photo'])) {
                // nếu tồn tại link ảnh trong CSDL thì tại vì ảnh là 1 chuỗi nên ta phải cắt theo dấu phẩy để đưa tên ảnh cụ thể vào mảng
                $fileName = explode(',', $item['photo']);
                foreach ($fileName as $nameFilenew) {
                    $linkFile = dirname(__DIR__) . "/View/layout/upload/" . $nameFilenew;
                    if (!file_exists($linkFile)) {
                    $sheet->setCellValue('C' .$row, '');
                    } else {
                    $drawing = new Drawing();
                    $drawing->setName('AnhKhachHang');
                    $drawing->setPath($linkFile); 
                    $drawing->setHeight(50);       // giúp cột hình ảnh trong excel lới rộng ra
                    $drawing->setCoordinates('C' . $row); // Đặt vào cột  dòng tương ứng
                    // Căn lề 
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    
                    $drawing->setWorksheet($sheet);

                    // Phải tăng chiều cao của dòng Excel để ảnh không đè lên chữ
                    $sheet->getRowDimension($row)->setRowHeight(50);
                    }
                }
             } else {
                    $sheet->setCellValue('C' . $row, '');
                }
             $row++;
         }
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $end = 'E' . ($row - 1);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $end)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getStyle('A1:E' . $row)
        ->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER) // Căn giữa ngang
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);   // Căn giữa dọc
        foreach (range('A', 'E') as $columnID) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Danhsachtaikhoan.xlsx"');
        //xóa mọi thứ trong bộ đệm file
        ob_end_clean();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function exportSearch() {
        $database = new Database();
        $db = $database->connect();
        $users = new Users($db);
        if (!empty($_GET['keyword'])) {
            $keyword = $_GET['keyword'];
            $user = $users->search($keyword);
        }

        // khởi tạo 1 file excel
        $spreadsheet = new Spreadsheet();
        // lấy trang tính để bắt đầu ghi file
        $sheet = $spreadsheet->getActiveSheet();
        // tạo tiêu đề trang
        $sheet->setTitle('Danh sách sản phẩm');
        // tạo tiêu đề cột
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Tên tài khoản');
        $sheet->setCellValue('C1', 'Hình ảnh');
        $sheet->setCellValue('D1', 'Ngày tạo');

        //đổ dữ liệu vào bắt đầu từ hàng 2
         $row = 2;
         foreach ($user as $item) {
            if ($item['status'] == 1) {
                $item['status'] = "Hoạt động";
            } else {
                $item['status'] = "Bị khóa";
            }
             $sheet->setCellValue('A' . $row, $item['id']);
             $sheet->setCellValue('B' . $row, $item['username']);
             $sheet->setCellValue('D' .$row, date('d/m/Y', strtotime($item['created_at'])));
             // kiểm tra xem có tồn tại file ảnh hay không và có đường dẫn ảnh trong project không
             if (!empty($item['photo'])) {
                // nếu tồn tại link ảnh trong CSDL thì tại vì ảnh là 1 chuỗi nên ta phải cắt theo dấu phẩy để đưa tên ảnh cụ thể vào mảng
                $fileName = explode(',', $item['photo']);
                foreach ($fileName as $nameFilenew) {
                    $linkFile = dirname(__DIR__) . "/View/layout/upload/" . $nameFilenew;
                    if (!file_exists($linkFile)) {
                    $sheet->setCellValue('C' .$row, '');
                    } else {
                    $drawing = new Drawing();
                    $drawing->setName('AnhKhachHang');
                    $drawing->setPath($linkFile); 
                    $drawing->setHeight(50);       // giúp cột hình ảnh trong excel lới rộng ra
                    $drawing->setCoordinates('C' . $row); // Đặt vào cột  dòng tương ứng
                    // Căn lề 
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    
                    $drawing->setWorksheet($sheet);

                    // Phải tăng chiều cao của dòng Excel để ảnh không đè lên chữ
                    $sheet->getRowDimension($row)->setRowHeight(50);
                    }
                }
             } else {
                    $sheet->setCellValue('C' . $row, '');
                }
             $row++;
         }
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $end = 'D' . ($row - 1);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $end)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getStyle('A1:D' . $row)
        ->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER) // Căn giữa ngang
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);   // Căn giữa dọc
        foreach (range('A', 'D') as $columnID) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Danhsachtaikhoan.xlsx"');
        //xóa mọi thứ trong bộ đệm file
        ob_end_clean();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function importshow() {
        require_once 'View/layout/users/import.php';
    }

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
            $errors['size'] = "Kích thước file quá lớn";
        }
        if (!empty($errors)){
            return $errors;
        }

        $filePath = $_FILES['filePush']['tmp_name'];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $data = $spreadsheet->getActiveSheet()->toArray('', true, true, true);

        $db = (new Database())->connect();
        $users = new Users($db);
        $user = $users->getAll();
        // 3. Duyệt dữ liệu
        foreach ($data as $rowNumber => $row) {
            if ($rowNumber == 1) continue; 

            if (empty($row['B'])) {
                // $errors[$rowNumber]['username'] = "Tên tài khoản không được để trống";
                $errors['valid']['username']['row'][] = $rowNumber;
                $errors['valid']['username']['content'] = "Tên tài khoản không được để trống";
            } else {
                foreach ($user as $u) {
                    if ($row['B'] == $u['username']) {
                        // $errors[$rowNumber]['username'] = "Tên tài khoản này đã có rồi";
                        $errors['valid']['usernamea']['row'][] = $rowNumber;
                        $errors['valid']['usernamea']['content'] = "Tên tài khoản này đã có rồi";
                    }
                }
            }
            if (empty($row['E'])) {
                // $errors[$rowNumber]['status'] = "Trường status không được để trống";
                $errors['valid']['status']['row'][] = $rowNumber;
                $errors['valid']['status']['content'] = "Trường status không được để trống";
            }
            // Validate ngày tạo
            if (empty($row['D'])) {
                // $errors[$rowNumber]['created_at'] = "Bạn chưa nhập ngày tạo";
                $errors['valid']['created_at']['row'][] = $rowNumber;
                $errors['valid']['created_at']['content'] = "Bạn chưa nhập ngày tạo";
            } else {
                // đọc định dạng Ngày/Tháng/Năm từ Excel
                $dateObj = DateTime::createFromFormat('d/m/Y', $row['D']);

                // Kiểm tra xem có đọc thành công không
                if (!$dateObj) {
                    // $errors[$rowNumber]['created_at'] = "Nhập sai định dạng ngày/tháng/năm";
                    $errors['valid']['created_ata']['row'][] = $rowNumber;
                    $errors['valid']['created_ata']['content'] = "Nhập sai định dạng ngày/tháng/năm";
                }
            }
            if (!empty($errors['valid'])) {
                $errorsFail[] = $rowNumber; // gán chuỗi vào mảng
            }
        }
        if (!empty($errorsFail)) {
            $errors['fail'] = $errorsFail; // gán mảng vào key là fail
        }
        return $errors;
    }
    
    public function import() {
        $errors = $this->validateImport();
        if (!empty($errors['fail'])) {
            $failRows = $errors['fail'];
        } else {
            $failRows = [];
        }
        if (!empty($errors)) {
            require_once __DIR__ . '/../View/layout/users/import.php';
        } 
        $filePath = $_FILES['filePush']['tmp_name']; 
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray('', true, true, true);
        $database = new Database();
        $db = $database->connect();
        $user = new Users($db);
        foreach ($data as $rowNumber => $row) {
            if ($rowNumber > 1) {
                if (!in_array($rowNumber, $failRows)) {
                    if ($row['E'] == "Hoạt động") {
                        $status = 1;
                    } else {
                        $status = 0;
                    }

                    // đọc định dạng Ngày/Tháng/Năm từ Excel
                    $dateObj = DateTime::createFromFormat('d/m/Y', $row['D']);

                    // Kiểm tra xem có đọc thành công không
                    if ($dateObj) {
                        $created_at = $dateObj->format('Y-m-d H:i:s');
                    } else {
                        $created_at = date('Y-m-d H:i:s');
                    }
                    // lấy được link ảnh
                    $drawings = $worksheet->getDrawingCollection();

                    foreach ($drawings as $drawing) {
                        // Lấy tọa độ ô chứa ảnh 
                        $cellCoordinates = $drawing->getCoordinates();
                        
                        // Nếu ảnh nằm ở cột C
                        if (strpos($cellCoordinates, 'C') === 0) {
                            $contents = file_get_contents($drawing->getPath());
                            $imageName = $drawing->getName() . '.' . $drawing->getExtension();
                            
                            // Lưu file ảnh vật lý vào thư mục server
                            file_put_contents('uploads/' . $imageName, $contents);
                            
                        }
                    }

                    $user->username = $row['B'];
                    $user->status = $status;
                    $user->created_at = $created_at;
                    $user->password = $row['F'];
                    $user->photo = $imageName;
                    $user->create();
                    
                } 
            } 
        }

        header("Location: index.php?controller=Users&action=index&msg=success");
        exit;
    }

    // Tìm kiếm
    public function search() {
        $totalpage = 0;
        $check = "users";
        $database = new Database();
        $db = $database->connect();
        
        $user = new Users($db);
        
        if (!empty($_POST['keyword'])) {
            $keyword = $_POST['keyword'];
            $users = $user->search($keyword);
        } else {
            $error = "Vui lòng nhập từ khóa tìm kiếm";
            $users = $user->getAll();
        }
        
        require_once 'View/layout/users/indexUsers.php';
    }
}
?>