<?php require_once __DIR__ . '/../customer/header.php'; ?>

<div class="row mt-3">
    <div class="col-md-3">
        <?php require_once __DIR__ . '/../customer/sidebar.php'; ?>
    </div>
    
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Sửa danh mục
                </h5>
            </div>
            <div class="card-body">
                <!-- Form sửa -->
                <form action="index.php?controller=Categories&action=update" method="POST">
                    <!-- Hidden field để gửi ID -->
                    <input type="hidden" name="id" value="<?php echo $categorie['id']; ?>">
                    
                    <!-- Tên danh mục -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php 
                                   if (!empty($categorie['name'])) {
                                       echo $categorie['name'];
                                   } 
                               ?>"
                               placeholder="Nhập tên danh mục">
                        <?php 
                        if (isset($errors['name'])) {
                            if (!empty($errors['name'])) {
                                echo $errors['name'];
                            }
                        }
                        ?>
                    </div>
                    
                    <!-- Trạng thái -->
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_active" 
                                       value="1" 
                                       <?php 
                                       if (!empty($categorie['status'])) {
                                           if ($categorie['status'] == 1) {
                                               echo 'checked';
                                           }
                                       }
                                       ?>>
                                <label class="form-check-label text-success" for="status_active">
                                    <i class="bi bi-check-circle me-1"></i>Active
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_inactive" 
                                       value="0" 
                                       <?php 
                                       if (!empty($categorie['status'])) {
                                           if ($categorie['status'] == 0) {
                                               echo 'checked';
                                           }
                                       }
                                       ?>>
                                <label class="form-check-label text-danger" for="status_inactive">
                                    <i class="bi bi-x-circle me-1"></i>Inactive
                                </label>
                            </div>
                        </div>
                        <?php 
                        
                            if (!empty($errors['status'])) {
                                echo $errors['status'];
                            }
                        
                        ?>
                    </div>
                    
                    <!-- Nút hành động -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php?controller=Categories&action=index" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../customer/footer.php'; ?>