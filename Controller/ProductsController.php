<?php
// Controller/ProductsController.php
require_once 'Model/Products.php';
require_once 'config/Database.php';
require_once 'Model/Users.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
class ProductsController {
    public function index() {
        $database = new Database();
        $db = $database->connect();
        $limit = 1;
        $productsModel = new Products($db);
        if (empty($_GET['page'])){
            $currentPage = 1;
        } else {
            $currentPage = $_GET['page'];
        }
        $offset = ($currentPage - 1) * $limit;
        // kiểm tra xem có bản ghi không
        $product = $productsModel->countAll();
        if (empty($product)){
            $totalRecord = 0;
        } else {
            $totalRecord = $product[0]['total'];
        }
        $totalpage = $totalRecord / $limit;
        $products = $productsModel->getProducts($limit, $offset);
        
        $check="product";
        //b1: vào model để tính tổng bản ghi
        //b2: khởi tạo và gán limit 
        //b2: tính offset
        //b3: tính tổng số trang
        //b5: lấy dữ liệu truyền limit với offset
        //b : kiểm tra $_GET['page'] từ view và kiểm tra có tồn tại hay không nếu không tồn tại gán là giá trị 1
        //b6: nạp dữ liệu vào view
        //b7: set số thự tự của trang

        require_once 'View/layout/products/indexProducts.php';
    }

    public function show($id) {
        $database = new Database();
        $db = $database->connect();
        
        $products = new Products($db);
        $data = $products->readOne($id);
        
        require_once 'View/layout/products/show.php';
    }
    // Xuất danh sách sản phẩm theo file excel
    public function export(){
        $database = new Database();
        $db = $database->connect();
        $productsModel = new Products($db);
        $products = $productsModel->readAll();

        // khởi tạo 1 file excel
        $spreadsheet = new Spreadsheet();
        // lấy trang tính để bắt đầu ghi file
        $sheet = $spreadsheet->getActiveSheet();
        // tạo tiêu đề trang
        $sheet->setTitle('Danh sách sản phẩm');
        // tạo tiêu đề cột
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Tên sản phẩm');
        $sheet->setCellValue('C1', 'Hình ảnh');
        $sheet->setCellValue('D1', 'description');
        $sheet->setCellValue('E1', 'code');
        $sheet->setCellValue('F1', 'Trạng thái');
        $sheet->setCellValue('G1', 'Người tạo');
        $sheet->setCellValue('H1', 'Ngày tạo');

        //đổ dữ liệu vào bắt đầu từ hàng 2
         $row = 2;
         foreach ($products as $item) {
            if ($item['status'] == 1) {
                $item['status'] = "Hoạt động";
            } else {
                $item['status'] = "Bị khóa";
            }
             $sheet->setCellValue('A' . $row, $item['id']);
             $sheet->setCellValue('B' . $row, $item['name']);
             $sheet->setCellValue('D' . $row, $item['description']);
             $sheet->setCellValue('E' .$row, $item['code']);
             $sheet->setCellValue('F' .$row, $item['status']);
             $sheet->setCellValue('G' .$row, $item['user_name']);
             $sheet->setCellValue('H' .$row, date('d/m/Y', strtotime($item['created_at'])));
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
    public function exportSearch() {
        if (!empty($_GET['keyword'])) {
            $keyword = $_GET['keyword'];
        }
        $database = new Database();
        $db = $database->connect();
        $productsModel = new Products($db);
        $products = $productsModel->search($keyword);

        // khởi tạo 1 file excel
        $spreadsheet = new Spreadsheet();
        // lấy trang tính để bắt đầu ghi file
        $sheet = $spreadsheet->getActiveSheet();
        // tạo tiêu đề trang
        $sheet->setTitle('Danh sách sản phẩm');
        // tạo tiêu đề cột
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Tên sản phẩm');
        $sheet->setCellValue('C1', 'Hình ảnh');
        $sheet->setCellValue('D1', 'description');
        $sheet->setCellValue('E1', 'code');
        $sheet->setCellValue('F1', 'Trạng thái');
        $sheet->setCellValue('G1', 'Người tạo');
        $sheet->setCellValue('H1', 'Ngày tạo');

        //đổ dữ liệu vào bắt đầu từ hàng 2
         $row = 2;
         foreach ($products as $item) {
            if ($item['status'] == 1) {
                $item['status'] = "Hoạt động";
            } else {
                $item['status'] = "Bị khóa";
            }
             $sheet->setCellValue('A' . $row, $item['id']);
             $sheet->setCellValue('B' . $row, $item['name']);
             $sheet->setCellValue('D' . $row, $item['description']);
             $sheet->setCellValue('E' .$row, $item['code']);
             $sheet->setCellValue('F' .$row, $item['status']);
             $sheet->setCellValue('G' .$row, $item['user_name']);
             $sheet->setCellValue('H' .$row, date('d/m/Y', strtotime($item['created_at'])));
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
    // Hiển thị form sửa
    public function edit() {
        $check="product";
        $id = isset($_GET['id']) ? $_GET['id'] : die('Thiếu ID');
        
        $database = new Database();
        $db = $database->connect();
        
        $products = new Products($db);
        $product_data = $products->readOne($id);
        
        if (!$product_data) {
            die("Không tìm thấy sản phẩm!");
        }
        
        $user = new Users($db); 
        $users = $user->getAll();
        
        require_once 'View/layout/products/edit.php';
    }
    //checkValidate cho modun import
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
        $userModel = new Users($db);
        $allUsersInDB = $userModel->getAll(); 

        $product = new Products($db);
        $products = $product->readAll();

        $userMap = [];
        foreach ($allUsersInDB as $u) {
            $userMap[$u['username']] = $u['id'];
        }

        // 3. Duyệt dữ liệu
        foreach ($data as $rowNumber => $row) {
            if ($rowNumber == 1) continue; 
            if ($rowNumber == 2) continue;

            if (empty($row['B'])) {
                $errors[$rowNumber]['name'] = "Tên không được để trống";
            }
            if (empty($row['C'])) {
                $errors[$rowNumber]['description'] = "Trường này không được để trống";
            }
            if (empty($row['E'])) {
                $errors[$rowNumber]['code'] = "Code không được để trống";
            } else {
                foreach ($products as $u) {
                    if ($u['code'] == $row['E']) {
                        $errors[$rowNumber]['code'] = "Code này đã tồn tại";
                    }
                }
            }
            // Validate Người tạo
            if (empty($row['F'])) {
                $errors[$rowNumber]['users_id'] = "Người tạo không được để trống";
            } elseif (empty($userMap[$row['F']])) {
                $errors[$rowNumber]['users_id'] = "Người tạo này không tồn tại trong hệ thống";
            }

            // Validate ngày tạo
            if (empty($row['G'])) {
                $errors[$rowNumber]['created_at'] = "Bạn chưa nhập ngày tạo";
            } else {
                // đọc định dạng Ngày/Tháng/Năm từ Excel
                $dateObj = DateTime::createFromFormat('d/m/Y', $row['G']);

                // Kiểm tra xem có đọc thành công không
                if (!$dateObj) {
                    $errors[$rowNumber]['created_at'] = "Nhập sai định dạng ngày/tháng/năm";
                }
            }
            if (!empty($errors[$rowNumber])) {
                $errorsFail[] = $rowNumber; // gán số vào mảng
            }
        }
        if (!empty($errorsFail)) {
            $errors['fail'] = $errorsFail; // gán mảng vào key là fail
        }
        return $errors;
    }

    // hiển thị form import
    public function importshow() {
        require_once 'View/layout/products/import.php';
    }
    // hàm để xử lý nhập excel
    public function import() {
        // gọi đến check validate
        $errors = $this->validateImport();
        if (!empty($errors['fail'])) {
            $failX = $errors['fail'];
        } else {
            $failX = [];
        }
        if (!empty($errors)) {
            require_once 'View/layout/products/import.php';
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
        $product = new Products($db);
        foreach ($data as $rowNumber => $row) {
            if ($rowNumber > 2) {
                if (!in_array($rowNumber, $failX)) {
                    if ($row['D'] == "Hoạt động") {
                        $status = 1;
                    } else {
                        $status = 0;
                    }

                    // đọc định dạng Ngày/Tháng/Năm từ Excel
                    $dateObj = DateTime::createFromFormat('d/m/Y', $row['G']);

                    // Kiểm tra xem có đọc thành công không
                    if ($dateObj) {
                        $created_at = $dateObj->format('Y-m-d H:i:s');
                    } else {
                        $created_at = date('Y-m-d H:i:s');
                    }

                    // Lấy tên từ Excel 
                    $userNameFromExcel = $row['F'];
                    
                    $users_id = $userMap[$userNameFromExcel]; 

                    if (!empty($row['B']) && !empty($row['C']) && !empty($row['E'])) {
                        $product->name = $row['B'];
                        $product->description = $row['C'];
                        $product->status = $status;
                        $product->code = $row['E'];
                        $product->created_at = $created_at;
                        $product->users_id = $users_id;

                        $product->create();
                    }
                } 
            } 
        }

        header("Location: index.php?controller=Products&action=index&msg=success");
        exit;
    }

    public function checkValidate() {
        $errors = [];
        
        // Validate name
        if(isset($_POST['name']) && trim($_POST['name']) == "") {
            $errors['name'] = "Bạn phải nhập tên sản phẩm";
        } else if(!empty($_POST['name']) && strlen($_POST['name']) > 45) {
            $errors['name'] = "Tên sản phẩm không được vượt quá 45 ký tự";
        }
        
        // Validate description
        if(isset($_POST['description']) && trim($_POST['description']) == "") {
            $errors['description'] = "Bạn phải nhập mô tả";
        }
        else if(strlen($_POST['description'])>20){
            $errors['description']="Bạn nhập quá ký tự";
        }
        
        // Validate code
        if(isset($_POST['code']) && trim($_POST['code']) == "") {
            $errors['code'] = "Bạn phải nhập mã sản phẩm";
        }else if(!empty($_POST['code']) && strlen($_POST['code']) > 45){
            $errors['code'] = "Mã sản phẩm không được vượt quá 45 ký tự";
        }
        
        // Validate users_id
        if(isset($_POST['users_id']) && $_POST['users_id'] == "") {
            $errors['users_id'] = "Bạn phải chọn người tạo";
        } else if(isset($_POST['users_id'])) {
            $database = new Database();
            $db = $database->connect();
            
            $user = new Users($db);
            $data = $user->getById($_POST['users_id']);
            
            if(!$data) {
                $errors['users_id'] = "Không có ID này trong bảng users";
            }
        }
        
        // Validate status
        if(isset($_POST['status']) && !in_array($_POST['status'], ['0', '1'])) {
            $errors['status'] = "Trạng thái không hợp lệ";
        }
        
        return $errors;
    }
    
    // Cập nhật dữ liệu
    public function update() {
        // 1. Kiểm tra validation
        $errors = $this->checkValidate();
        
        // 2. Nếu CÓ LỖI: hiển thị lại form edit
        if (!empty($errors)) {
            $id = $_POST['id'] ?? die('Thiếu ID');
            
            $database = new Database();
            $db = $database->connect();
            
            // Lấy dữ liệu từ POST để giữ lại thông tin đã nhập
            $product_data = $_POST;
            
            // Lấy danh sách users cho dropdown
            $user = new Users($db);
            $users = $user->getAll();
            
            // Truyền $errors và $product_data sang view
            require_once 'View/layout/products/edit.php';
            exit();
        }
        
        // 3. Nếu KHÔNG CÓ LỖI: cập nhật database
        $id = $_POST['id'] ?? die('Thiếu ID');
        
        $database = new Database();
        $db = $database->connect();
        
        $products = new Products($db);
        // lấy ra thông tin của ảnh đó là trường photo muốn lấy ra phải lấy đúng photo của id cần xem
        $products_data = $products->getProductsByIds($id);// thế ta lấy được thông tin ảnh cũ là $products_data['photo'] ,getProductsByIds($id)==> nó đang ở dạng mảng
        // kiểm tra xem người dùng có mở chọn file ảnh hay không
        // nếu người dùng chọn file thì
        if (!empty(array_filter($_FILES['photo']['name'])) && empty(array_filter($_FILES['photo']['error']))) {
            foreach ($_FILES['photo']['name'] as $key => $file) {
                //khởi tạo link ảnh mới
                $linkNew = dirname(__DIR__) . "/View/layout/upload/" . $file;
                //khởi tạo để lấy đúng tmp của file theo vòng lặp
                $fileTmp = $_FILES['photo']['tmp_name'][$key];
                //thì di chuyển file ảnh đó từ tmp đến link folder ảnh cần lưu
                if (move_uploaded_file($fileTmp, $linkNew)) {//sau khi di chuyển file ảnh đến file upload cần lưu thì cho filename vào 1 mảng
                    $listFile [] = $file;
                    //sau khi thêm ảnh xong thì kiểm tra xem csdl có chứa ảnh cũ không nếu có thì xóa đi
                    //xóa khi đã thành công thêm file ảnh
                    //kiểm tra xem trường photo đó có thông tin ảnh chưa
                    //lấy link thông tin ảnh cũ
                    // để xóa ảnh cũ mà ảnh cũ đang lưu ở dạng chuỗi thì ta phải explode
                    if (!empty($products_data[0]['photo'])) {
                        $listFileold = explode(',', $products_data[0]['photo']);
                        foreach ($listFileold as $key) {
                            $linkOld = dirname(__DIR__) . "/View/layout/upload/" . $key;
                                unlink($linkOld);
                        }
                    } 
               }
            }
            if (!empty($listFile)) {//sau khi xóa file ảnh cũ đi thì ta sẽ thêm file ảnh mới vào nhưng phải chuyển từ mảng sang chuỗi nhé 
                $products->photo = implode(',', $listFile);
            }
        } else {// nếu người dùng không chọn thì set thông tin ảnh như ban đầu
            $products->photo = $products_data[0]['photo'];
        }
        // Gán dữ liệu
        $products->id = $id;
        $products->name = $_POST['name'] ?? '';
        $products->description = $_POST['description'] ?? '';
        $products->code = $_POST['code'] ?? '';
        $products->users_id = $_POST['users_id'] ?? 1;
        $products->status = $_POST['status'] ?? 1;
        
        if($products->update()) {
            header('Location: index.php?controller=Products&action=index');
            exit();
        } else {
            echo "Cập nhật thất bại!";
        }
    }
    
    // Hiển thị form tạo mới
    public function create() {
        $check="product";
        $database = new Database();
        $db = $database->connect();
        
        // Lấy danh sách users để hiển thị dropdown
        $users = new Users($db);
        $users = $users->getAll();
        
        require_once 'View/layout/products/create.php';
    }
    
    // Xử lý tạo mới
    public function store() {
        // 1. Kiểm tra validation
        $errors = $this->checkValidate();
        
        // 2. Nếu CÓ LỖI: hiển thị lại form create
        if (!empty($errors)) {
            $database = new Database();
            $db = $database->connect();
            // Giữ lại dữ liệu đã nhập
            $product_data = $_POST;
            
            // Lấy lại danh sách users
            $user = new Users($db);
            $users = $user->getAll();
            
            require_once 'View/layout/products/create.php';
            exit();
        }
        
        // 3. Nếu KHÔNG CÓ LỖI: tạo mới
        $database = new Database();
        $db = $database->connect();
        
        $products = new Products($db); 
        //tạo đường dẫn lưu trữ ảnh
        // Mặc định là null nếu không có file hoặc upload lỗi
        if (!empty($_FILES['avatar']['name'])) {
            // sau khi lấy được thì ta thêm mảng chứa thông tin file (gửi nhiều file)
            // muốn thêm được ta nên duyệt từng phần tử trong mảng để thêm từng thông tin file đó vào link upload
            // print_r($_FILES['avatar']);
            $key = [];
            for($i = 0; $i < count($_FILES['avatar']['name']); $i++){
                // gán tên file mới 
                $nameFile  = $_FILES['avatar']['name'][$i];
                // gán link tạm vào
                $tmpNew = $_FILES['avatar']['tmp_name'][$i];
                // gán link mới vào đến file upload để lưu ảnh mới
                $linkNew = dirname(__DIR__) . "/View/layout/upload/" . $nameFile;
                //sau đó di chuyển file đó đến file upload
                if (move_uploaded_file($tmpNew, $linkNew)) {
                    // sau đó gán tên file vào 1 mảng
                    $nameFilenew [] = $nameFile;
                }
            }
            if (!empty($nameFilenew)) {
                // sau đó chuyển mảng thành 1 chuỗi ngăn cách nhau bởi dấu chấm phẩy
                $products->photo = implode(',', $nameFilenew);
            }
        }

        // Gán dữ liệu
        $products->name = $_POST['name'] ?? '';
        $products->description = $_POST['description'] ?? '';
        $products->code = $_POST['code'] ?? '';
        $products->users_id = $_POST['users_id'] ?? 1;
        $products->status = $_POST['status'] ?? 1;
        // $products->photo = $fileName;
        
        if($products->create()) {
            header('Location: index.php?controller=Products&action=index');
            exit();
        } else {
            echo "Tạo sản phẩm thất bại!";
        }
    }

    
    // Xóa sản phẩm (update status)
    public function destroy() {
        $id = $_GET['id'] ?? die('Thiếu ID');
        
        $database = new Database();
        $db = $database->connect();
        
        $products = new Products($db); 
        
        if($products->updateStatus($id)) {
            header('Location: index.php?controller=Products&action=index');
            exit();
        } else {
            echo "Lỗi khi xóa sản phẩm!";
        }
    }
    
    // Tìm kiếm
    public function search() {
        $check="product";
        $totalpage = 0;
        $database = new Database();
        $db = $database->connect();
        
        $products = new Products($db); // FIX: Products thay vì Customer
        
        if(!empty($_POST['keyword'])) {
            $keyword = $_POST['keyword'];
            $products = $products->search($keyword);
        } else {
            $error = "Vui lòng nhập từ khóa tìm kiếm";
            $products = $products->getProducts();
        }
        
        require_once 'View/layout/products/indexProducts.php';
    }
}
?>