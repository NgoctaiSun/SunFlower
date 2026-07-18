<?php
include_once '../pages/connect.php';
if (!isset($conn)) {
    $conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
}

$sql = "SELECT * FROM taikhoan ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container-fluid my-4">
    <div class="card shadow-sm border-0 rounded-3 bg-white p-4">
        <h3 class="text-success fw-bold mb-4">
            <i class="fa-solid fa-users shadow-sm me-2 p-2 bg-success text-white rounded"></i>Quản Lý Tài Khoản Người Dùng
        </h3>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-white fw-bold">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="20%">Họ tên</th>
                        <th width="20%">Email</th>
                        <th width="15%">Số điện thoại</th>
                        <th width="15%">Địa chỉ</th>
                        <th width="13%">Vai trò</th>
                        <th width="12%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="fw-bold text-secondary">#<?= $row['id'] ?></td>
                        <td><span class="fw-semibold text-dark"><?= htmlspecialchars($row['ten']) ?></span></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['sdt']) ?></td>
                        <td><?= htmlspecialchars($row['diachi']) ?></td>
                        <td>
                            <?php if ($row['vaitro'] == 'admin'): ?>
                                <span class="badge bg-danger px-2.5 py-1.5 rounded-pill shadow-sm">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-info text-dark px-2.5 py-1.5 rounded-pill">Khách hàng</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="xuly_taikhoan.php?action=toggle_role&id=<?= $row['id'] ?>&current=<?= $row['vaitro'] ?>" 
                                   class="btn btn-sm btn-outline-warning" title="Đổi quyền hạn">
                                    <i class="fa-solid fa-user-shield"></i>
                                </a>
                                <a href="xuly_taikhoan.php?action=delete&id=<?= $row['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này? Hoạt động này không thể hoàn tác!')">
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