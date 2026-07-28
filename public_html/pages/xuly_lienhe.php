<?php
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone"); 

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoten   = mysqli_real_escape_string($conn, $_POST['hoten']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $noidung = mysqli_real_escape_string($conn, $_POST['noidung']);

    // 1. Kiểm tra nhập thiếu thông tin
    if (empty($hoten) || empty($email) || empty($noidung)) {
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
                        title: 'Vui lòng nhập đầy đủ thông tin!',
                        showConfirmButton: false,
                        timer: 1000,
                        timerProgressBar: true
                    }).then(function() {
                        window.history.back();
                    });
                });
            </script>
        </body>
        </html>
        <?php
        exit();
    }

    // 2. Thêm vào CSDL
    $sql = "INSERT INTO lienhe (ten, email, noidung, ngaygui) VALUES ('$hoten', '$email', '$noidung', NOW())";

    if (mysqli_query($conn, $sql)) {
        // Gửi thành công -> Hiện Toast 2s rồi quay về trang liên hệ
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
                        title: 'Gửi thông tin liên hệ thành công!',
                        showConfirmButton: false,
                        timer: 500,
                        timerProgressBar: true
                    }).then(function() {
                        window.location.href = '../index.php?page=lienhe';
                    });
                });
            </script>
        </body>
        </html>
        <?php
    } else {
        // Lỗi CSDL -> Hiện Toast báo lỗi
        $error_msg = mysqli_real_escape_string($conn, mysqli_error($conn));
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
                        icon: 'error',
                        title: 'Lỗi hệ thống: <?= $error_msg ?>',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    }).then(function() {
                        window.history.back();
                    });
                });
            </script>
        </body>
        </html>
        <?php
    }
}

mysqli_close($conn);
?>