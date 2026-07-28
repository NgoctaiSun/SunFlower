<?php
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone"); 

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$username   = $_POST['hoten'] ?? '';
$diachi     = $_POST['diachi'] ?? '';
$email      = $_POST['email'] ?? '';
$password   = $_POST['matkhau'] ?? '';
$repassword = $_POST['nhaplaimatkhau'] ?? '';
$sdt        = $_POST['sdt'] ?? '';

// ĐÃ BỔ SUNG: Hàm hiển thị thông báo SweetAlert2 giao diện đẹp & chuyển trang
function showSweetAlert($icon, $title, $text, $redirectUrl) {
    echo '
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            body { font-family: "Segoe UI", Roboto, sans-serif; background-color: #f8f9fa; }
        </style>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: "' . $icon . '",
                title: "' . addslashes($title) . '",
                text: "' . addslashes($text) . '",
                confirmButtonColor: "#198754",
                confirmButtonText: "Đồng ý",
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "' . $redirectUrl . '";
                }
            });
        </script>
    </body>
    </html>';
    exit();
}

// 1. Kiểm tra rỗng
if (empty($username) || empty($email) || empty($password) || empty($repassword) || empty($sdt)) {
    showSweetAlert('warning', 'Thiếu thông tin!', 'Vui lòng nhập đầy đủ thông tin bắt buộc.', '../index.php?page=dangky');
}

// 2. Kiểm tra mật khẩu khớp nhau
if ($password != $repassword) {
    showSweetAlert('error', 'Lỗi mật khẩu!', 'Mật khẩu xác nhận không trùng khớp.', '../index.php?page=dangky');
}

// Mã hóa mật khẩu
$pass_hash = password_hash($password, PASSWORD_DEFAULT);

// Thêm tài khoản vào Database
$sql = "INSERT INTO taikhoan(ten, matkhau, sdt, diachi, email, vaitro) VALUES('$username', '$pass_hash', '$sdt', '$diachi', '$email', 'user')";

if (mysqli_query($conn, $sql)) {
    showSweetAlert('success', 'Thành công!', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.', '../index.php?page=login');
} else {
    showSweetAlert('error', 'Lỗi đăng ký!', 'Chi tiết: ' . mysqli_error($conn), '../index.php?page=dangky');
}

mysqli_close($conn);
?>