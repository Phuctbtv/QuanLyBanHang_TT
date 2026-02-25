<?php
require_once 'config/Database.php';
require_once 'Model/Users.php';
class AuthController {
	public function showRegister() {
		require_once 'View/layout/auth/register.php';
	} 
	public function validateRegister() {
		$errors = [];
		if (empty($_POST['fullname'])) {
			$errors['fullname'] = "Bạn chưa nhập tên người dùng";
		} else if (strlen($_POST['fullname']) > 45) {
			$errors['fullname'] = "Bạn nhập tên người dùng không đúng định dạng";
		}
		if (empty($_POST['password'])) {
			$errors['password'] = "Bạn chưa nhập mật khẩu";
		} else if (strlen($_POST['password']) > 45 || strlen($_POST['password']) < 2) {
			$errors['password'] = "Bạn nhập mật khẩu sai định dạng cho phép";
		}
		if (empty($_POST['confirm_password'])) {
			$errors['confirm_password'] = "Bạn chưa nhập xác nhận mật khẩu";
		}
		if (!empty($_POST['password']) && !empty($_POST['confirm_password'])){
			if ($_POST['password'] != $_POST['confirm_password']) {
				$errors['fail'] = "Trường mật khẩu và trường xác nhận mật khẩu không khớp";
			}
		}
		if (!empty($errors)) {
			return $errors;
		} else {
			$database = new Database();
	    	$db = $database->connect();
	    	$user = new Users($db);
	    	$users = $user->getAllregister();
	    	foreach ($users as $u) {
	    		if ($u['username'] == $_POST['fullname']) {
	    			$errors['fail1'] = "Người dùng này đã có tài khoản rồi";
	    		}
	    	}
		}
		return $errors;
	}
	public function Register() {
		$errors = $this->validateRegister();
		if (!empty($errors)) {
			require_once 'View/layout/auth/register.php';
		} else {
			$database = new Database();
        	$db = $database->connect();
        	$user = new Users($db);
        	$users = $user->username = $_POST['fullname'];
        	$users = $user->password = $_POST['password'];
        	$users = $user->status = 1;
        	$users = $user->create();
            header('Location: index.php?controller=Auth&action=showLogin');
	    	exit();
		}
	}
	public function validateLogin() {
		$errors = [];
		if (empty($_POST['username'])) {
			$errors['username'] = "Bạn đang bỏ trống trường tên người dùng";
		}
		if (empty($_POST['password'])) {
			$errors['password'] = "Bạn đang bỏ trống trường password";
		}
		return $errors;

	}
	public function showLogin() {
		require_once 'View/layout/auth/login.php';
	}
	public function Login () {
		$errors = $this->validateLogin();
		if (empty($errors)) {
			$username = $_POST['username'];
			$password = $_POST['password'];
			$hashpass = md5($password);
			$database = new Database();
        	$db = $database->connect();
        	$user = new Users($db);
        	$users = $user->find($username, $hashpass);
        	if (!empty($users)) {
        		$_SESSION['userID'] = $users[0]['id'];
        		if (isset($_POST['remember'])) {
		            // Lưu trong 30 ngày (86400 giây * 30)
		            // Đây chính là dòng giúp tắt trình duyệt mở lại vẫn còn!
		            setcookie("remember_user", $username, time() + (86400 * 30), "/");
		        }
        		$_SESSION['userName'] = $username;
        		header('Location: index.php?controller=customer&action=index');
            	exit();
        	} else {
        		$_SESSION['error'] = "Bạn nhập sai tên đăng nhập hoặc mật khẩu";
        	}
		}
		require_once 'View/layout/auth/login.php'; 
	}
	public function logout() {
	    session_destroy();
	    if (isset($_COOKIE['remember_user'])) {
	        setcookie("remember_user", "", time() + (86400 * 30), "/");
	    }
	    header('Location: index.php?controller=Auth&action=showLogin');
	    exit();
	} 
}
?>