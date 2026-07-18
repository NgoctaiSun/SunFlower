<?php
if (!isset($conn)) {
    $conn = mysqli_connect("localhost","root","","hoahuongduongphone"); 
}
$sql = "SELECT * FROM sanpham ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>
<div id="bannerHuongDuong" class="carousel slide mb-5" data-bs-ride="carousel" data-bs-interval="3000">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="images/iphone18.jpg" class="d-block w-100" style="height: auto; max-height: 500px; object-fit: contain; background-color: #f8f9fa;" alt="iPhone 18">
        </div>
        <div class="carousel-item">
            <img src="images/oppo.jpg" class="d-block w-100" style="height: auto; max-height: 500px; object-fit: contain; background-color: #f8f9fa;" alt="Oppo">
        </div>
        <div class="carousel-item">
            <img src="images/samsunga57.webp" class="d-block w-100" style="height: auto; max-height: 500px; object-fit: contain; background-color: #f8f9fa;" alt="Samsung">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#bannerHuongDuong" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#bannerHuongDuong" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<div class="container">
    <h2 class="text-center text-success fw-bold mb-4">SẢN PHẨM MỚI NHẤT</h2>
    <div class="row g-4">
        <?php while($sp = mysqli_fetch_assoc($result)): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden bg-white product-card-hover">
                <div class="p-3 text-center bg-light">
                    <img src="images/<?php echo $sp['hinhanh']; ?>" class="img-fluid" style="height:200px; object-fit:contain;">
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark text-truncate"><?php echo $sp['ten']; ?></h5>
                    <p class="text-danger fw-bold"><?php echo number_format($sp['gia'], 0, ',', '.'); ?> VNĐ</p>
                    <p class="text-muted small mb-0">
                        RAM: <?php echo $sp['ram']; ?> | Bộ nhớ: <?php echo $sp['bonho']; ?>
                    </p>
                </div>
                <div class="card-footer bg-white border-0 pt-0">
                    <a href="index.php?page=chitetsanpham&id=<?php echo $sp['id']; ?>" class="btn btn-success w-100 fw-bold rounded-pill">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>