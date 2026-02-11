<?php require_once __DIR__ . '/../customer/header.php'; ?>

<div class="row mt-3">
    <div class="col-md-3">
        <?php require_once __DIR__ . '/../customer/sidebar.php'; ?>
    </div>
    
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-box me-2"></i>Thêm đơn hàng mới
                </h5>
            </div>
            <div class="card-body">
                <!-- SỬA: action từ Products thành Orders -->
                <form action="index.php?controller=Orders&action=store" method="POST">
                    
                    <div class="mb-3">
                        <label for="date" class="form-label">Thời gian mua<span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control" 
                               value="<?php echo isset($orders_data['order_date']) ? $orders_data['order_date'] : ''; ?>">
                        <?php if(!empty($errors['order_date'])): ?>
                            <div class="text-danger small mt-1"><?php echo $errors['order_date']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="">Chọn khách hàng</label>
                        <select class="form-control" name="customers_id">
                            <option value="">Chọn khách hàng</option>
                            <?php 
                            foreach ($customer as $customers) { 
                                $selected = isset($orders_data['customers_id']) && $orders_data['customers_id'] == $customers['id'] ? 'selected' : '';
                            ?>
                                <option value="<?php echo $customers['id']; ?>" <?php echo $selected; ?>>
                                    <?php echo $customers['name']; ?>
                                </option>
                            <?php 
                            }
                            ?>
                        </select>
                        <?php if(!empty($errors['customers_id'])): ?>
                            <div class="text-danger small mt-1"><?php echo $errors['customers_id']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="">Chọn người tạo</label>
                        <select class="form-control" name="users_id">
                            <option value="">Chọn người tạo</option>
                            <?php 
                            foreach ($user as $users) { 
                                $selected = isset($orders_data['users_id']) && $orders_data['users_id'] == $users['id'] ? 'selected' : '';
                            ?>
                                <option value="<?php echo $users['id']; ?>" <?php echo $selected; ?>>
                                    <?php echo $users['username']; ?>
                                </option>
                            <?php 
                            }
                            ?>
                        </select>
                        <?php if(!empty($errors['users_id'])): ?>
                            <div class="text-danger small mt-1"><?php echo $errors['users_id']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="">Chọn sản phẩm</label>
                        <!-- SỬA: Thêm [] vào name -->
                        <select class="form-control" multiple="true" name="product_id">
                            <?php 
                            foreach ($product as $products) { 
                                $selected = '';
                                if (isset($orders_data['product_id'])) {
                                    if (is_array($orders_data['product_id']) && in_array($products['id'], $orders_data['product_id'])) {
                                        $selected = 'selected';
                                    } elseif ($orders_data['product_id'] == $products['id']) {
                                        $selected = 'selected';
                                    }
                                }
                            ?>
                                <option value="<?php echo $products['id']; ?>" <?php echo $selected; ?>>
                                    <?php echo $products['name']; ?>
                                </option>
                            <?php 
                            }
                            ?>
                        </select>
                        <?php if(!empty($errors['product_id'])): ?>
                            <div class="text-danger small mt-1"><?php echo $errors['product_id']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="total_money" class="">Tổng tiền<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="total_money" 
                               value="<?php echo isset($orders_data['total_money']) ? $orders_data['total_money'] : ''; ?>"
                               placeholder="Nhập tổng tiền...">
                        <?php if(!empty($errors['total_money'])): ?>
                            <div class="text-danger small mt-1"><?php echo $errors['total_money']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Nút hành động -->
                    <div class="d-flex justify-content-between mt-4">
                        <!-- SỬA: Quay lại trang Orders -->
                        <a href="index.php?controller=Orders&action=index" class="btn btn-secondary">
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