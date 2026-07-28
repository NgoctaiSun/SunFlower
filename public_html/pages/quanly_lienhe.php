<?php
$conn = mysqli_connect("localhost","root","","hoahuongduongphone"); 

if(!$conn){
    die("Kết nối thất bại");
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Xử lý xóa thư liên hệ
if (isset($_GET['delete_lh'])) {
    $id_lh = mysqli_real_escape_string($conn, $_GET['delete_lh']);
    $sql_del = "DELETE FROM lienhe WHERE id = '$id_lh'";
    if (mysqli_query($conn, $sql_del)) {
        header("Location: admin.php?action=lienhe&status=deleted_success");
        exit();
    } else {
        header("Location: admin.php?action=lienhe&status=delete_error");
        exit();
    }
}

// Truy vấn danh sách
$list_contacts = mysqli_query($conn, "SELECT * FROM lienhe ORDER BY id DESC");
$list_members = mysqli_query($conn, "SELECT * FROM taikhoan ORDER BY id DESC");
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white py-3">
        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-users-gear me-2"></i>Quản Lý Liên Hệ</h5>
    </div>
    <div class="card-body">
        
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-success" id="feedback-tab" data-bs-toggle="tab" data-bs-target="#feedback-panel" type="button" role="tab" aria-controls="feedback-panel" aria-selected="true">
                    <i class="fa-solid fa-envelope me-2"></i>Thư Góp Ý & Liên Hệ (<?= mysqli_num_rows($list_contacts) ?>)
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="adminTabsContent">
            
            <!-- Panel 1: Thư góp ý -->
            <div class="tab-pane fade show active" id="feedback-panel" role="tabpanel" aria-labelledby="feedback-tab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="8%" class="text-center">Mã thư</th>
                                <th width="20%">Họ và tên</th>
                                <th width="22%">Địa chỉ Email</th>
                                <th>Nội dung góp ý / hỗ trợ</th>
                                <th width="12%" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(mysqli_num_rows($list_contacts) > 0) {
                                while ($row = mysqli_fetch_assoc($list_contacts)) { 
                            ?>
                                <tr>
                                    <td class="text-center text-muted">#<?= $row['id'] ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['ten']) ?></td>
                                    <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="text-decoration-none text-success"><?= htmlspecialchars($row['email']) ?></a></td>
                                    <td class="text-secondary"><?= nl2br(htmlspecialchars($row['noidung'])) ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteLH(<?= $row['id'] ?>)">
                                            <i class="fa-solid fa-trash"></i> Xóa
                                        </button>
                                    </td>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center p-4 text-muted'>Hộp thư rỗng. Chưa nhận được phản hồi nào từ khách hàng.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Đảm bảo thư viện SweetAlert2 đã được nhúng -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Hàm xác nhận trước khi xóa thư liên hệ
function confirmDeleteLH(id) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: "Bạn có chắc chắn muốn xóa vĩnh viễn thư liên hệ này không?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Đồng ý xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'admin.php?action=lienhe&delete_lh=' + id;
        }
    });
}
</script>

<!-- Xử lý hiển thị Toast khi có tham số status trên URL -->
<?php if (isset($_GET['status'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    <?php if ($_GET['status'] == 'deleted_success'): ?>
        Swal.fire({
            toast: true,
            position: 'top-right',
            icon: 'success',
            title: 'Đã xóa thư liên hệ thành công!',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    <?php elseif ($_GET['status'] == 'delete_error'): ?>
        Swal.fire({
            toast: true,
            position: 'top-right',
            icon: 'error',
            title: 'Lỗi khi xóa thư liên hệ!',
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