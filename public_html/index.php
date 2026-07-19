<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include 'connect.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoa Hướng Dương Phone - Cửa hàng điện thoại uy tín</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    /* Hiệu ứng mượt mà khi di chuột qua sản phẩm */
    .product-card-hover {
        transition: all 0.3s ease-in-out;
    }
    
    .product-card-hover:hover {
        transform: translateY(-8px) scale(1.02); /* Nhấc sản phẩm lên và phóng to nhẹ */
        box-shadow: 0 10px 20px rgba(40, 167, 69, 0.2) !important; /* Tạo vệt sáng xanh dịu bao quanh */
    }
</style>
</head>
<body>

    <?php include 'pages/nav.php'; ?>

    <main style="min-height: 500px;" class="py-3">
    <?php 
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    
    switch ($page) {
        case 'home':
            include 'pages/home.php';
            break;
        case 'sanpham':
            include 'pages/sanpham.php';
            break;
        case 'lienhe':
            include 'pages/lienhe.php';
            break;
        case 'login':
            include 'pages/login.php';
            break;
        case 'dangky':
            include 'pages/dangky.php';
            break;
        case 'profile':
            include 'pages/profile.php';
            break;
        case 'lichsumuahang':
            include 'pages/lichsumuahang.php';
            break;
        case 'giohang':
            include 'pages/giohang.php'; 
            break;
        case 'chitetsanpham':
            include 'pages/chitetsanpham.php';
            break;
        case 'bang_gia_xml':
            include 'pages/bang_gia_xml.php';
            break;
        default:
            include 'pages/home.php';
            break;
    }
    ?>
    </main>

    <?php include 'pages/footer.php'; ?>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
