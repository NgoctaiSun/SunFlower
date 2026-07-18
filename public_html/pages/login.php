<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-box{
            max-width: 400px;
            margin: 80px auto;
        }
        .card{
            border: none;
            border-radius: 15px;
        }
        .card-header{
            background: #198754;
            color: white;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border-radius: 15px 15px 0 0 !important;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="login-box">
        <div class="card shadow">
            <div class="card-header">
                Đăng Nhập
            </div>
            <div class="card-body">
                <form action="pages/xuly_dangnhap.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Đăng nhập</button>
                    </div>
                </form>
                <hr>
                <div class="text-center">
                    Chưa có tài khoản? <a href="index.php?page=dangky">Đăng ký ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>