<?php require_once __DIR__ . '/../customer/header.php'; ?>
<div class="row mt-3">
    <div class="col-md-3">
        <?php require_once __DIR__ . '/../customer/sidebar.php'; ?>
    </div>
    
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-box me-2"></i>Thêm sản phẩm mới
                </h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=Products&action=store" method="POST" enctype="multipart/form-data">
    
                    <!-- Tên sản phẩm -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        <?php
                        $name_class = 'form-control';
                        $name_value = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
                        
                        if (isset($errors['name']) && !empty($errors['name'])) {
                            $name_class .= ' is-invalid';
                        }
                        ?>
                        <input type="text" class="<?php echo $name_class; ?>" 
                               id="name" name="name" 
                               value="<?php echo $name_value; ?>"
                               placeholder="Nhập tên sản phẩm">
                        <?php if (isset($errors['name']) && !empty($errors['name'])): ?>
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i><?php echo $errors['name']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Mô tả sản phẩm -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                        <?php
                        $description_class = 'form-control';
                        $description_value = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';
                        
                        if (isset($errors['description']) && !empty($errors['description'])) {
                            $description_class .= ' is-invalid';
                        }
                        ?>
                        <textarea class="<?php echo $description_class; ?>" 
                                  id="description" name="description" 
                                  rows="5" 
                                  placeholder="Nhập mô tả chi tiết về sản phẩm..."><?php echo $description_value; ?></textarea>
                        <?php if (isset($errors['description']) && !empty($errors['description'])): ?>
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i><?php echo $errors['description']; ?>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Có thể nhập đầy đủ thông tin, tính năng, ưu điểm của sản phẩm</small>
                    </div>
                    
                    <!-- Mã sản phẩm -->
                    <div class="mb-3">
                        <label for="code" class="form-label">Mã sản phẩm <span class="text-danger">*</span></label>
                        <?php
                        $code_class = 'form-control';
                        $code_value = isset($_POST['code']) ? htmlspecialchars($_POST['code']) : '';
                        
                        if (isset($errors['code']) && !empty($errors['code'])) {
                            $code_class .= ' is-invalid';
                        }
                        ?>
                        <input type="text" class="<?php echo $code_class; ?>" 
                               id="code" name="code" 
                               value="<?php echo $code_value; ?>"
                               placeholder="VD: SP001, IPHONE15, etc.">
                        <?php if (isset($errors['code']) && !empty($errors['code'])): ?>
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i><?php echo $errors['code']; ?>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Mã sản phẩm duy nhất để phân biệt</small>
                    </div>
                    
                    <!-- Người tạo -->
                    <div class="mb-3">
                        <label for="users_id" class="form-label">Người tạo <span class="text-danger">*</span></label>
                        <?php
                        $users_id_class = 'form-control';
                        if (isset($errors['users_id']) && !empty($errors['users_id'])) {
                            $users_id_class .= ' is-invalid';
                        }
                        ?>
                        <select class="<?php echo $users_id_class; ?>" 
                                id="users_id" name="users_id">
                            <option value="">-- Chọn người tạo --</option>
                            <?php if (isset($users) && !empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <?php
                                    $selected = '';
                                    if (isset($_POST['users_id']) && $_POST['users_id'] == $user['id']) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">Không có người dùng nào</option>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Chọn người sẽ tạo và quản lý sản phẩm này</small>
                        <?php if (isset($errors['users_id']) && !empty($errors['users_id'])): ?>
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i><?php echo $errors['users_id']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- chọn ảnh -->
                    <div class="mb-3">
                      <label for="upload-anh">Chọn ảnh:</label>
                      <input type="file" id="upload-anh" name="avatar[]" accept="image/*" multiple="true">
                    </div>
                    <!-- Trạng thái -->
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <?php
                                $status_active_checked = 'checked'; // Mặc định active
                                if (isset($_POST['status'])) {
                                    $status_active_checked = ($_POST['status'] == 1) ? 'checked' : '';
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
                                if (isset($_POST['status'])) {
                                    $status_inactive_checked = ($_POST['status'] == 0) ? 'checked' : '';
                                }
                                ?>
                                <input class="form-check-input" type="radio" name="status" id="status_inactive" 
                                       value="0" <?php echo $status_inactive_checked; ?>>
                                <label class="form-check-label text-danger" for="status_inactive">
                                    <i class="bi bi-x-circle me-1"></i>Inactive
                                </label>
                            </div>
                        </div>
                        <small class="text-muted">Active: Hiển thị và bán được, Inactive: Ẩn và không bán được</small>
                    </div>
                    
                    <!-- Nút hành động -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php?controller=Products&action=index" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Thêm mới
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../customer/footer.php'; ?>