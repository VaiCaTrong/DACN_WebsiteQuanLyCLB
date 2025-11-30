<?php include $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php'; ?>

<style>
    /* === CSS CHO BỐ CỤC 50/50 === */
    .login-container {
        display: flex;
        align-items: center;
        /* Căn giữa theo chiều dọc */
        justify-content: center;
        min-height: 50vh;
        /* Chiều cao tối thiểu 100% */
        padding-bottom: 20px;
        background-color: #ffffffff;
        /* Màu nền xám nhạt */
    }

    .login-box {
        max-width: 1000px;
        /* Chiều rộng tối đa của cả box */
        width: 100%;
        background: #d0cbcbff;
        border-radius: 1.5rem;
        /* Bo góc nhiều hơn */
        box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
        overflow: hidden;
        /* Quan trọng để giữ bo góc */
        display: flex;
        flex-wrap: wrap;
        /* Cho phép xuống hàng trên mobile */
    }

    /* 50% BÊN TRÁI: ẢNH LOGO */
    .login-image-side {
        flex: 1 1 50%;
        /* 50% */
        background: linear-gradient(135deg, #f386aaff 0%, #eb5f90ff 50%, #F48FB1 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem;
        min-height: 300px;
        /* Chiều cao tối thiểu cho mobile */
    }

    .logo-container {
        text-align: center;
        color: white;
    }

    .logo-container img {
        max-width: 200px;
        /* Kích thước logo */
        height: auto;
        margin-bottom: 1.5rem;
        border-radius: 50%;
        border: 4px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .logo-container h1 {
        font-weight: 700;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
    }

    .logo-container p {
        color: rgba(255, 255, 255, 0.85);
        /* Cho chữ trắng mờ hơn */
    }

    /* 50% BÊN PHẢI: FORM ĐĂNG NHẬP */
    .login-form-side {
        flex: 1 1 50%;
        /* 50% */
        padding: 3.5rem;
        /* Tăng padding */
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Ẩn ảnh bên trái trên màn hình nhỏ */
    @media (max-width: 767.98px) {
        .login-image-side {
            display: none;
            /* Ẩn cột ảnh */
        }

        .login-form-side {
            flex-basis: 100%;
            /* Form chiếm 100% */
            padding: 2rem;
        }

        .login-box {
            margin: 1rem;
            /* Thêm khoảng cách trên mobile */
        }
    }

    /* === CSS CŨ ĐÃ SỬA LẠI === */

    .form-outline .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(233, 30, 99, 0.25);
        border-color: #E91E63;
    }

    .btn-login {
        background-color: #E91E63;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        letter-spacing: 1px;
        transition: all 0.3s;
        width: 100%;
        display: inline-flex;
        /* Dùng flex để căn giữa spinner */
        align-items: center;
        justify-content: center;
    }

    .btn-login:hover {
        background-color: #C2185B;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(233, 30, 99, 0.4);
    }

    .btn-login:disabled {
        background-color: #cccccc;
        transform: none;
        box-shadow: none;
        cursor: not-allowed;
    }

    .social-icon {
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f8f9fa;
        color: #333;
        /* Thêm màu chữ */
    }

    .social-icon:hover {
        transform: scale(1.1);
        color: #E91E63 !important;
        background: #e9ecef;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 5px;
        /* Tăng vùng nhấn */
    }

    .password-container {
        position: relative;
    }

    .loading-spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s linear infinite;
        margin-right: 8px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .alert {
        border-radius: 0.75rem;
    }

    .form-control-lg {
        /* Đảm bảo input đủ lớn */
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }

    .p {
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }
</style>

<div class="login-container">
    <div class="container-fluid px-0">
        <div class="row g-0 justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="login-box">
                    <div class="login-image-side">
                        <div class="logo-container">
                            <img src="/webdacn_quanlyclb/uploads/LogoPMT.png" alt="Logo Hutech">
                            <h1 class="text-black">Welcome!</h1>
                            <h5 class="text-black-50">Hệ thống Quản lý Câu lạc bộ HUTECH</h5>
                        </div>
                    </div>

                    <div class="login-form-side">

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['logout_success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= htmlspecialchars($_SESSION['logout_success'], ENT_QUOTES, 'UTF-8') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['logout_success']); ?>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['register_success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= htmlspecialchars($_SESSION['register_success'], ENT_QUOTES, 'UTF-8') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['register_success']); ?>
                        <?php endif; ?>


                        <form id="loginForm" action="/webdacn_quanlyclb/account/login" method="post" novalidate>
                            <h2 class="fw-bold mb-4 text-center" style="color: #E91E63;">
                                <i class="fas fa-sign-in-alt me-2"></i>ĐĂNG NHẬP
                            </h2>

                            <div class="form-outline form-white mb-4 text-start">
                                <label class="form-label fw-medium" style="color: #E91E63;">
                                    <i class="fas fa-user me-2"></i>Tên đăng nhập
                                </label>
                                <input type="text" name="username" class="form-control form-control-lg"
                                    placeholder="Nhập tên đăng nhập" required
                                    minlength="3" maxlength="50"
                                    value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '' ?>" />
                                <div class="invalid-feedback text-start">
                                    Vui lòng nhập tên đăng nhập (ít nhất 3 ký tự)
                                </div>
                            </div>

                            <div class="form-outline form-white mb-4 text-start">
                                <label class="form-label fw-medium" style="color: #E91E63;">
                                    <i class="fas fa-lock me-2"></i>Mật khẩu
                                </label>
                                <div class="password-container">
                                    <input type="password" name="password" class="form-control form-control-lg"
                                        placeholder="Nhập mật khẩu" required
                                        minlength="6" maxlength="100"
                                        id="passwordInput" />
                                    <button type="button" class="password-toggle" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback text-start">
                                    Vui lòng nhập mật khẩu (ít nhất 6 ký tự)
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        <?= isset($_POST['remember']) ? 'checked' : '' ?>>
                                    <label class="form-check-label text-muted small" for="remember">
                                        Ghi nhớ đăng nhập
                                    </label>
                                </div>
                                <a class="text-muted small" href="/webdacn_quanlyclb/account/forgot_password" style="text-decoration: none;">
                                    <i class="fas fa-question-circle me-1"></i>Quên mật khẩu?
                                </a>
                            </div>

                            <button class="btn btn-login btn-lg" type="submit" id="submitBtn">
                                <span class="loading-spinner" id="loadingSpinner"></span>
                                <span class="login-text"><i class="fas fa-sign-in-alt me-2"></i>ĐĂNG NHẬP</span>
                            </button>

                            <div class="divider d-flex align-items-center my-4">
                                <hr class="flex-grow-1">
                                <p class="text-center mx-3 mb-0 text-muted">HOẶC</p>
                                <hr class="flex-grow-1">
                            </div>


                            <div class="border-top pt-3 mt-4">
                                <p class="mb-0 text-muted text-center">Bạn chưa có tài khoản?
                                    <a href="/webdacn_quanlyclb/account/register" style="color: #E91E63; font-weight: 600; text-decoration: none;">
                                        Đăng ký ngay
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('loginForm');
        const passwordInput = document.getElementById('passwordInput');
        const togglePassword = document.getElementById('togglePassword');
        const submitBtn = document.getElementById('submitBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const loginText = submitBtn.querySelector('.login-text'); // Lấy phần text

        // Toggle password visibility
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });

        // Form validation
        loginForm.addEventListener('submit', function(e) {
            if (!loginForm.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                // Show loading state
                submitBtn.disabled = true;
                loadingSpinner.style.display = 'inline-block';
                loginText.style.display = 'none'; // Ẩn text đi
            }

            loginForm.classList.add('was-validated');
        });

        // Real-time validation
        const inputs = loginForm.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        });

        // Clear validation on focus
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.classList.remove('is-invalid', 'is-valid');
            });
        });
    });
</script>

<?php include 'app/views/shares/footer.php'; ?>