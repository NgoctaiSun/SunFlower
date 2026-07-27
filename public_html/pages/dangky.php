<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>

    <!-- Bootstrap CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Thư viện Icon FontAwesome (Để dùng icon mắt sắc nét) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f8f9fa;
        }

        .register-box {
            max-width: 500px;
            margin: 50px auto;
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
            border-top-left-radius: 15px !important;
            border-top-right-radius: 15px !important;
            padding: 20px;
        }

        /* 1. TẮT HOÀN TOÀN CON MẮT MẶC ĐỊNH CỦA TRÌNH DUYỆT (EDGE, CHROME, IE) */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none !important;
        }

        .error-message {
            font-size: 0.875rem;
            margin-top: 5px;
            display: block;
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
    <div class="register-box">

        <div class="card shadow-sm">

            <div class="card-header">
                Đăng Ký Tài Khoản
            </div>

            <div class="card-body p-4">

                <form action="pages/xuly_dangky.php" method="POST" id="registerForm">

                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="hoten" class="form-control"  required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="diachi" class="form-control"  required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="sdt" class="form-control"  required>
                    </div>
                    
                    <!-- Ô MẬT KHẨU CÓ ICON MẮT CUSTOM -->
                    <div class="mb-3">
                        <label for="matkhau" class="form-label">Mật khẩu</label>
                        <div class="input-group">
                            <input type="password" id="matkhau" name="matkhau" class="form-control" required>
                            <button class="btn btn-toggle-pwd" type="button" id="togglePasswordBtn">
                                <i class="fa-solid fa-eye" id="eyeIcon" style="color: #6c757d;"></i>
                            </button>
                        </div>
                        <small id="passwordError" class="error-message"></small>
                    </div>

                    <!-- Ô NHẬP LẠI MẬT KHẨU CÓ ICON MẮT CUSTOM -->
                    <div class="mb-3">
                        <label for="nhaplaimatkhau" class="form-label">Nhập lại mật khẩu</label>
                        <div class="input-group">
                            <input type="password" name="nhaplaimatkhau" class="form-control" id="nhaplaimatkhau" required>
                            <button class="btn btn-toggle-pwd" type="button" id="toggleRePasswordBtn">
                                <i class="fa-solid fa-eye" id="reEyeIcon" style="color: #6c757d;"></i>
                            </button>
                        </div>
                        <small id="repasswordError" class="error-message"></small>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg fs-6">Đăng ký</button>
                    </div>

                </form>

                <hr class="my-4">

                <div class="text-center text-muted">
                    Đã có tài khoản? 
                    <a href="index.php?page=login" class="text-success text-decoration-none fw-bold">Đăng nhập ngay</a>
                </div>

            </div>

        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. XỬ LÝ ẨN/HIỆN MẬT KHẨU KHÔNG GIỚI HẠN LẦN BẤM
    function setupPasswordToggle(inputId, buttonId, iconId) {
        const input = document.getElementById(inputId);
        const btn = document.getElementById(buttonId);
        const icon = document.getElementById(iconId);

        if (btn && input && icon) {
            btn.addEventListener("click", function () {
                const isPassword = input.getAttribute("type") === "password";
                input.setAttribute("type", isPassword ? "text" : "password");
                
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
    }

    // Áp dụng tính năng cho cả 2 ô mật khẩu
    setupPasswordToggle("matkhau", "togglePasswordBtn", "eyeIcon");
    setupPasswordToggle("nhaplaimatkhau", "toggleRePasswordBtn", "reEyeIcon");


    // 2. KIỂM TRA MẬT KHẨU KHI RỜI CON TRỎ (BLUR)
    const passwordInput = document.getElementById("matkhau");
    const repasswordInput = document.getElementById("nhaplaimatkhau");
    const error = document.getElementById("passwordError");
    const repasswordError = document.getElementById("repasswordError");

    const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,64}$/;

    if (passwordInput) {
        passwordInput.addEventListener("blur", function () {
            const val = passwordInput.value;

            if (val === "") {
                error.innerText = "";
                return;
            }

            if (!pattern.test(val)) {
                error.innerText = "Mật khẩu từ 8-64 ký tự, gồm chữ hoa, thường, số & ký tự đặc biệt (@$!%*?&).";
                error.className = "error-message text-danger";
            } else {
                error.innerText = "Mật khẩu hợp lệ!";
                error.className = "error-message text-success";
            }
        });
    }

    if (repasswordInput) {
        repasswordInput.addEventListener("blur", function () {
            if (repasswordInput.value === "") {
                repasswordError.innerText = "";
                return;
            }

            if (repasswordInput.value !== passwordInput.value) {
                repasswordError.innerText = "Mật khẩu xác nhận không trùng khớp!";
                repasswordError.className = "error-message text-danger";
            } else {
                repasswordError.innerText = "Mật khẩu trùng khớp!";
                repasswordError.className = "error-message text-success";
            }
        });
    }
});
</script>

</body>
</html>