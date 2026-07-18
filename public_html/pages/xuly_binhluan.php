<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kết nối database trực tiếp vì file này nằm trong thư mục pages/
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone"); 
if (!$conn) {
    die("Kết nối database thất bại");
}

// ĐÃ SỬA: Kiểm tra đăng nhập đồng nhất theo $_SESSION['user_id'] giống các trang khác
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để đánh giá!'); window.location.href='index.php?page=login';</script>";
    exit();
}

$iduser = intval($_SESSION['user_id']);

// Nhận dữ liệu an toàn từ form
$idsp    = (int)$_POST['idsp'];
$noidung = mysqli_real_escape_string($conn, trim($_POST['noidung']));
$sosao   = (int)$_POST['sosao'];

if (empty($noidung)) {
    echo "<script>alert('Vui lòng nhập nội dung đánh giá'); window.history.back();</script>";
    exit();
}

if ($sosao < 1 || $sosao > 5) {
    echo "<script>alert('Vui lòng chọn số sao đánh giá!'); window.history.back();</script>";
    exit();
}

$ngaydang = date('Y-m-d H:i:s');

// Chèn bình luận vào DB
$sql = "INSERT INTO binhluan (id_taikhoan, id_sanpham, noidung, ngaydang, sosao, trangthai) 
        VALUES ('$iduser', '$idsp', '$noidung', '$ngaydang', '$sosao', '1')";

if (mysqli_query($conn, $sql)) {
    // ĐÃ SỬA: Vì file này được gọi, việc điều hướng quay lại trang chi tiết sản phẩm chuẩn từ thư mục gốc
    echo "<script>alert('Gửi đánh giá sản phẩm thành công!'); window.location.href='../index.php?page=chitetsanpham&id=".$idsp."';</script>";
    exit();
} else {
    echo "<script>alert('Lỗi hệ thống, không thể gửi đánh giá!'); window.history.back();</script>";
}
?>