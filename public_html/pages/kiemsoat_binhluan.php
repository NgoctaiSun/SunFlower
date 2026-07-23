
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once '../pages/connect.php';
if (!isset($conn)) {
    $conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone"); 
}

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo "<script>alert('ID bình luận không hợp lệ!'); window.history.back();</script>";
    exit();
}

// 1. Chức năng Ẩn hoặc Hiện bình luận
if ($action == 'toggle_status') {
    $current_status = isset($_GET['current']) ? $_GET['current'] : '1';
    
    // Đảo trạng thái: Nếu đang là 1 hoặc Hiển thị thì chuyển thành 0 (Ẩn), ngược lại chuyển thành 1
    $new_status = ($current_status == '1' || $current_status == 'Hiển thị') ? '0' : '1';
    
    $sql = "UPDATE binhluan SET trangthai = '$new_status' WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Thay đổi trạng thái bình luận thành công!'); window.location.href='admin.php?action=quanly_binhluan';</script>";
        exit();
    } else {
        echo "<script>alert('Lỗi cập nhật trạng thái!'); window.history.back();</script>";
        exit();
    }
} 
// 2. Chức năng xóa bình luận vĩnh viễn
elseif ($action == 'delete') {
    $sql = "DELETE FROM binhluan WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Xóa bình luận thành công!'); window.location.href='admin.php?action=quanly_binhluan';</script>";
        exit();
    } else {
        echo "<script>alert('Lỗi xóa bình luận!'); window.history.back();</script>";
        exit();
    }
} 
else {
    echo "<script>alert('Hành động không hợp lệ!'); window.history.back();</script>";
    exit();
}

mysqli_close($conn);
?>
