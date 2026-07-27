<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>

    <!-- Bootstrap CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Thư viện Icon FontAwesome (Để dùng icon mắt sắc nét) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f8f9fa;
        }

        .login-box {
            max-width: 400px;
            margin: 80px auto;
        }

        .card {
            border: none;
            border-radius: 15px;
        }

        .card-header {
            background: #198754;
            color: white;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }

        /* 1. TẮT HOÀN TOÀN CON MẮT MẶC ĐỊNH CỦA TRÌNH DUYỆT (EDGE, CHROME, IE) */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none !important;
        }
        
        /* Chỉnh nút con mắt cho khớp viền Bootstrap */
        .btn-toggle-pwd {
            border-color: #ced4da;
            background-color: #fff;
        }
        .btn-toggle-pwd:hover {
            background-color: #f8f9fa;
            border-color: #ced4da;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="login-box">

        <div class="card shadow-sm">

            <div class="card-header">
                Đăng Nhập
            </div>

            <div class="card-body p-4">

                <form action="pages/xuly_dangnhap.php" method="POST" id="loginForm">

                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập" required>
                    </div>
                    
                    <!-- Ô MẬT KHẨU CÓ ICON MẮT CUSTOM -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                            <button class="btn btn-toggle-pwd" type="button" id="togglePasswordBtn">
                                <i class="fa-solid fa-eye" id="eyeIcon" style="color: #6c757d;"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg fs-6">Đăng nhập</button>
                    </div>

                </form>

                <hr class="my-4">

                <div class="text-center text-muted">
                    Chưa có tài khoản? 
                    <a href="index.php?page=dangky" class="text-success text-decoration-none fw-bold">Đăng ký ngay</a>
                </div>

            </div>

        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. XỬ LÝ ẨN/HIỆN MẬT KHẨU KHÔNG GIỚI HẠN LẦN BẤM
    const passwordInput = document.getElementById("password");
    const toggleBtn = document.getElementById("togglePasswordBtn");
    const icon = document.getElementById("eyeIcon");

    if (toggleBtn && passwordInput && icon) {
        toggleBtn.addEventListener("click", function () {
            const isPassword = passwordInput.getAttribute("type") === "password";
            passwordInput.setAttribute("type", isPassword ? "text" : "password");
            
            // Đổi icon mắt mở <-> mắt gạch chéo
            if (isPassword) {
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        });
    }
});
</script>

</body>
</html>