<?php require_once 'header.php'; ?>

<div class="row mt-3">
    <div class="col-md-3">
        <?php require_once 'sidebar.php'; ?>
    </div>

    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary fw-bold">
                <i class="bi bi-people-fill me-2"></i>Danh sách khách hàng
            </h5>

            <div class="d-flex gap-2">
                <a href="index.php?controller=customer&action=<?php if ($_GET['action'] == 'index') { echo "export";} elseif ($_GET['action'] == 'search') { 
                    echo "exportSearch&keyword=". $keyword;} ?>" class="btn btn-outline-success btn-sm shadow-sm d-flex align-items-center">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Xuất Excel
                </a>

                <a href="index.php?controller=customer&action=importshow" class="btn btn-outline-success btn-sm shadow-sm d-flex align-items-center">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Thêm Excel
                </a>

                <a href="index.php?controller=customer&action=create" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center">
                    <i class="bi bi-plus-circle me-1"></i> Tạo mới
                </a>
            </div>
        </div>
    </div>

             <form action="index.php?controller=customer&action=search" method="POST">
                <input type="text" name="keyword" class="form-control"  placeholder="Nhập tên rồi nhấn Enter...">
             </form>
              
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-customer table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên khách hàng</th>
                                <th>Số điện thoại</th>
                                <th>Email</th>
                                <th>Trạng thái</th> 
                                <th>Ngày tạo</th>   
                                <th class="text-center">Hành động</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($customers)): ?>
                                <?php foreach ($customers as $item): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td><strong><?php echo $item['name']; ?></strong></td>
                                    <td><?php echo $item['phone']; ?></td>
                                    <td><?php echo $item['email']; ?></td>
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
                                            echo "Bị khóa";
                                        } ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($item['created_at'])); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="index.php?controller=customer&action=edit&id=<?= $item['id'] ?>" 
                                               class="btn btn-sm btn-outline-warning">Sửa</a>
                                            <a href="index.php?controller=customer&action=destroy&id=<?= $item['id'] ?>" 
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
                                    <td colspan="7" class="text-center text-muted py-4">Chưa có dữ liệu khách hàng.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation example">
                      <ul class="pagination">
                        <!-- <li class="page-item"><a class="page-link" href="#">Previous</a></li> -->
                        <?php for ($i = 0; $i < $page; $i++) {
                         ?>
                        <li class="page-item"><a class="page-link <?php if (!empty($_GET['page']) && $_GET['page'] == ($i+1)){ echo "active";}  ?>" href="index.php?controller=customer&action=index&page=<?php echo $i+1; ?>"><?php echo $i+1; ?></a></li>
                        <?php } ?>
                      </ul>
                    </nav>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>