<?php require_once __DIR__ . '/../customer/header.php'; ?>



<div class="row mt-3">
    <div class="col-md-3">
        <?php require_once __DIR__ . '/../customer/sidebar.php'; ?>
    </div>
    
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-box me-2"></i>Thêm danh mục
                </h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=Categories&action=store" method="POST">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên danh mục<span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Nhập tên danh mục" 
                        value="<?php if (!empty($_POST['name'])){ echo $_POST['name'];}  ?>">
                        <?php
                                if (!empty($errors['name'])){
                                    echo $errors['name'];
                                }
                             ?>
                    </div>
                    <!-- Trạng thái -->
                    <div class="mb-3">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <?php
                                $status_active_checked = '';
                                if (isset($categorie['status'])) {
                                    if ($categorie['status'] == 1) {
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
                                if (isset($categorie['status'])) {
                                    if ($categorie['status'] == 0) {
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
                            <?php if (!empty($errors['status'])): ?>
                                <div class="text-danger small mt-1">
                                    <i class="bi bi-exclamation-circle me-1"></i><?php echo $errors['status']; ?>
                                </div>
                            <?php endif; ?>
                    </div>
                    </div>
                    <!-- Nút hành động -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php?controller=Categories&action=index" class="btn btn-secondary">
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