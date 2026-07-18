<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ĐÃ SỬA: Nhúng đúng file connect.php vì nó nằm cùng thư mục pages/ với file này
include_once 'connect.php'; 

if (!isset($conn)) {
    $conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
}

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Vui lòng đăng nhập để thực hiện chức năng này!');
        window.location.href = 'index.php?page=login';
    </script>";
    exit();
}

// Xử lý thêm sản phẩm vào giỏ hàng
if (isset($_GET['id_sanpham'])) {
    $id_taikhoan = $_SESSION['user_id'];
    $id_sanpham = intval($_GET['id_sanpham']);
    $soluong = 1; 

    $check_cart = mysqli_query($conn, "SELECT * FROM giohang WHERE id_taikhoan = '$id_taikhoan' AND id_sanpham = '$id_sanpham'");

    if (mysqli_num_rows($check_cart) > 0) {
        $row = mysqli_fetch_assoc($check_cart);
        $new_qty = $row['soluong'] + 1;
        mysqli_query($conn, "UPDATE giohang SET soluong = '$new_qty' WHERE id_taikhoan = '$id_taikhoan' AND id_sanpham = '$id_sanpham'");
    } else {
        mysqli_query($conn, "INSERT INTO giohang(id_taikhoan, id_sanpham, soluong) VALUES ('$id_taikhoan', '$id_sanpham', '$soluong')");
    }

    // ĐÃ SỬA: Điều hướng chuẩn về trang giỏ hàng tại file index gốc
    echo "<script>
        alert('Đã thêm sản phẩm vào giỏ hàng thành công!');
        window.location.href = 'index.php?page=giohang';
    </script>";
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>