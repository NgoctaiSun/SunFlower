<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kết nối trực tiếp để tránh lỗi include sai đường dẫn giữa các cấp thư mục
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
if (!$conn) {
    die("Kết nối database thất bại tại giỏ hàng: " . mysqli_connect_error());
}

// Bắt buộc phải đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để thực hiện chức năng này!'); window.location.href='../index.php?page=login';</script>";
    exit();
}

$uid = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
if(isset($_POST['add_to_cart']) || isset($_POST['buy_now'])){
$action = 'add';


$id_sanpham = isset($_POST['idsp']) ? intval($_POST['idsp']) : 0;

$_GET['id'] = $id_sanpham;


}
else{
$action = isset($_GET['action']) ? $_GET['action'] : '';
}


switch ($action) {
    // 1. CHỨC NĂNG: THÊM SẢN PHẨM VÀO GIỎ HÀNG
    case 'add':


$id_sanpham = isset($_GET['id']) ? intval($_GET['id']) : 0;
$soluong = isset($_POST['soluong']) ? intval($_POST['soluong']) : 1;

if($soluong <= 0){
    $soluong = 1;
}

if($id_sanpham > 0){

    $check_q = mysqli_query(
        $conn,
        "SELECT id, soluong
         FROM giohang
         WHERE id_taikhoan='$uid'
         AND id_sanpham='$id_sanpham'"
    );

    if(mysqli_num_rows($check_q) > 0){

        $row = mysqli_fetch_assoc($check_q);

        $new_qty = $row['soluong'] + $soluong;

        mysqli_query(
            $conn,
            "UPDATE giohang
             SET soluong='$new_qty'
             WHERE id='{$row['id']}'"
        );

        $id_giohang = $row['id'];

    }else{

        mysqli_query(
            $conn,
            "INSERT INTO giohang(id_taikhoan,id_sanpham,soluong)
             VALUES('$uid','$id_sanpham','$soluong')"
        );

        $id_giohang = mysqli_insert_id($conn);
    }

    if(isset($_POST['buy_now'])){

        header("Location: ../index.php?page=thanhtoan&id_giohang=".$id_giohang);
        exit();

    }else{

        echo "
        <script>
            alert('Đã thêm sản phẩm vào giỏ hàng!');
            window.location.href='../index.php?page=giohang';
        </script>";
        exit();
    }
}

header("Location: ../index.php");
exit();
break; 



    // 2. CHỨC NĂNG: CẬP NHẬT SỐ LƯỢNG QUA Ô INPUT (AJAX HOẶC TRỰC TIẾP)
    case 'update_qty_direct':
        $id_giohang = isset($_GET['id_giohang']) ? intval($_GET['id_giohang']) : 0;
        $soluong = isset($_GET['soluong']) ? intval($_GET['soluong']) : 1;
        if($soluong <= 0) $soluong = 1;
        
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