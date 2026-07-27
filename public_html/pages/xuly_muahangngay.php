<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
mysqli_set_charset($conn, "utf8mb4");

if (!$conn) {
    die("Kết nối cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// 1. Kiểm tra trạng thái đăng nhập
if (!isset($_SESSION['user_id']) && !isset($_SESSION['user']['id'])) {
    echo "<script>alert('Vui lòng đăng nhập để tiến hành mua sản phẩm!'); window.location.href='../index.php?page=login';</script>";
    exit();
}

$uid = $_SESSION['user_id'] ?? $_SESSION['user']['id'];

// Hứng id sản phẩm (Nhận cả 'idsp' lẫn 'id_sanpham' để tránh lỗi)
$id_sp = isset($_POST['idsp']) ? intval($_POST['idsp']) : (isset($_POST['id_sanpham']) ? intval($_POST['id_sanpham']) : 0);

// Hứng số lượng (Nhận cả 'soluong' lẫn 'so_luong')
$soluong = isset($_POST['soluong']) ? intval($_POST['soluong']) : (isset($_POST['so_luong']) ? intval($_POST['so_luong']) : 1);

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

// 3. Lấy thông tin giao hàng từ Form Modal (Nếu không có thì lấy mặc định từ tài khoản)
$sql_user = mysqli_query($conn, "SELECT * FROM taikhoan WHERE id = '$uid'");
$user_info = mysqli_fetch_assoc($sql_user);

$hoten  = mysqli_real_escape_string($conn, $_POST['hoten_order'] ?? $user_info['ten'] ?? $user_info['hoten'] ?? '');
$sdt    = mysqli_real_escape_string($conn, $_POST['sdt_order'] ?? $user_info['sdt'] ?? '');
$email  = mysqli_real_escape_string($conn, $_POST['email_order'] ?? $user_info['email'] ?? '');
$diachi = mysqli_real_escape_string($conn, $_POST['diachi_order'] ?? $user_info['diachi'] ?? 'Chưa cập nhật địa chỉ');

$thanhtoan = "COD (Thanh toán khi nhận hàng)";
$trangthai = "Chờ xác nhận";

// 4. Tiến hành khởi tạo đơn hàng mới trong bảng `donhang` (Có lưu địa chỉ, tên, SĐT)
$sql_order = "INSERT INTO donhang (id_taikhoan, ngaymua, tongtien, trangthai, diachigiaohang, tennguoinhan, sdtnhan, email, thanhtoan) 
              VALUES ('$uid', NOW(), '$tong_tien', '$trangthai', '$diachi', '$hoten', '$sdt', '$email', '$thanhtoan')";

// Dự phòng nếu CSDL dùng tên cột là 'ngaydat' hoặc chưa có cột 'email'
if (!mysqli_query($conn, $sql_order)) {
    $sql_order = "INSERT INTO donhang (id_taikhoan, ngaydat, tongtien, trangthai, diachigiaohang, tennguoinhan, sdtnhan, thanhtoan) 
                  VALUES ('$uid', NOW(), '$tong_tien', '$trangthai', '$diachi', '$hoten', '$sdt', '$thanhtoan')";
    mysqli_query($conn, $sql_order);
}

$id_donhang_moi = mysqli_insert_id($conn);

if ($id_donhang_moi > 0) {
    // 5. Lưu thông tin sản phẩm vào bảng `chitietdonhang`
    $sql_detail = "INSERT INTO chitietdonhang (id_donhang, id_sanpham, soluong, dongia) 
                   VALUES ('$id_donhang_moi', '$id_sp', '$soluong', '$gia_ban')";
    mysqli_query($conn, $sql_detail);
    
    // 6. Khấu trừ số lượng tồn kho của mặt hàng
    mysqli_query($conn, "UPDATE sanpham SET soluong = soluong - $soluong WHERE id = $id_sp");
    
    // 7. Xóa sản phẩm này ra khỏi giỏ hàng nếu nó đã từng nằm trong giỏ
    mysqli_query($conn, "DELETE FROM giohang WHERE id_taikhoan = '$uid' AND id_sanpham = '$id_sp'");

    echo "<script>alert('Đặt hàng thành công! Đơn hàng của bạn đang được duyệt.'); window.location.href='../index.php?page=lichsu_donhang';</script>";
} else {
    echo "<script>alert('Đặt hàng thất bại do lỗi hệ thống đơn hàng!'); window.history.back();</script>";
}

mysqli_close($conn);
?>
