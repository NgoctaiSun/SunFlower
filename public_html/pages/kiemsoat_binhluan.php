<?php
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>alert('Yêu cầu bất hợp lệ!'); window.history.back();</script>";
    exit();
}

// 1. Chức năng Ẩn hoặc Hiện bình luận
if ($action == 'toggle_status') {
    $current_status = $_GET['current'];
    // Nếu trạng thái cũ là 1 (Hiển thị) thì chuyển thành 0 (Ẩn) và ngược lại
    $new_status = ($current_status == '1' || $current_status == 'Hiển thị') ? '0' : '1';
    
    $sql = "UPDATE binhluan SET trangthai = '$new_status' WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Thay đổi trạng thái bình luận thành công!');window.location.href='admin.php?action=quanly_binhluan';</script>";
    }
}

// 2. Chức năng xóa bình luận vĩnh viễn
if ($action == 'delete') {
    $sql = "DELETE FROM binhluan WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Xóa bình luận thành công!');window.location.href='admin.php?action=quanly_binhluan';</script>";
    }
}

mysqli_close($conn);
?>