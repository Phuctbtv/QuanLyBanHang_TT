<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .card h1 { font-size: 24px; margin-bottom: 8px; text-align: center; }
        .card p { color: #64748b; text-align: center; margin-bottom: 24px; font-size: 14px; }
        .input-group { margin-bottom: 16px; position: relative; }
        .input-group i { position: absolute; left: 12px; top: 40px; color: #94a3b8; }
        .input-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        .input-group input { width: 100%; padding: 10px 10px 10px 35px; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn:hover { background: #4338ca; }
        .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #64748b; }
        .footer a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .error { color: #ef4444; background: #fef2f2; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
    <h1>Chào mừng trở lại</h1>
    <p>Vui lòng đăng nhập để tiếp tục</p>
    <?php if (!empty($_SESSION['error'])) { echo $_SESSION['error']; 
            unset($_SESSION['error']); }  ?>

    <?php if(isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="/QuanLyBanHang_TT/index.php?controller=Auth&action=Login" method="POST" novalidate>
        
        <div class="input-group">
            <label>Tên người dùng</label>
            <i class="fas fa-envelope"></i>
            <input type="text" name="username" placeholder="Nhập tên đăng nhập" value="<?= $_POST['username'] ?? '' ?>">
            
            <?php if (!empty($errors['username'])): ?>
                <span style="color: red; font-size: 11px;"><?php echo $errors['username']; ?></span>
            <?php endif; ?>
        </div>

        <div class="input-group">
            <label>Mật khẩu</label>
            <i class="fas fa-lock"></i>
            <input type="password" name="password">
            
            <?php if (!empty($errors['password'])): ?>
                <span style="color: red; font-size: 11px;"><?php echo $errors['password']; ?></span>
            <?php endif; ?>
        </div>

        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 20px;">
            <label style="cursor: pointer;">
                <input type="checkbox" name="remember"> Ghi nhớ tôi
            </label>
            <a href="#" style="color: var(--primary); text-decoration: none;">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="btn">Đăng nhập</button>
    </form>

    <div class="footer">
        Chưa có tài khoản? <a href="/QuanLyBanHang_TT/index.php?controller=Auth&action=showRegister">Đăng ký</a>
    </div>
    </div>
</body>
</html>