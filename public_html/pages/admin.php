<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền truy cập admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Bạn không có quyền truy cập trang quản trị!'); window.location.href='../index.php';</script>";
    exit();
}

// Kết nối database
$conn = mysqli_connect("localhost","root","","hoahuongduongphone"); 

if(!$conn){
    die("Kết nối thất bại");
}

// Đếm dữ liệu tổng quan để hiển thị trên Dashboard
$total_products = 0;
$total_orders = 0;
$total_revenue = 0;
$total_contacts = 0;
$total_members = 0;

// Kiểm tra và đếm sản phẩm
$check_sp = mysqli_query($conn, "SHOW TABLES LIKE 'sanpham'");
if(mysqli_num_rows($check_sp) > 0) {
    $total_products = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM sanpham"));
}

// Kiểm tra và đếm đơn hàng + doanh thu
$check_dh = mysqli_query($conn, "SHOW TABLES LIKE 'donhang'");
if(mysqli_num_rows($check_dh) > 0) {
    $total_orders = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM donhang"));
    $revenue_q = mysqli_query($conn, "SELECT SUM(tongtien) as total FROM donhang WHERE trangthai='Hoàn thành'");
    $revenue_data = mysqli_fetch_assoc($revenue_q);
    $total_revenue = $revenue_data['total'] ?? 0;
}

// Kiểm tra và đếm liên hệ
$check_lh = mysqli_query($conn, "SHOW TABLES LIKE 'lienhe'");
if(mysqli_num_rows($check_lh) > 0) {
    $total_contacts = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM lienhe"));
}

// Kiểm tra và đếm thành viên
$check_tk = mysqli_query($conn, "SHOW TABLES LIKE 'taikhoan'");
if(mysqli_num_rows($check_tk) > 0) {
    $total_members = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM taikhoan"));
}

// Lấy danh sách mới nhất cho Dashboard
$recent_orders = [];
if (mysqli_num_rows($check_dh) > 0) {
    $recent_orders_q = mysqli_query($conn, "SELECT * FROM donhang ORDER BY ngaymua DESC, id DESC LIMIT 5");
    while ($row = mysqli_fetch_assoc($recent_orders_q)) {
        $recent_orders[] = $row;
    }
}

$recent_contacts = [];
if (mysqli_num_rows($check_lh) > 0) {
    $recent_contacts_q = mysqli_query($conn, "SELECT * FROM lienhe ORDER BY id DESC LIMIT 5");
    while ($row = mysqli_fetch_assoc($recent_contacts_q)) {
        $recent_contacts[] = $row;
    }
}

// ĐỒNG BỘ: Sử dụng chung tham số 'action' để nhận diện các trang con
$action = $_GET['action'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Quản Trị - Hoa Hướng Dương Phone</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --admin-green: #198754; }
        body { background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #212529; }
        .sidebar .nav-link { color: #c2c7d0; padding: 12px 20px; transition: all 0.2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: var(--admin-green); color: #fff; }
        .card-stat { border-left: 5px solid var(--admin-green); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 px-0 sidebar shadow">
            <div class="p-3 text-white text-center border-bottom border-secondary">
                <h5 class="fw-bold mb-0 text-success"><i class="fa-solid fa-user-shield me-2"></i>HHD ADMIN</h5>
            </div>
            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <a class="nav-link <?= $action == 'dashboard' ? 'active' : '' ?>" href="admin.php?action=dashboard">
                        <i class="fa-solid fa-gauge me-2"></i> Tổng quan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $action == 'sanpham' ? 'active' : '' ?>" href="admin.php?action=sanpham">
                        <i class="fa-solid fa-mobile-screen me-2"></i> Quản lý sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $action == 'donhang' ? 'active' : '' ?>" href="admin.php?action=donhang">
                        <i class="fa-solid fa-shopping-cart me-2"></i> Quản lý đơn hàng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $action == 'lienhe' ? 'active' : '' ?>" href="admin.php?action=lienhe">
                        <i class="fa-solid fa-envelope me-2"></i> Quản lý liên hệ
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin.php?action=quanly_taikhoan" class="nav-link <?= $action == 'quanly_taikhoan' ? 'active' : '' ?>">
                        <i class="fa-solid fa-users me-2"></i> Quản lý tài khoản
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin.php?action=quanly_binhluan" class="nav-link <?= $action == 'quanly_binhluan' ? 'active' : '' ?>">
                        <i class="fa-solid fa-comments me-2"></i> Quản lý bình luận
                    </a>
                </li>
                <li class="nav-item mt-5 border-top border-secondary">
                    <a class="nav-link text-warning" href="../index.php">
                        <i class="fa-solid fa-arrow-left me-2"></i> Xem trang chủ
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-md-9 col-lg-10 px-4 py-4">
            <?php
            switch ($action) {
                case 'dashboard':
                    ?>
                    <h2 class="mb-4 fw-bold text-dark">Tổng Quan Hệ Thống</h2>
                    <div class="row g-4">
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="card card-stat shadow-sm p-3 bg-white h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Sản Phẩm</h6>
                                        <h3 class="fw-bold mb-0"><?= $total_products ?></h3>
                                    </div>
                                    <i class="fa-solid fa-box text-success fs-2"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="card card-stat shadow-sm p-3 bg-white h-100" style="border-left-color: #0dcaf0;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Đơn Hàng</h6>
                                        <h3 class="fw-bold mb-0"><?= $total_orders ?></h3>
                                    </div>
                                    <i class="fa-solid fa-cart-shopping text-info fs-2"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="card card-stat shadow-sm p-3 bg-white h-100" style="border-left-color: #ffc107;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Doanh Thu</h6>
                                        <h3 class="fw-bold mb-0 text-danger"><?= number_format($total_revenue, 0, ',', '.') ?> đ</h3>
                                    </div>
                                    <i class="fa-solid fa-coins text-warning fs-2"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="card card-stat shadow-sm p-3 bg-white h-100" style="border-left-color: #fd7e14;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Thành Viên</h6>
                                        <h3 class="fw-bold mb-0"><?= $total_members ?></h3>
                                    </div>
                                    <i class="fa-solid fa-users text-warning fs-2" style="color: #fd7e14 !important;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="card card-stat shadow-sm p-3 bg-white h-100" style="border-left-color: #6c757d;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Liên Hệ / Góp Ý</h6>
                                        <h3 class="fw-bold mb-0"><?= $total_contacts ?></h3>
                                    </div>
                                    <i class="fa-solid fa-comments text-secondary fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5 g-4">
                        <div class="col-lg-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-receipt me-2 text-success"></i>Đơn Hàng Gần Đây</h6>
                                    <a href="admin.php?action=donhang" class="btn btn-sm btn-outline-success border-0 fw-semibold">Xem tất cả</a>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr class="table-light">
                                                <th>Mã đơn</th>
                                                <th>Khách hàng</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($recent_orders) > 0): ?>
                                                <?php foreach ($recent_orders as $order): ?>
                                                    <tr>
                                                        <td class="fw-bold text-success">#<?= $order['id'] ?></td>
                                                        <td>
                                                            <div><?= htmlspecialchars($order['tennguoinhan']) ?></div>
                                                            <small class="text-muted"><?= htmlspecialchars($order['sdtnhan']) ?></small>
                                                        </td>
                                                        <td class="text-danger fw-bold"><?= number_format($order['tongtien'], 0, ',', '.') ?> đ</td>
                                                        <td>
                                                            <?php
                                                            if($order['trangthai'] == 'Chờ xác nhận') echo "<span class='badge bg-warning text-dark'>Chờ xác nhận</span>";
                                                            elseif($order['trangthai'] == 'Đang giao') echo "<span class='badge bg-primary'>Đang giao</span>";
                                                            elseif($order['trangthai'] == 'Hoàn thành') echo "<span class='badge bg-success'>Hoàn thành</span>";
                                                            else echo "<span class='badge bg-danger'>Đã hủy</span>";
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted">Chưa có đơn hàng nào</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-comments me-2 text-primary"></i>Phản Hồi Mới Nhất</h6>
                                    <a href="admin.php?action=lienhe" class="btn btn-sm btn-outline-primary border-0 fw-semibold">Xem tất cả</a>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr class="table-light">
                                                <th>Họ tên</th>
                                                <th>Email</th>
                                                <th>Nội dung</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($recent_contacts) > 0): ?>
                                                <?php foreach ($recent_contacts as $contact): ?>
                                                    <tr>
                                                        <td class="fw-bold"><?= htmlspecialchars($contact['ten']) ?></td>
                                                        <td><small class="text-success"><?= htmlspecialchars($contact['email']) ?></small></td>
                                                        <td class="text-secondary text-truncate" style="max-width: 150px;"><?= htmlspecialchars($contact['noidung']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center py-4 text-muted">Chưa có liên hệ nào</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    break;

                case 'sanpham':
                    include 'quanly_sanpham.php';
                    break;

                case 'donhang':
                    include 'quanly_donhang.php';
                    break;

                case 'lienhe':
                    include 'quanly_lienhe.php';
                    break;
                
                // ĐÃ BỔ SUNG 2 TRANG QUẢN LÝ MỚI VÀO ĐÂY:
                case 'quanly_taikhoan':
                    include 'quanly_taikhoan.php';
                    break;

                case 'quanly_binhluan':
                    include 'quanly_binhluan.php';
                    break;
            }
            ?>
        </div>
    </div>
</div>

<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>