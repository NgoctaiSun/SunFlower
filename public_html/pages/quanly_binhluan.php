<?php
include_once '../connect.php';
if (!isset($conn)) {
    $conn = mysqli_connect("localhost", "root", "", "hoahuongduongphone");
}

// Câu lệnh JOIN 3 bảng để lấy tên Tài khoản và tên Sản phẩm tương ứng với bình luận
$sql = "SELECT b.*, t.ten AS ten_taikhoan, s.ten AS ten_sanpham 
        FROM binhluan b
        JOIN taikhoan t ON b.id_taikhoan = t.id
        JOIN sanpham s ON b.id_sanpham = s.id
        ORDER BY b.id DESC";
$result = mysqli_query($conn, $sql);
?>

<!-- Thư viện SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid my-4">
    <div class="card shadow-sm border-0 rounded-3 bg-white p-4">
        <h3 class="text-success fw-bold mb-4">
            <i class="fa-solid fa-comments shadow-sm me-2 p-2 bg-success text-white rounded"></i>Quản Lý Bình Luận & Đánh Giá
        </h3>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-white fw-bold">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">Khách hàng</th>
                        <th width="15%">Sản phẩm</th>
                        <th width="25%">Nội dung bình luận</th>
                        <th width="10%">Đánh giá</th>
                        <th width="12%">Trạng thái</th>
                        <th width="18%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="fw-bold text-secondary">#<?= $row['id'] ?></td>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($row['ten_taikhoan']) ?></td>
                            <td class="text-primary fw-medium"><?= htmlspecialchars($row['ten_sanpham']) ?></td>
                            <td>
                                <p class="mb-1 text-muted small" style="max-width: 300px;"><?= htmlspecialchars($row['noidung']) ?></p>
                                
                                <?php if (!empty($row['traloi_admin'])): ?>
                                    <div class="p-2 bg-light border-start border-3 border-success rounded text-dark small mt-1">
                                        <strong class="text-success"><i class="fa-solid fa-reply me-1"></i>Admin:</strong> <?= htmlspecialchars($row['traloi_admin']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-warning fw-bold">
                                    <?= $row['sosao'] ?> <i class="fa-solid fa-star text-warning"></i>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['trangthai'] == '1' || $row['trangthai'] == 'Hiển thị'): ?>
                                    <span class="badge bg-success rounded-pill px-2 py-1">Đang hiển thị</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill px-2 py-1">Đang ẩn</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- Nút Trả lời bình luận -->
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="openReplyModal(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['traloi_admin'] ?? '')) ?>')"
                                            title="Trả lời bình luận">
                                        <i class="fa-solid fa-reply"></i>
                                    </button>

                                    <!-- Nút Ẩn / Hiện bình luận -->
                                    <a href="kiemsoat_binhluan.php?action=toggle_status&id=<?= $row['id'] ?>&current=<?= $row['trangthai'] ?>" 
                                       class="btn btn-sm <?= ($row['trangthai'] == '1' || $row['trangthai'] == 'Hiển thị') ? 'btn-outline-warning' : 'btn-outline-success' ?>" 
                                       title="<?= ($row['trangthai'] == '1' || $row['trangthai'] == 'Hiển thị') ? 'Ẩn bình luận này' : 'Hiện bình luận này' ?>">
                                        <i class="fa-solid <?= ($row['trangthai'] == '1' || $row['trangthai'] == 'Hiển thị') ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                    </a>

                                    <!-- Nút Xóa bình luận với Popup SweetAlert2 -->
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            title="Xóa vĩnh viễn"
                                            onclick="confirmDelete(<?= $row['id'] ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Chưa có bình luận nào trong hệ thống.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL POPUP ADMIN TRẢ LỜI BÌNH LUẬN -->
<div class="modal fade" id="modalTraLoiBinhLuan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-reply me-2"></i>Trả lời bình luận</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="xuly_traloibinhluan.php" method="POST">
          <div class="modal-body p-4">
            <input type="hidden" name="id_binhluan" id="reply_id_binhluan">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nội dung phản hồi từ Admin:</label>
                <textarea class="form-control" name="traloi_admin" id="reply_noidung" rows="4" placeholder="Nhập câu trả lời của shop..." required></textarea>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Gửi phản hồi</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
// Hàm xác nhận trước khi xóa
function confirmDelete(id) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: "Bạn có chắc chắn muốn xóa vĩnh viễn đánh giá này không?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Đồng ý xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'kiemsoat_binhluan.php?action=delete&id=' + id;
        }
    });
}

// Hàm mở Modal Trả lời
function openReplyModal(id, currentReply) {
    document.getElementById('reply_id_binhluan').value = id;
    document.getElementById('reply_noidung').value = currentReply;
    var replyModal = new bootstrap.Modal(document.getElementById('modalTraLoiBinhLuan'));
    replyModal.show();
}
</script>

<!-- XỬ LÝ THÔNG BÁO TOAST NẰM BÊN PHẢI VÀ TỰ MẤT SAU 3 GIÂY -->
<?php if (isset($_GET['status'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    <?php if ($_GET['status'] == 'toggled_success'): ?>
        // Thông báo Toast nằm phía trên bên góc phải, tự biến mất sau 3 giây
        Swal.fire({
            toast: true,
            position: 'top-right',
            icon: 'success',
            title: 'Thay đổi thành công!',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    <?php elseif ($_GET['status'] == 'deleted_success'): ?>
        // Thông báo Popup giữa màn hình khi xóa
        Swal.fire({
            toast: true,
            position: 'top-right',
            icon: 'success',
            title: 'Đã xóa thành công !',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    <?php endif; ?>
});
const url = new URL(window.location.href);
    url.searchParams.delete('status');
    window.history.replaceState({}, document.title, url.toString());
</script>
<?php endif; ?>