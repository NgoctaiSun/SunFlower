<?php
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone"); 

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$username = $_POST['hoten'];
$diachi = $_POST['diachi'];
$email = $_POST['email'];
$password = $_POST['matkhau'];
$repassword = $_POST['nhaplaimatkhau'];
$sdt = $_POST['sdt'];

if ($password != $repassword) {
    echo "<script>
        alert('Mật khẩu xác nhận không trùng khớp!');
        window.location='../index.php?page=dangky';
    </script>";
    exit();
}

if (empty($_POST['hoten']) || empty($_POST['email']) || empty($_POST['matkhau']) || empty($_POST['nhaplaimatkhau']) || empty($_POST['sdt'])) {
    echo "<script>
        alert('Vui lòng nhập đầy đủ thông tin!');
        window.location='../index.php?page=dangky';
    </script>";
    exit(); 
}

$pass_hash = password_hash($password, PASSWORD_DEFAULT);

// ĐÃ SỬA: Thay đổi `díachi` thành `diachi` để khớp chính xác với cấu trúc bảng trong file SQL
$sql = "INSERT INTO taikhoan(ten, matkhau, sdt, diachi, email, vaitro) VALUES('$username', '$pass_hash', '$sdt', '$diachi', '$email', 'user')";

if (mysqli_query($conn, $sql)) {
    echo "<script>
        alert('Đăng ký tài khoản thành công!');
        window.location='../index.php?page=login';
    </script>";
} else {
    echo "<script>
        alert('Lỗi đăng ký: " . mysqli_error($conn) . "');
        window.location='../index.php?page=dangky';
    </script>";
}

mysqli_close($conn);
?>