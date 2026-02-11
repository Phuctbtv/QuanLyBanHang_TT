<?php 
require_once __DIR__ . '/../customer/header.php'; 
?>

<div class="row mt-3">
    <div class="col-md-3">
        <?php require_once __DIR__ . '/../customer/sidebar.php'; ?>
    </div>

    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary fw-bold">Danh sách tài khoản hiện có</h5>
               <div class="d-flex gap-2">
                <a href="index.php?controller=Users&action=<?php if ($_GET['action'] == 'index') { echo "export";} elseif ($_GET['action'] == 'search') { 
                    if (!empty($keyword)) {echo "exportSearch&keyword=". $keyword;} else { echo "export";}
                   } ?>" class="btn btn-outline-success btn-sm shadow-sm d-flex align-items-center">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Xuất Excel
                </a>
                <a href="index.php?controller=Users&action=importshow" class="btn btn-outline-success btn-sm shadow-sm d-flex align-items-center">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Thêm Excel
                </a>
                <a href="index.php?controller=Users&action=create" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center">
                    <i class="bi bi-plus-circle me-1"></i> Tạo mới
                </a>
            </div>
            </div>
            
            <div class="card-header bg-light">
                <form action="index.php?controller=Users&action=search" method="POST" class="d-flex">
                    <input type="text" name="keyword" class="form-control me-2" placeholder="Tìm kiếm tài khoản...">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search me-1"></i> Tìm
                    </button>
                </form>
                <?php if(!empty($error)): ?>
                    <div class="text-danger small mt-2"><?php echo $error; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên tài khoản</th>
                                <th>Hình ảnh</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td>
                                        <strong><?php echo $user['username']; ?></strong>
                                    </td>
                                    <td><img src="View/layout/upload/<?php echo $user['photo']; ?>" alt="ảnh" width="50"></td>
                                    <td>
                                        <span class="badge rounded-pill bg-<?php if ($user['status'] == 1){
                                            echo "success";
                                        }else{
                                            echo "danger";
                                        } ?>"> 
                                        <!-- success
                                        danger -->
                                            <?php if ($user['status'] == 1){
                                                echo "Active";
                                            }else{
                                                echo "Inactive";
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y H:i:s',strtotime($user['created_at'])); ?>
                                    </td>
                                    <td class="text-center">
                                            <a href="index.php?controller=Users&action=edit&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-outline-warning" title="Sửa">
                                                <i class="bi bi-pencil">Sửa</i>
                                            </a>
                                            <a href="index.php?controller=Users&action=destroy&id=<?php echo $user['id']; ?>" 
                                                class="btn btn-outline-danger" 
                                                onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')"
                                                title="Xóa">
                                                <i class="bi bi-trash">Xóa</i>
                                            </a>
                                            <a href="index.php?controller=Users&action=editPassword&id=<?php echo $user['id']; ?>" class="btn btn-outline-success">
                                            <i class="bi bi-plus-circle me-1 "></i>Cập nhật mật khẩu
                                            </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                        Không có tài khoản nào.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation example">
                      <ul class="pagination">
                        <!-- <li class="page-item"><a class="page-link" href="#">Previous</a></li> -->
                        <?php for ($i = 0; $i < $totalpage; $i++) {
                         ?>
                        <li class="page-item"><a class="page-link <?php if (!empty($_GET['page']) && $_GET['page'] == ($i+1)){ echo "active";}  ?>" href="index.php?controller=Users&action=index&page=<?php echo $i+1; ?>"><?php echo $i+1; ?></a></li>
                        <?php } ?>
                      </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../customer/footer.php'; ?>