<?php
if (!isset($conn)) {
    $conn = mysqli_connect("localhost","root","","hoahuongduongphone"); 
    mysqli_set_charset($conn, "utf8mb4");
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

// Lấy danh sách bình luận (chỉ lấy bình luận có trangthai = '1' hoặc 'Hiển thị')
$sql_dg = "SELECT bl.*, tk.ten FROM binhluan bl 
           INNER JOIN taikhoan tk ON bl.id_taikhoan = tk.id 
           WHERE bl.id_sanpham = $idsp AND (bl.trangthai = '1' OR bl.trangthai = 'Hiển thị') 
           ORDER BY bl.ngaydang DESC";
$result_dg = mysqli_query($conn, $sql_dg);

// Kiểm tra thông tin người dùng đăng nhập & lấy thông tin chi tiết để điền vào Modal
$iduser = 0;
$user_info = [];
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $iduser = intval($_SESSION['user_id']);
} elseif (isset($_SESSION['user']['id'])) {
    $iduser = intval($_SESSION['user']['id']);
}

if ($iduser > 0) {
    $user_query = mysqli_query($conn, "SELECT * FROM taikhoan WHERE id = '{$iduser}'");
    if ($user_query && mysqli_num_rows($user_query) > 0) {
        $user_info = mysqli_fetch_assoc($user_query);
    }
}

$da_mua_hang = false;
$da_binh_luan = false;

if ($iduser > 0) {
    // 1. Kiểm tra điều kiện đã mua và hoàn thành đơn hàng chưa
    $sql_check_buy = "SELECT d.id 
                      FROM donhang d 
                      JOIN chitietdonhang c ON d.id = c.id_donhang 
                      WHERE d.id_taikhoan = '$iduser' 
                        AND c.id_sanpham = '$idsp'
                        AND (d.trangthai = 'Hoàn thành' OR d.trangthai = 'Đã giao' OR d.trangthai = '1')";
    $query_check_buy = mysqli_query($conn, $sql_check_buy);
    if ($query_check_buy && mysqli_num_rows($query_check_buy) > 0) {
        $da_mua_hang = true;
    }

    // 2. Kiểm tra xem người dùng này đã từng bình luận sản phẩm này chưa
    $sql_check_comment = "SELECT id FROM binhluan WHERE id_taikhoan = '$iduser' AND id_sanpham = '$idsp'";
    $query_check_comment = mysqli_query($conn, $sql_check_comment);
    if ($query_check_comment && mysqli_num_rows($query_check_comment) > 0) {
        $da_binh_luan = true;
    }
}
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
    
    /* Giao diện chọn số sao */
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

                <!-- KHU VỰC NÚT MUA HÀNG -->
                <div class="row g-3" id="buy-section">
                    <div class="col-sm-6">
                        <form action="pages/xuly_giohang.php?action=add&id=<?= $sp['id'] ?>" method="POST">
                            <input type="hidden" name="soluong" id="qty_cart" value="1">
                            <button type="submit" class="btn btn-outline-success btn-lg w-100 py-3 fw-bold rounded-pill shadow-sm">
                                <i class="fa-solid fa-cart-plus me-2"></i>Thêm vào giỏ
                            </button>
                        </form>
                    </div>

                    <div class="col-sm-6">
                        <?php if ($iduser > 0): ?>
                            <!-- Nếu đã đăng nhập: Bấm nút sẽ bật Modal Popup xác nhận -->
                            <button type="button" id="btnOpenBuyNowModal" class="btn btn-success btn-lg w-100 py-3 fw-bold rounded-pill shadow-sm">
                                <i class="fa-solid fa-bolt me-2"></i>Mua ngay
                            </button>
                        <?php else: ?>
                            <!-- Nếu chưa đăng nhập: Chuyển sang trang Login -->
                            <a href="index.php?page=login" class="btn btn-success btn-lg w-100 py-3 fw-bold rounded-pill shadow-sm text-center" onclick="return confirm('Vui lòng đăng nhập để mua hàng!')">
                                <i class="fa-solid fa-bolt me-2"></i>Mua ngay
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5 pt-4 border-top">
            <!-- KHU VỰC VIẾT ĐÁNH GIÁ -->
            <div class="col-lg-5 mb-5 mb-lg-0">
                <div class="p-4 bg-light rounded-3 shadow-sm">
                    <h4 class="fw-bold text-success mb-3"><i class="fa-solid fa-pen-to-square me-2"></i>Viết đánh giá của bạn</h4>
                    
                    <?php if ($da_mua_hang && !$da_binh_luan): ?>
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

                    <?php elseif ($da_binh_luan): ?>
                        <div class="alert alert-info mb-0 rounded-3 border-0 shadow-sm p-3">
                            <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-circle-check me-2 text-info"></i>Bạn đã bình luận sản phẩm này rồi!</h6>
                            <p class="small text-muted mb-0">Mỗi tài khoản chỉ được phép gửi bình luận 1 lần cho mỗi sản phẩm.</p>
                        </div>

                    <?php else: ?>
                        <div class="alert alert-warning mb-0 rounded-3 border-0 shadow-sm p-3">
                            <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>Bạn chưa thể đánh giá sản phẩm này!</h6>
                            <p class="small text-muted mb-3">Chỉ những khách hàng đã mua sản phẩm này và nhận hàng thành công mới có thể viết đánh giá.</p>
                            
                            <a href="#buy-section" class="btn btn-sm btn-success fw-bold rounded-pill px-3 py-2 w-100 text-center">
                                <i class="fa-solid fa-cart-shopping me-1"></i> Mua ngay sản phẩm này
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- HIỂN THỊ DANH SÁCH BÌNH LUẬN -->
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
                                <div class="review-content mt-1 d-flex justify-content-between align-items-center">
                                    <span><?= nl2br(htmlspecialchars($dg['noidung'])) ?></span>
                                    
                                    <?php if ($iduser > 0 && $iduser == $dg['id_taikhoan']): ?>
                                        <?php if (isset($dg['solansua']) && $dg['solansua'] < 1): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary ms-2 rounded-pill px-3" 
                                                    onclick="openEditModal(<?= $dg['id'] ?>, '<?= htmlspecialchars(addslashes($dg['noidung'])) ?>')">
                                                <i class="fa-solid fa-pen"></i> Sửa
                                            </button>
                                        <?php else: ?>
                                            <small class="text-muted ms-2 fs-7"><i>(Đã sửa 1 lần)</i></small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- ĐÃ BỔ SUNG: Hiển thị phản hồi từ Quản trị viên (Admin) -->
                                <?php if (!empty($dg['traloi_admin'])): ?>
                                    <div class="mt-2 p-3 bg-light border-start border-4 border-success rounded-3 shadow-sm ms-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-success small">
                                                <i class="fa-solid fa-user-shield me-1"></i>Phản hồi từ Quản trị viên
                                            </span>
                                            <?php if (!empty($dg['ngay_traloi'])): ?>
                                                <span class="text-muted" style="font-size: 11px;">
                                                    <i class="fa-regular fa-clock me-1"></i><?= date("d/m/Y H:i", strtotime($dg['ngay_traloi'])) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="mb-0 text-dark small" style="line-height: 1.5;"><?= nl2br(htmlspecialchars($dg['traloi_admin'])) ?></p>
                                    </div>
                                <?php endif; ?>
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

<!-- MODAL POPUP XÁC NHẬN VÀ SỬA THÔNG TIN MUA NGAY -->
<div class="modal fade" id="confirmBuyNowModal" tabindex="-1" aria-labelledby="buyNowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="buyNowModalLabel">
                    <i class="fa-solid fa-pen-to-square me-2"></i>Xác Nhận & Sửa Thông Tin Giao Hàng
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="pages/xuly_muahangngay.php" method="POST">
                <input type="hidden" name="idsp" value="<?= $sp['id'] ?>">
                <input type="hidden" name="soluong" value="1">
                <input type="hidden" name="buy_now" value="1">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- CỘT TRÁI: NHẬP VÀ SỬA THÔNG TIN -->
                        <div class="col-md-6 border-end pe-md-4">
                            <h6 class="fw-bold text-success border-bottom pb-2 mb-3">
                                <i class="fa-solid fa-user-pen me-1"></i>Thông tin người nhận (Có thể chỉnh sửa):
                            </h6>
                            
                            <div class="mb-2">
                                <label class="form-label small fw-semibold mb-1">Họ và tên người nhận:</label>
                                <input type="text" name="hoten_order" class="form-control form-control-sm" 
                                       value="<?= htmlspecialchars($user_info['hoten'] ?? $user_info['ten'] ?? $_SESSION['user'] ?? '') ?>" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold mb-1">Số điện thoại liên hệ:</label>
                                <input type="tel" name="sdt_order" class="form-control form-control-sm" 
                                       value="<?= htmlspecialchars($user_info['sdt'] ?? '') ?>" 
                                       placeholder="Nhập số điện thoại nhận hàng" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold mb-1">Email nhận thông báo / hóa đơn:</label>
                                <input type="email" name="email_order" class="form-control form-control-sm" 
                                       value="<?= htmlspecialchars($user_info['email'] ?? '') ?>" 
                                       placeholder="example@gmail.com" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold mb-1">Địa chỉ giao hàng chi tiết:</label>
                                <textarea name="diachi_order" class="form-control form-control-sm" rows="3" 
                                          placeholder="Số nhà, đường, phường/xã, quận/huyện..." required><?= htmlspecialchars($user_info['diachi'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- CỘT PHẢI: XEM LẠI SẢN PHẨM & TỔNG TIỀN -->
                        <div class="col-md-6 ps-md-4">
                            <h6 class="fw-bold text-success border-bottom pb-2 mb-3">
                                <i class="fa-solid fa-bag-shopping me-1"></i>Sản phẩm thanh toán ngay:
                            </h6>
                            
                            <div class="card p-3 border mb-3 bg-light rounded-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="images/<?= htmlspecialchars($sp['hinhanh']) ?>" 
                                         alt="<?= htmlspecialchars($sp['ten']) ?>" 
                                         style="width: 70px; height: 70px; object-fit: contain;">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($sp['ten']) ?></h6>
                                        <div class="text-secondary small">
                                            Đơn giá: <span class="text-danger fw-bold"><?= number_format($sp['gia'], 0, ',', '.') ?> VNĐ</span>
                                        </div>
                                        <div class="text-secondary small">
                                            Số lượng: <strong>1</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-warning bg-opacity-10 p-3 rounded-3 border border-warning text-center">
                                <span class="fw-semibold text-dark d-block mb-1">Tổng tiền thanh toán:</span>
                                <span class="fw-bold text-danger fs-4"><?= number_format($sp['gia'], 0, ',', '.') ?> VNĐ</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill fw-semibold" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success rounded-pill fw-bold px-4">
                        <i class="fa-solid fa-check me-1"></i>Xác Nhận Đặt Hàng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL POPUP CHỈNH SỬA BÌNH LUẬN -->
<div class="modal fade" id="modalSuaBinhLuan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-success"><i class="fa-solid fa-pen-to-square me-2"></i>Chỉnh sửa bình luận</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="pages/xuly_suabinhluan.php" method="POST">
          <div class="modal-body">
            <input type="hidden" name="id_binhluan" id="edit_id_binhluan">
            <div class="mb-3">
                <label class="form-label font-semibold">Nội dung đánh giá mới:</label>
                <textarea class="form-control" name="noidung_moi" id="edit_noidung" rows="4" required></textarea>
                <small class="text-danger mt-1 d-block">* Lưu ý: Bạn chỉ được chỉnh sửa tối đa 1 lần.</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-success rounded-pill px-4">Lưu thay đổi</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const btnOpenBuyNowModal = document.getElementById("btnOpenBuyNowModal");
    if (btnOpenBuyNowModal) {
        const confirmBuyNowModal = new bootstrap.Modal(document.getElementById('confirmBuyNowModal'));
        btnOpenBuyNowModal.addEventListener("click", function () {
            confirmBuyNowModal.show();
        });
    }
});

// Hàm bật Modal Popup Chỉnh sửa bình luận
function openEditModal(id, noidung) {
    document.getElementById('edit_id_binhluan').value = id;
    document.getElementById('edit_noidung').value = noidung;
    var myModal = new bootstrap.Modal(document.getElementById('modalSuaBinhLuan'));
    myModal.show();
}
</script>