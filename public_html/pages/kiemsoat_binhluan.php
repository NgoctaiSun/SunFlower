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
    header("Location: admin.php?action=quanly_binhluan&status=error");
    exit();
}

// 1. Chức năng Ẩn hoặc Hiện bình luận
if ($action == 'toggle_status') {
    $current_status = isset($_GET['current']) ? $_GET['current'] : '1';
    
    // Đảo trạng thái
    $new_status = ($current_status == '1' || $current_status == 'Hiển thị') ? '0' : '1';
    
    $sql = "UPDATE binhluan SET trangthai = '$new_status' WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        // Chuyển hướng kèm tham số status (Không echo alert JavaScript)
        header("Location: admin.php?action=quanly_binhluan&status=toggled_success");
        exit();
    } else {
        header("Location: admin.php?action=quanly_binhluan&status=error");
        exit();
    }
} 
// 2. Chức năng Xóa bình luận vĩnh viễn
elseif ($action == 'delete') {
    $sql = "DELETE FROM binhluan WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: admin.php?action=quanly_binhluan&status=deleted_success");
        exit();
    } else {
        header("Location: admin.php?action=quanly_binhluan&status=error");
        exit();
    }
} 
else {
    header("Location: admin.php?action=quanly_binhluan&status=error");
    exit();
}

mysqli_close($conn);
?>