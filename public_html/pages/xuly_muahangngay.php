<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Kết nối cơ sở dữ liệu
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
mysqli_set_charset($conn, "utf8mb4");

if (!$conn) {
    die("Kết nối cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// 2. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) && !isset($_SESSION['user']['id'])) {
    echo "<script>alert('Vui lòng đăng nhập để tiến hành mua sản phẩm!'); window.location.href='../index.php?page=login';</script>";
    exit();
}

$uid = $_SESSION['user_id'] ?? $_SESSION['user']['id'];

// 3. Hứng dữ liệu từ Form Modal (Nhận cả name 'idsp' lẫn 'id_sanpham')
$id_sp = isset($_POST['idsp']) ? intval($_POST['idsp']) : (isset($_POST['id_sanpham']) ? intval($_POST['id_sanpham']) : 0);
$soluong = isset($_POST['soluong']) ? intval($_POST['soluong']) : (isset($_POST['so_luong']) ? intval($_POST['so_luong']) : 1);

if ($id_sp <= 0) {
    echo "<script>alert('Sản phẩm không hợp lệ!'); window.history.back();</script>";
    exit();
}

// 4. Kiểm tra tồn kho sản phẩm
$sp_res = mysqli_query($conn, "SELECT ten, gia, soluong FROM sanpham WHERE id = $id_sp");
if (mysqli_num_rows($sp_res) == 0) {
    echo "<script>alert('Sản phẩm này không tồn tại trên hệ thống!'); window.history.back();</script>";
    exit();
}

$sp = mysqli_fetch_assoc($sp_res);
if ($sp['soluong'] < $soluong) {
    echo "<script>alert('Sản phẩm này hiện tại không đủ số lượng để cung ứng!'); window.history.back();</script>";
    exit();
}

$gia_ban = $sp['gia'];
$tong_tien = $gia_ban * $soluong;

// 5. Lấy thông tin người nhận từ Form Modal (Nếu trống sẽ lấy từ bảng taikhoan)
$sql_user = mysqli_query($conn, "SELECT * FROM taikhoan WHERE id = '$uid'");
$user_info = mysqli_fetch_assoc($sql_user);

$hoten  = mysqli_real_escape_string($conn, $_POST['hoten_order'] ?? $user_info['ten'] ?? '');
$sdt    = intval($_POST['sdt_order'] ?? $user_info['sdt'] ?? 0); // Ép kiểu int cho khớp CSDL INT(11)
$diachi = mysqli_real_escape_string($conn, $_POST['diachi_order'] ?? $user_info['diachi'] ?? 'Chưa cập nhật địa chỉ');

$thanhtoan = "COD (Thanh toán khi nhận hàng)";
$trangthai = "Chờ xác nhận";

// 6. Thêm đơn hàng vào bảng `donhang` (Chuẩn 100% cột CSDL)
$sql_order = "INSERT INTO donhang (id_taikhoan, ngaymua, tongtien, trangthai, diachigiaohang, tennguoinhan, sdtnhan, thanhtoan) 
              VALUES ('$uid', NOW(), '$tong_tien', '$trangthai', '$diachi', '$hoten', '$sdt', '$thanhtoan')";

if (mysqli_query($conn, $sql_order)) {
    $id_donhang_moi = mysqli_insert_id($conn);

    // 7. Lưu chi tiết đơn hàng vào bảng `chitietdonhang`
    $sql_detail = "INSERT INTO chitietdonhang (id_donhang, id_sanpham, soluong, dongia) 
                   VALUES ('$id_donhang_moi', '$id_sp', '$soluong', '$gia_ban')";
    mysqli_query($conn, $sql_detail);
    
    // 8. Trừ số lượng tồn kho của sản phẩm
    mysqli_query($conn, "UPDATE sanpham SET soluong = soluong - $soluong WHERE id = $id_sp");
    
    // 9. Xóa sản phẩm khỏi giỏ hàng nếu từng có trong giỏ
    mysqli_query($conn, "DELETE FROM giohang WHERE id_taikhoan = '$uid' AND id_sanpham = '$id_sp'");

    echo "<script>alert('Đặt hàng thành công! Đơn hàng của bạn đang được duyệt.'); window.location.href='../index.php?page=lichsumuahang';</script>";
} else {
    echo "<script>alert('Đặt hàng thất bại do lỗi: " . mysqli_error($conn) . "'); window.history.back();</script>";
}

mysqli_close($conn);
?>
