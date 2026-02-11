<?php require_once __DIR__ . '/../customer/header.php'; ?>

<?php 
    // Khởi tạo dữ liệu mặc định nếu chưa có
    if (!isset($product_data)) {
        $product_data = [
            'name' => '',
            'description' => '',
            'code' => '',
            'users_id' => '',
            'status' => 1 // Mặc định là Active
        ];
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
                    <i class="bi bi-box me-2"></i>Sửa sản phẩm 
                </h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=Products&action=update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $product_data['id'] ?>">
                    <!-- Tên sản phẩm -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo !empty($errors['name']) ? 'is-invalid' : ''; ?>" 
                               id="name" name="name" 
                               value="<?= htmlspecialchars($product_data['name']) ?>"
                               placeholder="Nhập tên sản phẩm">
                        <?php if (!empty($errors['name'])): ?>
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i><?= $errors['name'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- hình ảnh -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Hình ảnh <span class="text-danger">*</span></label>
                        <br>
                        <?php $listImg = explode(',', $product_data['photo']); 
                        foreach ($listImg as $key) { ?>
                             <img src="View/layout/upload/<?php echo $key; ?>" alt="ảnh" width="70";>
                         <?php } ?>
                        <br><br><input type="file" name="photo[]" multiple="true">
                    </div>
                    <!-- Mô tả sản phẩm -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                        <textarea class="form-control <?php echo !empty($errors['description']) ? $errors['description'] : ''; ?>" 
                                  id="description" name="description" 
                                  rows="5" 
                                  placeholder="Nhập mô tả chi tiết về sản phẩm..."><?= htmlspecialchars($product_data['description']) ?></textarea>
                        <?php if (!empty($errors['description'])): ?>
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i><?= $errors['description'] ?>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Có thể nhập đầy đủ thông tin, tính năng, ưu điểm của sản phẩm</small>
                    </div>
                    
                    <!-- Mã sản phẩm -->
                    <div class="mb-3">
                        <label for="code" class="form-label">Mã sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo !empty($errors['code']) ? $errors['code'] : ''; ?>" 
                               id="code" name="code" 
                               value="<?= htmlspecialchars($product_data['code']) ?>"
                               placeholder="VD: SP001, IPHONE15, etc.">
                        <?php if (!empty($errors['code'])): ?>
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i><?= $errors['code'] ?>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Mã sản phẩm duy nhất để phân biệt</small>
                    </div>
                    
                    <!-- Người tạo -->
                    <div class="mb-3">
                        <label for="users_id" class="form-label">Người tạo <span class="text-danger">*</span></label>
                        <select class="form-control <?php echo !empty($errors['users_id']) ? $errors['users_id'] : ''; ?>" 
                                id="users_id" name="users_id">
                            <option value="">-- Chọn người tạo --</option>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" 
                                <?php 
                                // Kiểm tra xem có chọn user này không
                                if (isset($product_data['users_id']) && $product_data['users_id'] == $user['id']) {
                                    echo 'selected';
                                }
                                ?>>
                                
                                <?php 
                                // Hiển thị tên user
                                $show_name = $user['username']; // Mặc định hiển thị username    
                                // In ra và bảo vệ XSS
                                echo htmlspecialchars($show_name);
                                ?>
                            </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">Không có người dùng nào</option>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Chọn người sẽ tạo và quản lý sản phẩm này</small>
                        <?php if (!empty($errors['users_id'])): ?>
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i><?= $errors['users_id'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Trạng thái -->
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_active" 
                                       value="1" <?= (int)($product_data['status'] ?? 1) === 1 ? 'checked' : '' ?>>
                                <label class="form-check-label text-success" for="status_active">
                                    <i class="bi bi-check-circle me-1"></i>Active
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_inactive" 
                                       value="0" <?= (int)($product_data['status'] ?? 1) === 0 ? 'checked' : '' ?>>
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
                            <i class="bi bi-arrow-left me-1">Quay lại</i>
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1">Xác nhận</i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../customer/footer.php'; ?>