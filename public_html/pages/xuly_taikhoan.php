<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>alert('Yêu cầu không hợp lệ!'); window.history.back();</script>";
    exit();
}

// 1. Chức năng đổi vai trò (Admin <-> Khách hàng)
if ($action == 'toggle_role') {
    $current_role = $_GET['current'];
    $new_role = ($current_role == 'admin') ? 'khachhang' : 'admin';
    
    // Ngăn chặn admin tự hạ quyền của chính mình
    if ($id == $_SESSION['user_id']) {
        echo "<script>alert('Bạn không thể tự hạ quyền của chính mình!'); window.history.back();</script>";
        exit();
    }
    
    $sql = "UPDATE taikhoan SET vaitro = '$new_role' WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Cập nhật vai trò tài khoản thành công!'); window.location.href='admin.php?action=quanly_taikhoan';</script>";
    }
}

// 2. Chức năng xóa tài khoản
if ($action == 'delete') {
    if ($id == $_SESSION['user_id']) {
        echo "<script>alert('Bạn không thể xóa tài khoản admin đang đăng nhập!'); window.history.back();</script>";
        exit();
    }
    
    // Xóa các dữ liệu ràng buộc trước (Giỏ hàng, Bình luận) để tránh lỗi Khóa ngoại (Foreign Key)
    mysqli_query($conn, "DELETE FROM giohang WHERE id_taikhoan = $id");
    mysqli_query($conn, "DELETE FROM binhluan WHERE id_taikhoan = $id");
    
    $sql = "DELETE FROM taikhoan WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Đã xóa tài khoản thành công!');window.location.href='admin.php?action=quanly_taikhoan';</script>";
    } else {
        echo "<script>alert('Lỗi: Không thể xóa do tài khoản này có lịch sử mua hàng chưa xử lý.'); window.history.back();</script>";
    }
}

mysqli_close($conn);
?>