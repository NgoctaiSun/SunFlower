<?php
if (!isset($conn)) {
    $conn = mysqli_connect("localhost","root","","hoahuongduongphone"); 
}

if (!isset($_GET['id'])) {
    echo "<div class='container my-5 alert alert-danger'>Không tìm thấy sản phẩm!</div>";
    return;
}

$idsp = (int)$_GET['id'];
$sql = "SELECT * FROM sanpham WHERE id = $idsp";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0){
    echo "<div class='container my-5 alert alert-danger'>Sản phẩm không tồn tại!</div>";
    return;
}

$sp = mysqli_fetch_assoc($result);

// Lấy danh sách bình luận
$sql_dg = "SELECT bl.*, tk.ten FROM binhluan bl 
           INNER JOIN taikhoan tk ON bl.id_taikhoan = tk.id \r\n" .
          "WHERE bl.id_sanpham = $idsp ORDER BY bl.ngaydang DESC";
$result_dg = mysqli_query($conn, $sql_dg);
?>

<style>
    .product-img-container {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.02);
    }
    .specs-table th {
        width: 35%;
        background-color: #f8f9fa;
        color: #495057;
    }
    .review-box { 
        background: #fff; 
        border-radius: 15px; 
        padding: 25px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
        margin-top: 30px; 
    }
    .review-title { font-size: 20px; font-weight: 700; color: #198754; margin-bottom: 20px; border-bottom: 2px solid #20c997; padding-bottom: 8px; }
    .review-item { padding: 15px 0; border-bottom: 1px dashed #dee2e6; }
    .review-item:last-child { border-bottom: none; }
    .review-user { font-weight: 600; color: #212529; }
    .review-date { font-size: 12px; color: #6c757d; }
    .review-star { color: #ffc107; font-size: 14px; margin: 3px 0; }
    .review-content { color: #495057; font-size: 14px; }
    
    /* Giao diện chọn số sao chuyên nghiệp */
    .rating-wrapper {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    .rating-wrapper input { display: none; }
    .rating-wrapper label {
        font-size: 30px;
        color: #dee2e6;
        cursor: pointer;
        transition: color 0.2s;
        padding: 0 2px;
    }
    .rating-wrapper label:hover,
    .rating-wrapper label:hover ~ label,
    .rating-wrapper input:checked ~ label {
        color: #ffc107;
    }
</style>

<div class="container my-5">
    <div class="card shadow-sm border-0 rounded-3 bg-white p-4 p-md-5">
        <div class="row g-5">
            <div class="col-md-6">
                <div class="product-img-container">
                    <img src="images/<?= htmlspecialchars($sp['hinhanh']) ?>" class="img-fluid rounded" style="max-height: 400px; object-fit: contain;" alt="<?= htmlspecialchars($sp['ten']) ?>">
                </div>
            </div>

            <div class="col-md-6">
                <span class="badge bg-success mb-2 px-3 py-2 rounded-pill fs-7"><?= htmlspecialchars($sp['hang']) ?></span>
                <h1 class="fw-bold text-dark mb-2"><?= htmlspecialchars($sp['ten']) ?></h1>
                <h2 class="text-danger fw-bold mb-4"><?= number_format($sp['gia'], 0, ',', '.') ?> VNĐ</h2>

                <h5 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-gear me-2"></i>Thông số kỹ thuật</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered specs-table align-middle m-0">
                        <tbody>
                            <tr>
                                <th>Thương hiệu</th>
                                <td><?= htmlspecialchars($sp['hang']) ?></td>
                            </tr>
                            <tr>
                                <th>Bộ nhớ RAM</th>
                                <td><?= htmlspecialchars($sp['ram']) ?></td>
                            </tr>
                            <tr>
                                <th>Bộ nhớ trong</th>
                                <td><?= htmlspecialchars($sp['bonho']) ?></td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td><span class="text-success fw-semibold"><i class="fa-solid fa-check-circle me-1"></i>Còn hàng sẵn tại shop</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row g-3">

    <!-- THÊM VÀO GIỎ -->
    <div class="col-sm-6">
        <form action="pages/xuly_giohang.php?action=add&id=<?= $sp['id'] ?>" method="POST">
            <input type="hidden" name="soluong" id="qty_cart" value="1">

            <button type="submit"
                    class="btn btn-outline-success btn-lg w-100 py-3 fw-bold rounded-pill shadow-sm">
                <i class="fa-solid fa-cart-plus me-2"></i>Thêm vào giỏ
            </button>
        </form>
    </div>

    <!-- MUA NGAY -->
    <div class="col-sm-6">
        <form action="pages/xuly_muahang.php" method="POST">
            <input type="hidden" name="idsp" value="<?= $sp['id'] ?>">
            <input type="hidden" name="soluong" id="qty_buy" value="1">

            <button type="submit"
                    name="buy_now"
                    class="btn btn-success btn-lg w-100 py-3 fw-bold rounded-pill shadow-sm">
                <i class="fa-solid fa-bolt me-2"></i>Mua ngay
            </button>
        </form>
    </div>

</div>
            </div>
        </div>

        <div class="row mt-5 pt-4 border-top">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <div class="p-4 bg-light rounded-3 shadow-sm">
                    <h4 class="fw-bold text-success mb-3"><i class="fa-solid fa-pen-to-square me-2"></i>Viết đánh giá của bạn</h4>
                    
                    <?php if(isset($_SESSION['user'])): ?>
                    <form action="pages/xuly_binhluan.php" method="POST">
                        <input type="hidden" name="idsp" value="<?= $sp['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary d-block">Chọn số sao:</label>
                            <div class="rating-wrapper">
                                <input type="radio" id="star5" name="sosao" value="5" required><label for="star5" title="5 sao">★</label>
                                <input type="radio" id="star4" name="sosao" value="4"><label for="star4" title="4 sao">★</label>
                                <input type="radio" id="star3" name="sosao" value="3"><label for="star3" title="3 sao">★</label>
                                <input type="radio" id="star2" name="sosao" value="2"><label for="star2" title="2 sao">★</label>
                                <input type="radio" id="star1" name="sosao" value="1"><label for="star1" title="1 sao">★</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="noidung" class="form-label fw-semibold text-secondary">Nội dung bình luận:</label>
                            <textarea id="noidung" name="noidung" class="form-control" rows="4" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm này..." required style="border-radius: 10px;"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success fw-bold px-4 rounded-pill shadow-sm">
                            Gửi đánh giá ngay
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-warning mb-0 rounded-3">
                        <i class="fa-solid fa-circle-info me-2"></i>Bạn cần <a href="index.php?page=login" class="fw-bold text-decoration-none">Đăng nhập</a> để viết đánh giá cho sản phẩm này.
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="review-box mt-0 bg-transparent shadow-none p-0">
                    <h3 class="review-title"><i class="fa-solid fa-comments me-2"></i>Khách hàng nhận xét (<?= mysqli_num_rows($result_dg) ?>)</h3>
                    
                    <?php if(mysqli_num_rows($result_dg) > 0): ?>
                        <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                            <?php while($dg = mysqli_fetch_assoc($result_dg)): ?>
                            <div class="review-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="review-user"><i class="fa-solid fa-user-circle me-1 text-secondary"></i><?= htmlspecialchars($dg['ten']) ?></span>
                                    <span class="review-date"><i class="fa-regular fa-clock me-1"></i><?= date("d/m/Y H:i", strtotime($dg['ngaydang'])) ?></span>
                                </div>
                                <div class="review-star"><?= str_repeat("★", $dg['sosao']) ?></div>
                                <div class="review-content mt-1"><?= nl2br(htmlspecialchars($dg['noidung'])) ?></div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-muted py-4"><i class="fa-solid fa-comment-slash me-2"></i>Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên nhận xét!</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>