<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
if (!$conn) {
    die("Kết nối thất bại");
}

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Bạn không có quyền thực hiện!'); window.location.href='../index.php';</script>";
    exit();
}

$id_binhluan  = isset($_POST['id_binhluan']) ? intval($_POST['id_binhluan']) : 0;
$traloi_admin = isset($_POST['traloi_admin']) ? mysqli_real_escape_string($conn, trim($_POST['traloi_admin'])) : '';
$ngay_traloi  = date('Y-m-d H:i:s');

if ($id_binhluan <= 0) {
    echo "<script>alert('Bình luận không hợp lệ!'); window.history.back();</script>";
    exit();
}

// Cập nhật câu trả lời của admin
$sql = "UPDATE binhluan 
        SET traloi_admin = '$traloi_admin', ngay_traloi = '$ngay_traloi' 
        WHERE id = '$id_binhluan'";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Lưu phản hồi thành công!'); window.location.href='admin.php?action=quanly_binhluan';</script>";
} else {
    echo "<script>alert('Lỗi cập nhật phản hồi!'); window.history.back();</script>";
}

mysqli_close($conn);
?>