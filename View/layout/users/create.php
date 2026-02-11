<?php 
require_once __DIR__ . '/../customer/header.php'; 

// Khởi tạo dữ liệu mặc định nếu chưa có
if (!isset($user_data)) {
    $user_data = array(
        'username' => '',
        'status' => 1
    );
}
?>

<div class="row mt-3">
    <div class="col-md-3">
        <?php require_once __DIR__ . '/../customer/sidebar.php'; ?>
    </div>
    
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-person-plus me-2"></i>Thêm tài khoản mới
                </h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=Users&action=store" method="POST" enctype="multipart/form-data">
    
                    <!-- Tên đăng nhập -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <?php
                        $username_class = 'form-control';
                        if (isset($errors['username'])) {
                            if (!empty($errors['username'])) {
                                $username_class .= ' is-invalid';
                            }
                        }
                        ?>
                        <input type="text" class="<?php echo $username_class; ?>" 
                               id="username" name="username" 
                               value="<?php echo htmlspecialchars($user_data['username']); ?>"
                               placeholder="Nhập tên đăng nhập"
                               >
                        <?php if (isset($errors['username'])): ?>
                            <?php if (!empty($errors['username'])): ?>
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle me-1"></i><?php echo $errors['username']; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                     <div class="mb-3">
                        <input type="file" name="photo">
                     </div>
                    
                    <!-- Mật khẩu -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <?php
                        $password_class = 'form-control';
                        if (isset($errors['password'])) {
                            if (!empty($errors['password'])) {
                                $password_class .= ' is-invalid';
                            }
                        }
                        ?>
                        <input type="password" class="<?php echo $password_class; ?>" 
                               id="password" name="password" 
                               placeholder="Nhập mật khẩu"
                               >
                        <?php if (isset($errors['password'])): ?>
                            <?php if (!empty($errors['password'])): ?>
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle me-1"></i><?php echo $errors['password']; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Xác nhận mật khẩu -->
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" 
                               id="confirm_password" name="confirm_password" 
                               placeholder="Nhập lại mật khẩu"
                               >
                    </div>
                    
                    <!-- Trạng thái -->
                    <div class="mb-3">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <?php
                                $status_active_checked = '';
                                if (isset($user_data['status'])) {
                                    if ($user_data['status'] == 1) {
                                        $status_active_checked = 'checked';
                                    }
                                } else {
                                    $status_active_checked = 'checked';
                                }
                                ?>
                                <input class="form-check-input" type="radio" name="status" id="status_active" 
                                       value="1" <?php echo $status_active_checked; ?>>
                                <label class="form-check-label text-success" for="status_active">
                                    <i class="bi bi-check-circle me-1"></i>Active
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <?php
                                $status_inactive_checked = '';
                                if (isset($user_data['status'])) {
                                    if ($user_data['status'] == 0) {
                                        $status_inactive_checked = 'checked';
                                    }
                                }
                                ?>
                                <input class="form-check-input" type="radio" name="status" id="status_inactive" 
                                       value="0" <?php echo $status_inactive_checked; ?>>
                                <label class="form-check-label text-danger" for="status_inactive">
                                    <i class="bi bi-x-circle me-1"></i>Inactive
                                </label>
                            </div>
                        </div>
                        <?php if (isset($errors['status'])): ?>
                            <?php if (!empty($errors['status'])): ?>
                                <div class="text-danger small mt-1">
                                    <i class="bi bi-exclamation-circle me-1"></i><?php echo $errors['status']; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Nút hành động -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php?controller=Users&action=index" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Tạo tài khoản
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../customer/footer.php'; ?>