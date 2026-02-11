<?php
// Controller/CustomerController.php
require_once 'Model/Categories.php';
require_once 'config/Database.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
class CategoriesController {
    public function index() {
        $database = new Database();
        $db = $database->connect();
        //khởi tạo biến limit và gán limit
        $limit = 1;
        $categories = new Categories($db);
        //lấy tổng số bản ghi trên categories
        $totalRecord = $categories->countAll();
        //kiểm tra xem có lấy được bản ghi không 
        if (empty($totalRecord)){
            $totalRecords = 0;
        } else {
            $totalRecords = $totalRecord[0]['total'];
        }
        //lấy tổng số trang
        $totalpage = $totalRecords / $limit;
        //kiểm tra xem có tồn tại $get để xem page đang ở trang bao nhiêu
        if (empty($_GET['page'])){
            $currentPage = 1;
        } else {
            $currentPage = $_GET['page'];
        }
        //tính offset
        $offset = ($currentPage - 1) * $limit;
        $categorie = $categories->getAll($limit, $offset);
        $check = "categories";
        require_once 'View/layout/categories/indexCategories.php';
    }
    //ham hien thi 1 khach hang theo id duoc chon
    public function show($id) {
        $database = new Database();
        $db = $database->connect();

        $customer = new Customer($db);
        $data = $customer->readOne($id); // Lấy 1 khách hàng

    }

    //edit
    // Hiển thị form sửa
    public function edit() {
        // kết nối đến db
        $database = new Database();
        $db = $database->connect();
        // lấy id từ form 
        if (empty($_GET['id'])){
            die("Chưa lấy được id");
        }
        $id = $_GET['id'];
        // khởi tạo đối tượng catagories
        $categories = new Categories($db);
        //lấy hết thuộc tính của bảng catagories để hiển thị lại form
        $categorie = $categories->getById($id);
        if (!$categorie) {
            die("Không tìm thấy người dùng!");
        }
        // Truyền dữ liệu sang view
        require_once 'View/layout/categories/edit.php';
    }

    public function checkValidate(){
        $errors = [];
        if (empty($_POST['name'])){
            $errors['name'] = "Bạn không được bỏ trống trường này";
        }else if (strlen($_POST['name']) > 45){
            $errors['name'] = "Bạn nhập quá ký tự cho phép";
        }
        if (empty($_POST['status'])){
            $errors['status'] = "Bạn không được bỏ trống trường này";
        } else if(!in_array($_POST['status'],['1','0'])){
            $errors['status'] = "Không có status này";
        }
        return $errors;
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
            $errors['size'] = "File tải lên tối đa là 5MB";
        }
        if (!empty($errors)){
            return $errors;
        }
        // 2. Khởi tạo dữ liệu (NGOÀI vòng lặp)
    $filePath = $_FILES['filePush']['tmp_name'];
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $data = $spreadsheet->getActiveSheet()->toArray('', true, true, true);

    $db = (new Database())->connect();
    $categories = new Categories($db);
    $categorie = $categories->readAll();
    // 3. Duyệt dữ liệu
    foreach ($data as $rowNumber => $row) {
        if ($rowNumber == 1) continue; 

        if (empty($row['B'])) {
            $errors[$rowNumber]['name'] = "Tên danh mục không được để trống";
        } else {
            foreach ($categorie as $c) {
                if ($row['B'] == $c['name']) {
                    $errors[$rowNumber]['name'] = "Tên danh mục này đã có rồi";
                }
            }
        }
        // Validate ngày tạo
        if (empty($row['D'])) {
            $errors[$rowNumber]['created_at'] = "Bạn chưa nhập ngày tạo";
        } else {
            // đọc định dạng Ngày/Tháng/Năm từ Excel
            $dateObj = DateTime::createFromFormat('d/m/Y', $row['D']);

            // Kiểm tra xem có đọc thành công không
            if (!$dateObj) {
                $errors[$rowNumber]['created_at'] = "Nhập sai định dạng ngày/tháng/năm";
            }
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

    public function importshow() {
        require_once 'View/layout/categories/import.php';
    }
    public function import(){
        $errors = $this->validateImport();
        if (!empty($errors['fail'])) {
            $failRows = $errors['fail'];
        } else {
            $failRows = [];
        }
        if (!empty($errors)) {
            require_once __DIR__ . '/../View/layout/categories/import.php';
        } 
        $filePath = $_FILES['filePush']['tmp_name']; 
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray('', true, true, true);
        $database = new Database();
        $db = $database->connect();
        $categorie = new Categories($db);
        foreach ($data as $rowNumber => $row) {
            if ($rowNumber > 1) {
                if (!in_array($rowNumber, $failRows)) {
                    if ($row['C'] == "Hoạt động") {
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

                    $categorie->name = $row['B'];
                    $categorie->status = $status;
                    $categorie->created_at = $created_at;
                    $categorie->create();
                    
                } 
            } 
        }

        header("Location: index.php?controller=Categories&action=index&msg=success");
        exit;
    }

    public function export() {
        $database = new Database();
        $db = $database->connect();
        $categories = new Categories($db);
        $categorie = $categories->readAll();
        // sau khi lấy được thông tin khách hàng của trang ta khởi tạo excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách khách hàng');
        // Tạo tiêu đề cột
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Tên danh mục');
        $sheet->setCellValue('C1', 'Trạng thái');
        $sheet->setCellValue('D1', 'Ngày tạo');
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        // Đổ dữ liệu vào các hàng trong excel
        $row = 2;
        foreach ($categorie as $item) {
            if ($item['status'] == 1) {
            $item['status'] = "Hoạt động";
            } else {
            $item['status'] = "Bị khóa";
            }
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['name']);
            $sheet->setCellValue('C' . $row, $item['status']);
            $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($item['created_at'])));
            $row++;
        }
        $end = 'D' . ($row - 1);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $end)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $end)->getAlignment()->setHorizontal('center');
        foreach (range('A', 'D') as $columnID) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        $filename = 'DanhsachDM - ' . date('d-m-Y') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename='. $filename);
        //xóa mọi thứ trong bộ đệm file
        ob_end_clean();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
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
        $categorie = $_POST;

        $categories = new Categories($db);
        $categorie = $categories->getById($id);

        // Truyền $errors sang view để hiển thị lỗi
        require_once __DIR__ . '/../View/layout/categories/edit.php';
        exit();
    } else {

    // 3. Nếu KHÔNG CÓ LỖI: cập nhật database
    $id = $_POST['id'] ?? die('Thiếu ID');

    $database = new Database();
    $db = $database->connect();

    $categorie = new Categories($db);

    $categorie->id = $id;
    $categorie->name = $_POST['name'];
    $categorie->status = $_POST['status'];
    if($categorie->update()) {
        header('Location: index.php?controller=categories&action=index');
        exit();
    }
}
    }

    // phương thức này để hiển thị form để người dùng nhập dữ liệu
        public function create() {
            // TRUYỀN BIẾN $users SANG VIEW
            $database = new Database();
            $db = $database->connect();
            $categories = new Categories($db);
            $categorie = $categories->getAll();

            require_once __DIR__ . '/../View/layout/categories/create.php';
            }
    //hàm này để xử lý dữ liệu khi người dùng submit form
  // store() method ngắn gọn
    public function store() {
    //kết nối db
        $database = new Database();
        $db = $database->connect();
    //check validate
    $errors = $this->checkValidate();
    if (!empty($errors)){
        //nếu tồn tại lỗi thì hiển thị lại form lỗi 
        $categories = new Categories($db);
        $categorie = $categories->getAll();
        require_once __DIR__ . '/../View/layout/categories/create.php';
    }else {
        // nếu không có lỗi thì lưu vào csdl
        //khởi tạo đối tượng mới
        $categories = new Categories($db);
        //gán giá trị thu thập được biến post vào các trường của csdl
        $categories->name = $_POST['name'];
        $categories->status = $_POST['status'];
        if($categories->create()) {
            header('Location: index.php?controller=categories&action=index');
            exit();
        }
    }
}

    public function destroy() {  // XÓA $id từ tham số
    // Lấy ID từ URL
    $id = $_GET['id'] ?? die('Thiếu ID');
    $database = new Database();
    $db = $database->connect();
    $categories = new Categories($db);

    if($categories->updatestatus($id)) {
        // Thành công
        header('Location: index.php?controller=Categories&action=index');
        exit();
    } else {
        echo "Lỗi khi xóa khách hàng!";
    }

}
public function search(){
        $check="categories";
        $totalpage = 0;
        $database = new Database();
        $db = $database->connect();

        $categories = new Categories($db);
        if(!empty($_POST['keyword'])){
            $keyword=$_POST['keyword'];
            $categorie=$categories->search($keyword);
        }else{
           $keyword="vui long nhap tu khoa tim kiem";
           $categorie = $categories->readAll();
            // echo $keyword;
        }


        require_once 'View/layout/categories/indexCategories.php';
    }
}
?>