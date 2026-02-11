<?php require_once __DIR__ . '/../customer/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-3">
            <?php require_once __DIR__ . '/../customer/sidebar.php'; ?>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i>Nhập dữ liệu excel
                    </h5>
                    <a href="index.php?controller=Categories&action=index" class="btn btn-light btn-sm shadow-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
                
                <div class="card-body p-4">
                    <label for="filePush" class="form-label fw-bold">Chọn file Excel danh sách danh mục:</label>
                    
                    <form action="index.php?controller=Categories&action=import" method="POST" enctype="multipart/form-data">
                        <div class="input-group mb-3">
                            <input type="file" name="filePush" class="form-control" id="filePush">
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="bi bi-upload me-1"></i> Tải lên ngay
                            </button>
                        </div>
                        
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> Chỉ chấp nhận định dạng .xlsx hoặc .xls, hoặc .csv
                            <a href="#" class="text-decoration-none ms-2">Tải file mẫu tại đây.</a>
                        </div>
                        <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger mt-3">
                            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Opps! Có lỗi xảy ra:</strong>
                            <ul class="mb-0 mt-2">
                                <?php if (!empty($errors['file'])) echo "<li>".$errors['file']."</li>"; ?>
                                <?php if (!empty($errors['format'])) echo "<li>".$errors['format']."</li>"; ?>
                                <?php if (!empty($errors['size'])) echo "<li>".$errors['size']."</li>"; ?>

                                <?php 
                                foreach ($errors as $rowNum => $messages) {
                                    // Chỉ duyệt qua các key là số (số dòng)
                                    if (is_numeric($rowNum)) {
                                        foreach ($messages as $field => $msg) {
                                            echo "<strong>Dòng $rowNum:</strong> $msg ".",";
                                        }
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../customer/footer.php'; ?>