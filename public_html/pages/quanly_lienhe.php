<?php
$conn = mysqli_connect("localhost","root","","hoahuongduongphone"); 

if(!$conn){
    die("Kết nối thất bại");
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Xử lý xóa thư liên hệ
if (isset($_GET['delete_lh'])) {
    $id_lh = mysqli_real_escape_string($conn, $_GET['delete_lh']);
    $sql_del = "DELETE FROM lienhe WHERE id = '$id_lh'";
    if (mysqli_query($conn, $sql_del)) {
        echo "<script>alert('Đã xóa thư liên hệ thành công!'); window.location.href='admin.php?action=lienhe';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa thư liên hệ!');</script>";
    }
}

// Xử lý xóa tài khoản thành viên
if (isset($_GET['delete_tk'])) {
    $id_tk = mysqli_real_escape_string($conn, $_GET['delete_tk']);
    
    // Kiểm tra không cho tự xóa tài khoản của mình
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id_tk) {
        echo "<script>alert('Bạn không thể tự xóa tài khoản quản trị của chính mình!'); window.location.href='admin.php?action=lienhe';</script>";
    } else {
        // Xóa các dữ liệu liên quan do ràng buộc khóa ngoại (Foreign Key Constraints)
        mysqli_query($conn, "DELETE FROM giohang WHERE id_taikhoan = '$id_tk'");
        mysqli_query($conn, "DELETE FROM binhluan WHERE id_taikhoan = '$id_tk'");
        
        // Lấy danh sách các đơn hàng của tài khoản này
        $orders_q = mysqli_query($conn, "SELECT id FROM donhang WHERE id_taikhoan = '$id_tk'");
        while ($ord = mysqli_fetch_assoc($orders_q)) {
            $oid = $ord['id'];
            mysqli_query($conn, "DELETE FROM chitietdonhang WHERE id_donhang = '$oid'");
        }
        mysqli_query($conn, "DELETE FROM donhang WHERE id_taikhoan = '$id_tk'");
        
        // Tiến hành xóa tài khoản
        $sql_del_tk = "DELETE FROM taikhoan WHERE id = '$id_tk'";
        if (mysqli_query($conn, $sql_del_tk)) {
            echo "<script>alert('Đã xóa tài khoản thành công!'); window.location.href='admin.php?action=lienhe';</script>";
        } else {
            echo "<script>alert('Lỗi khi xóa tài khoản!');</script>";
        }
    }
}

// Truy vấn danh sách
$list_contacts = mysqli_query($conn, "SELECT * FROM lienhe ORDER BY id DESC");
$list_members = mysqli_query($conn, "SELECT * FROM taikhoan ORDER BY id DESC");
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white py-3">
        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-users-gear me-2"></i>Quản Lý Thành Viên & Liên Hệ</h5>
    </div>
    <div class="card-body">
        
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-success" id="feedback-tab" data-bs-toggle="tab" data-bs-target="#feedback-panel" type="button" role="tab" aria-controls="feedback-panel" aria-selected="true">
                    <i class="fa-solid fa-envelope me-2"></i>Thư Góp Ý & Liên Hệ (<?= mysqli_num_rows($list_contacts) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-success" id="members-tab" data-bs-toggle="tab" data-bs-target="#members-panel" type="button" role="tab" aria-controls="members-panel" aria-selected="false">
                    <i class="fa-solid fa-users me-2"></i>Danh Sách Thành Viên (<?= mysqli_num_rows($list_members) ?>)
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="adminTabsContent">
            
            <!-- Panel 1: Thư góp ý -->
            <div class="tab-pane fade show active" id="feedback-panel" role="tabpanel" aria-labelledby="feedback-tab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="8%" class="text-center">Mã thư</th>
                                <th width="20%">Họ và tên</th>
                                <th width="22%">Địa chỉ Email</th>
                                <th>Nội dung góp ý / hỗ trợ</th>
                                <th width="12%" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(mysqli_num_rows($list_contacts) > 0) {
                                while ($row = mysqli_fetch_assoc($list_contacts)) { 
                            ?>
                                <tr>
                                    <td class="text-center text-muted">#<?= $row['id'] ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['ten']) ?></td>
                                    <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="text-decoration-none text-success"><?= htmlspecialchars($row['email']) ?></a></td>
                                    <td class="text-secondary"><?= nl2br(htmlspecialchars($row['noidung'])) ?></td>
                                    <td class="text-center">
                                        <a href="admin.php?action=lienhe&delete_lh=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa thư góp ý này?')">
                                            <i class="fa-solid fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center p-4 text-muted'>Hộp thư rỗng. Chưa nhận được phản hồi nào từ khách hàng.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Panel 2: Danh sách thành viên -->
            <div class="tab-pane fade" id="members-panel" role="tabpanel" aria-labelledby="members-tab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="8%" class="text-center">Mã TV</th>
                                <th>Họ và tên</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Địa chỉ</th>
                                <th width="15%" class="text-center">Vai trò</th>
                                <th width="12%" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(mysqli_num_rows($list_members) > 0) {
                                while ($row = mysqli_fetch_assoc($list_members)) { 
                                    // Xử lý lỗi chính tả 'díachi' trong DB schema
                                    $address = $row['díachi'] ?? $row['diachi'] ?? '';
                            ?>
                                <tr class="<?= (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']) ? 'table-warning' : '' ?>">
                                    <td class="text-center text-muted">#<?= $row['id'] ?></td>
                                    <td class="fw-bold text-dark">
                                        <?= htmlspecialchars($row['ten']) ?>
                                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']): ?>
                                            <span class="badge bg-secondary ms-1">Bạn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['sdt']) ?></td>
                                    <td><?= htmlspecialchars($address) ?></td>
                                    <td class="text-center">
                                        <?php if ($row['vaitro'] === 'admin'): ?>
                                            <span class="badge bg-danger px-2 py-1"><i class="fa-solid fa-user-shield me-1"></i>Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-success px-2 py-1"><i class="fa-solid fa-user me-1"></i>Khách hàng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']): ?>
                                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                                <i class="fa-solid fa-ban"></i> Khóa
                                            </button>
                                        <?php else: ?>
                                            <a href="admin.php?action=lienhe&delete_tk=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản thành viên này và mọi dữ liệu liên quan?')">
                                                <i class="fa-solid fa-user-xmark"></i> Xóa
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center p-4 text-muted'>Chưa có thành viên nào đăng ký tài khoản.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>