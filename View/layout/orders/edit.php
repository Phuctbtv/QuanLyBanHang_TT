<?php require_once __DIR__ . '/../customer/header.php'; ?>

<div class="row mt-3">
    <div class="col-md-3">
        <?php require_once __DIR__ . '/../customer/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-box me-2"></i>Sửa đơn hàng 
                </h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=Orders&action=update" method="POST">
                    <input type="hidden" name="id" value="<?php echo $orders_data['id']; ?>">
                    <div class="mb-3">
                        <label for="date" class="form-label">Thời gian mua<span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control" 
                               value="<?php echo date("Y-m-d",strtotime($orders_data['order_date'])); ?>">

                        <?php if(!empty($errors['order_date'])): ?>
                            <div class="text-danger small mt-1"><?php echo $errors['order_date']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="">Chọn khách hàng</label>
                        <select class="form-control" name="customers_id">
                            <option value="">Chọn khách hàng</option>
                            <?php 
                            foreach ($customers as $customer) { 
                                $selected = isset($orders_data['customers_id']) && $orders_data['customers_id'] == $customer['id'] ? 'selected' : '';
                            ?>
                                <option value="<?php echo $customer['id']; ?>" <?php echo $selected; ?>>
                                    <?php echo $customer['name']; ?>
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
                            foreach ($users as $user) { 
                                $selected = isset($orders_data['users_id']) && $orders_data['users_id'] == $user['id'] ? 'selected' : '';
                            ?>
                                <option value="<?php echo $user['id']; ?>" <?php echo $selected; ?>>
                                    <?php echo $user['username']; ?>
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
                        
                        <select class="form-control" multiple="true" name="product_id">
                            <?php 
                            foreach ($products as $product) { 
                                    $selected = '';
                                if (isset($orderDetails)){
                                    foreach ($orderDetails as $orderDetail) {
                                        if ($orderDetail['products_id'] == $product['id']) {
                                            $selected = 'selected';
                                        }
                                    }
                                }
                            ?>
                            <option value="<?php echo $product['id']; ?>" <?php echo $selected; ?>>
                                <?php echo $product['name']; ?>
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
                       
                        <a href="index.php?controller=Orders&action=index" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../customer/footer.php'; ?>