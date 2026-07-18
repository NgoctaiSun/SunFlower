<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

// Kiểm tra kết nối phòng hờ nếu file index chưa nhúng
if (!isset($conn)) {
    if (file_exists('pages/connect.php')) {
        include_once 'pages/connect.php';
    } elseif (file_exists('connect.php')) {
        include_once 'connect.php';
    }
    if (!isset($conn)) {
        $conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
    }
}
?>
<header class="bg-success shadow">
    <style>
        .custom-nav .nav-link:not(.dropdown-toggle) {
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        .custom-nav .nav-link:not(.dropdown-toggle)::after {
            content: '';
            position: absolute;
            width: 0%;
            height: 2px;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            transition: all 0.3s ease;
        }
        .custom-nav .nav-link:not(.dropdown-toggle):hover::after {
            width: 80%;
        }
        .search-container input:focus {
            box-shadow: 0 0 10px rgba(255,255,255,0.5) !important;
            border-color: #fff !important;
        }
    </style>

    <nav class="navbar navbar-expand-lg navbar-dark custom-nav py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3 text-white" href="index.php?page=home">
                <i class="fa-solid fa-sun text-warning me-2"></i>Sunflower Phone
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item px-2">
                        <a class="nav-link text-white" href="index.php?page=home">Trang chủ</a>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link text-white" href="index.php?page=sanpham">Sản phẩm</a>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link text-white" href="index.php?page=lienhe">Liên hệ</a>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link text-white position-relative" href="index.php?page=giohang">
                            <i class="fa-solid fa-basket-shopping fs-5"></i> Giỏ hàng
                        </a>
                    </li>
                    <li class="nav-item">
    <a class="nav-link" href="index.php?page=bang_gia_xml">Bảng giá XML</a>
</li>
                    <?php if(isset($_SESSION['user'])): ?>
                        <li class="nav-item dropdown px-2">
                            <a class="nav-link dropdown-toggle text-white fw-bold d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-user fs-5 me-2 text-warning"></i><?= htmlspecialchars($_SESSION['user']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="profileDropdown">
                                <li>
                                    <a class="dropdown-item py-2" href="index.php?page=profile">
                                        <i class="fa-solid fa-id-card me-2 text-success"></i>Hồ sơ cá nhân
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="index.php?page=lichsumuahang">
                                        <i class="fa-solid fa-clock-history me-2 text-primary"></i>Lịch sử mua hàng
                                    </a>
                                </li>
                                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <li>
                                        <a class="dropdown-item py-2 fw-bold text-warning" href="pages/admin.php">
                                            <i class="fa-solid fa-user-gear me-2"></i>Trang quản trị
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2 text-danger" href="pages/dangxuat.php" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?')">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item px-2">
                            <a href="index.php?page=login" class="nav-link text-white fw-semibold">Đăng nhập</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container pb-4 pt-2 d-flex justify-content-center">
        <form class="d-flex search-container" style="max-width: 500px; width: 100%;" onsubmit="event.preventDefault(); timKiem();">
            <input class="form-control me-2 rounded-pill px-4 shadow-sm" type="search" id="search" placeholder="Tìm điện thoại..." name="keyword">
            <button class="btn btn-light rounded-pill px-4 fw-bold text-success shadow-sm" type="button" onclick="timKiem()">Tìm</button>
        </form>
    </div>

    <script>
    function timKiem(){
        let key = document.getElementById("search").value;
        if(key.trim() === "") {
            alert("Vui lòng nhập từ khóa tìm kiếm!");
            return;
        }
        window.location.href = "pages/xuly_search.php?keyword=" + encodeURIComponent(key);
    }
    </script>
</header>