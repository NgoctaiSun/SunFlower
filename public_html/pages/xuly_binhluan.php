<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Kết nối database
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone"); 
if (!$conn) {
    die("Kết nối database thất bại");
}

// 2. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để đánh giá!'); window.location.href='../index.php?page=login';</script>";
    exit();
}

$iduser = intval($_SESSION['user_id']);

// 3. Nhận dữ liệu an toàn từ form
$idsp    = isset($_POST['idsp']) ? (int)$_POST['idsp'] : 0;
$noidung = isset($_POST['noidung']) ? mysqli_real_escape_string($conn, trim($_POST['noidung'])) : '';
$sosao   = isset($_POST['sosao']) ? (int)$_POST['sosao'] : 0;

// Kiểm tra tính hợp lệ của dữ liệu
if ($idsp <= 0) {
    echo "<script>alert('Sản phẩm không hợp lệ!'); window.history.back();</script>";
    exit();
}

if (empty($noidung)) {
    echo "<script>alert('Vui lòng nhập nội dung đánh giá!'); window.history.back();</script>";
    exit();
}

if ($sosao < 1 || $sosao > 5) {
    echo "<script>alert('Vui lòng chọn số sao đánh giá!'); window.history.back();</script>";
    exit();
}

// =========================================================================
// RÀNG BUỘC 1: KIỂM TRA XEM USER ĐÃ MUA VÀ NHẬN HÀNG THÀNH CÔNG CHƯA
// =========================================================================
$sql_check_buy = "SELECT d.id 
                  FROM donhang d 
                  JOIN chitietdonhang c ON d.id = c.id_donhang 
                  WHERE d.id_taikhoan = '$iduser' 
                    AND c.id_sanpham = '$idsp' 
                    AND (d.trangthai = 'Đã giao' OR d.trangthai = '1' OR d.trangthai = 'Hoàn thành')";

$query_check_buy = mysqli_query($conn, $sql_check_buy);

if (mysqli_num_rows($query_check_buy) == 0) {
    echo "<script>alert('Bạn phải mua sản phẩm này và nhận hàng thành công mới được gửi đánh giá!'); window.history.back();</script>";
    exit();
}

// =========================================================================
// RÀNG BUỘC 2: KIỂM TRA XEM USER ĐÃ BÌNH LUẬN SẢN PHẨM NÀY CHƯA (CHỈ CỦA 1 LẦN)
// =========================================================================
$sql_check_comment = "SELECT id FROM binhluan WHERE id_taikhoan = '$iduser' AND id_sanpham = '$idsp'";
$query_check_comment = mysqli_query($conn, $sql_check_comment);

if (mysqli_num_rows($query_check_comment) > 0) {
    echo "<script>alert('Bạn đã đánh giá sản phẩm này rồi! Mỗi tài khoản chỉ được đánh giá 1 lần.'); window.history.back();</script>";
    exit();
}

// =========================================================================
// THỰC HIỆN THÊM BÌNH LUẬN NẾU THỎA MÃN CÁC ĐIỀU KIỆN TRÊN
// =========================================================================
$ngaydang = date('Y-m-d H:i:s');

// Chèn bình luận vào DB (bao gồm solansua mặc định = 0)
$sql = "INSERT INTO binhluan (id_taikhoan, id_sanpham, noidung, ngaydang, sosao, trangthai, solansua) 
        VALUES ('$iduser', '$idsp', '$noidung', '$ngaydang', '$sosao', '1', 0)";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Gửi đánh giá sản phẩm thành công!'); window.location.href='../index.php?page=chitetsanpham&id=".$idsp."';</script>";
    exit();
} else {
    echo "<script>alert('Lỗi hệ thống, không thể gửi đánh giá!'); window.history.back();</script>";
}

mysqli_close($conn);
?> 
