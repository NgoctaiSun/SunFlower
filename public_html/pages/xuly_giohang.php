<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kết nối trực tiếp để tránh lỗi include sai đường dẫn giữa các cấp thư mục
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
if (!$conn) {
    die("Kết nối database thất bại tại giỏ hàng: " . mysqli_connect_error());
}

// Lấy link trang hiện tại người dùng đang đứng (để quay lại đúng trang đó)
$back_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';

// 1. KIỂM TRA ĐĂNG NHẬP
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
                    window.location.href = '../index.php?page=login';
                });
            });
        </script>
    </body>
    </html>
    <?php
    exit();
}

$uid = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if (isset($_POST['add_to_cart']) || isset($_POST['buy_now'])) {
    $action = 'add';
    $id_sanpham = isset($_POST['idsp']) ? intval($_POST['idsp']) : 0;
    $_GET['id'] = $id_sanpham;
} else {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
}

switch ($action) {
    // 1. CHỨC NĂNG: THÊM SẢN PHẨM VÀO GIỎ HÀNG
    case 'add':
        $id_sanpham = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $soluong = isset($_POST['soluong']) ? intval($_POST['soluong']) : 1;

        if ($soluong <= 0) {
            $soluong = 1;
        }

        if ($id_sanpham > 0) {
            $check_q = mysqli_query(
                $conn,
                "SELECT id, soluong
                 FROM giohang
                 WHERE id_taikhoan='$uid'
                 AND id_sanpham='$id_sanpham'"
            );

            if (mysqli_num_rows($check_q) > 0) {
                $row = mysqli_fetch_assoc($check_q);
                $new_qty = $row['soluong'] + $soluong;

                mysqli_query(
                    $conn,
                    "UPDATE giohang
                     SET soluong='$new_qty'
                     WHERE id='{$row['id']}'"
                );

                $id_giohang = $row['id'];
            } else {
                mysqli_query(
                    $conn,
                    "INSERT INTO giohang(id_taikhoan,id_sanpham,soluong)
                     VALUES('$uid','$id_sanpham','$soluong')"
                );

                $id_giohang = mysqli_insert_id($conn);
            }

            // Nếu người dùng chọn "Mua ngay" -> Chuyển sang thanh toán
            if (isset($_POST['buy_now'])) {
                header("Location: ../index.php?page=thanhtoan&id_giohang=" . $id_giohang);
                exit();
            } else {
                // Thêm thành công -> Hiện Toast ở góc phải và Ở LẠI TRANG ĐANG XEM
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
                                title: 'Đã thêm vào giỏ hàng!',
                                showConfirmButton: false,
                                timer: 1000,
                                timerProgressBar: true
                            }).then(function() {
                                // Quay lại chính trang sản phẩm người dùng vừa đứng
                                window.location.href = '<?= $back_url ?>';
                            });
                        });
                    </script>
                </body>
                </html>
                <?php
                exit();
            }
        }

        header("Location: " . $back_url);
        exit();
        break; 

    // 2. CHỨC NĂNG: CẬP NHẬT SỐ LƯỢNG QUA Ô INPUT
    case 'update_qty_direct':
        $id_giohang = isset($_GET['id_giohang']) ? intval($_GET['id_giohang']) : 0;
        $soluong = isset($_GET['soluong']) ? intval($_GET['soluong']) : 1;
        if ($soluong <= 0) $soluong = 1;
        
        if ($id_giohang > 0) {
            mysqli_query($conn, "UPDATE giohang SET soluong = '$soluong' WHERE id = '$id_giohang' AND id_taikhoan = '$uid'");
            echo "Success";
        }
        break;

    // 3. CHỨC NĂNG: XÓA SẢN PHẨM KHỎI GIỎ HÀNG
    case 'delete':
        $id_giohang = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id_giohang > 0) {
            mysqli_query($conn, "DELETE FROM giohang WHERE id = '$id_giohang' AND id_taikhoan = '$uid'");
        }
        header("Location: ../index.php?page=giohang");
        break;

    default:
        header("Location: ../index.php");
        break;
}
?>