<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['user_id'])){
    echo "
    <div class='container mt-5'>
        <div class='alert alert-danger'>
            Vui lòng đăng nhập để xem lịch sử mua hàng!
        </div>
    </div>";
    exit();
}

$conn = mysqli_connect("localhost","root","","hoahuongduongphone");

$iduser = $_SESSION['user_id'];

$sql = "SELECT * FROM donhang
        WHERE id_taikhoan = '$iduser'
        ORDER BY ngaymua DESC";

$result = mysqli_query($conn,$sql);
?>

<div class="container my-5">

    <h2 class="text-center mb-4 text-success">
        Lịch sử mua hàng
    </h2>

    <?php if(mysqli_num_rows($result) > 0){ ?>

    <div class="table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-success">
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày mua</th>
                    <th>Tổng tiền</th>
                    <th>Người nhận</th>
                    <th>SĐT</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>

            <tbody>

                <?php while($row = mysqli_fetch_assoc($result)){ ?>

                <tr>

                    <td>
                        #<?= $row['id']; ?>
                    </td>

                    <td>
                        <?= $row['ngaymua']; ?>
                    </td>

                    <td class="text-danger fw-bold">
                        <?= number_format($row['tongtien'],0,',','.'); ?> đ
                    </td>

                    <td>
                        <?= $row['tennguoinhan']; ?>
                    </td>

                    <td>
                        <?= $row['sdtnhan']; ?>
                    </td>

                    <td>
                        <?= $row['thanhtoan']; ?>
                    </td>

                    <td>

                        <?php
                        if($row['trangthai'] == 'Chờ xác nhận'){
                            echo "<span class='badge bg-warning'>Chờ xác nhận</span>";
                        }
                        elseif($row['trangthai'] == 'Đang giao'){
                            echo "<span class='badge bg-primary'>Đang giao</span>";
                        }
                        elseif($row['trangthai'] == 'Hoàn thành'){
                            echo "<span class='badge bg-success'>Hoàn thành</span>";
                        }
                        else{
                            echo "<span class='badge bg-danger'>Đã hủy</span>";
                        }
                        ?>

                    </td>

                </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

    <?php } else { ?>

        <div class="alert alert-info text-center">
            Bạn chưa có đơn hàng nào.
        </div>

    <?php } ?>

</div>