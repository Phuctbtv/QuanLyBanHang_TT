<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #10b981; --bg: #f8fafc; } /* Màu xanh lá cho cảm giác tạo mới */
        body { font-family: 'Inter', sans-serif; background: var(--bg); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        .card h1 { font-size: 24px; margin-bottom: 8px; text-align: center; }
        .card p { color: #64748b; text-align: center; margin-bottom: 24px; font-size: 14px; }
        .input-group { margin-bottom: 16px; position: relative; }
        .input-group i { position: absolute; left: 12px; top: 40px; color: #94a3b8; }
        .input-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        .input-group input { width: 100%; padding: 10px 10px 10px 35px; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn:hover { background: #059669; }
        .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #64748b; }
        .footer a { color: var(--primary); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Tạo tài khoản</h1>
        <p>Tham gia cùng cộng đồng của chúng tôi</p>

        <form action="/QuanLyBanHang_TT/index.php?controller=Auth&action=register" method="POST" novalidate>
            <div class="input-group">
                <label>Tên đăng nhập</label>
                <i class="fas fa-user"></i>
                <input type="text" name="fullname" placeholder="Cris Phạm" value="<?php if (!empty($_POST['fullname'])) { echo $_POST['fullname'];} ?>">
                <?php if (!empty($errors['fullname'])): ?>
                <span style="color: red; font-size: 11px;"><?php echo $errors['fullname']; ?></span>
                <?php endif; ?>
                <?php if (!empty($errors['fail1'])): ?>
                <span style="color: red; font-size: 11px;"><?php echo $errors['fail1']; ?></span>
                <?php endif; ?>
            </div>
            <div class="input-group">
                <label>Mật khẩu</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="password" >
                <?php if (!empty($errors['password'])): ?>
                <span style="color: red; font-size: 11px;"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
            </div>
            <div class="input-group">
                <label>Xác nhận mật khẩu</label>
                <i class="fas fa-check-double"></i>
                <input type="password" name="confirm_password" >
                <?php if (!empty($errors['confirm_password'])): ?>
                <span style="color: red; font-size: 11px;"><?php echo $errors['confirm_password']; ?></span>
                <?php endif; ?>
                <?php if (!empty($errors['fail'])): ?>
                <span style="color: red; font-size: 11px;"><?php echo $errors['fail']; ?></span>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn">Đăng ký ngay</button>
        </form>
        <div class="footer">
            Đã có tài khoản? <a href="/QuanLyBanHang_TT/View/layout/auth/login.php">Đăng nhập</a>
        </div>
    </div>
</body>
</html>