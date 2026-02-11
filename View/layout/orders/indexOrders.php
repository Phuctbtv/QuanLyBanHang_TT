<?php 
require_once __DIR__ . '/../customer/header.php'; 
?>

<div class="row mt-3">
    <div class="col-md-3">
         <?php require_once __DIR__ . '/../customer/sidebar.php'; ?>
    </div>

    <div class="col-md-9">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-primary fw-bold">
            <i class="bi bi-list-ul me-2"></i>Danh sách các đơn hàng
        </h5>
            <div class="d-flex gap-2">
                <a href="index.php?controller=orders&action=create" class="btn btn-success btn-sm shadow-sm px-3">
                    <i class="bi bi-plus-circle me-1"></i> Tạo mới
                </a>
                
                <div class="btn-group shadow-sm">
                    <a href="index.php?controller=orders&action=<?php if ($_GET['action'] == 'index') { echo "export";} elseif ($_GET['action'] == 'search') { echo "exportSearch&keyword=". $keyword;} ?>" 
                       class="btn btn-outline-success btn-sm d-flex align-items-center">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Xuất Excel
                    </a>

                    <a href="index.php?controller=orders&action=importshow" 
                       class="btn btn-outline-success btn-sm d-flex align-items-center">
                        <i class="bi bi-file-earmark-arrow-up me-1"></i> Thêm Excel
                    </a>
                </div>
            </div>
        </div>
             <form action="index.php?controller=orders&action=search" method="POST">
                <input type="text" name="keyword" class="form-control"  placeholder="Nhập tên rồi nhấn Enter...">
               
             </form>
              
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-customer table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Mã đơn hàng</th>
                                <th>Thời gian mua </th>
                                <th>Tổng tiền </th>
                                <th>Trạng thái</th> 
                                <th>Người tạo</th> 
                                <th>Khách hàng</th>
                                <th>Tạo lúc</th>
                                <th class="text-center">Hành động</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($order)): ?>
                                <?php foreach ($order as $item): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td><strong><?php echo $item['code']; ?></strong></td>
                                    <td><?php echo $item['order_date']; ?></td>
                                    <td><?php echo $item['total_money']; ?></td>
                                    <td>
                                        <span class="badge rounded-pill bg-<?php 
                                        if ($item['status'] == 1){
                                            echo "success";
                                        }else{
                                            echo "danger";
                                        }
                                        ?>">
                                        <?php if ($item['status'] == 1){
                                            echo "Hoạt động";
                                        }else{
                                            echo "Không hoạt động";
                                        } ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $item['user_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $item['customer_name']; ?>
                                    </td>
                                    <td><?php echo $item['order_date']; ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="index.php?controller=orders&action=edit&id=<?= $item['id'] ?>" 
                                               class="btn btn-sm btn-outline-warning">Sửa</a>
                                            <a href="index.php?controller=orders&action=destroy&id=<?= $item['id'] ?>" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?')">
                                                    <i class="bi bi-trash me-1"></i> Xóa
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Chưa có dữ liệu đơn hàng.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                     <nav aria-label="Page navigation example">
                      <ul class="pagination">
                        <li class="page-item"><a class="page-link" href="index.php?controller=orders&action=index&page=<?php if(isset($_GET['page']) && $_GET['page'] > 1) { echo $_GET['page'] - 1; } else { echo 1; } ?>">Previous</a></li>
                        <?php for ($i = 0; $i < $page; $i++) {
                         ?>
                        <li class="page-item"><a class="page-link <?php if (!empty($_GET['page']) && (int)$_GET['page'] == ($i+1)){ echo "active";}  ?>" href="index.php?controller=orders&action=index&page=<?php echo $i+1; ?>"><?php echo $i+1; ?></a></li>
                        <?php } ?>
                      </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../customer/footer.php'; ?>