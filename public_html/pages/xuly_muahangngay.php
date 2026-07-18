<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
if (!$conn) {
    die("Kết nối cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// 1. Kiểm tra trạng thái đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để tiến hành mua sản phẩm!'); window.location.href='../index.php?page=login';</script>";
    exit();
}

$uid = $_SESSION['user_id'];
$id_sp = isset($_POST['id_sanpham']) ? intval($_POST['id_sanpham']) : 0;
$soluong = isset($_POST['soluong']) ? intval($_POST['soluong']) : 1;

if ($id_sp <= 0) {
    echo "<script>alert('Sản phẩm không hợp lệ!'); window.history.back();</script>";
    exit();
}

// 2. Lấy thông tin giá bán và kiểm tra số lượng kho thực tế
$sp_res = mysqli_query($conn, "SELECT ten, gia, soluong FROM sanpham WHERE id = $id_sp");
if (mysqli_num_rows($sp_res) == 0) {
    echo "<script>alert('Sản phẩm này không tồn tại trên hệ thống!'); window.history.back();</script>";
    exit();
}

$sp = mysqli_fetch_assoc($sp_res);
if ($sp['soluong'] < $soluong) {
    echo "<script>alert('Sản phẩm này hiện tại đã hết hàng hoặc không đủ số lượng để cung ứng!'); window.history.back();</script>";
    exit();
}

$gia_ban = $sp['gia'];
$tong_tien = $gia_ban * $soluong;
$ngay_dat = date('Y-m-d H:i:s');

// 3. Tiến hành khởi tạo đơn hàng mới trong bảng `donhang`
$sql_order = "INSERT INTO donhang (id_taikhoan, ngaydat, tongtien, trangthai) VALUES ('$uid', '$ngay_dat', '$tong_tien', 'Chờ xử lý')";
if (mysqli_query($conn, $sql_order)) {
    $id_donhang_moi = mysqli_insert_id($conn);
    
    // 4. Lưu thông tin sản phẩm vào bảng `chitietdonhang` (Sử dụng cột dongia)
    $sql_detail = "INSERT INTO chitietdonhang (id_donhang, id_sanpham, soluong, dongia) VALUES ('$id_donhang_moi', '$id_sp', '$soluong', '$gia_ban')";
    mysqli_query($conn, $sql_detail);
    
    // 5. Khấu trừ số lượng tồn kho của mặt hàng
    mysqli_query($conn, "UPDATE sanpham SET soluong = soluong - $soluong WHERE id = $id_sp");
    
    // 6. Xóa sản phẩm này ra khỏi giỏ hàng nếu nó đã từng nằm trong giỏ (Dọn sạch dữ liệu thừa)
    mysqli_query($conn, "DELETE FROM giohang WHERE id_taikhoan = '$uid' AND id_sanpham = '$id_sp'");

    echo "<script>alert('Đặt hàng thành công! Đơn hàng của bạn đang được duyệt.'); window.location.href='../index.php?page=lichsumuahang';</script>";
} else {
    echo "<script>alert('Đặt hàng thất bại do lỗi hệ thống đơn hàng!'); window.history.back();</script>";
}
?>