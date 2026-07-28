<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Nhúng file kết nối cơ sở dữ liệu
include_once 'connect.php'; 

if (!isset($conn)) {
    $conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
}

// 1. KIỂM TRA CHƯA ĐĂNG NHẬP: Hiển thị SweetAlert2 cảnh báo rồi chuyển hướng sang trang Login
if (!isset($_SESSION['user_id'])) {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    toast: true,
                    position: 'top-right',
                    icon: 'warning',
                    title: 'Vui lòng đăng nhập để thực hiện chức năng này!',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                }).then(function() {
                    window.location.href = 'index.php?page=login';
                });
            });
        </script>
    </body>
    </html>
    <?php
    exit();
}

// 2. XỬ LÝ THÊM SẢN PHẨM VÀO GIỎ HÀNG
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

    // Hiển thị thông báo Toast thành công ở góc phải trên rồi chuyển hướng về Giỏ hàng
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    toast: true,
                    position: 'top-right',
                    icon: 'success',
                    title: 'Đã thêm sản phẩm vào giỏ hàng!',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                }).then(function() {
                    window.location.href = 'index.php?page=giohang';
                });
            });
        </script>
    </body>
    </html>
    <?php
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>