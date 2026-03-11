<?php 
require_once __DIR__ . '/../customer/header.php'; 
?>

<style>
    .stats-container { margin: 30px auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .header-group { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .filter-form { display: flex; align-items: center; gap: 10px; background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #dee2e6; }
    .btn-back { text-decoration: none; background-color: #6c757d; color: white; padding: 8px 16px; border-radius: 5px; transition: 0.3s; }
    .btn-back:hover { background-color: #5a6268; }
    .btn-submit { background-color: #007bff; color: white; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer; text-decoration: none; }
    .btn-submit:hover { background-color: #0069d9; }
    .stats-table { width: 100%; border-collapse: collapse; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
    .stats-table thead { background-color: #343a40; color: white; }
    .stats-table th, .stats-table td { padding: 15px; border-bottom: 1px solid #dee2e6; }
    .stats-table tbody tr:hover { background-color: #f1f1f1; }
    .money-column { color: #28a745; font-weight: bold; }
    .filter-input-year {
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    outline: none;
    width: 100px; /* Độ rộng vừa đủ cho 4 chữ số */
    font-weight: 500;
    color: #495057;
    transition: all 0.2s ease;
    text-align: center;
    }

    .filter-input-year:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        background-color: #fff;
    }
    .filter-input-year::-webkit-outer-spin-button,
    .filter-input-year::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .filter-input-year[type=number] {
        -moz-appearance: textfield;
    }
    select { padding: 7px; border-radius: 4px; border: 1px solid #ced4da; }
</style>

<div class="container stats-container">
    <div class="header-group">
        <h2>Báo cáo thống kê doanh thu tất cả các ngày trong tháng được chọn</h2>
        <a href="index.php?controller=orders&action=showStatistical" class="btn-back">Quay lại </a>
         <a href="index.php?controller=orders&action=exportStatisticalDay&month=<?php echo $_GET['month'] ?>&year=<?php echo $_GET['year'] ?>" 
                       class="btn-submit"> Xuất Excel
         </a>
    </div>

    <table class="stats-table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Ngày</th>
                <th>Tổng sản phẩm đã bán</th>
                <th>Tổng khách hàng</th>
                <th>Tổng đơn hàng</th>
                <th>Tổng Doanh Thu</th>
            </tr>
        </thead>
        <tbody>
            <?php $stt = 1;
            foreach ($finalResult as $row) { 
            ?>
            <tr>
                <td><?= $stt++ ?></td>
                    <td><strong>Ngày <?= $row['day'] ?></strong></td>
                    <td><?= number_format($row['sumProduct']) ?></td>
                    <td><?= number_format($row['sumCustomer']) ?></td>
                    <td><?= number_format($row['sumOrder']) ?></td>
                    <td class="money-column"><?= number_format($row['sumDT']) ?> VNĐ</td>

            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<div class="container stats-container">
    <div class="header-group">
        <h2>Báo cáo KPI nhân viên tháng</h2>
    </div>

    <table class="stats-table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Nhân viên</th>
                <th>Tổng sản phẩm đã bán</th>
                <th>Tổng đơn hàng</th>
                <th>Tổng Doanh Thu</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($order1)): ?>
                <?php $stt = 1; foreach ($order1 as $row): ?>
                <tr>
                    <td><?= $stt++ ?></td>
                    <td><strong><?= $row['ten'] ?></strong></td>
                    <td><?= number_format($row['tongSP']) ?></td>
                    <td><?= number_format($row['tongDH']) ?></td>
                    <td class="money-column"><?= number_format($row['tongDT']) ?> VNĐ</td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="padding: 30px; color: #666; text-align: center;">Không có dữ liệu cho tháng này.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
require_once __DIR__ . '/../customer/footer.php'; 
?>