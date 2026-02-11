<?php
// index.php - File chính của dự án
require_once 'vendor/autoload.php';
// 1. Xác định Controller và Action từ URL
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'customer';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// 2. Tạo tên Controller
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = 'Controller/' . $controllerName . '.php';

// 3. Kiểm tra và load Controller
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