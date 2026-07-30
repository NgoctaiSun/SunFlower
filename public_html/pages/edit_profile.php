<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Chặn người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit();
}

include_once '../connect.php';
if (!isset($conn)) {$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
    mysqli_set_charset($conn, "utf8mb4");
}

$userId = $_SESSION['user_id'];$success_msg = "";
$error_msg = "";

// 2. XỬ LÝ LƯU THÔNG TIN KHI SUBMIT FORM
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoten  = trim($_POST['hoten'] ?? '');
    $sdt    = trim($_POST['sdt'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $diachi = trim($_POST['diachi'] ?? '');

    // Kiểm tra dữ liệu đầu vào cơ bản
    if (empty($hoten)) {$error_msg = "Họ và tên không được để trống!";
    } else {
        // Cập nhật CSDL (lưu ý tên cột 'díachi' có dấu trong CSDL của bạn)
        $sql_update = "UPDATE taikhoan SET ten = ?, sdt = ?, email = ?, `díachi` = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "ssssi", $hoten, $sdt, $email, $diachi, $userId);
            if (mysqli_stmt_execute($stmt_update)) {
                // Cập nhật lại Session tên người dùng nếu có thay đổi
                $_SESSION['user'] = $hoten;
                $success_msg = "Cập nhật thông tin thành công!";
            } else {
                $error_msg = "Có lỗi xảy ra khi cập nhật dữ liệu!";
            }
            mysqli_stmt_close($stmt_update);
        } else {
            $error_msg = "Lỗi kết nối câu lệnh SQL!";
        }
    }
}

// 3. LẤY THÔNG TIN HIỆN TẠI ĐỂ ĐIỀN VÀO FORM
$sql = "SELECT * FROM taikhoan WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
$username = $_SESSION['user'] ?? '';
$phone = "";
$email = "";
$diachi = "";
$vaitro = "Thành viên";

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $username = $user['ten'];
        $phone    = $user['sdt'] ?? '';
        $email    = $user['email'] ?? '';
        $diachi   = $user['díachi'] ?? ''; // Đọc trường díachi từ CSDL
        $vaitro   = ($user['vaitro'] === 'admin') ? 'Quản trị viên' : 'Thành viên thân thiết';
    }
    mysqli_stmt_close($stmt);
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                <div class="row g-0">
                    
                    <!-- Cột bên trái: Avatar & Vai trò -->
                    <div class="col-sm-4 bg-success text-center text-white d-flex flex-column justify-content-center align-items-center p-4">
                        <div class="avatar-wrapper mb-3 position-relative">
                            <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 100px; height: 100px;">
                                <i class="fa-solid fa-user-pen fs-1"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-1 text-truncate w-100"><?php echo htmlspecialchars($username); ?></h4>
                        <span class="badge bg-white text-success rounded-pill px-3 py-1 fw-bold mb-3 small shadow-sm">
                            <?php echo $vaitro; ?>
                        </span>
                        
                        <p class="small text-white-50 text-center mb-0 mt-2">
                            <i class="fa-solid fa-circle-info me-1"></i> Chỉnh sửa thông tin tài khoản của bạn để nhận hàng chính xác hơn.
                        </p>
                    </div>

                    <!-- Cột bên phải: Form chỉnh sửa -->
                    <div class="col-sm-8 bg-white p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h3 class="fw-bold text-dark mb-4 pb-2 border-bottom text-uppercase fs-5 tracking-wide text-secondary">
                                <i class="fa-solid fa-user-gear me-2 text-success"></i>Cập nhật thông tin
                            </h3>

                            <!-- Thông báo thành công hoặc thất bại -->
                            <?php if (!empty($success_msg)): ?>
                                <div class="alert alert-success alert-dismissible fade show rounded-3 small py-2" role="alert">
                                    <i class="fa-solid fa-circle-check me-1"></i> <?php echo $success_msg; ?>
                                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($error_msg)): ?>
                                <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2" role="alert">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> <?php echo $error_msg; ?>
                                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form action="" method="POST" id="editProfileForm">
                                <div class="row g-3">
                                    
                                    <!-- Họ và tên -->
                                    <div class="col-12">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">
                                            Họ và tên <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-signature text-muted"></i></span>
                                            <input type="text" name="hoten" class="form-control bg-light border-start-0" value="<?php echo htmlspecialchars($username); ?>" required>
                                        </div>
                                    </div>

                                    <!-- Số điện thoại -->
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Số điện thoại</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-phone text-muted"></i></span>
                                            <input type="text" name="sdt" class="form-control bg-light border-start-0" value="<?php echo htmlspecialchars($phone); ?>" placeholder="0901234567">
                                        </div>
                                    </div>

                                    <!-- Địa chỉ Email -->
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Địa chỉ Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                                            <input type="email" name="email" class="form-control bg-light border-start-0" value="<?php echo htmlspecialchars($email); ?>" placeholder="example@gmail.com">
                                        </div>
                                    </div>

                                    <!-- Địa chỉ nhận hàng -->
                                    <div class="col-12 mb-2">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Địa chỉ nhận hàng</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-location-dot text-muted"></i></span>
                                            <input type="text" name="diachi" class="form-control bg-light border-start-0" value="<?php echo htmlspecialchars($diachi); ?>" placeholder="Số nhà, đường, phường/xã...">
                                        </div>
                                    </div>

                                </div>

                                <!-- Nút thao tác -->
                                <div class="d-flex flex-wrap gap-2 pt-3 border-top mt-3">
                                    <a href="index.php?page=profile" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold btn-sm">
                                        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                                    </a>
                                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold ms-auto btn-sm shadow-sm">
                                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-wrapper div {
        transition: transform 0.4s ease;
    }
    .card:hover .avatar-wrapper div {
        transform: scale(1.05) rotate(-5deg);
    }
    .input-group-text {
        color: #6c757d;
    }
    .form-control:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
</style>