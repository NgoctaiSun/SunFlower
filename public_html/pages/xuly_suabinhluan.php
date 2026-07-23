<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
if (!$conn) {
    die("Kết nối database thất bại");
}

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.history.back();</script>";
    exit();
}

$iduser      = intval($_SESSION['user_id']);
$id_binhluan = isset($_POST['id_binhluan']) ? (int)$_POST['id_binhluan'] : 0;
$noidung_moi = isset($_POST['noidung_moi']) ? mysqli_real_escape_string($conn, trim($_POST['noidung_moi'])) : '';

if (empty($noidung_moi)) {
    echo "<script>alert('Nội dung không được để trống!'); window.history.back();</script>";
    exit();
}

// -----------------------------------------------------------------
// KIỂM TRA ĐIỀU KIỆN: Kiểm tra số lần sửa (solansua < 1)
// -----------------------------------------------------------------
$sql_check = "SELECT solansua FROM binhluan WHERE id = '$id_binhluan' AND id_taikhoan = '$iduser'";
$query_check = mysqli_query($conn, $sql_check);
$row = mysqli_fetch_assoc($query_check);

if (!$row) {
    echo "<script>alert('Bình luận không tồn tại!'); window.history.back();</script>";
    exit();
}

if ($row['solansua'] >= 1) {
    echo "<script>alert('Bạn đã hết lượt chỉnh sửa! (Mỗi bình luận chỉ được sửa 1 lần).'); window.history.back();</script>";
    exit();
}

// -----------------------------------------------------------------
// THỰC HIỆN CẬP NHẬT VÀ TĂNG `solansua` LÊN 1
// -----------------------------------------------------------------
$sql_update = "UPDATE binhluan 
               SET noidung = '$noidung_moi', solansua = solansua + 1 
               WHERE id = '$id_binhluan' AND id_taikhoan = '$iduser'";

if (mysqli_query($conn, $sql_update)) {
    echo "<script>alert('Chỉnh sửa bình luận thành công!'); window.history.back();</script>";
} else {
    echo "<script>alert('Lỗi cập nhật bình luận!'); window.history.back();</script>";
}

mysqli_close($conn);
?> 
