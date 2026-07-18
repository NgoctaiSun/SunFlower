<?php
include_once '../pages/connect.php';
if (!isset($conn)) {
    $conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
}

// Câu lệnh JOIN 3 bảng để lấy tên Tài khoản và tên Sản phẩm tương ứng với bình luận
$sql = "SELECT b.*, t.ten AS ten_taikhoan, s.ten AS ten_sanpham 
        FROM binhluan b
        JOIN taikhoan t ON b.id_taikhoan = t.id
        JOIN sanpham s ON b.id_sanpham = s.id
        ORDER BY b.id DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container-fluid my-4">
    <div class="card shadow-sm border-0 rounded-3 bg-white p-4">
        <h3 class="text-success fw-bold mb-4">
            <i class="fa-solid fa-comments shadow-sm me-2 p-2 bg-success text-white rounded"></i>Quản Lý Bình Luận & Đánh Giá
        </h3>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-white fw-bold">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">Khách hàng</th>
                        <th width="20%">Sản phẩm</th>
                        <th width="25%">Nội dung bình luận</th>
                        <th width="10%">Đánh giá</th>
                        <th width="13%">Trạng thái</th>
                        <th width="12%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="fw-bold text-secondary">#<?= $row['id'] ?></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($row['ten_taikhoan']) ?></td>
                        <td class="text-primary fw-medium"><?= htmlspecialchars($row['ten_sanpham']) ?></td>
                        <td><p class="mb-0 text-muted small" style="max-width: 300px;"><?= htmlspecialchars($row['noidung']) ?></p></td>
                        <td>
                            <span class="text-warning fw-bold">
                                <?= $row['sosao'] ?> <i class="fa-solid fa-star text-warning"></i>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['trangthai'] == '1' || $row['trangthai'] == 'Hiển thị'): ?>
                                <span class="badge bg-success rounded-pill px-2 py-1">Đang hiển thị</span>
                            <?php else: ?>
                                <span class="badge bg-secondary rounded-pill px-2 py-1">Đang ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="kiemsoat_binhluan.php?action=toggle_status&id=<?= $row['id'] ?>&current=<?= $row['trangthai'] ?>" ...
                                   class="btn btn-sm btn-outline-secondary" title="Ẩn / Hiện bình luận">
                                    <i class="fa-solid <?= ($row['trangthai'] == '1' || $row['trangthai'] == 'Hiển thị') ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                </a>
                                <a href="kiemsoat_binhluan.php?action=delete&id=<?= $row['id'] ?>" ...
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Bạn có chắc muốn xóa vĩnh viễn đánh giá này không?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>