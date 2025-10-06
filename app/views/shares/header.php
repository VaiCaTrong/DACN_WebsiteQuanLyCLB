<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/helpers/SessionHelper.php';
SessionHelper::start();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý CLB Hutech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            overflow-x: hidden;
            margin: 0;
            padding-top: 30px;
        }

        #sidebar-toggle {
            position: fixed;
            top: 10px;
            left: 20px;
            z-index: 1100;
            background-color: #E91E63;
            border: none;
            color: white;
            font-size: 24px;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: transform 0.3s ease;
            height: 40px;
        }

        #sidebar-toggle:hover {
            transform: scale(1.1);
            background-color: #C2185B;
        }

        #sidebar {
            min-height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: rgb(0, 0, 0);
            padding-top: 70px;
            transition: left 0.3s ease;
            z-index: 1000;

        }

        #sidebar.active {
            left: 0;
        }

        #sidebar .nav-link {
            color: white;
            padding: 10px 10px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        #sidebar .nav-link:hover {
            background-color: rgb(244, 243, 243);
        }

        #sidebar .nav-link i {
            margin-right: 10px;
        }

        #content {
            margin-left: 0;
            transition: margin-left 0.3s ease, max-width 0.3s ease;
            max-width: 100%;
        }

        #content.active {
            margin-left: 250px;
            max-width: calc(100% - 250px);
        }

        @media (max-width: 768px) {
            #content.active {
                margin-left: 0;
                max-width: 100%;
            }
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            height: 60px;
        }

        .navbar-brand {
            color: #E91E63 !important;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-left: 50px;
        }

        .navbar-brand img {
            height: 60px;
            margin-right: 5px;
        }

        .nav-link {
            color: #E91E63 !important;
        }

        .navbar-brand:hover,
        .nav-link:hover {
            color: #C2185B !important;
        }

        .dropdown-menu {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(233, 30, 99, 0.2);
        }

        .dropdown-item:hover {
            background-color: #FCE4EC;
            color: #E91E63 !important;
        }

        .container {
            padding-top: 0px;
        }

        /* Style cho modal xác nhận */
        .modal-content {
            border-radius: 15px;
            box-shadow: 0 0.5rem 1.5rem rgba(233, 30, 99, 0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-body {
            padding: 2rem;
            font-size: 1.1rem;
            color: #333;
        }

        .modal-footer .btn {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
        }

        .btn-confirm {
            background-color: #E91E63;
            color: white;
            border: none;
        }

        .btn-confirm:hover {
            background-color: #C2185B;
            transform: translateY(-2px);
            box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.2);
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            border: none;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.2);
        }

        /* Modal Backdrop Fix */
        .modal-backdrop {
            z-index: 1040 !important;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal {
            z-index: 1050 !important;
        }

        .btn-close-white {
            filter: invert(1) brightness(100%);
        }

        /* Modal đồng bộ với hệ thống */
        #logoutModal .modal-content {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0.5rem 1.5rem rgba(233, 30, 99, 0.3);
        }

        #logoutModal .modal-header {
            border-bottom: none;
            padding: 1.25rem 1.5rem;
        }

        #logoutModal .modal-body {
            padding: 1.5rem;
            font-size: 1.05rem;
            color: #5a5a5a;
        }

        #logoutModal .modal-footer {
            padding: 1rem 1.5rem;
            background-color: #f8f9fa;
            border-top: none;
        }

        #logoutModal .btn {
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        #logoutModal .btn-outline-secondary {
            border-color: #E91E63;
            color: #E91E63;
        }

        #logoutModal .btn-outline-secondary:hover {
            background-color: #FCE4EC;
        }

        #logoutModal .btn-danger {
            background-color: #E91E63;
            border: none;
        }

        #logoutModal .btn-danger:hover {
            background-color: #C2185B;
            transform: translateY(-2px);
            box-shadow: 0 0.2rem 0.5rem rgba(233, 30, 99, 0.3);
        }

        /* Loading Spinner Styles */
        .spinner-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            /*rgba(255, 255, 255, 0.8)*/
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        /* Logo hình tròn */
        .logo-circle {
            position: absolute;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #FCE4EC;
            box-shadow: 0 0 15px rgba(233, 30, 99, 0.1);
            z-index: 2;
        }

        .rounded-logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Spinner xoay xung quanh logo */
        .spinner {
            width: 110px;
            height: 110px;
            border: 5px solid rgba(233, 30, 99, 0.1);
            border-top: 5px solid #E91E63;
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
            position: relative;
            z-index: 1;
        }

        .logo-circle {
            width: 70px;
            height: 70px;
        }

        .spinner {
            width: 90px;
            height: 90px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Hiệu ứng fade-in cho nội dung sau khi load */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        body.loaded #loading-spinner {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease;
        }


        /* Hiệu ứng xoay cho nút khi click */
        .btn-loading {
            position: relative;
        }

        .btn-loading::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            display: none;
        }

        .btn-loading.loading {
            color: transparent !important;
            pointer-events: none;
        }

        .btn-loading.loading::after {
            display: block;
        }
    </style>
</head>

<body>
    <div id="loading-spinner" class="spinner-container">
        <div class="logo-circle">
            <img src="/webdacn_quanlyclb/uploads/NTD001545.jpg" alt="Logo" class="rounded-logo">
        </div>
        <div class="spinner"></div>
    </div>
    <button id="sidebar-toggle" type="button">
        <i class="fas fa-bars"></i>
    </button>

    <div id="sidebar" class="d-flex flex-column">
        <h5 class="text-center text-white mb-4">Menu</h5>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="/webdacn_quanlyclb"><i class="fas fa-home"></i> Trang chủ</a>
            </li>
            <?php if (SessionHelper::isLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/webdacn_quanlyclb/account/profile"><i class="fas fa-user"></i> Thông tin cá nhân</a>
                </li>
            <?php endif; ?>
            <?php if (SessionHelper::isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/webdacn_quanlyclb/account"><i class="fas fa-cogs"></i> Quản trị</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/webdacn_quanlyclb/team/requests"><i class="fas fa-check-circle"></i> Duyệt yêu cầu</a>
                </li>
            <?php endif; ?>
            <?php if (SessionHelper::isStaff()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/webdacn_quanlyclb/team/userjoin"><i class="fas fa-check-circle"></i> Duyệt yêu cầu thành viên</a>
                </li>
            <?php endif; ?>
            <?php if (SessionHelper::isLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/webdacn_quanlyclb/importantday/"><i class="fas fa-calendar-alt"></i> Lịch thời gian</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="/webdacn_quanlyclb/team"><i class="fas fa-users"></i> Danh sách Câu lạc Bộ</a>
            </li>
            <?php if (SessionHelper::isLoggedIn()): ?>
                <li>
                    <a class="nav-link dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fas fa-sign-out-alt me-2" style="width: 20px; text-align: center;"></i>
                        <span>Đăng xuất</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="/webdacn_quanlyclb">
                    <img src="/webdacn_quanlyclb/uploads/logo-hutech-1.png" alt="Logo 1" class="logo">
                    <img src="/webdacn_quanlyclb/uploads/chuan.png" alt="Logo 2" class="logo">
                    Câu lạc bộ Hutech
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav align-items-center">
                        <li class="header-notification">
                            <a class="nav-link" href="/webdacn_quanlyclb/account/notifications"><i class="fas fa-bell"></i> Thông báo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/webdacn_quanlyclb"><i class="fas fa-home me-1"></i>Trang chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/webdacn_quanlyclb/team"><i class="fas fa-users me-1"></i> Câu lạc bộ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/webdacn_quanlyclb/friend/searchFriends">
                                <i class="fas fa-user-friends me-1"></i>Bạn bè
                            </a>
                        </li>
                        <?php if (SessionHelper::isLoggedIn()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-1"></i> Tài khoản
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="/webdacn_quanlyclb/account/profile"><i class="fas fa-user-edit me-2"></i> Profile</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="/webdacn_quanlyclb/account/logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>

                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/webdacn_quanlyclb/account/login"><i class="fas fa-sign-in-alt me-1"></i> Đăng nhập</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
        </nav>

        <!-- Hiển thị thông báo -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <!-- Modal xác nhận đăng xuất -->
        <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <!-- Header với gradient giống các thành phần khác -->
                    <div class="modal-header" style="background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%);">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Xác nhận đăng xuất
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Body thống nhất với các modal khác -->
                    <div class="modal-body py-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-question-circle text-warning me-3 fs-4"></i>
                            <p class="mb-0">Bạn có chắc chắn muốn đăng xuất?</p>
                        </div>
                    </div>

                    <!-- Footer với button đồng bộ -->
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Hủy
                        </button>
                        <a href="/webdacn_quanlyclb/account/logout" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <main class="container my-4">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                // Sidebar toggle logic (giữ nguyên)
                document.addEventListener('DOMContentLoaded', function() {
                    // Ẩn spinner sau khi tất cả tài nguyên được tải
                    window.addEventListener('load', function() {
                        setTimeout(function() {
                            document.body.classList.add('loaded');

                            document.getElementById('loading-spinner').style.display = 'none';
                        }, 700); // Thời gian hiển thị tối thiểu
                    });
                    const sidebar = document.getElementById('sidebar');
                    const content = document.getElementById('content');
                    const isSidebarActive = localStorage.getItem('sidebarActive') === 'true';

                    sidebar.style.transition = 'none';
                    content.style.transition = 'none';

                    if (isSidebarActive) {
                        sidebar.classList.add('active');
                        content.classList.add('active');
                    }

                    setTimeout(() => {
                        sidebar.style.transition = 'left 0.3s ease';
                        content.style.transition = 'margin-left 0.3s ease, max-width 0.3s ease';
                    }, 0);
                });

                document.getElementById('sidebar-toggle').addEventListener('click', function() {
                    const sidebar = document.getElementById('sidebar');
                    const content = document.getElementById('content');
                    const isActive = sidebar.classList.toggle('active');
                    content.classList.toggle('active');
                    localStorage.setItem('sidebarActive', isActive);
                });

                document.addEventListener('click', function(event) {
                    const sidebar = document.getElementById('sidebar');
                    const toggleButton = document.getElementById('sidebar-toggle');
                    const content = document.getElementById('content');

                    if (sidebar.classList.contains('active') && !sidebar.contains(event.target) && !toggleButton.contains(event.target)) {
                        sidebar.classList.remove('active');
                        content.classList.remove('active');
                        localStorage.setItem('sidebarActive', false);
                    }
                });
            </script>
        </main>
    </div>
</body>

</html>