<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/helpers/SessionHelper.php';
SessionHelper::start();
if (isset($show_banner) && $show_banner) {
    if (!class_exists('AdvertisingBannerModel')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/models/AdvertisingBannerModel.php';
    }
    if (!class_exists('EventModel')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/models/EventModel.php';
    }
    $bannerModel = new AdvertisingBannerModel();
    $banners = $bannerModel->getAllActiveBanners();
    $eventModel = new EventModel();
    $activeEvents = $eventModel->getActiveEvents(3); 
}
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
        body { min-height: 100vh; overflow-x: hidden; margin: 0; padding-top: 60px; }
        #sidebar-toggle { position: fixed; top: 10px; left: 20px; z-index: 1100; background-color: #E91E63; border: none; color: white; font-size: 24px; padding: 5px 10px; cursor: pointer; border-radius: 5px; transition: transform 0.3s ease; height: 40px; }
        #sidebar-toggle:hover { transform: scale(1.1); background-color: #C2185B; }
        #sidebar { min-height: 100vh; width: 250px; position: fixed; top: 0; left: -250px; background-color: rgb(0, 0, 0); padding-top: 70px; transition: left 0.3s ease; z-index: 1000; }
        #sidebar.active { left: 0; }
        #sidebar .nav-link { color: white; padding: 10px 10px; font-size: 16px; transition: background-color 0.3s ease; }
        #sidebar .nav-link:hover { background-color: rgb(244, 243, 243); }
        #sidebar .nav-link i { margin-right: 10px; }
        #content { margin-left: 0; transition: margin-left 0.3s ease, max-width 0.3s ease; max-width: 100%; }
        #content.active { margin-left: 250px; max-width: calc(100% - 250px); }
        @media (max-width: 768px) { #content.active { margin-left: 0; max-width: 100%; } }
        .navbar { position: fixed; top: 0; left: 0; width: 100%; background-color: #fff; box-shadow: 0 2px 5px rgba(31, 29, 29, 0.1); z-index: 1050; height: 60px; }
        .navbar-brand { color: #E91E63 !important; display: flex; align-items: center; gap: 10px; padding-left: 50px; }
        .navbar-brand img { height: 60px; margin-right: 5px; }
        .nav-link { color: #E91E63 !important; }
        .navbar-brand:hover, .nav-link:hover { color: #C2185B !important; }
        .dropdown-menu { border-radius: 10px; border: none; box-shadow: 0 5px 15px rgba(233, 30, 99, 0.2); }
        .dropdown-item:hover { background-color: #FCE4EC; color: #E91E63 !important; }
        .container { padding-top: 0px; }
        /* Style Modal */
        .modal-content { border-radius: 15px; box-shadow: 0 0.5rem 1.5rem rgba(233, 30, 99, 0.3); }
        .modal-header { background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%); color: white; border-top-left-radius: 15px; border-top-right-radius: 15px; }
        .modal-title { font-weight: 600; }
        .modal-body { padding: 2rem; font-size: 1.1rem; color: #333; }
        .modal-footer .btn { padding: 0.5rem 1.5rem; font-weight: 500; border-radius: 8px; }
        .btn-confirm { background-color: #E91E63; color: white; border: none; }
        .btn-confirm:hover { background-color: #C2185B; transform: translateY(-2px); box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.2); }
        .btn-cancel { background-color: #6c757d; color: white; border: none; }
        .btn-cancel:hover { background-color: #5a6268; transform: translateY(-2px); box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.2); }
        .modal-backdrop { z-index: 1040 !important; background-color: rgba(0, 0, 0, 0.5); }
        .modal { z-index: 1050 !important; }
        .btn-close-white { filter: invert(1) brightness(100%); }
        /* Loading Spinner */
        .spinner-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: white; display: flex; justify-content: center; align-items: center; z-index: 9999; transition: opacity 0.5s ease; }
        .logo-circle { position: absolute; width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 3px solid #FCE4EC; box-shadow: 0 0 15px rgba(233, 30, 99, 0.1); z-index: 2; }
        .rounded-logo { width: 100%; height: 100%; object-fit: cover; }
        .spinner { width: 110px; height: 110px; border: 5px solid rgba(233, 30, 99, 0.1); border-top: 5px solid #E91E63; border-radius: 50%; animation: spin 1.2s linear infinite; position: relative; z-index: 1; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        body.loaded #loading-spinner { opacity: 0; pointer-events: none; transition: opacity 0.5s ease; }
        /* CSS Banner & Event */
        #advertisingCarousel { margin-top: 20px; margin-bottom: 1.5rem; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); background-color: #f0f0f0; max-width: 1200px; margin-left: auto; margin-right: auto; border: 3px solid #E91E63; }
        .carousel-item { height: 400px; background-color: #e9ecef; }
        .carousel-item img { width: 100%; height: 100%; object-fit: cover; object-position: center; display: block; }
        .event-sidebar-box { position: fixed; top: 80px; right: 20px; width: 200px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1); padding: 15px; z-index: 1000; border-top: 4px solid #E91E63; max-height: calc(100vh - 200px); overflow-y: auto; }
        .event-sidebar-title { font-size: 1rem; font-weight: 600; color: #E91E63; text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        .floating-event-container { display: flex; flex-direction: column; align-items: center; gap: 15px; width: 100%; }
        .floating-event-item { width: 100px; height: 100px; border-radius: 50%; border-width: 4px; border-style: solid; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); overflow: hidden; transition: transform 0.3s ease; flex-shrink: 0; }
        .floating-event-item:hover { transform: scale(1.1); }
        .floating-event-item a { display: block; position: relative; height: 100%; }
        .floating-event-item img { width: 100%; height: 100%; object-fit: cover; }
        .event-title-overlay { position: absolute; bottom: 0; left: 0; width: 100%; padding: 8px 5px; font-size: 0.8rem; font-weight: 600; text-align: center; background-color: rgba(0, 0, 0, 0.7); color: white; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .floating-event-item:hover .event-title-overlay { opacity: 1; visibility: visible; }
        .floating-event-item.category-clb { border-color: #198754; box-shadow: 0 5px 15px rgba(25, 135, 84, 0.5); }
        .floating-event-item.category-clb .event-title-overlay { background-color: rgba(25, 135, 84, 0.9); }
        .floating-event-item.category-truong { border-color: #dc3545; box-shadow: 0 5px 15px rgba(220, 53, 69, 0.5); }
        .floating-event-item.category-truong .event-title-overlay { background-color: rgba(220, 53, 69, 0.9); }
        .floating-event-item.category-sponsor { border-color: #ffc107; box-shadow: 0 5px 15px rgba(255, 193, 7, 0.5); }
        .floating-event-item.category-sponsor .event-title-overlay { background-color: rgba(255, 193, 7, 0.9); color: #000; }
        .main-content-container { max-width: 1200px; margin: 0 auto; padding: 0 15px; position: relative; }
        #content.active .main-content-container { max-width: calc(1200px - 250px); }
        @media (max-width: 1200px) { .event-sidebar-box { display: none; } #content.active .main-content-container { max-width: 1200px; } }
        .footer { width: 100%; transition: margin-left 0.3s ease, max-width 0.3s ease; }
        #content.active ~ .footer { margin-left: 250px; max-width: calc(100% - 250px); }
        body { display: flex; flex-direction: column; min-height: 100vh; }
        #content { flex: 1; }
        @media (max-width: 768px) { #content.active ~ .footer { margin-left: 0; max-width: 100%; } }
        
        /* Hiệu ứng rung nhẹ cho nút AI header */
        @keyframes ring { 0% { transform: rotate(0); } 10% { transform: rotate(10deg); } 20% { transform: rotate(-10deg); } 30% { transform: rotate(10deg); } 40% { transform: rotate(-10deg); } 50% { transform: rotate(0); } 100% { transform: rotate(0); } }
        #header-ai-trigger i { animation: ring 4s infinite ease-in-out; color: #E91E63; font-size: 1.2rem; }
    </style>
</head>

<body>
    <div id="loading-spinner" class="spinner-container">
        <div class="logo-circle"><img src="/webdacn_quanlyclb/uploads/NTD001545.jpg" alt="Logo" class="rounded-logo"></div>
        <div class="spinner"></div>
    </div>
    <button id="sidebar-toggle" type="button"><i class="fas fa-bars"></i></button>

    <div id="sidebar" class="d-flex flex-column">
        <h5 class="text-center text-white mb-4">Menu</h5>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb"><i class="fas fa-home"></i> Trang chủ</a></li>
            <?php if (SessionHelper::isLoggedIn()): ?>
                <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb/account/profile"><i class="fas fa-user"></i> Thông tin cá nhân</a></li>
            <?php endif; ?>
            <?php if (SessionHelper::isAdmin()): ?>
                <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb/account"><i class="fas fa-cogs"></i> Quản trị</a></li>
                <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb/team/requests"><i class="fas fa-check-circle"></i> Duyệt yêu cầu</a></li>
                <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb/team/locked"><i class="fas fa-lock"></i> Nhóm bị khóa</a></li>
            <?php endif; ?>
            <?php if (SessionHelper::isStaff()): ?>
                <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb/team/userjoin"><i class="fas fa-check-circle"></i> Duyệt yêu cầu thành viên</a></li>
            <?php endif; ?>
            <?php if (SessionHelper::isLoggedIn()): ?>
                <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb/importantday/"><i class="fas fa-calendar-alt"></i> Lịch thời gian</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb/team"><i class="fas fa-users"></i> Danh sách Câu lạc Bộ</a></li>
            <?php if (SessionHelper::isLoggedIn()): ?>
                <li><a class="nav-link dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt me-2" style="width: 20px; text-align: center;"></i><span>Đăng xuất</span></a></li>
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
                        
                        <li class="nav-item me-2">
                            <a class="nav-link" href="#" id="header-ai-trigger" title="Chat với AI">
                                <i class="fas fa-robot"></i> AI Chat
                            </a>
                        </li>
                        <li class="header-notification">
                            <a class="nav-link" href="/webdacn_quanlyclb/account/notifications"><i class="fas fa-bell"></i> Thông báo</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb"><i class="fas fa-home me-1"></i>Trang chủ</a></li>
                        <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb/team"><i class="fas fa-users me-1"></i> Câu lạc bộ</a></li>
                        <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb/friend/searchFriends"><i class="fas fa-user-friends me-1"></i>Bạn bè</a></li>
                        <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb/chat"><i class="fas fa-envelope me-1"></i>Tin nhắn</a></li>

                        <?php if (SessionHelper::isLoggedIn()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-1"></i> Tài khoản
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="/webdacn_quanlyclb/account/profile"><i class="fas fa-user-edit me-2"></i> Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/webdacn_quanlyclb/account/logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="/webdacn_quanlyclb/account/login"><i class="fas fa-sign-in-alt me-1"></i> Đăng nhập</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <?php if (isset($show_banner) && $show_banner): ?>
            <?php if (isset($banners) && !empty($banners)): ?>
                <div id="advertisingCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                    <?php if (count($banners) > 1): ?>
                        <div class="carousel-indicators">
                            <?php foreach ($banners as $index => $banner): ?>
                                <button type="button" data-bs-target="#advertisingCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="carousel-inner">
                        <?php foreach ($banners as $index => $banner): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <?php if (!empty($banner['link_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($banner['link_url']); ?>" target="_blank" rel="noopener noreferrer">
                                        <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($banner['image_path']); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($banner['alt_text'] ?? 'Quảng cáo'); ?>">
                                    </a>
                                <?php else: ?>
                                    <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($banner['image_path']); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($banner['alt_text'] ?? 'Quảng cáo'); ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($banners) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#advertisingCarousel" data-bs-slide="prev"> <span class="carousel-control-prev-icon" aria-hidden="true"></span> <span class="visually-hidden">Previous</span> </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#advertisingCarousel" data-bs-slide="next"> <span class="carousel-control-next-icon" aria-hidden="true"></span> <span class="visually-hidden">Next</span> </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="main-content-container">
            <?php if (isset($show_banner) && $show_banner): ?>
                <?php if (isset($activeEvents) && !empty($activeEvents)): ?>
                    <div class="event-sidebar-box">
                        <h5 class="event-sidebar-title"><i class="fas fa-star text-warning"></i> Sự kiện nổi bật</h5>
                        <div class="floating-event-container">
                            <?php foreach ($activeEvents as $event): ?>
                                <div class="floating-event-item category-<?php echo htmlspecialchars($event['category']); ?>" data-id="<?php echo $event['id']; ?>" style="<?php echo !$event['is_active'] ? 'display:none;' : ''; ?>">
                                    <a href="/webdacn_quanlyclb/event/detail/<?php echo $event['id']; ?>" title="<?php echo htmlspecialchars($event['title']); ?>">
                                        <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($event['image_path']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                                        <span class="event-title-overlay"><?php echo htmlspecialchars($event['title']); ?></span>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0">
                        <div class="modal-header" style="background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%);">
                            <h5 class="modal-title text-white"><i class="fas fa-sign-out-alt me-2"></i> Xác nhận đăng xuất</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-4">
                            <div class="d-flex align-items-center"><i class="fas fa-question-circle text-warning me-3 fs-4"></i><p class="mb-0">Bạn có chắc chắn muốn đăng xuất?</p></div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Hủy</button>
                            <a href="/webdacn_quanlyclb/account/logout" class="btn btn-danger"><i class="fas fa-sign-out-alt me-1"></i> Đăng xuất</a>
                        </div>
                    </div>
                </div>
            </div>

            <main class="container my-4">
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        window.addEventListener('load', function() { setTimeout(function() { document.body.classList.add('loaded'); document.getElementById('loading-spinner').style.display = 'none'; }, 400); });
                        const sidebar = document.getElementById('sidebar'); const content = document.getElementById('content'); const isSidebarActive = localStorage.getItem('sidebarActive') === 'true';
                        sidebar.style.transition = 'none'; content.style.transition = 'none';
                        if (isSidebarActive) { sidebar.classList.add('active'); content.classList.add('active'); }
                        setTimeout(() => { sidebar.style.transition = 'left 0.3s ease'; content.style.transition = 'margin-left 0.3s ease, max-width 0.3s ease'; }, 0);
                    });
                    document.getElementById('sidebar-toggle').addEventListener('click', function() { const sidebar = document.getElementById('sidebar'); const content = document.getElementById('content'); const isActive = sidebar.classList.toggle('active'); content.classList.toggle('active'); localStorage.setItem('sidebarActive', isActive); });
                    document.addEventListener('click', function(event) { const sidebar = document.getElementById('sidebar'); const toggleButton = document.getElementById('sidebar-toggle'); const content = document.getElementById('content'); if (window.innerWidth <= 768 && sidebar.classList.contains('active') && !sidebar.contains(event.target) && !toggleButton.contains(event.target)) { sidebar.classList.remove('active'); content.classList.remove('active'); localStorage.setItem('sidebarActive', false); } });
                    window.addEventListener('storage', function(e) { if (e.key === 'event_toggle_realtime' && e.newValue) { try { const data = JSON.parse(e.newValue); const eventId = data.event_id; const isActive = data.is_active; const eventBox = document.querySelector(`.floating-event-item[data-id="${eventId}"]`); if (eventBox) { if (isActive) { eventBox.style.display = 'block'; eventBox.classList.remove('d-none'); eventBox.style.opacity = '0'; eventBox.style.transition = 'opacity 0.4s ease'; setTimeout(() => { eventBox.style.opacity = '1'; }, 50); } else { eventBox.style.opacity = '1'; eventBox.style.transition = 'opacity 0.4s ease'; eventBox.style.opacity = '0'; setTimeout(() => { eventBox.style.display = 'none'; }, 400); } } } catch (error) { console.error('Lỗi parse event_toggle_realtime:', error); } } });
                    setTimeout(() => { localStorage.removeItem('event_toggle_realtime'); }, 5000);
                    const BASE_URL = "<?= defined('BASE_URL') ? BASE_URL : '/webdacn_quanlyclb' ?>";
                </script>