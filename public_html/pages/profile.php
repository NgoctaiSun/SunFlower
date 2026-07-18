<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit();
}

include_once 'pages/connect.php';
if (!isset($conn)) {
    $conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
}

$userId = $_SESSION['user_id'];

// Lấy thông tin người dùng
$sql = "SELECT * FROM taikhoan WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
$username = $_SESSION['user'];
$phone = "Chưa cập nhật";
$email = "Chưa cập nhật";
$diachi = "Chưa cập nhật";
$vaitro = "Thành viên";

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $username = $user['ten'];
        $phone = !empty($user['sdt']) ? $user['sdt'] : "Chưa cập nhật";
        $email = !empty($user['email']) ? $user['email'] : "Chưa cập nhật";
        // Đọc trường díachi từ CSDL
        $diachi = !empty($user['díachi']) ? $user['díachi'] : "Chưa cập nhật";
        $vaitro = ($user['vaitro'] === 'admin') ? 'Quản trị viên' : 'Thành viên thân thiết';
    }
    mysqli_stmt_close($stmt);
}

// Đếm số đơn hàng đã đặt để hiển thị Badge thống kê cho "xịn"
$order_count = 0;
$count_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM donhang WHERE id_taikhoan = '$userId'");
if($count_q) {
    $count_data = mysqli_fetch_assoc($count_q);
    $order_count = $count_data['total'];
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                <div class="row g-0">
                    <div class="col-sm-4 bg-success text-center text-white d-flex flex-column justify-content-center align-items-center p-4">
                        <div class="avatar-wrapper mb-3 position-relative">
                            <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 100px; height: 100px;">
                                <i class="fa-solid fa-user-shield fs-1"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-1 text-truncate w-100"><?php echo htmlspecialchars($username); ?></h4>
                        <span class="badge bg-white text-success rounded-pill px-3 py-1 fw-bold mb-3 small shadow-sm">
                            <?php echo $vaitro; ?>
                        </span>
                        
                        <div class="mt-2 text-white-50 border-top pt-2 w-100 small">
                            <i class="fa-solid fa-box-open me-1"></i> Đã đặt: <span class="text-white fw-bold"><?php echo $order_count; ?> đơn hàng</span>
                        </div>
                    </div>

                    <div class="col-sm-8 bg-white p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h3 class="fw-bold text-dark mb-4 pb-2 border-bottom text-uppercase fs-5 tracking-wide text-secondary">
                                <i class="fa-solid fa-id-card me-2 text-success"></i>Thông tin tài khoản
                            </h3>
                            
                            <div class="row g-3">
                                <div class="col-100 mb-2">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Họ và tên</label>
                                    <div class="p-2 bg-light rounded border-start border-4 border-success text-dark fw-semibold">
                                        <i class="fa-solid fa-signature me-2 text-muted"></i><?php echo htmlspecialchars($username); ?>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Số điện thoại</label>
                                    <div class="p-2 bg-light rounded text-dark">
                                        <i class="fa-solid fa-phone me-2 text-muted"></i><?php echo htmlspecialchars($phone); ?>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Địa chỉ Email</label>
                                    <div class="p-2 bg-light rounded text-dark text-truncate">
                                        <i class="fa-solid fa-envelope me-2 text-muted"></i><?php echo htmlspecialchars($email); ?>
                                    </div>
                                </div>

                                <div class="col-100 mb-3">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Địa chỉ nhận hàng</label>
                                    <div class="p-2 bg-light rounded text-dark">
                                        <i class="fa-solid fa-location-dot me-2 text-muted"></i><?php echo htmlspecialchars($diachi); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 pt-3 border-top mt-4">
                            <a href="index.php?page=lichsumuahang" class="btn btn-outline-success rounded-pill px-4 fw-semibold btn-sm">
                                <i class="fa-solid fa-clock-history me-1"></i> Lịch sử mua hàng
                            </a>
                            <a href="index.php?page=edit_profile" class="btn btn-success rounded-pill px-4 fw-semibold ms-auto btn-sm shadow-sm">
                                <i class="fa-solid fa-user-pen me-1"></i> Chỉnh sửa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hiệu ứng hover nhẹ cho khối Profile Card thêm sinh động */
    .avatar-wrapper div {
        transition: transform 0.4s ease;
    }
    .card:hover .avatar-wrapper div {
        transform: scale(1.05) rotate(5deg);
    }
    .bg-light {
        background-color: #f8f9fa !important;
        font-size: 0.95rem;
    }
</style>