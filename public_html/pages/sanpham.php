<?php
if (!isset($conn)) {
    $conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
}
?>
<div class="container my-4">
    <div class="row">
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card shadow border-0 p-3 bg-white sticky-top" style="top: 20px; z-index: 10;">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold mb-0 text-success"><i class="fa-solid fa-filter me-2"></i>Bộ lọc</h5>
                    <button class="btn btn-sm btn-link text-danger p-0 text-decoration-none fw-semibold" onclick="resetBoLoc()">Bỏ chọn</button>
                </div>

                <div class="filter-group mb-4">
                    <h6 class="fw-bold text-secondary mb-2">Hãng sản xuất</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-secondary filter-btn brand-btn" data-brand="iPhone" onclick="toggleBrand('iPhone', this)">iPhone</button>
                        <button class="btn btn-outline-secondary filter-btn brand-btn" data-brand="Samsung" onclick="toggleBrand('Samsung', this)">Samsung</button>
                        <button class="btn btn-outline-secondary filter-btn brand-btn" data-brand="Oppo" onclick="toggleBrand('Oppo', this)">Oppo</button>
                        <button class="btn btn-outline-secondary filter-btn brand-btn" data-brand="Xiaomi" onclick="toggleBrand('Xiaomi', this)">Xiaomi</button>
                        <button class="btn btn-outline-secondary filter-btn brand-btn" data-brand="Vivo" onclick="toggleBrand('Vivo', this)">Vivo</button>
                    </div>
                </div>

                <div class="filter-group mb-3">
                    <h6 class="fw-bold text-secondary mb-2">Mức giá</h6>
                    <div class="d-flex flex-column gap-2">
                        <button class="btn btn-outline-secondary filter-btn text-start price-tag" data-min="0" data-max="5000000" onclick="selectPrice(this)">Dưới 5 triệu</button>
                        <button class="btn btn-outline-secondary filter-btn text-start price-tag" data-min="5000000" data-max="15000000" onclick="selectPrice(this)">Từ 5 - 15 triệu</button>
                        <button class="btn btn-outline-secondary filter-btn text-start price-tag" data-min="15000000" data-max="25000000" onclick="selectPrice(this)">Từ 15 - 25 triệu</button>
                        <button class="btn btn-outline-secondary filter-btn text-start price-tag" data-min="25000000" data-max="999999999" onclick="selectPrice(this)">Trên 25 triệu</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 col-lg-9">
            <div class="row g-4" id="productContainer">
                <?php
                $sql = "SELECT * FROM sanpham ORDER BY id DESC";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($result)):
                ?>
                <div class="col-sm-6 col-md-6 col-lg-4 product-item" data-brand="<?= htmlspecialchars($row['hang']) ?>" data-price="<?= $row['gia'] ?>">
                    <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden position-relative product-card bg-white product-card-hover">
                        <div class="p-3 text-center bg-light">
                            <img src="images/<?= htmlspecialchars($row['hinhanh']) ?>" class="img-fluid rounded" style="height: 200px; object-fit: contain;">
                        </div>
                        <div class="card-body p-3">
                            <span class="badge bg-secondary mb-2 px-2.5 py-1.5 rounded-pill small"><?= htmlspecialchars($row['hang']) ?></span>
                            <h5 class="card-title fw-bold text-dark text-truncate mb-1"><?= htmlspecialchars($row['ten']) ?></h5>
                            <p class="text-danger fw-bold fs-5 mb-2"><?= number_format($row['gia'], 0, ',', '.') ?> đ</p>
                            <div class="text-muted small mb-0">
                                <span><i class="fa-solid fa-microchip me-1"></i>RAM: <?= htmlspecialchars($row['ram']) ?></span><br>
                                <span><i class="fa-solid fa-database me-1"></i>Bộ nhớ: <?= htmlspecialchars($row['bonho']) ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <a href="index.php?page=chitetsanpham&id=<?= $row['id'] ?>" class="btn btn-success w-100 fw-bold rounded-pill shadow-sm">
                                <i class="fa-solid fa-eye me-1"></i>Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<script>
let selectedBrands = [];
let selectedMinPrice = 0;
let selectedMaxPrice = 999999999;

function toggleBrand(brand, element) {
    if (selectedBrands.includes(brand)) {
        selectedBrands = selectedBrands.filter(b => b !== brand);
        element.classList.remove('active', 'bg-success', 'text-white');
    } else {
        selectedBrands.push(brand);
        element.classList.add('active', 'bg-success', 'text-white');
    }
    locSanPham();
}

function selectPrice(element) {
    if (element.classList.contains('active')) {
        element.classList.remove('active', 'bg-success', 'text-white');
        selectedMinPrice = 0;
        selectedMaxPrice = 999999999;
    } else {
        document.querySelectorAll('.price-tag').forEach(btn => btn.classList.remove('active', 'bg-success', 'text-white'));
        element.classList.add('active', 'bg-success', 'text-white');
        selectedMinPrice = parseInt(element.getAttribute('data-min'));
        selectedMaxPrice = parseInt(element.getAttribute('data-max'));
    }
    locSanPham();
}

function locSanPham() {
    document.querySelectorAll('.product-item').forEach(product => {
        const pBrand = product.getAttribute('data-brand');
        const pPrice = parseInt(product.getAttribute('data-price'));
        const matchBrand = selectedBrands.length === 0 || selectedBrands.includes(pBrand);
        const matchPrice = pPrice >= selectedMinPrice && pPrice <= selectedMaxPrice;

        product.style.display = (matchBrand && matchPrice) ? "block" : "none";
    });
}

function resetBoLoc() {
    selectedBrands = [];
    selectedMinPrice = 0;
    selectedMaxPrice = 999999999;
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active', 'bg-success', 'text-white'));
    document.querySelectorAll('.product-item').forEach(product => product.style.display = "block");
}
</script>