<?php
session_start();
// index.php - File chính của dự án
require_once 'vendor/autoload.php';
// 1. Lấy và ép về chữ thường ngay lập tức (strtolower)
$controller = isset($_GET['controller']) ? strtolower($_GET['controller']) : 'auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'showLogin';

if (empty($_SESSION['userID']) && !empty($_COOKIE['remember_user'])) {
    $_SESSION['userID'] = $_COOKIE['remember_user'];
}
// 2. Kiểm tra quyền 
if (empty($_SESSION['userID'])) {
    if ($controller != 'auth') { 
        header('Location: index.php?controller=Auth&action=showLogin');
        exit();
    }
} else {
    // Nếu đã đăng nhập mà vẫn cố vào trang login
    if ($controller == 'auth' && $action != 'logout') {
        header('Location: index.php?controller=Customer&action=index');
        exit();
    }
}

// 3. Tạo tên Controller 
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = 'Controller/' . $controllerName . '.php';

// 4. Kiểm tra và load Controller
if (file_exists($controllerFile)) {
    require_once 'config/Database.php';
    require_once $controllerFile;
    
    // Tạo instance Controller
    $controllerInstance = new $controllerName();
    
    // Gọi Action
    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        die("Action '$action' không tồn tại!");
    }
} else {
    die("Controller '$controllerName' không tồn tại!");
}