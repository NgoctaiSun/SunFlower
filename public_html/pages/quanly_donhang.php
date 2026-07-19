<?php
$conn = mysqli_connect("localhost","root","","hoahuongduongphone"); 

if(!$conn){
    die("Kết nối thất bại");
}

// Xử lý khi Admin cập nhật trạng thái đơn
if (isset($_POST['capnhat_trangthai'])) {
    $id_dh = mysqli_real_escape_string($conn, $_POST['id_donhang']);
    $trangthai_moi = mysqli_real_escape_string($conn, $_POST['trangthai']);
    
    $sql_update = "UPDATE donhang SET trangthai = '$trangthai_moi' WHERE id = '$id_dh'";
    if(mysqli_query($conn, $sql_update)) {
        echo "<script>alert('Cập nhật trạng thái đơn hàng thành công!'); window.location.href='admin.php?action=donhang';</script>";
    }
}

// Tải danh sách đơn hàng toàn hệ thống
$list_orders = mysqli_query($conn, "SELECT * FROM donhang ORDER BY ngaymua DESC");
?>

<div class="card shadow-sm">
    <div class="card-header bg-success text-white fw-bold">
        <i class="fa-solid fa-file-invoice-dollar me-1"></i> Điều Phối & Quản Lý Đơn Hàng
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã đơn</th>
                    <th>Thông tin khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái hiện tại</th>
                    <th>Xử lý đơn hàng</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($list_orders) > 0) {
                    while ($row = mysqli_fetch_assoc($list_orders)) { 
                        $id_dh = $row['id'];
                        
                        // Lấy chi tiết sản phẩm của đơn hàng này để chuẩn bị dữ liệu cho nút "Xem chi tiết"
                        // Truy vấn kết hợp (JOIN) bảng chitietdonhang với bảng sanpham để lấy tên sản phẩm
                        $sql_ct = "SELECT ctdh.*, sp.ten AS ten_sanpham 
                                   FROM chitietdonhang ctdh 
                                   JOIN sanpham sp ON ctdh.id_sanpham = sp.id 
                                   WHERE ctdh.id_donhang = '$id_dh'";
                        $list_details = mysqli_query($conn, $sql_ct);
                        
                        $array_details = [];
                        if($list_details) {
                            while($ct = mysqli_fetch_assoc($list_details)) {
                                $array_details[] = $ct;
                            }
                        }
                ?>
                <tr>
                    <td class="fw-bold text-center text-success">#<?= $row['id'] ?></td>
                    <td>
                        <strong class="text-dark"><?= htmlspecialchars($row['tennguoinhan']) ?></strong><br>
                        <small class="text-muted d-block mt-1"><i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($row['sdtnhan']) ?></small>
                        <!-- BỔ SUNG: Địa chỉ giao hàng -->
                        <small class="text-muted d-block"><i class="fa-solid fa-location-dot me-1"></i> Đ/C: <?= htmlspecialchars($row['diachi']??'') ?></small>
                        <small class="text-muted d-block"><i class="fa-solid fa-wallet me-1"></i> HTTT: <?= htmlspecialchars($row['thanhtoan']) ?></small>
                    </td>
                    <td><?= $row['ngaymua'] ?></td>
                    <td class="text-danger fw-bold"><?= number_format($row['tongtien'], 0, ',', '.') ?> đ</td>
                    <td class="text-center">
                        <?php
                        if($row['trangthai'] == 'Chờ xác nhận') echo "<span class='badge bg-warning text-dark px-2 py-2'>Chờ xác nhận</span>";
                        elseif($row['trangthai'] == 'Đang giao') echo "<span class='badge bg-primary px-2 py-2'>Đang giao</span>";
                        elseif($row['trangthai'] == 'Hoàn thành') echo "<span class='badge bg-success px-2 py-2'>Hoàn thành</span>";
                        else echo "<span class='badge bg-danger px-2 py-2'>Đã hủy</span>";
                        ?>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-2 align-items-center">
                            <form method="POST" class="d-flex gap-2 justify-content-center w-100">
                                <input type="hidden" name="id_donhang" value="<?= $row['id'] ?>">
                                <select name="trangthai" class="form-select form-select-sm" style="width: 130px;">
                                    <option value="Chờ xác nhận" <?= $row['trangthai']=='Chờ xác nhận'?'selected':'' ?>>Chờ xác nhận</option>
                                    <option value="Đang giao" <?= $row['trangthai']=='Đang giao'?'selected':'' ?>>Đang giao</option>
                                    <option value="Hoàn thành" <?= $row['trangthai']=='Hoàn thành'?'selected':'' ?>>Hoàn thành</option>
                                    <option value="Đã hủy" <?= $row['trangthai']=='Đã hủy'?'selected':'' ?>>Đã hủy</option>
                                </select>
                                <button type="submit" name="capnhat_trangthai" class="btn btn-sm btn-success fw-bold">Cập nhật</button>
                            </form>
                            
                            <!-- BỔ SUNG: Nút Xem chi tiết kích hoạt Bootstrap Modal -->
                            <button type="button" class="btn btn-sm btn-outline-primary fw-medium w-100" data-bs-toggle="modal" data-bs-target="#modalChiTiet<?= $row['id'] ?>">
                                <i class="fa-solid fa-eye me-1"></i> Xem chi tiết
                            </button>
                        </div>

                        <!-- BỔ SUNG: Khung giao diện Modal ẩn cho từng đơn hàng -->
                        <div class="modal fade" id="modalChiTiet<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-dark text-white">
                                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-circle-info me-1"></i> Chi Tiết Đơn Hàng #<?= $row['id'] ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-0">
                                        <div class="p-3 bg-light border-bottom">
                                            <p class="mb-1"><strong>Người nhận:</strong> <?= htmlspecialchars($row['tennguoinhan']) ?> - <?= htmlspecialchars($row['sdtnhan']) ?></p>
                                            <p class="mb-0 text-muted"><strong>Địa chỉ nhận hàng:</strong> <?= htmlspecialchars($row['diachi']??'') ?></p>
                                        </div>
                                        <table class="table table-striped table-hover align-middle mb-0 text-start">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th class="ps-3" width="50%">Tên sản phẩm</th>
                                                    <th class="text-center" width="15%">Số lượng</th>
                                                    <th class="text-end" width="15%">Đơn giá</th>
                                                    <th class="text-end pe-3" width="20%">Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                if (!empty($array_details)) {
                                                    foreach ($array_details as $item) {
                                                        $thanh_tien = $item['soluong'] * $item['dongia'];
                                                ?>
                                                <tr>
                                                    <td class="ps-3 fw-bold text-secondary"><?= htmlspecialchars($item['ten_sanpham']) ?></td>
                                                    <td class="text-center"><?= $item['soluong'] ?></td>
                                                    <td class="text-end"><?= number_format($item['dongia'], 0, ',', '.') ?> đ</td>
                                                    <td class="text-end fw-bold text-dark pe-3"><?= number_format($thanh_tien, 0, ',', '.') ?> đ</td>
                                                </tr>
                                                <?php 
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='4' class='text-center p-3 text-muted'>Không tìm thấy dữ liệu sản phẩm của đơn hàng này.</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                            <tfoot class="table-light fw-bold border-top-2">
                                                <tr>
                                                    <td colspan="3" class="text-end text-uppercase fs-6">Tổng cộng thanh toán:</td>
                                                    <td class="text-end text-danger fs-5 pe-3"><?= number_format($row['tongtien'], 0, ',', '.') ?> đ</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-shadow data-bs-dismiss="modal">Đóng cửa sổ</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Kết thúc Khung Modal -->

                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center p-4 text-muted'>Hệ thống chưa ghi nhận đơn mua hàng nào.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>