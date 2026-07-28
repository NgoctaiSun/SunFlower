<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Hàm bổ trợ hiển thị thông báo Toast bằng SweetAlert2 và chuyển hướng
function showToastAndRedirect($icon, $title, $redirectUrl = null, $isBack = false) {
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
                    timer: 2500,
                    timerProgressBar: true
                }).then(function() {
                    <?php if ($isBack): ?>
                        window.history.back();
                    <?php elseif ($redirectUrl): ?>
                        window.location.href = '<?= $redirectUrl ?>';
                    <?php endif; ?>
                });
            });
        </script>
    </body>
    </html>
    <?php
    exit();
}

// 1. Kết nối cơ sở dữ liệu
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");

if (!$conn) {
    die("Kết nối cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// 2. Kiểm tra trạng thái đăng nhập
if (!isset($_SESSION['user_id'])) {
    showToastAndRedirect('warning', 'Vui lòng đăng nhập để tiến hành mua hàng!', '../index.php?page=login');
}

$id_taikhoan = $_SESSION['user_id'];

$valid_items = [];
$tongtien = 0;
$id_list_string = "";

/* -------------------------------------------------------------------------- */
/* LUỒNG 1: MUA NGAY TỪ TRANG CHI TIẾT SẢN PHẨM                               */
/* -------------------------------------------------------------------------- */
if (isset($_POST['buy_now'])) 
{ 
    $id_sanpham = intval($_POST['idsp']);
    $soluong_mua = intval($_POST['so_luong']); 

    if ($soluong_mua <= 0) {
        $soluong_mua = 1;
    }

    $sql_sp = mysqli_query($conn, "SELECT * FROM sanpham WHERE id = '$id_sanpham'");

    if (mysqli_num_rows($sql_sp) == 0) {
        showToastAndRedirect('error', 'Sản phẩm không tồn tại trên hệ thống!', null, true);
    }

    $sp = mysqli_fetch_assoc($sql_sp);

    // Kiểm tra số lượng tồn kho
    if ($soluong_mua > $sp['soluong']) {
        showToastAndRedirect('warning', 'Sản phẩm không đủ số lượng tồn kho!', null, true);
    }

    $tongtien = $sp['gia'] * $soluong_mua;

    $valid_items[] = [
        'id_sanpham' => $sp['id'],
        'ten' => $sp['ten'],
        'soluong_mua' => $soluong_mua,
        'gia' => $sp['gia'],
        'soluong_kho' => $sp['soluong']
    ];
}
/* -------------------------------------------------------------------------- */
/* LUỒNG 2: MUA TỪ GIỎ HÀNG                                                   */
/* -------------------------------------------------------------------------- */
else 
{ 
    if (!isset($_POST['cart_items']) || empty($_POST['cart_items'])) {
        showToastAndRedirect('warning', 'Bạn chưa chọn sản phẩm nào để đặt hàng!', '../index.php?page=cart');
    }

    $selected_cart_ids = $_POST['cart_items'];
    $id_list_string = implode(",", array_map('intval', $selected_cart_ids));

    $sql_cart = "
        SELECT
            g.id AS id_cart,
            g.soluong AS soluong_mua,
            s.id AS id_sanpham,
            s.ten,
            s.gia,
            s.soluong AS soluong_kho
        FROM giohang g
        JOIN sanpham s ON g.id_sanpham = s.id
        WHERE g.id_taikhoan = '$id_taikhoan'
        AND g.id IN ($id_list_string)
    ";

    $result_cart = mysqli_query($conn, $sql_cart);

    if (!$result_cart || mysqli_num_rows($result_cart) == 0) {
        showToastAndRedirect('error', 'Dữ liệu giỏ hàng không hợp lệ!', '../index.php?page=cart');
    }

    while ($item = mysqli_fetch_assoc($result_cart))
    {
        if ($item['soluong_mua'] > $item['soluong_kho'])
        {
            $msg = "Sản phẩm ".$item['ten']." không đủ hàng (Kho còn: ".$item['soluong_kho'].")!";
            showToastAndRedirect('warning', $msg, '../index.php?page=cart');
        }

        $tongtien += $item['gia'] * $item['soluong_mua'];
        $valid_items[] = $item;
    }
}

/* -------------------------------------------------------------------------- */
/* TIẾN HÀNH TẠO ĐƠN HÀNG VÀ LƯU CHI TIẾT                                    */
/* -------------------------------------------------------------------------- */

$sql_user = mysqli_query($conn, "SELECT * FROM taikhoan WHERE id = '$id_taikhoan'");
$user_info = mysqli_fetch_assoc($sql_user);

$tennguoinhan   = mysqli_real_escape_string($conn, $user_info['ten']);
$sdtnhan        = mysqli_real_escape_string($conn, $user_info['sdt']);
$diachigiaohang = mysqli_real_escape_string($conn, !empty($user_info['diachi']) ? $user_info['diachi'] : 'Chưa cập nhật địa chỉ');
$thanhtoan      = "COD (Thanh toán khi nhận hàng)";
$trangthai      = "Chờ xác nhận";

$sql_order = "INSERT INTO donhang (id_taikhoan, ngaymua, tongtien, trangthai, diachigiaohang, tennguoinhan, sdtnhan, thanhtoan) 
              VALUES ('$id_taikhoan', NOW(), '$tongtien', '$trangthai', '$diachigiaohang', '$tennguoinhan', '$sdtnhan', '$thanhtoan')";

if (mysqli_query($conn, $sql_order)) {
    $id_donhang_moi = mysqli_insert_id($conn);
    
    foreach ($valid_items as $product) {
        $id_sanpham  = $product['id_sanpham'];
        $soluong_mua = $product['soluong_mua'];
        $dongia      = $product['gia'];
        
        $sql_detail = "INSERT INTO chitietdonhang (id_donhang, id_sanpham, soluong, dongia) 
                       VALUES ('$id_donhang_moi', '$id_sanpham', '$soluong_mua', '$dongia')";
        mysqli_query($conn, $sql_detail);
                             
        // Trừ tồn kho
        mysqli_query($conn, "UPDATE sanpham SET soluong = soluong - $soluong_mua WHERE id = '$id_sanpham'");
    }
    
    // Xóa sản phẩm khỏi giỏ hàng nếu đặt thành công từ giỏ
    if (!isset($_POST['buy_now']) && !empty($id_list_string)) {
        mysqli_query($conn, "DELETE FROM giohang WHERE id_taikhoan = '$id_taikhoan' AND id IN ($id_list_string)");
    }

    // Thông báo đặt hàng thành công
    showToastAndRedirect('success', 'Đơn hàng của bạn đã được tạo thành công!', '../index.php?page=home');
} else {
    // Thông báo lỗi phát sinh
    showToastAndRedirect('error', 'Có lỗi phát sinh: ' . mysqli_error($conn), '../index.php?page=cart');
}

mysqli_close($conn);
?>