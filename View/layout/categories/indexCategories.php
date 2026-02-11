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
                <h5 class="mb-0 text-primary fw-bold">Danh sách danh mục hiện có</h5>
               <div class="d-flex gap-2">
                <a href="index.php?controller=Categories&action=<?php if ($_GET['action'] == 'index') { echo "export";} elseif ($_GET['action'] == 'search') { 
                    echo "exportSearch&keyword=". $keyword;} ?>" class="btn btn-outline-success btn-sm shadow-sm d-flex align-items-center">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Xuất Excel
                </a>
                <a href="index.php?controller=Categories&action=importshow" class="btn btn-outline-success btn-sm shadow-sm d-flex align-items-center">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Thêm Excel
                </a>
                <a href="index.php?controller=Categories&action=create" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center">
                    <i class="bi bi-plus-circle me-1"></i> Tạo mới
                </a>
            </div>
            </div>
             <form action="index.php?controller=Categories&action=search" method="POST">
                <input type="text" name="keyword" class="form-control"  placeholder="Nhập tên rồi nhấn Enter...">
               
             </form>
              
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-customer table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên danh mục</th>
                                <th>Trạng thái </th>
                                <th class="text-center">Hành động</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                          
                        <?php if(!empty($categorie)){ ?>
                            <?php foreach ($categorie as $item) {  ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                               
                                <td><?php echo $item['name']; ?></td>
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
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="index.php?controller=Categories&action=edit&id=<?= $item['id'] ?>" 
                                           class="btn btn-sm btn-outline-warning">Sửa</a>
                                        <a href="index.php?controller=Categories&action=destroy&id=<?= $item['id'] ?>" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?')">
                                            <i class="bi bi-trash me-1"></i> Xóa
                                        </a>
                                    </div>
                                </td>
                            </tr> 
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation example">
                      <ul class="pagination">
                        <!-- <li class="page-item"><a class="page-link" href="#">Previous</a></li> -->
                        <?php for ($i = 0; $i < $totalpage; $i++) {
                         ?>
                        <li class="page-item"><a class="page-link <?php if (!empty($_GET['page']) && $_GET['page'] == ($i+1)){ echo "active";}  ?>" href="index.php?controller=Categories&action=index&page=<?php echo $i+1; ?>"><?php echo $i+1; ?></a></li>
                        <?php } ?>
                      </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../customer/footer.php'; ?>