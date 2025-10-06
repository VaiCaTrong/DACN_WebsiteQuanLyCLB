<?php include 'app/views/shares/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <form method="post" action="/webdacn_quanlyclb/account/save" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="card shadow-lg border-0" style="border-radius: 15px; border-top: 5px solid #E91E63;">
                    <div class="card-body p-4 p-md-5">

                        <!-- Hiển thị thông báo lỗi -->
                        <?php if (isset($errors) && !empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Có lỗi xảy ra!</strong>
                                </div>
                                <ul class="mt-2 mb-0 ps-3">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <h3 class="text-center mb-4" style="color: #E91E63;">
                            <i class="fas fa-user-plus me-2"></i>ĐĂNG KÝ TÀI KHOẢN
                        </h3>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="username" name="username" 
                                        placeholder="Tên đăng nhập" required
                                        pattern="[a-zA-Z0-9_]{4,20}"
                                        title="Tên đăng nhập từ 4-20 ký tự (chữ, số hoặc gạch dưới)"
                                        style="border-color: #E91E63;">
                                    <label for="username" style="color: #E91E63;">
                                        <i class="fas fa-user me-2"></i>Tên đăng nhập *
                                    </label>
                                    <div class="invalid-feedback">
                                        Vui lòng nhập tên đăng nhập hợp lệ (4-20 ký tự)
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="fullname" name="fullname" 
                                        placeholder="Họ và tên" required
                                        style="border-color: #E91E63;">
                                    <label for="fullname" style="color: #E91E63;">
                                        <i class="fas fa-id-card me-2"></i>Họ và tên *
                                    </label>
                                    <div class="invalid-feedback">
                                        Vui lòng nhập họ và tên
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating position-relative">
                                    <input type="password" class="form-control" id="password" name="password" 
                                        placeholder="Mật khẩu" required
                                        minlength="6"
                                        style="border-color: #E91E63;">
                                    <label for="password" style="color: #E91E63;">
                                        <i class="fas fa-lock me-2"></i>Mật khẩu *
                                    </label>
                                    <div class="invalid-feedback">
                                        Mật khẩu phải có ít nhất 6 ký tự
                                    </div>
                                    <span toggle="#password" class="fa fa-fw fa-eye-slash field-icon toggle-password" 
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;"></span>
                                </div>
                                <small class="text-muted">Mật khẩu phải có ít nhất 6 ký tự</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating position-relative">
                                    <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" 
                                        placeholder="Xác nhận mật khẩu" required
                                        style="border-color: #E91E63;">
                                    <label for="confirmpassword" style="color: #E91E63;">
                                        <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu *
                                    </label>
                                    <div class="invalid-feedback">
                                        Vui lòng xác nhận mật khẩu
                                    </div>
                                    <span toggle="#confirmpassword" class="fa fa-fw fa-eye-slash field-icon toggle-password" 
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" 
                                        placeholder="Email"
                                        style="border-color: #E91E63;">
                                    <label for="email" style="color: #E91E63;">
                                        <i class="fas fa-envelope me-2"></i>Email
                                    </label>
                                    <div class="invalid-feedback">
                                        Vui lòng nhập email hợp lệ
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                        placeholder="Số điện thoại"
                                        pattern="[0-9]{10,11}"
                                        title="Số điện thoại 10-11 chữ số"
                                        style="border-color: #E91E63;">
                                    <label for="phone" style="color: #E91E63;">
                                        <i class="fas fa-phone me-2"></i>Số điện thoại
                                    </label>
                                    <div class="invalid-feedback">
                                        Số điện thoại phải có 10-11 chữ số
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="avatar" class="form-label" style="color: #E91E63;">
                                <i class="fas fa-image me-2"></i>Ảnh đại diện (tối đa 2MB)
                            </label>
                            <input class="form-control" type="file" id="avatar" name="avatar" accept="image/*">
                            <div class="invalid-feedback">
                                Chỉ chấp nhận file ảnh (JPG, PNG, GIF)
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-floating">
                                <select class="form-select" id="role" name="role" style="border-color: #E91E63;">
                                    <option value="user" selected>Người dùng thường</option>
                                    <option value="admin">Quản trị viên</option>
                                </select>
                                <label for="role" style="color: #E91E63;">
                                    <i class="fas fa-user-tag me-2"></i>Loại tài khoản
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill text-white fw-bold py-3"
                                style="background-color: #E91E63; border: none;">
                                <i class="fas fa-user-plus me-2"></i>ĐĂNG KÝ NGAY
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-1">Đã có tài khoản?</p>
                            <a href="/webdacn_quanlyclb/account/login" class="btn btn-outline-danger rounded-pill px-4">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập ngay
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
</div>
<?php include 'app/views/shares/footer.php'; ?>


<style>
    .form-control:focus,
    .form-select:focus {
        border-color: #FF4081;
        box-shadow: 0 0 0 0.25rem rgba(233, 30, 99, 0.25);
    }

    .alert {
        border-radius: 10px;
        border-left: 4px solid #E91E63;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .field-icon {
        color: #E91E63;
    }

    .toggle-password:hover {
        color: #C2185B;
    }
</style>

<script>
    // Xử lý hiển thị/ẩn mật khẩu
    document.querySelectorAll('.toggle-password').forEach(function(icon) {
        icon.addEventListener('click', function() {
            const target = document.querySelector(this.getAttribute('toggle'));
            const type = target.getAttribute('type') === 'password' ? 'text' : 'password';
            target.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
    });

    // Xử lý validation form
    (function() {
        'use strict';
        
        const forms = document.querySelectorAll('.needs-validation');
        
        Array.from(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
                
                // Kiểm tra mật khẩu trùng khớp
                const password = document.getElementById('password');
                const confirmPassword = document.getElementById('confirmpassword');
                
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Mật khẩu không khớp');
                    confirmPassword.classList.add('is-invalid');
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }, false);
        });
    })();
</script>