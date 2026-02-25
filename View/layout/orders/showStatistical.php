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
    select { padding: 7px; border-radius: 4px; border: 1px solid #ced4da; }
</style>

<div class="container stats-container">
    <div class="header-group">
        <h2>Báo cáo thống kê doanh thu theo tháng trong năm hiện tại</h2>
        <a href="index.php?controller=orders&action=index" class="btn-back">Quay lại trang quản trị</a>
    </div>

    <div style="margin-bottom: 25px;">
        <form action="index.php?controller=orders&action=showStatistical" method="POST" class="filter-form">
            <label style="font-weight: bold;">Chọn tháng báo cáo:</label>
            <select name="month">
                <?php 
                for ($m = 1; $m <= 12; $m++) {
                    $selected = "";
                    if ($currentSelectedMonth == $m) {
                        $selected = "selected";
                    }
                ?>
                    <option value="<?php echo $m; ?>" <?php echo $selected; ?>>
                        Tháng <?php echo $m; ?>
                    </option>
                <?php 
                } 
                ?>
            </select>

            <button type="submit" class="btn-submit">Xem kết quả</button>
            <a href="index.php?controller=orders&action=exportStatistical&month=<?php echo $currentSelectedMonth ?>" 
                       class="btn-submit"> Xuất Excel
            </a>
        </form>
    </div>

    <table class="stats-table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tháng</th>
                <th>Tổng sản phẩm đã bán</th>
                <th>Tổng khách hàng</th>
                <th>Tổng đơn hàng</th>
                <th>Tổng Doanh Thu</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($order)): ?>
                <?php $stt = 1; foreach ($order as $row): ?>
                <tr>
                    <td><?= $stt++ ?></td>
                    <td><strong>Tháng <?= $row['thang'] ?></strong></td>
                    <td><?= number_format($row['tongSP']) ?></td>
                    <td><?= number_format($row['tongKH']) ?></td>
                    <td><?= number_format($row['tongDH']) ?></td>
                    <td class="money-column"><?= number_format($row['tongDT']) ?> VNĐ</td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="padding: 30px; color: #666; text-align: center;">Không có dữ liệu cho tháng này.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
require_once __DIR__ . '/../customer/footer.php'; 
?>