<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Bắt buộc đăng nhập mới xem được giỏ hàng cá nhân
if (!isset($_SESSION['user_id'])) {
    echo "
    <div class='container my-5'>
        <div class='alert alert-warning text-center shadow-sm'>
            <i class='fa-solid fa-circle-exclamation me-2 fs-5'></i>
            Vui lòng <a href='index.php?page=login' class='fw-bold text-decoration-none'>Đăng nhập</a> để xem và quản lý giỏ hàng của bạn!
        </div>
    </div>";
    return;
}


if (!isset($conn)) {
    $conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
}

$uid = $_SESSION['user_id'];

// LẤY GIỎ HÀNG: Sắp xếp theo ID mới nhất lên đầu
$sql = "SELECT g.*, s.ten, s.gia, s.hinhanh 
        FROM giohang g 
        JOIN sanpham s ON g.id_sanpham = s.id 
        WHERE g.id_taikhoan = '$uid'
        ORDER BY g.id DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container my-5">
    <h2 class="text-success fw-bold mb-4">
        <i class="fa-solid fa-cart-shopping me-2"></i>Giỏ Hàng Của Bạn
    </h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <form id="formCheckout" action="pages/xuly_muahang.php" method="POST">
            <div class="table-responsive shadow-sm rounded-3">
                <table class="table table-hover align-middle mb-0 bg-white">
                    <thead class="table-success text-dark fw-bold">
                        <tr>
                            <th width="5%" class="text-center">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th width="15%">Hình ảnh</th>
                            <th width="30%">Tên sản phẩm</th>
                            <th width="15%">Đơn giá</th>
                            <th width="15%">Số lượng</th>
                            <th width="15%">Thành tiền</th>
                            <th width="5%" class="text-center">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        while ($row = mysqli_fetch_assoc($result)): 
                            $thanhtien = $row['gia'] * $row['soluong'];
                        ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="cart_items[]" value="<?= $row['id'] ?>" 
                                       class="form-check-input item-checkbox" 
                                       data-price="<?= $row['gia'] ?>" 
                                       data-qty="<?= $row['soluong'] ?>">
                            </td>
                            <td>
                                <img src="images/<?= htmlspecialchars($row['hinhanh']) ?>" 
                                     alt="<?= htmlspecialchars($row['ten']) ?>" 
                                     class="img-thumbnail" style="max-height: 80px; object-fit: contain;">
                            </td>
                            <td>
                                <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($row['ten']) ?></h6>
                            </td>
                            <td class="text-danger fw-bold">
                                <?= number_format($row['gia'], 0, ',', '.') ?> VNĐ
                            </td>
                            <td>
                                <input type="number" name="soluong[<?= $row['id'] ?>]" value="<?= $row['soluong'] ?>" 
                                       min="1" class="form-control input-soluong text-center" style="width: 80px;"
                                       data-id="<?= $row['id'] ?>">
                            </td>
                            <td class="text-danger fw-bold row-total">
                                <?= number_format($thanhtien, 0, ',', '.') ?> VNĐ
                            </td>
                            <td class="text-center">
                                <a href="pages/xuly_giohang.php?action=delete&id=<?= $row['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mt-4 bg-light">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 py-3">
                    <div>
                        <span class="fs-5 fw-semibold text-secondary">Tổng tiền thanh toán (Đã chọn):</span>
                        <span id="grandTotal" class="fs-4 fw-bold text-danger ms-2">0 VNĐ</span>
                    </div>
                    <button type="submit" id="btnCheckout" class="btn btn-success btn-lg fw-bold px-5 py-2.5 shadow-sm">
                        <i class="fa-solid fa-money-check-dollar me-2"></i>Tiến Hành Đặt Hàng
                    </button>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="text-center py-5 shadow-sm bg-white rounded-3 border">
            <i class="fa-solid fa-basket-shopping text-muted mb-3" style="font-size: 4rem;"></i>
            <h5 class="text-muted fw-semibold">Giỏ hàng của bạn đang trống rỗng!</h5>
            <p class="text-secondary small">Hãy chọn sản phẩm bạn yêu thích nhé.</p>
            <a href="index.php?page=sanpham" class="btn btn-success fw-bold px-4 mt-2">
                <i class="fa-solid fa-arrow-left me-2"></i>Mua sắm ngay
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const selectAll = document.getElementById("selectAll");
    const checkboxes = document.querySelectorAll(".item-checkbox");
    const grandTotalElement = document.getElementById("grandTotal");
    const formCheckout = document.getElementById("formCheckout");

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll(".item-checkbox:checked").forEach(cb => {
            let price = parseInt(cb.getAttribute("data-price"));
            let qty = parseInt(cb.getAttribute("data-qty"));
            total += price * qty;
        });
        grandTotalElement.textContent = total.toLocaleString('vi-VN') + " VNĐ";
    }

    if (selectAll) {
        selectAll.addEventListener("change", function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            calculateTotal();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener("change", function () {
            calculateTotal();
            if (!this.checked && selectAll) selectAll.checked = false;
        });
    });

    // Cập nhật số lượng bằng AJAX
    const inputs = document.querySelectorAll(".input-soluong");
    inputs.forEach(input => {
        input.addEventListener("change", function () {
            let id = this.getAttribute("data-id");
            let val = parseInt(this.value);
            
            if (val <= 0 || isNaN(val)) {
                val = 1;
                this.value = 1;
            }
            
            let rowCheckbox = this.closest('tr').querySelector('.item-checkbox');
            let price = parseInt(rowCheckbox.getAttribute('data-price'));
            let rowTotalElement = this.closest('tr').querySelector('.row-total');
            
            rowTotalElement.textContent = (price * val).toLocaleString('vi-VN') + " VNĐ";
            
            if (rowCheckbox) {
                rowCheckbox.setAttribute('data-qty', val);
                calculateTotal();
            }
            
            // Gửi Fetch chính xác đến file xuly_giohang.php nằm trong pages/
            fetch(`pages/xuly_giohang.php?action=update_qty_direct&id_giohang=${id}&soluong=${val}`)
            .then(res => res.text())
            .then(data => { console.log("AJAX update:", data); })
            .catch(err => { console.error('Lỗi AJAX:', err); });
        });
    });

    if (formCheckout) {
        formCheckout.addEventListener("submit", function (e) {
            if (document.querySelectorAll(".item-checkbox:checked").length === 0) {
                e.preventDefault();
                alert("Vui lòng chọn ít nhất một sản phẩm để mua hàng!");
            }
        });
    }
});
</script>