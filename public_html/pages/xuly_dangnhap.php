<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

// Vì file này nằm trong pages, kết nối trực tiếp với connect.php cùng cấp
$conn = mysqli_connect("localhost","root","","hoahuongduongphone"); 

if (!$conn) {
    die("Kết nối thất bại");
}

if (empty($_POST['username']) || empty($_POST['password'])) {
    echo "<script>
        alert('Vui lòng nhập đầy đủ tài khoản và mật khẩu!');
        window.location='../index.php?page=login';
    </script>";
    exit();
}

$username = trim($_POST['username']);
$password = $_POST['password'];

$sql = "SELECT * FROM taikhoan WHERE ten = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['matkhau'])) {
            $_SESSION['user_id'] = $row['id']; 
            $_SESSION['user'] = $row['ten'];
            $_SESSION['role'] = $row['vaitro'];

            // Nếu là admin, chuyển sang file admin.php nằm cùng cấp trong thư mục pages
            if ($row['vaitro'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: ../index.php");
            }
            exit();

        } else {
            echo "<script>
                alert('Sai mật khẩu!');
                window.location='../index.php?page=login';
            </script>";
            exit();
        }
    } else {
        echo "<script>
            alert('Tài khoản không tồn tại!');
            window.location='../index.php?page=login';
        </script>";
        exit();
    }
}
?>