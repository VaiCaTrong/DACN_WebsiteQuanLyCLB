<?php include 'app/views/shares/header.php'; ?>

<style>
    .gradient-custom {
        background: linear-gradient(135deg, #FCE4EC 0%, #F8BBD0 50%, #F48FB1 100%);
    }
    .container {
        max-width: 1200px;
        padding: 0rem 1rem;
        transition: margin-left 0.3s ease;
    }
    .login-card {
        border-radius: 1rem;
        border-top: 5px solid #E91E63;
        box-shadow: 0 10px 20px rgba(233, 30, 99, 0.2);
        transition: transform 0.3s ease;
    }
    
    .login-card:hover {
        transform: translateY(-5px);
    }
    
    .form-outline .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(233, 30, 99, 0.25);
        border-color: #E91E63;
    }
    
    .btn-login {
        background-color: #E91E63;
        color: white;
        border: none;
        padding: 10px 30px;
        border-radius: 50px;
        font-weight: 600;
        letter-spacing: 1px;
        transition: all 0.3s;
    }
    
    .btn-login:hover {
        background-color: #C2185B;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(233, 30, 99, 0.4);
    }
    
    .social-icon {
        transition: all 0.3s;
    }
    
    .social-icon:hover {
        transform: scale(1.2);
        color: #E91E63 !important;
    }
</style>

<section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <!-- Hiển thị thông báo lỗi -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Hiển thị thông báo đăng xuất -->
                <?php if (isset($_SESSION['logout_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= $_SESSION['logout_success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['logout_success']); ?>
                <?php endif; ?>
                
                <!-- Hiển thị thông báo đăng ký thành công -->
                <?php if (isset($_SESSION['register_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= $_SESSION['register_success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['register_success']); ?>
                <?php endif; ?>

                <div class="card login-card">
                    <div class="card-body p-5 text-center">
                        <form action="/webdacn_quanlyclb/account/login" method="post">
                            <div class="mb-md-5 mt-md-4 pb-5">
                                <h2 class="fw-bold mb-4 text-uppercase" style="color: #E91E63;">
                                    <i class="fas fa-sign-in-alt me-2"></i>ĐĂNG NHẬP
                                </h2>
                                
                                <div class="form-outline form-white mb-4">
                                    <label class="form-label" style="color: #E91E63;">
                                        <i class="fas fa-user me-2"></i>Tên đăng nhập
                                    </label>
                                    <input type="text" name="username" class="form-control form-control-lg" 
                                           placeholder="Nhập tên đăng nhập" required />
                                </div>
                                
                                <div class="form-outline form-white mb-4">
                                    <label class="form-label" style="color: #E91E63;">
                                        <i class="fas fa-lock me-2"></i>Mật khẩu
                                    </label>
                                    <input type="password" name="password" class="form-control form-control-lg" 
                                           placeholder="Nhập mật khẩu" required />
                                </div>
                                
                                <p class="small mb-4">
                                    <a class="text-muted" href="/webdacn_quanlyclb/account/forgot_password" style="text-decoration: none;">
                                        <i class="fas fa-question-circle me-1"></i>Quên mật khẩu?
                                    </a>
                                </p>
                                
                                <button class="btn btn-login btn-lg px-5" type="submit">
                                    <i class="fas fa-sign-in-alt me-2"></i>ĐĂNG NHẬP
                                </button>
                                
                                <div class="divider d-flex align-items-center my-4">
                                    <p class="text-center mx-3 mb-0" style="color: #E91E63;">HOẶC</p>
                                </div>
                                
                                <div class="d-flex justify-content-center text-center mt-2">
                                    <a href="#!" class="text-dark social-icon mx-3">
                                        <i class="fab fa-facebook-f fa-lg"></i>
                                    </a>
                                    <a href="#!" class="text-dark social-icon mx-3">
                                        <i class="fab fa-google fa-lg"></i>
                                    </a>
                                    <a href="#!" class="text-dark social-icon mx-3">
                                        <i class="fab fa-twitter fa-lg"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <div>
                                <p class="mb-0">Bạn chưa có tài khoản? 
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
    <?php include 'app/views/shares/footer.php'; ?>
</section>