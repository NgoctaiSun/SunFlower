<?php
$conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone"); 

if (!$conn) {
    die("Kết nối thất bại");
}

// Khởi tạo biến chỉnh sửa sản phẩm
$edit_row = null;
if (isset($_GET['edit_sp'])) {
    $id_edit = mysqli_real_escape_string($conn, $_GET['edit_sp']);
    $edit_res = mysqli_query($conn, "SELECT * FROM sanpham WHERE id = '$id_edit'");
    if (mysqli_num_rows($edit_res) > 0) {
        $edit_row = mysqli_fetch_assoc($edit_res);
    }
}

// XỬ LÝ 1: THÊM MỚI SẢN PHẨM HOẶC CẬP NHẬT (SỬA) SẢN PHẨM
if (isset($_POST['luusanpham'])) {
    $tensp = mysqli_real_escape_string($conn, $_POST['tensp']);
    $gia = mysqli_real_escape_string($conn, $_POST['gia']);
    $soluong = intval($_POST['soluong']);
    $ram = mysqli_real_escape_string($conn, $_POST['ram']);
    $bonho = mysqli_real_escape_string($conn, $_POST['bonho']);
    $mota = mysqli_real_escape_string($conn, $_POST['mota']);
    $hang = mysqli_real_escape_string($conn, $_POST['hang']);
    
    $id_updating = isset($_POST['id_updating']) ? intval($_POST['id_updating']) : 0;
    $target_dir = "../images/";
    $image_name = "";

    // Xử lý upload ảnh nếu người dùng có chọn file ảnh mới
    if (!empty($_FILES["hinhanh"]["name"])) {
        $image_name = time() . "_" . basename($_FILES["hinhanh"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["hinhanh"]["tmp_name"], $target_file);
    } else {
        // Nếu sửa mà không chọn ảnh mới, giữ nguyên ảnh cũ
        if ($id_updating > 0 && $edit_row) {
            $image_name = $edit_row['hinhanh'];
        }
    }

    if ($id_updating > 0) {
        // Thực hiện câu lệnh CẬP NHẬT (UPDATE)
        $sql_action = "UPDATE sanpham SET 
                        ten='$tensp', 
                        gia='$gia', 
                        soluong='$soluong', 
                        ram='$ram', 
                        bonho='$bonho', 
                        mota='$mota', 
                        hang='$hang', 
                        hinhanh='$image_name' 
                       WHERE id='$id_updating'";
        $msg = "Cập nhật sản phẩm thành công!";
    } else {
        // Thực hiện câu lệnh THÊM MỚI (INSERT)
        $sql_action = "INSERT INTO sanpham (ten, gia, soluong, ram, bonho, mota, hang, hinhanh) 
                       VALUES ('$tensp', '$gia', '$soluong', '$ram', '$bonho', '$mota', '$hang', '$image_name')";
        $msg = "Thêm mới sản phẩm thành công!";
    }

    if (mysqli_query($conn, $sql_action)) {
        echo "<script>alert('$msg'); window.location.href='admin.php?action=sanpham';</script>";
    } else {
        echo "<script>alert('Lỗi xử lý dữ liệu!');</script>";
    }
}

// XỬ LÝ 2: XÓA SẢN PHẨM KHỎI HỆ THỐNG
if (isset($_GET['delete_sp'])) {
    $id_del = mysqli_real_escape_string($conn, $_GET['delete_sp']);
    
    // Trước tiên xóa các dữ liệu liên quan ở bảng giỏ hàng để tránh lỗi khóa ngoại
    mysqli_query($conn, "DELETE FROM giohang WHERE id_sanpham = '$id_del'");
    
    $sql_del = "DELETE FROM sanpham WHERE id = '$id_del'";
    if (mysqli_query($conn, $sql_del)) {
        echo "<script>alert('Đã xóa sản phẩm khỏi hệ thống thành công!'); window.location.href='admin.php?action=sanpham';</script>";
    } else {
        echo "<script>alert('Không thể xóa sản phẩm do vướng ràng buộc đơn hàng!'); window.location.href='admin.php?action=sanpham';</script>";
    }
}

// Tải toàn bộ danh sách sản phẩm hiện có
$list_products = mysqli_query($conn, "SELECT * FROM sanpham ORDER BY id DESC");
?>

<div class="row g-4">
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
            <div class="card-header <?= $edit_row ? 'bg-primary' : 'bg-success' ?> text-white fw-bold">
                <i class="fa-solid <?= $edit_row ? 'fa-pen-to-square' : 'fa-plus' ?> me-1"></i>
                <?= $edit_row ? 'Cập nhật sản phẩm #' . $edit_row['id'] : 'Thêm Sản Phẩm Mới' ?>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($edit_row): ?>
                        <input type="hidden" name="id_updating" value="<?= $edit_row['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Tên sản phẩm *</label>
                        <input type="text" name="tensp" class="form-control" value="<?= $edit_row ? htmlspecialchars($edit_row['ten']) : '' ?>" required placeholder="Ví dụ: iPhone 15 Pro Max">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small">Giá bán (VNĐ) *</label>
                            <input type="number" name="gia" class="form-control" value="<?= $edit_row ? $edit_row['gia'] : '' ?>" required placeholder="Nhập số liền nhau">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small">Số lượng kho *</label>
                            <input type="number" name="soluong" class="form-control" value="<?= $edit_row ? $edit_row['soluong'] : '10' ?>" required min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small">Dung lượng RAM *</label>
                            <input type="text" name="ram" class="form-control" value="<?= $edit_row ? htmlspecialchars($edit_row['ram']) : '' ?>" placeholder="Ví dụ: 8GB" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small">Bộ nhớ trong *</label>
                            <input type="text" name="bonho" class="form-control" value="<?= $edit_row ? htmlspecialchars($edit_row['bonho']) : '' ?>" placeholder="Ví dụ: 256GB" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Hãng sản xuất *</label>
                        <select name="hang" class="form-select" required>
                            <option value="">-- Chọn hãng sản xuất --</option>
                            <option value="Apple" <?= ($edit_row && $edit_row['hang'] == 'Apple') ? 'selected' : '' ?>>Apple (iPhone)</option>
                            <option value="Samsung" <?= ($edit_row && $edit_row['hang'] == 'Samsung') ? 'selected' : '' ?>>Samsung</option>
                            <option value="Oppo" <?= ($edit_row && $edit_row['hang'] == 'Oppo') ? 'selected' : '' ?>>Oppo</option>
                            <option value="Xiaomi" <?= ($edit_row && $edit_row['hang'] == 'Xiaomi') ? 'selected' : '' ?>>Xiaomi</option>
                            <option value="Vivo" <?= ($edit_row && $edit_row['hang'] == 'Vivo') ? 'selected' : '' ?>>Vivo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Hình ảnh sản phẩm</label>
                        <input type="file" name="hinhanh" class="form-control" <?= $edit_row ? '' : 'required' ?>>
                        <?php if ($edit_row): ?>
                            <div class="mt-2 text-center bg-light p-2 rounded">
                                <small class="text-muted d-block mb-1">Ảnh hiện tại:</small>
                                <img src="../images/<?= $edit_row['hinhanh'] ?>" style="height: 60px; object-fit: contain;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary small">Mô tả sản phẩm</label>
                        <textarea name="mota" class="form-control" rows="4" placeholder="Nhập cấu hình chi tiết, quà tặng khuyến mãi..."><?= $edit_row ? htmlspecialchars($edit_row['mota']) : '' ?></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="luusanpham" class="btn <?= $edit_row ? 'btn-primary' : 'btn-success' ?> fw-bold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> <?= $edit_row ? 'Cập Nhật Thay Đổi' : 'Đăng Bán Sản Phẩm' ?>
                        </button>
                        <?php if ($edit_row): ?>
                            <a href="admin.php?action=sanpham" class="btn btn-outline-secondary btn-sm fw-medium">Hủy chỉnh sửa</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-boxes-stacked me-1"></i> Danh Sách Kho Hàng Thực Tế</span>
                <span class="badge bg-light text-dark"><?= mysqli_num_rows($list_products) ?> mặt hàng</span>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light text-secondary text-uppercase small font-monospace">
                        <tr>
                            <th width="10%" class="text-center">Ảnh</th>
                            <th width="35%">Thông tin sản phẩm</th>
                            <th width="18%" class="text-end">Giá bán</th>
                            <th width="12%" class="text-center">Kho hàng</th>
                            <th width="25%" class="text-center">Hành động</th> <!-- Tăng độ rộng cột hành động lên 25% để không bị vỡ nút -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($list_products) > 0) {
                            while ($row = mysqli_fetch_assoc($list_products)) {
                        ?>
                        <tr>
                            <td class="text-center">
                                <img src="../images/<?= htmlspecialchars($row['hinhanh']) ?>" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: contain;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($row['ten']) ?></div>
                                <div class="text-muted small mt-1">
                                    <span class="badge bg-light text-secondary border me-1">Hãng: <?= htmlspecialchars($row['hang']) ?></span>
                                    <span class="badge bg-info-subtle text-info me-1">RAM: <?= htmlspecialchars($row['ram']) ?></span>
                                    <span class="badge bg-warning-subtle text-warning">ROM: <?= htmlspecialchars($row['bonho']) ?></span>
                                </div>
                            </td>
                            <td class="text-end fw-bold text-danger">
                                <?= number_format($row['gia'], 0, ',', '.') ?> đ
                            </td>
                            <td class="text-center">
                                <?php if($row['soluong'] <= 0): ?>
                                    <span class="badge bg-danger px-2 py-1">Hết hàng</span>
                                <?php elseif($row['soluong'] <= 5): ?>
                                    <span class="badge bg-warning text-dark px-2 py-1">Còn ít: <?= $row['soluong'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success px-2 py-1"><?= $row['soluong'] ?> cái</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <!-- Nút Sửa -->
                                    <a href="admin.php?action=sanpham&edit_sp=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary shadow-sm" title="Sửa sản phẩm">
                                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                                    </a>
                                    
                                    <!-- Nút Xóa -->
                                    <a href="admin.php?action=sanpham&delete_sp=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger shadow-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn sản phẩm [ <?= htmlspecialchars($row['ten']) ?> ] và dọn sạch sản phẩm này khỏi giỏ hàng của khách?')" title="Xóa sản phẩm">
                                        <i class="fa-solid fa-trash"></i> Xóa
                                    </a>

                                    <!-- Nút Hiện/Ẩn (Đã sửa lại class chuẩn Bootstrap) -->
                                    <?php if(isset($row['hienthi']) && $row['hienthi'] == 0){ ?>
                                        <a href="lay.php?id=<?= $row['id'] ?>&action=show" class="btn btn-sm btn-outline-success shadow-sm" title="Hiện sản phẩm">
                                            <i class="fa-solid fa-eye"></i> Hiện
                                        </a>
                                    <?php } else { ?>
                                        <a href="lay.php?id=<?= $row['id'] ?>&action=hide" class="btn btn-sm btn-outline-secondary shadow-sm" title="Ẩn sản phẩm">
                                            <i class="fa-solid fa-eye-slash"></i> Ẩn
                                        </a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center p-4 text-muted'>Chưa có sản phẩm nào trong kho. Hãy thêm mới ngay!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>