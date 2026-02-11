<?php require_once 'header.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-3">
            <?php require_once 'sidebar.php'; ?>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i>Nhập dữ liệu excel
                    </h5>
                    <a href="index.php?controller=Customer&action=index" class="btn btn-light btn-sm shadow-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
                
                <div class="card-body p-4">
                    <label for="filePush" class="form-label fw-bold">Chọn file Excel danh sách khách hàng:</label>
                    
                    <form action="index.php?controller=Customer&action=import" method="POST" enctype="multipart/form-data">
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
                            <div class="alert alert-danger">
                                <strong>Opps! Có lỗi xảy ra:</strong>
                                <ul>
                                    <?php if (!empty($errors['file'])) { ?>
                                        <li><?php echo $errors['file']; ?></li>
                                    <?php } ?>
                                    <?php if (!empty($errors['format'])) { ?>
                                        <li><?php echo $errors['format']; ?></li>
                                    <?php } ?>
                                    <?php if (!empty($errors['size'])) { ?>
                                        <li><?php echo $errors['size']; ?></li>
                                    <?php } ?>
                                    <?php if (empty($errors['file']) && empty($errors['format']) && empty($errors['size']) && !empty($errors['fail'])) { ?>
                                    <?php foreach ($errors as $rowNum => $messages): ?>
                                        <?php foreach ($messages as $field => $msg): ?>
                                            <?php if ($rowNum > 1) {?>
                                            <li>Dòng <?php echo $rowNum; ?>: <?php echo $msg; ?></li>
                                        <?php } ?>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php }  ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>