<?php require_once 'header.php'; ?>

<div class="row mt-3">
    <div class="col-md-3">
        <?php require_once 'sidebar.php'; ?>
    </div>
    
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Sửa thông tin khách hàng
                </h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=customer&action=update" method="POST">
                    <input type="hidden" name="id" value="<?= $customer_data['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($customer_data['name']) ?>" >
                                <?php if (!empty($errors['name'])){
                                echo'<p> '.$errors['name'].' </p>';
                    }?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="<?= htmlspecialchars($customer_data['phone']) ?>">
                             <?php if(!empty($errors['phone'])){
                                echo '<p>'.$errors['phone'].'</p>';
                             }  ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($customer_data['email']) ?>" >
                              <?php if(!empty($errors['email'])){
                                echo '<p>'.$errors['email'].'</p>';
                             }  ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="users_id" class="form-label">Người phụ trách <span class="text-danger">*</span></label>
                        <select class="form-control" id="users_id" name="users_id" >
                            <option value="">-- Chọn người phụ trách --</option>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= $customer_data['users_id'] == $user['id'] ? 'selected' : '' ?>>
                                        <?php echo $user['username'] ."-".date('d/m/Y H:i:s',strtotime($user['created_at'])); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">Không có người dùng nào</option>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Chọn người sẽ phụ trách khách hàng này</small>
                        <?php if(!empty($errors['users_id'])){
                                echo '<p>'.$errors['users_id'].'</p>';
                             }  ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_active" 
                                       value="1" <?= $customer_data['status'] == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_active">Hoạt động</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_inactive" 
                                       value="0" <?= $customer_data['status'] == 0 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_inactive">Bị khóa</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php?controller=customer&action=index" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>