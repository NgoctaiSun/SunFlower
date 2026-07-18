<?php
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone"); 

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoten   = mysqli_real_escape_string($conn, $_POST['hoten']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $noidung = mysqli_real_escape_string($conn, $_POST['noidung']);

    if (empty($hoten) || empty($email) || empty($noidung)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.history.back();</script>";
        exit();
    }

    // ĐÃ SỬA: Loại bỏ dấu nháy đơn quanh NOW() để MySQL hiểu đây là hàm lấy thời gian hiện tại
    $sql = "INSERT INTO lienhe (ten, email, noidung, ngaygui) VALUES ('$hoten', '$email', '$noidung', NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Gửi thông tin liên hệ thành công!'); window.location='../index.php?page=lienhe';</script>";
    } else {
        echo "<script>alert('Lỗi hệ thống: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
}

mysqli_close($conn);
?>