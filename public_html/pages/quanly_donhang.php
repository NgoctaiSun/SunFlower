<?php
$conn = mysqli_connect("localhost","root","","hoahuongduongphone"); 

if(!$conn){
    die("Kết nối thất bại");
}

// Xử lý khi Admin cập nhật trạng thái đơn
if (isset($_POST['capnhat_trangthai'])) {
    $id_dh = mysqli_real_escape_string($conn, $_POST['id_donhang']);
    $trangthai_moi = mysqli_real_escape_string($conn, $_POST['trangthai']);
    
    $sql_update = "UPDATE donhang SET trangthai = '$trangthai_moi' WHERE id = '$id_dh'";
    if(mysqli_query($conn, $sql_update)) {
        echo "<script>alert('Cập nhật trạng thái đơn hàng thành công!'); window.location.href='admin.php?action=donhang';</script>";
    }
}

// Tải danh sách đơn hàng toàn hệ thống
$list_orders = mysqli_query($conn, "SELECT * FROM donhang ORDER BY ngaymua DESC");
?>

<div class="card shadow-sm">
    <div class="card-header bg-success text-white fw-bold">
        <i class="fa-solid fa-file-invoice-dollar me-1"></i> Điều Phối & Quản Lý Đơn Hàng
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã đơn</th>
                    <th>Thông tin khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái hiện tại</th>
                    <th>Xử lý đơn hàng</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($list_orders) > 0) {
                    while ($row = mysqli_fetch_assoc($list_orders)) { 
                ?>
                <tr>
                    <td class="fw-bold text-center text-success">#<?= $row['id'] ?></td>
                    <td>
                        <strong class="text-dark"><?= htmlspecialchars($row['tennguoinhan']) ?></strong><br>
                        <small class="text-muted d-block mt-1"><i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($row['sdtnhan']) ?></small>
                        <small class="text-muted d-block"><i class="fa-solid fa-wallet me-1"></i> HTTT: <?= htmlspecialchars($row['thanhtoan']) ?></small>
                    </td>
                    <td><?= $row['ngaymua'] ?></td>
                    <td class="text-danger fw-bold"><?= number_format($row['tongtien'], 0, ',', '.') ?> đ</td>
                    <td class="text-center">
                        <?php
                        if($row['trangthai'] == 'Chờ xác nhận') echo "<span class='badge bg-warning text-dark px-2 py-2'>Chờ xác nhận</span>";
                        elseif($row['trangthai'] == 'Đang giao') echo "<span class='badge bg-primary px-2 py-2'>Đang giao</span>";
                        elseif($row['trangthai'] == 'Hoàn thành') echo "<span class='badge bg-success px-2 py-2'>Hoàn thành</span>";
                        else echo "<span class='badge bg-danger px-2 py-2'>Đã hủy</span>";
                        ?>
                    </td>
                    <td>
                        <form method="POST" class="d-flex gap-2 justify-content-center">
                            <input type="hidden" name="id_donhang" value="<?= $row['id'] ?>">
                            <select name="trangthai" class="form-select form-select-sm" style="width: 140px;">
                                <option value="Chờ xác nhận" <?= $row['trangthai']=='Chờ xác nhận'?'selected':'' ?>>Chờ xác nhận</option>
                                <option value="Đang giao" <?= $row['trangthai']=='Đang giao'?'selected':'' ?>>Đang giao</option>
                                <option value="Hoàn thành" <?= $row['trangthai']=='Hoàn thành'?'selected':'' ?>>Hoàn thành</option>
                                <option value="Đã hủy" <?= $row['trangthai']=='Đã hủy'?'selected':'' ?>>Đã hủy</option>
                            </select>
                            <button type="submit" name="capnhat_trangthai" class="btn btn-sm btn-success fw-bold">Cập nhật</button>
                        </form>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center p-4 text-muted'>Hệ thống chưa ghi nhận đơn mua hàng nào.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>