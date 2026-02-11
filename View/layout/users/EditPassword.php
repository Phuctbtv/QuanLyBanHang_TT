<?php require_once 'View/layout/customer/header.php'; ?>

<div class="row mt-3">
    <div class="col-md-3">
        <?php require_once 'View/layout/customer/sidebar.php'; ?>
    </div>
    
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-person me-2"></i>Cập nhật mật khẩu 
                </h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=Users&action=updatePassword" method="POST">
                    <input type="hidden" name="id" value="<?php echo $user_data['id']; ?>">
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu cần sửa <span class="text-danger">*</span></label>
                        <?php
                        $password_class = 'form-control';
                        if (!empty($errors['password'])) {
                            $password_class .= ' is-invalid';
                        }
                        ?>
                        <input type="password" class="<?php echo $password_class; ?>" 
                               id="password" name="password" 
                               placeholder="Nhập mật khẩu cần sửa">
                        <?php if (!empty($errors['password'])): ?>
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i><?php echo $errors['password']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <?php
                        $confirm_password_class = 'form-control';
                        if (!empty($errors[''])) {
                            $confirm_password_class .= ' is-invalid';
                        }
                        ?>
                        <input type="password" class="<?php echo $confirm_password_class; ?>" 
                               id="confirm_password" name="confirm_password" 
                               placeholder="Nhập mật khẩu cần sửa">
                        <?php if (!empty($errors['confirm_password'])): ?>
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i><?php echo $errors['confirm_password']; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Nút hành động -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php?controller=Users&action=index" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Xác nhận
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../customer/footer.php'; ?>