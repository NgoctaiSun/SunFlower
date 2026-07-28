<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Hàm bổ trợ hiển thị thông báo Toast bằng SweetAlert2 và quay lại trang trước
function showToastAndBack($icon, $title) {
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
                    icon: '<?= $icon ?>',
                    title: '<?= addslashes($title) ?>',
                    showConfirmButton: false,
                    timer: 2000,
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

$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
if (!$conn) {
    die("Kết nối database thất bại");
}

if (!isset($_SESSION['user_id'])) {
    showToastAndBack('warning', 'Vui lòng đăng nhập!');
}

$iduser      = intval($_SESSION['user_id']);
$id_binhluan = isset($_POST['id_binhluan']) ? (int)$_POST['id_binhluan'] : 0;
$noidung_moi = isset($_POST['noidung_moi']) ? mysqli_real_escape_string($conn, trim($_POST['noidung_moi'])) : '';

if (empty($noidung_moi)) {
    showToastAndBack('warning', 'Nội dung không được để trống!');
}

// -----------------------------------------------------------------
// KIỂM TRA ĐIỀU KIỆN: Kiểm tra số lần sửa (solansua < 1)
// -----------------------------------------------------------------
$sql_check = "SELECT solansua FROM binhluan WHERE id = '$id_binhluan' AND id_taikhoan = '$iduser'";
$query_check = mysqli_query($conn, $sql_check);
$row = mysqli_fetch_assoc($query_check);

if (!$row) {
    showToastAndBack('error', 'Bình luận không tồn tại!');
}

if ($row['solansua'] >= 1) {
    showToastAndBack('warning', 'Bạn đã hết lượt chỉnh sửa! (Mỗi bình luận chỉ được sửa 1 lần).');
}

// -----------------------------------------------------------------
// THỰC HIỆN CẬP NHẬT VÀ TĂNG `solansua` LÊN 1
// -----------------------------------------------------------------
$sql_update = "UPDATE binhluan 
               SET noidung = '$noidung_moi', solansua = solansua + 1 
               WHERE id = '$id_binhluan' AND id_taikhoan = '$iduser'";

if (mysqli_query($conn, $sql_update)) {
    showToastAndBack('success', 'Chỉnh sửa bình luận thành công!');
} else {
    showToastAndBack('error', 'Lỗi cập nhật bình luận!');
}

mysqli_close($conn);
?>