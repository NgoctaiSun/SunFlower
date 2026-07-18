<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Kết nối cơ sở dữ liệu store_db của bạn
$conn = mysqli_connect("localhost", "root", "", "store_db");

if (!$conn) {
    die("Kết nối cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// 2. Kiểm tra trạng thái đăng nhập của tài khoản (Khớp với $_SESSION['iduser'] của bạn)
if (!isset($_SESSION['iduser'])) {
    echo "<script>alert('Vui lòng đăng nhập để tiến hành mua hàng!'); window.location.href='index.php?page=login';</script>";
    exit();
}

$iduser = $_SESSION['iduser'];

$valid_items = [];
$tongtien = 0;
$id_list_string = ""; // Khởi tạo biến để dùng chung khi xóa giỏ hàng

/* -------------------------------------------------------------------------- */
/* LUỒNG 1: MUA NGAY TỪ TRANG CHI TIẾT SẢN PHẨM                               */
/* -------------------------------------------------------------------------- */
if(isset($_POST['buy_now'])) 
{ 
    $idsp = intval($_POST['idsp']);
    $soluong = intval($_POST['so_luong']); // Đồng bộ tên biến ô nhập so_luong của bạn

    if($soluong <= 0){
        $soluong = 1;
    }

    // Truy vấn bảng products và cột idsp của bạn
    $sql_sp = mysqli_query($conn,"
        SELECT *
        FROM products
        WHERE idsp='$idsp' AND trang_thai = 1
    ");

    if(mysqli_num_rows($sql_sp) == 0){
        echo "<script>
                alert('Sản phẩm không tồn tại hoặc đã bị ẩn!');
                history.back();
              </script>";
        exit();
    }

    $sp = mysqli_fetch_assoc($sql_sp);

    // Kiểm tra số lượng tồn kho (cột so_luong của bạn)
    if($soluong > $sp['so_luong']){
        echo "<script>
                alert('Sản phẩm không đủ số lượng tồn kho!');
                history.back();
              </script>";
        exit();
    }

    $tongtien = $sp['gia'] * $soluong;

    $valid_items[] = [
        'idsp' => $sp['idsp'],
        'ten' => $sp['ten'],
        'so_luong_mua' => $soluong,
        'gia' => $sp['gia'],
        'so_luong_kho' => $sp['so_luong']
    ];
}
/* -------------------------------------------------------------------------- */
/* LUỒNG 2: MUA TỪ GIỎ HÀNG (Có tích chọn nhiều sản phẩm)                      */
/* -------------------------------------------------------------------------- */
else 
{ 
    if (!isset($_POST['cart_items']) || empty($_POST['cart_items'])) {
        echo "<script>
                alert('Bạn chưa chọn sản phẩm nào để đặt hàng!');
                window.location.href='index.php?page=cart';
              </script>";
        exit();
    }

    $selected_cart_ids = $_POST['cart_items'];
    $id_list_string = implode(",", array_map('intval', $selected_cart_ids));

    // Đồng bộ: bảng `cart` nối với `products` qua `idsp`, điều kiện `iduser`
    $sql_cart = "
        SELECT
            c.id AS id_cart,
            c.so_luong AS so_luong_mua,
            p.idsp,
            p.ten,
            p.gia,
            p.so_luong AS so_luong_kho
        FROM cart c
        JOIN products p ON c.idsp = p.idsp
        WHERE c.iduser='$iduser'
        AND c.id IN ($id_list_string)
    ";

    $result_cart = mysqli_query($conn, $sql_cart);

    if(!$result_cart || mysqli_num_rows($result_cart) == 0){
        echo "<script>
                alert('Dữ liệu giỏ hàng không hợp lệ!');
                window.location.href='index.php?page=cart';
              </script>";
        exit();
    }

    while($item = mysqli_fetch_assoc($result_cart))
    {
        if($item['so_luong_mua'] > $item['so_luong_kho'])
        {
            echo "<script>
                    alert('Sản phẩm ".$item['ten']." không đủ hàng trong kho (Hiện còn: ".$item['so_luong_kho']."). Vui lòng kiểm tra lại!');
                    window.location.href='index.php?page=cart';
                  </script>";
            exit();
        }

        $tongtien += $item['gia'] * $item['so_luong_mua'];
        $valid_items[] = $item;
    }
}

/* -------------------------------------------------------------------------- */
/* TIẾN HÀNH TẠO ĐƠN HÀNG VÀ LƯU CHI TIẾT                                    */
/* -------------------------------------------------------------------------- */

// 5. Lấy thông tin từ bảng `users` bằng `iduser` của bạn để làm thông tin nhận hàng
$sql_user = mysqli_query($conn, "SELECT * FROM users WHERE iduser = '$iduser'");
$user_info = mysqli_fetch_assoc($sql_user);

$ten_nguoi_nhan    = mysqli_real_escape_string($conn, $user_info['ten']);
$sdt_nguoi_nhan    = mysqli_real_escape_string($conn, $user_info['sdt']);
$dia_chi_giao_hang = mysqli_real_escape_string($conn, !empty($user_info['dia_chi']) ? $user_info['dia_chi'] : 'Chưa cập nhật địa chỉ');
$trang_thai        = 0; // 0: Chờ xử lý / Chờ xác nhận theo cấu trúc số của bạn

// 6. Thêm thông tin vào bảng `orders` (Khớp tên các cột trong bài của bạn)
$sql_order = "INSERT INTO orders (iduser, ten_nguoi_nhan, sdt_nguoi_nhan, dia_chi_giao_hang, tong_tien, trang_thai, ngay_mua) 
              VALUES ('$iduser', '$ten_nguoi_nhan', '$sdt_nguoi_nhan', '$dia_chi_giao_hang', '$tongtien', '$trang_thai', NOW())";

if (mysqli_query($conn, $sql_order)) {
    // Lấy ID tự động tăng của đơn hàng vừa tạo thành công
    $id_order_moi = mysqli_insert_id($conn);
    
    // 7. Vòng lặp lưu thông tin sản phẩm vào bảng `order_details` và thực hiện trừ kho
    foreach ($valid_items as $product) {
        $idsp   = $product['idsp'];
        $sl_mua  = $product['so_luong_mua'];
        $gia_ban = $product['gia'];
        
        $sql_detail = "INSERT INTO order_details (id_order, idsp, so_luong, gia) 
                       VALUES ('$id_order_moi', '$idsp', '$sl_mua', '$gia_ban')";
        mysqli_query($conn, $sql_detail);
                             
        // Khấu trừ đi số lượng tồn kho thực tế của sản phẩm
        mysqli_query($conn, "UPDATE products SET so_luong = so_luong - $sl_mua WHERE idsp = '$idsp'");
    }
    
    // 8. DỌN SẠCH GIỎ HÀNG: Chỉ xóa duy nhất những sản phẩm vừa tích chọn thanh toán thành công (Luồng 2)
    if(!isset($_POST['buy_now']) && !empty($id_list_string)) {
        mysqli_query($conn, "DELETE FROM cart WHERE iduser='$iduser' AND id IN ($id_list_string)");
    }

    echo "<script>alert('Đơn hàng của bạn đã được tạo thành công!'); window.location.href='index.php?page=home';</script>";
} else {
    echo "<script>alert('Có lỗi hệ thống phát sinh khi đặt hàng: " . mysqli_error($conn) . "'); window.location.href='index.php?page=cart';</script>";
}

mysqli_close($conn);
?>