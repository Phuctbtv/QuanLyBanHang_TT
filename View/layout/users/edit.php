<?php require_once 'View/layout/customer/header.php'; ?>

<div class="row mt-3">
    <div class="col-md-3">
        <?php require_once 'View/layout/customer/sidebar.php'; ?>
    </div>
    
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-person me-2"></i>Sửa tài khoản
                </h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=Users&action=update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $user_data['id']; ?>">
                    
                    <!-- Tên đăng nhập -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <?php
                        $username_class = 'form-control';
                        if (!empty($errors['username'])) {
                            $username_class .= ' is-invalid';
                        }
                        ?>
                        <input type="text" class="<?php echo $username_class; ?>" 
                               id="username" name="username" 
                               value="<?php echo htmlspecialchars($user_data['username']); ?>"
                               placeholder="Nhập tên đăng nhập">
                        <?php if (!empty($errors['username'])): ?>
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i><?php echo $errors['username']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Hình ảnh <span class="text-danger">*</span></label>
                        <br>
                        <img src="View/layout/upload/<?php echo $user_data['photo']; ?>" alt="ảnh" width="50">
                        <input type="file" name="photo">
                    </div>
                    <!-- Trạng thái -->
                    <div class="mb-3">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <?php
                                $status_active_checked = '';
                                if (!isset($user_data['status']) || (int)$user_data['status'] === 1) {
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
                                if (isset($user_data['status']) && (int)$user_data['status'] === 0) {
                                    $status_inactive_checked = 'checked';
                                }
                                ?>
                                <input class="form-check-input" type="radio" name="status" id="status_inactive" 
                                       value="0" <?php echo $status_inactive_checked; ?>>
                                <label class="form-check-label text-danger" for="status_inactive">
                                    <i class="bi bi-x-circle me-1"></i>Inactive
                                </label>
                            </div>
                        </div>
                        <small class="text-muted">Active: Tài khoản hoạt động, Inactive: Tài khoản bị vô hiệu hóa</small>
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