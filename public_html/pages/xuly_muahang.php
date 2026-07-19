<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Kết nối cơ sở dữ liệu hoahuongduongphone chuẩn CSDL
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");

if (!$conn) {
    die("Kết nối cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// 2. Kiểm tra trạng thái đăng nhập dựa trên id tài khoản
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để tiến hành mua hàng!'); window.location.href='index.php?page=login';</script>";
    exit();
}

$id_taikhoan = $_SESSION['user_id']; // ID của tài khoản đang đăng nhập

$valid_items = [];
$tongtien = 0;
$id_list_string = ""; // Chuỗi danh sách ID giỏ hàng cần xóa sau khi đặt thành công

/* -------------------------------------------------------------------------- */
/* LUỒNG 1: MUA NGAY TỪ TRANG CHI TIẾT SẢN PHẨM                               */
/* -------------------------------------------------------------------------- */
if(isset($_POST['buy_now'])) 
{ 
    $id_sanpham = intval($_POST['idsp']);
    $soluong_mua = intval($_POST['so_luong']); 

    if($soluong_mua <= 0){
        $soluong_mua = 1;
    }

    // Truy vấn chính xác từ bảng `sanpham`
    $sql_sp = mysqli_query($conn, "
        SELECT *
        FROM sanpham
        WHERE id = '$id_sanpham'
    ");

    if(mysqli_num_rows($sql_sp) == 0){
        echo "<script>
                alert('Sản phẩm không tồn tại trên hệ thống!');
                history.back();
              </script>";
        exit();
    }

    $sp = mysqli_fetch_assoc($sql_sp);

    // Kiểm tra số lượng tồn kho (cột `soluong`)
    if($soluong_mua > $sp['soluong']){
        echo "<script>
                alert('Sản phẩm không đủ số lượng tồn kho!');
                history.back();
              </script>";
        exit();
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

    // Truy vấn kết hợp bảng `giohang` và bảng `sanpham` chuẩn CSDL của bạn
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

    if(!$result_cart || mysqli_num_rows($result_cart) == 0){
        echo "<script>
                alert('Dữ liệu giỏ hàng không hợp lệ!');
                window.location.href='index.php?page=cart';
              </script>";
        exit();
    }

    while($item = mysqli_fetch_assoc($result_cart))
    {
        if($item['soluong_mua'] > $item['soluong_kho'])
        {
            echo "<script>
                    alert('Sản phẩm ".$item['ten']." không đủ hàng trong kho (Hiện còn: ".$item['soluong_kho']."). Vui lòng kiểm tra lại!');
                    window.location.href='index.php?page=cart';
                  </script>";
            exit();
        }

        $tongtien += $item['gia'] * $item['soluong_mua'];
        $valid_items[] = $item;
    }
}

/* -------------------------------------------------------------------------- */
/* TIẾN HÀNH TẠO ĐƠN HÀNG VÀ LƯU CHI TIẾT ĐƠN HÀNG                            */
/* -------------------------------------------------------------------------- */

// 5. Lấy thông tin mặc định từ bảng `taikhoan` của người mua
$sql_user = mysqli_query($conn, "SELECT * FROM taikhoan WHERE id = '$id_taikhoan'");
$user_info = mysqli_fetch_assoc($sql_user);

$tennguoinhan   = mysqli_real_escape_string($conn, $user_info['ten']);
$sdtnhan        = mysqli_real_escape_string($conn, $user_info['sdt']);
$diachigiaohang = mysqli_real_escape_string($conn, !empty($user_info['diachi']) ? $user_info['diachi'] : 'Chưa cập nhật địa chỉ');
$thanhtoan      = "COD (Thanh toán khi nhận hàng)"; // Giá trị đồng bộ theo dữ liệu mẫu của bạn
$trangthai      = "Chờ xác nhận"; // Đồng bộ dạng chuỗi chữ khớp với trang Admin

// 6. Thêm thông tin vào bảng `donhang` (Khớp 100% cột CSDL thực tế)
$sql_order = "INSERT INTO donhang (id_taikhoan, ngaymua, tongtien, trangthai, diachigiaohang, tennguoinhan, sdtnhan, thanhtoan) 
              VALUES ('$id_taikhoan', NOW(), '$tongtien', '$trangthai', '$diachigiaohang', '$tennguoinhan', '$sdtnhan', '$thanhtoan')";

if (mysqli_query($conn, $sql_order)) {
    // Lấy ID tự tăng vừa tạo của đơn hàng này
    $id_donhang_moi = mysqli_insert_id($conn);
    
    // 7. Vòng lặp lưu thông tin từng sản phẩm vào bảng `chitietdonhang` và trừ kho
    foreach ($valid_items as $product) {
        $id_sanpham  = $product['id_sanpham'];
        $soluong_mua = $product['soluong_mua'];
        $dongia      = $product['gia'];
        
        // Lưu vào bảng chi tiết đơn hàng
        $sql_detail = "INSERT INTO chitietdonhang (id_donhang, id_sanpham, soluong, dongia) 
                       VALUES ('$id_donhang_moi', '$id_sanpham', '$soluong_mua', '$dongia')";
        mysqli_query($conn, $sql_detail);
                             
        // Thực hiện trừ đi số lượng tồn kho của sản phẩm trong bảng `sanpham`
        mysqli_query($conn, "UPDATE sanpham SET soluong = soluong - $soluong_mua WHERE id = '$id_sanpham'");
    }
    
    // 8. XÓA GIỎ HÀNG: Chỉ xóa những sản phẩm vừa được mua thành công ở Luồng 2
    if(!isset($_POST['buy_now']) && !empty($id_list_string)) {
        mysqli_query($conn, "DELETE FROM giohang WHERE id_taikhoan = '$id_taikhoan' AND id IN ($id_list_string)");
    }

    echo "<script>alert('Đơn hàng của bạn đã được tạo thành công!'); window.location.href='index.php?page=home';</script>";
} else {
    echo "<script>alert('Có lỗi hệ thống phát sinh khi tạo đơn hàng: " . mysqli_error($conn) . "'); window.location.href='index.php?page=cart';</script>";
}

mysqli_close($conn);
?>