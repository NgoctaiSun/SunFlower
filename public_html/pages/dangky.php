<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>

    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f5f5f5;
        }

        .register-box{
            max-width:500px;
            margin:50px auto;
        }

        .card{
            border:none;
            border-radius:15px;
        }

        .card-header{
            background:#198754;
            color:white;
            text-align:center;
            font-size:24px;
            font-weight:bold;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="register-box">

        <div class="card shadow">

            <div class="card-header">
                Đăng Ký Tài Khoản
            </div>

            <div class="card-body">

                <form action="pages/xuly_dangky.php" method="POST">

                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="hoten" class="form-control" required>
                    </div>

                    <div class="mb-3">
    <label class="form-label">Địa chỉ</label>
    <input type="text" name="diachi" class="form-control" required>
</div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="sdt" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="matkhau" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nhập lại mật khẩu</label>
                        <input type="password" name="nhaplaimatkhau" class="form-control" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Đăng ký</button>
                    </div>

                </form>

                <hr>

                <div class="text-center">
                    Đã có tài khoản?
                    <a href="index.php?page=login">Đăng nhập ngay</a>
                </div>

            </div>

        </div>

    </div>
</div>


</body>
</html>