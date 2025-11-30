<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/helpers/SessionHelper.php';
SessionHelper::start();
SessionHelper::requireLogin();

// Giả định notifications được lấy từ AccountController hoặc model
$notifications = $notifications ?? [];
$user_id = SessionHelper::getUserId();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo Của Tôi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #E91E63;
            --primary-light: #FCE4EC;
            --primary-dark: #C2185B;
            --success: #00b894;
            --danger: #d63031;
            --warning: #fdcb6e;
            --info: #0984e3;
            --bg-light: #f8f9fa;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --border-radius: 12px;
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
            margin: 0;
            padding-top: 60px;
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        /* Style cho trang thông báo */
        .notification-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1rem 0;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .notification-header h1 {
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .notification-column {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .column-header {
            padding: 1rem;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .account-column .column-header {
            background: linear-gradient(135deg, var(--info) 0%, #74b9ff 100%);
        }

        .team-column .column-header {
            background: linear-gradient(135deg, var(--success) 0%, #55efc4 100%);
        }

        .friend-column .column-header {
            background: linear-gradient(135deg, var(--warning) 0%, #ffeaa7 100%);
        }

        .other-column .column-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        }

        .column-title {
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }

        .column-count {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }

        .notification-list {
            max-height: 500px;
            overflow-y: auto;
            padding: 0.5rem;
        }

        .notification-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 0.75rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: white;
        }

        .notification-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .notification-header-card {
            padding: 0.75rem 1rem 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .notification-title {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .notification-body {
            padding: 0 1rem 0.75rem;
            color: var(--text-dark);
            font-size: 0.85rem;
        }

        .notification-time {
            font-size: 0.7rem;
            color: var(--text-light);
            margin-top: 0.5rem;
        }

        .notification-link {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.2s ease;
            font-size: 0.8rem;
            display: inline-block;
            margin-top: 0.5rem;
        }

        .notification-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .delete-btn {
            color: var(--danger);
            font-size: 0.8rem;
            padding: 0.2rem 0.5rem;
            border: none;
            background: none;
            cursor: pointer;
            transition: color 0.2s ease;
            border-radius: 4px;
        }

        .delete-btn:hover {
            background-color: rgba(214, 48, 49, 0.1);
        }

        .delete-all-btn {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .delete-all-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-light);
        }

        .empty-state i {
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .friend-request-btn {
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .notification-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
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
                        <li class="nav-item">
                            <a class="nav-link" href="/webdacn_quanlyclb/chat">
                                <i class="fas fa-envelope me-1"></i>Tin nhắn
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

        <!-- Nội dung trang thông báo -->
        <div class="notification-header">
            <div class="container">
                <h1><i class="fas fa-bell me-2"></i>Thông Báo Của Tôi</h1>
                <button class="delete-all-btn" onclick="confirmDeleteAll()"><i class="fas fa-trash me-1"></i>Xóa tất cả</button>
            </div>
        </div>

        <div class="container">
            <div class="notification-container">
                <!-- Cột thông báo tài khoản -->
                <div class="notification-column account-column">
                    <div class="column-header">
                        <div class="column-title">
                            <i class="fas fa-user-circle me-2"></i>Tài khoản
                            <span class="column-count" id="account-count">0</span>
                        </div>
                    </div>
                    <div class="notification-list" id="account-notifications">
                        <!-- Thông báo tài khoản sẽ được thêm ở đây bằng JavaScript -->
                    </div>
                </div>

                <!-- Cột thông báo CLB -->
                <div class="notification-column team-column">
                    <div class="column-header">
                        <div class="column-title">
                            <i class="fas fa-users me-2"></i>Câu lạc bộ
                            <span class="column-count" id="team-count">0</span>
                        </div>
                    </div>
                    <div class="notification-list" id="team-notifications">
                        <!-- Thông báo CLB sẽ được thêm ở đây bằng JavaScript -->
                    </div>
                </div>

                <!-- Cột thông báo bạn bè -->
                <div class="notification-column friend-column">
                    <div class="column-header">
                        <div class="column-title">
                            <i class="fas fa-user-friends me-2"></i>Bạn bè
                            <span class="column-count" id="friend-count">0</span>
                        </div>
                    </div>
                    <div class="notification-list" id="friend-notifications">
                        <!-- Thông báo bạn bè sẽ được thêm ở đây bằng JavaScript -->
                    </div>
                </div>

                <!-- Cột thông báo khác -->
                <div class="notification-column other-column">
                    <div class="column-header">
                        <div class="column-title">
                            <i class="fas fa-ellipsis-h me-2"></i>Khác
                            <span class="column-count" id="other-count">0</span>
                        </div>
                    </div>
                    <div class="notification-list" id="other-notifications">
                        <!-- Thông báo khác sẽ được thêm ở đây bằng JavaScript -->
                    </div>
                </div>
            </div>
        </div>

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

        <!-- Modal xác nhận xóa -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Bạn có chắc chắn muốn xóa thông báo này?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Xóa</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal xóa tất cả -->
        <div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteAllModalLabel">Xác nhận xóa tất cả</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Bạn có chắc chắn muốn xóa tất cả thông báo?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteAllBtn">Xóa tất cả</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal xác nhận yêu cầu kết bạn -->
        <div class="modal fade" id="friendRequestModal" tabindex="-1" aria-labelledby="friendRequestModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="friendRequestModalLabel">Xác nhận yêu cầu kết bạn</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Bạn có muốn chấp nhận yêu cầu kết bạn này?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-success" id="acceptFriendBtn">Chấp nhận</button>
                        <button type="button" class="btn btn-danger" id="rejectFriendBtn">Từ chối</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Dữ liệu mẫu - thay thế bằng dữ liệu thực từ PHP
            const notifications = <?php echo json_encode($notifications ?? []); ?>;
            const user_id = <?php echo $user_id ?? 0; ?>;

            // Sidebar toggle logic
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

                // Hiển thị thông báo
                displayNotifications();
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

            // Phân loại thông báo theo đường dẫn
            function categorizeNotifications(notifications) {
                const categories = {
                    account: [],
                    team: [],
                    friend: [],
                    other: []
                };

                notifications.forEach(notification => {
                    const link = notification.link || '';
                    
                    if (link.includes('/account/')) {
                        categories.account.push(notification);
                    } else if (link.includes('/Team/') || link.includes('/team/')) {
                        categories.team.push(notification);
                    } else if (link.includes('/friend/')) {
                        categories.friend.push(notification);
                    } else {
                        categories.other.push(notification);
                    }
                });

                return categories;
            }

            // Hiển thị thông báo theo danh mục
            function displayNotifications() {
                const categorized = categorizeNotifications(notifications);
                
                // Cập nhật số lượng
                document.getElementById('account-count').textContent = categorized.account.length;
                document.getElementById('team-count').textContent = categorized.team.length;
                document.getElementById('friend-count').textContent = categorized.friend.length;
                document.getElementById('other-count').textContent = categorized.other.length;
                
                // Hiển thị thông báo tài khoản
                const accountContainer = document.getElementById('account-notifications');
                if (categorized.account.length === 0) {
                    accountContainer.innerHTML = '<div class="empty-state"><i class="fas fa-bell-slash fa-2x"></i><p>Không có thông báo</p></div>';
                } else {
                    accountContainer.innerHTML = categorized.account.map(notification => createNotificationCard(notification)).join('');
                }
                
                // Hiển thị thông báo CLB
                const teamContainer = document.getElementById('team-notifications');
                if (categorized.team.length === 0) {
                    teamContainer.innerHTML = '<div class="empty-state"><i class="fas fa-bell-slash fa-2x"></i><p>Không có thông báo</p></div>';
                } else {
                    teamContainer.innerHTML = categorized.team.map(notification => createNotificationCard(notification)).join('');
                }
                
                // Hiển thị thông báo bạn bè
                const friendContainer = document.getElementById('friend-notifications');
                if (categorized.friend.length === 0) {
                    friendContainer.innerHTML = '<div class="empty-state"><i class="fas fa-bell-slash fa-2x"></i><p>Không có thông báo</p></div>';
                } else {
                    friendContainer.innerHTML = categorized.friend.map(notification => createNotificationCard(notification)).join('');
                }
                
                // Hiển thị thông báo khác
                const otherContainer = document.getElementById('other-notifications');
                if (categorized.other.length === 0) {
                    otherContainer.innerHTML = '<div class="empty-state"><i class="fas fa-bell-slash fa-2x"></i><p>Không có thông báo</p></div>';
                } else {
                    otherContainer.innerHTML = categorized.other.map(notification => createNotificationCard(notification)).join('');
                }
                
                // Thêm sự kiện cho các nút xóa và kết bạn
                addEventListeners();
            }

            // Tạo thẻ thông báo
            function createNotificationCard(notification) {
                const time = new Date(notification.created_at);
                const formattedTime = `${time.getDate().toString().padStart(2, '0')}/${(time.getMonth()+1).toString().padStart(2, '0')}/${time.getFullYear()} ${time.getHours().toString().padStart(2, '0')}:${time.getMinutes().toString().padStart(2, '0')}`;
                
                let friendButton = '';
                if (notification.friend_id) {
                    friendButton = `<button class="btn btn-sm btn-primary friend-request-btn open-friend-request-modal" 
                                        data-friend-id="${notification.friend_id}" 
                                        data-notification-id="${notification.id}">
                                    Xem yêu cầu kết bạn
                                </button>`;
                }
                
                let linkElement = '';
                if (notification.link) {
                    linkElement = `<a href="${notification.link}" class="notification-link">Xem chi tiết <i class="fas fa-arrow-right ms-1"></i></a>`;
                }
                
                return `
                    <div class="notification-card" data-id="${notification.id}">
                        <div class="notification-header-card">
                            <span class="notification-title">${notification.title}</span>
                            <button class="delete-btn" onclick="confirmDelete(${notification.id})"><i class="fas fa-trash"></i></button>
                        </div>
                        <div class="notification-body">
                            <p class="mb-2">${notification.message}</p>
                            ${friendButton}
                            ${linkElement}
                            <div class="notification-time"><i class="fas fa-clock me-1"></i>${formattedTime}</div>
                        </div>
                    </div>
                `;
            }

            // Thêm sự kiện cho các nút
            function addEventListeners() {
                // Mở modal xác nhận yêu cầu kết bạn
                document.querySelectorAll('.open-friend-request-modal').forEach(button => {
                    button.addEventListener('click', function() {
                        currentFriendId = this.dataset.friendId;
                        currentNotificationId = this.dataset.notificationId;
                        const modal = new bootstrap.Modal(document.getElementById('friendRequestModal'));
                        modal.show();
                    });
                });
            }

            let currentNotificationId = null;
            let currentFriendId = null;

            function confirmDelete(notificationId) {
                currentNotificationId = notificationId;
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            }

            function confirmDeleteAll() {
                const modal = new bootstrap.Modal(document.getElementById('deleteAllModal'));
                modal.show();
            }

            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                if (currentNotificationId) {
                    fetch('/webdacn_quanlyclb/account/deleteNotification', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': '<?php echo htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                        },
                        body: JSON.stringify({ notification_id: currentNotificationId, user_id: user_id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                        modal.hide();
                        if (data.success) {
                            const card = document.querySelector(`.notification-card[data-id="${currentNotificationId}"]`);
                            if (card) card.remove();
                            // Cập nhật lại số lượng và hiển thị
                            displayNotifications();
                        } else {
                            alert('Có lỗi xảy ra khi xóa thông báo.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi gửi yêu cầu.');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                        modal.hide();
                    });
                }
            });

            document.getElementById('confirmDeleteAllBtn').addEventListener('click', function() {
                fetch('/webdacn_quanlyclb/account/deleteAllNotifications', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                    },
                    body: JSON.stringify({ user_id: user_id })
                })
                .then(response => response.json())
                .then(data => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteAllModal'));
                    modal.hide();
                    if (data.success) {
                        // Xóa tất cả thông báo khỏi giao diện
                        document.querySelectorAll('.notification-card').forEach(card => card.remove());
                        // Cập nhật lại số lượng và hiển thị
                        displayNotifications();
                    } else {
                        alert('Có lỗi xảy ra khi xóa tất cả thông báo.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi gửi yêu cầu.');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteAllModal'));
                    modal.hide();
                });
            });

            // Xử lý chấp nhận kết bạn
            document.getElementById('acceptFriendBtn').addEventListener('click', function() {
                if (currentFriendId && currentNotificationId) {
                    fetch('/webdacn_quanlyclb/friend/acceptFriend', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': '<?php echo htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                        },
                        body: JSON.stringify({ friend_id: currentFriendId, notification_id: currentNotificationId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('friendRequestModal'));
                        modal.hide();
                        if (data.success) {
                            alert('Đã chấp nhận kết bạn!');
                            const card = document.querySelector(`.notification-card[data-id="${currentNotificationId}"]`);
                            if (card) card.remove();
                            // Cập nhật lại số lượng và hiển thị
                            displayNotifications();
                        } else {
                            alert('Lỗi khi chấp nhận.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi chấp nhận yêu cầu kết bạn.');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('friendRequestModal'));
                        modal.hide();
                    });
                }
            });

            // Xử lý từ chối kết bạn
            document.getElementById('rejectFriendBtn').addEventListener('click', function() {
                if (currentFriendId && currentNotificationId) {
                    fetch('/webdacn_quanlyclb/friend/rejectFriend', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': '<?php echo htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                        },
                        body: JSON.stringify({ friend_id: currentFriendId, notification_id: currentNotificationId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('friendRequestModal'));
                        modal.hide();
                        if (data.success) {
                            alert('Đã từ chối kết bạn!');
                            const card = document.querySelector(`.notification-card[data-id="${currentNotificationId}"]`);
                            if (card) card.remove();
                            // Cập nhật lại số lượng và hiển thị
                            displayNotifications();
                        } else {
                            alert('Lỗi khi từ chối.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi từ chối yêu cầu kết bạn.');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('friendRequestModal'));
                        modal.hide();
                    });
                }
            });
        </script>
    </div>
</body>

</html>