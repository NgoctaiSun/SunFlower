<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kết nối database an toàn
if (file_exists('connect.php')) {
    include_once 'connect.php';
} else {
    include_once '../pages/connect.php';
}

if (!isset($conn)) {
    $conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
}
mysqli_set_charset($conn, "utf8");

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tìm kiếm - Sunflower Phone</title>

    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .product-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        .product-img {
            height: 230px;
            object-fit: cover;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
    </style>
</head>
<body>

    <?php include 'nav.php'; ?>

    <div class="container my-5" style="min-height: 60px;">
        <h3 class="mb-4 fw-bold text-dark">
            <i class="fa-solid fa-magnifying-glass me-2 text-success"></i>Kết quả tìm kiếm cho: 
            <span class="text-success">"<?= htmlspecialchars($keyword) ?>"</span>
        </h3>

        <div class="row g-4">
            <?php
            if ($keyword != "") {
                // Xử lý tìm kiếm an toàn chống SQL Injection
                $keyword_clean = mysqli_real_escape_string($conn, $keyword);
                
                // Câu lệnh tìm kiếm theo tên, hãng sản xuất hoặc mô tả sản phẩm
                $sql = "SELECT * FROM sanpham WHERE ten LIKE '%$keyword_clean%' OR mota LIKE '%$keyword_clean%' ORDER BY id DESC";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($sp = mysqli_fetch_assoc($result)) {
            ?>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card product-card shadow-sm h-100">
                                <img src="../images/<?= htmlspecialchars($sp['hinhanh']) ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($sp['ten']) ?>">
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold text-dark text-truncate mb-2" title="<?= htmlspecialchars($sp['ten']) ?>">
                                        <?= htmlspecialchars($sp['ten']) ?>
                                    </h5>
                                    
                                    <p class="text-danger fw-bold fs-5 mb-3">
                                        <?= number_format($sp['gia'], 0, ',', '.') ?> đ
                                    </p>
                                    
                                    <div class="text-secondary small mb-3 mt-auto">
                                        <p class="mb-1"><i class="fa-solid fa-microchip me-1"></i> RAM: <?= htmlspecialchars($sp['ram']) ?></p>
                                        <p class="mb-0"><i class="fa-solid fa-database me-1"></i> Bộ nhớ: <?= htmlspecialchars($sp['bonho']) ?></p>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-white border-0 pt-0 pb-3">
                                    <a href="../index.php?page=chitetsanpham&id=<?= $sp['id'] ?>" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm">
                                        <i class="fa-solid fa-eye me-1"></i> Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
            <?php
                    }
                } else {
                    // Không tìm thấy sản phẩm trùng khớp
                    echo '
                    <div class="col-12 text-center py-5">
                        <div class="text-muted mb-3"><i class="fa-solid fa-box-open display-1"></i></div>
                        <h4 class="fw-bold text-secondary">Rất tiếc, không tìm thấy sản phẩm nào!</h4>
                        <p class="text-muted">Hãy thử lại với từ khóa khác (ví dụ: iPhone, Samsung, Oppo...)</p>
                        <a href="../index.php?page=sanpham" class="btn btn-outline-success rounded-pill fw-bold px-4 mt-2">Quay lại cửa hàng</a>
                    </div>';
                }
            } else {
                echo '
                <div class="col-12 text-center py-5">
                    <h4 class="text-danger fw-bold">Vui lòng nhập từ khóa để tìm kiếm sản phẩm!</h4>
                    <a href="../index.php?page=home" class="btn btn-success rounded-pill fw-bold px-4 mt-3">Về trang chủ</a>
                </div>';
            }
            ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>