<?php
// === PHẦN 1: LOGIC PHP ===
require_once 'app/helpers/SessionHelper.php';
require_once 'app/models/PostModel.php';
$show_banner = true; 
include $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php';

SessionHelper::start();

// --- LOGIC LẤY VÀ PHÂN LOẠI BÀI VIẾT ---
$postModel = new PostModel();
$allPosts = $postModel->getAllPosts(); 
$isAdmin = SessionHelper::isAdmin();
$currentUserId = SessionHelper::getUserId();
$canManage = SessionHelper::isLoggedIn() && in_array(SessionHelper::getRole(), ['admin', 'staff']);

$main_list_posts = []; 
$team_posts = [];      

foreach ($allPosts as $post) {
    $user = $postModel->getUserById($post['author_id']); 
    $post['authorAvatar'] = $user['avatar'] ?? '/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg'; 
    $post['authorName'] = $user['username'] ?? 'Người dùng'; 

    $images = $postModel->getPostImages($post['id']); 
    $post['firstImage'] = !empty($images) ? $images[0]['image_path'] : '/webdacn_quanlyclb/public/uploads/default_thumbnail.jpg'; 

    $post['reactions_summary'] = $postModel->getReactionsSummary($post['id']); 
    $post['user_reaction'] = $currentUserId ? $postModel->getUserReaction($post['id'], $currentUserId) : null; 

    if (!empty($post['team_id'])) {
        $team_posts[] = $post; 
    } else {
        $main_list_posts[] = $post; 
    }
}
?>

<style>
    /* ... (GIỮ NGUYÊN TOÀN BỘ CSS CŨ CỦA BẠN TỪ FILE TRƯỚC) ... */
    .list-container { max-width: 1200px; margin: 0 auto; padding: 5px; }
    .post-list-card { margin-bottom: 30px; border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5); transition: transform 0.3s ease; }
    .post-list-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); }
    .post-list-card.category-thongbao { background-color: #f8d7da; border-left: 5px solid #dc3545; }
    .post-list-card.category-chieu-sinh { background-color: #fff3cd; border-left: 5px solid #ffc107; }
    .post-list-card.category-su-kien { background-color: #d1e7dd; border-left: 5px solid #198754; }
    .post-list-card .card-header { display: flex; justify-content: space-between; align-items: center; background-color: transparent; border-bottom: 1px solid rgba(0, 0, 0, 0.05); padding: 1rem 1.25rem; }
    .post-list-card .card-header .post-list-category-label { padding: 4px 12px; border-radius: 12px; font-size: 0.9rem; font-weight: 500; }
    .post-list-card.category-thongbao .post-list-category-label { background-color: #dc3545; color: white; }
    .post-list-card.category-chieu-sinh .post-list-category-label { background-color: #ffc107; color: white; }
    .post-list-card.category-su-kien .post-list-category-label { background-color: #198754; color: white; }
    .post-list-card .card-body { padding: 1.25rem; }
    .post-list-card .post-list-title { font-size: 1.5rem; font-weight: 600; color: #333; margin-bottom: 0.75rem; }
    .post-list-card.category-thongbao .post-list-title { color: #842029; }
    .post-list-card.category-chieu-sinh .post-list-title { color: #664d03; }
    .post-list-card.category-su-kien .post-list-title { color: #0f5132; }
    .post-list-card .post-list-meta { font-size: 0.85rem; color: #6c757d; margin-bottom: 1rem; }
    .post-list-card .post-list-excerpt { color: #495057; margin-bottom: 1.5rem; line-height: 1.6; }
    .post-list-card .post-list-btn-custom { border-radius: 50px; padding: 6px 15px; font-size: 0.9rem; font-weight: 500; transition: all 0.3s ease; }
    .post-list-card .post-list-image-container { max-height: 250px; overflow: hidden; margin-bottom: 1rem; }
    .post-list-card .post-list-image { width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform 0.5s ease; }
    .post-list-card a:hover .post-list-image { transform: scale(1.03); }
    .post-list-team-badge { background-color: #000000ff; color: #a18484ff; }
    /* CSS REACTION */
    .reaction-wrapper { position: relative; display: inline-block; vertical-align: middle; padding-top: 10px; margin-top: -10px; }
    .reaction-options { display: none; position: absolute; bottom: 100%; left: 0; background-color: white; border: 1px solid #ddd; border-radius: 20px; padding: 5px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 0px; white-space: nowrap; transform: scale(0.9); opacity: 0; transition: all 0.2s ease; z-index: 999; }
    .reaction-wrapper:hover .reaction-options, .reaction-wrapper:focus-within .reaction-options { display: flex; transform: scale(1); opacity: 1; }
    .reaction-icon { font-size: 1.5rem; padding: 5px; cursor: pointer; transition: transform 0.2s ease; }
    .reaction-icon:hover { transform: scale(1.3); }
    .btn-reaction-trigger { border-radius: 50px; font-weight: 500; transition: all 0.3s ease; padding: 6px 15px; font-size: 0.9rem; background-color: #f0f2f5; border: none; color: #606770; }
    .btn-reaction-trigger.reacted-like { color: #1877f2; background-color: #e7f3ff; }
    .btn-reaction-trigger.reacted-love { color: #e44d61; background-color: #fde7ea; }
    .btn-reaction-trigger.reacted-haha { color: #f7b125; background-color: #fef5e7; }
    .btn-reaction-trigger.reacted-wow { color: #f7b125; background-color: #fef5e7; }
    .btn-reaction-trigger.reacted-sad { color: #f7b115; background-color: #fef5e7; }
    .btn-reaction-trigger.reacted-angry { color: #e9710f; background-color: #fdebe1; }
    .reaction-summary-display { display: flex; align-items: center; gap: 2px; cursor: default; vertical-align: middle; padding: 6px 0; }
    .reaction-summary-icon { font-size: 1rem; }
    .reaction-summary-count { font-size: 0.9rem; color: #606770; margin-left: 3px; font-weight: 500; }
    /* BG PINK TITLE */
    .bg-pink { background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%) !important; color: white !important; font-weight: 600; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(233, 30, 99, 0.3); transition: all 0.3s ease; }
    .bg-pink:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(233, 30, 99, 0.4); }
    @media (max-width: 576px) { .bg-pink { font-size: 0.95rem; padding: 0.5rem 1rem !important; display: block !important; text-align: center; } }
    /* TEAM POST CARD */
    .section-title-pink { color: #E91E63; font-weight: 700; text-align: center; margin-top: 4rem; margin-bottom: 2rem; padding-bottom: 10px; border-bottom: 2px solid #FCE4EC; }
    .team-post-card { background-color: #fff; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5); overflow: hidden; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; border-top: 4px solid; position: relative; }
    .team-post-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); }
    .team-category-badge { position: absolute; top: 15px; right: 15px; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; color: white; z-index: 2; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2); }
    .team-post-card.category-thongbao { border-top-color: #dc3545; border-left: 2px solid #f8d7da; border-right: 2px solid #f8d7da; border-bottom: 2px solid #f8d7da; }
    .team-post-card.category-thongbao .team-category-badge { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
    .team-post-card.category-thongbao .card-title { color: #000000ff; }
    .team-post-card.category-su-kien { border-top-color: #198754; border-left: 2px solid #d1e7dd; border-right: 2px solid #d1e7dd; border-bottom: 2px solid #d1e7dd; }
    .team-post-card.category-su-kien .team-category-badge { background: linear-gradient(135deg, #198754 0%, #146c43 100%); }
    .team-post-card.category-su-kien .card-title { color: #000000ff; }
    .team-post-card.category-chieu-sinh { border-top-color: #ffc107; border-left: 2px solid #fff3cd; border-right: 2px solid #fff3cd; border-bottom: 2px solid #fff3cd; }
    .team-post-card.category-chieu-sinh .team-category-badge { background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: #000; }
    .team-post-card.category-chieu-sinh .card-title { color: #000000ff; }
    .team-post-card .card-img-top { width: 100%; height: 200px; object-fit: cover; transition: transform 0.5s ease; }
    .team-post-card:hover .card-img-top { transform: scale(1.05); }
    .team-post-card .card-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; background: linear-gradient(to bottom, #ffffff, #fafafa); }
    .team-post-card .card-title { font-weight: 600; font-size: 1.2rem; margin-bottom: 10px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 2.6rem; transition: color 0.3s ease; text-decoration: none !important; text-align: left; padding-left: 10px; padding-right: 0; background-color: #ed2366ff; padding-top: 5px; padding-bottom: 5px; border-radius: 10px; white-space: normal; line-height: 1.4; height: 3.5rem; }
    @media (max-width: 768px) { .team-post-card .card-title { font-size: 1.1rem; height: 2.8rem; min-height: 2.4rem; } }
    .team-post-card .card-text { color: #555; font-size: 0.95rem; flex-grow: 1; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.5; }
    .team-post-card .card-author { display: flex; align-items: center; font-size: 0.9em; color: #000000ff; padding-top: 10px; padding-bottom: 10px; border-top: 1px solid #f0f0f0; background-color: #b8b7b7ff; border-radius: 10px; padding-left: 10px; }
    .team-post-card .card-author img { width: 30px; height: 30px; border-radius: 50%; margin-right: 10px; object-fit: cover; border: 2px solid #f0f0f0; }
    .team-post-card .team-name { color: #E91E63; font-weight: 600; font-size: 0.85rem; }
    .team-post-card { text-decoration: none; color: inherit; }
    @media (max-width: 768px) { .team-post-card .card-title { font-size: 1.1rem; } .team-post-card .card-text { font-size: 0.9rem; } .team-category-badge { font-size: 0.7rem; padding: 4px 8px; } }
</style>

<div class="list-container" style="padding-top: 20px;">

    <div class="text-end mb-4">
        <?php if ($isAdmin): ?>
            <a href="/webdacn_quanlyclb/advertisingbanner" class="btn" style="background-color: #ff9800; color: white; border: none; border-radius: 50px; padding: 8px 20px; box-shadow: 0 4px 8px rgba(255, 152, 0, 0.3);">
                <i class="fas fa-images me-2"></i>Quản lý Banner
            </a>
            <a href="/webdacn_quanlyclb/event/index" class="btn" style="background-color: #00BCD4; color: white; border: none; border-radius: 50px; padding: 8px 20px; box-shadow: 0 4px 8px rgba(0, 188, 212, 0.3);">
                <i class="fas fa-calendar-plus me-2"></i>Quản lý Sự kiện
            </a>
        <?php endif; ?>
        <?php if ($canManage): ?>
            <a href="/webdacn_quanlyclb/default/create" class="btn btn-primary" style="background-color: #FF6B9E; border: none; border-radius: 50px; padding: 8px 20px; box-shadow: 0 4px 8px rgba(255, 107, 158, 0.3);">
                <i class="fas fa-plus me-2"></i>Thêm bài viết
            </a>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php if (empty($main_list_posts)): ?>
            <div class="col-12 text-center py-5" style="color: #FF6B9E;">
                <i class="fas fa-newspaper fa-3x mb-3"></i>
                <p class="fs-4">Chưa có bài viết nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($main_list_posts as $post): ?>
                <?php
                $categoryClass = '';
                $categoryLabel = '';
                switch ($post['category'] ?? 'Thông báo') {
                    case 'Thông báo': $categoryClass = 'category-thongbao'; $categoryLabel = 'Thông báo'; break;
                    case 'Chiêu sinh': $categoryClass = 'category-chieu-sinh'; $categoryLabel = 'Chiêu sinh'; break;
                    case 'Sự kiện': $categoryClass = 'category-su-kien'; $categoryLabel = 'Sự kiện'; break;
                }
                ?>
                <div class="col-12">
                    <div class="card post-list-card <?= $categoryClass ?>" style="border-radius: 12px;">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo htmlspecialchars($post['authorAvatar']); ?>" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #FFB6C1;">
                                <div>
                                    <div style="font-weight: 600; color: #D23369;"><?php echo htmlspecialchars($post['authorName']); ?></div>
                                    <div style="font-size: 0.8rem; color: #FF6B9E;">
                                        <i class="fas fa-clock me-1"></i><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($post['created_at']))); ?>
                                        <?php if (!empty($post['team_name'])): ?>
                                            <span class="badge post-list-team-badge ms-2">
                                                <i class="fas fa-users me-1"></i><?= htmlspecialchars($post['team_name']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="post-list-category-label"><?= htmlspecialchars($categoryLabel) ?></div>
                        </div>
                        <div class="row g-0">
                            <?php if ($post['firstImage']): ?>
                                <div class="col-md-5">
                                    <a href="/webdacn_quanlyclb/default/detail/<?php echo $post['id']; ?>" class="d-block" style="height: 250px; overflow: hidden;">
                                        <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($post['firstImage']); ?>" class="w-100 h-100 post-list-image" alt="Ảnh bài viết">
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="<?php echo $post['firstImage'] ? 'col-md-7' : 'col-12'; ?>">
                                <div class="card-body p-5" style="background-color: #FFF9FB;">
                                    <div class="text-start w-100">
                                        <h5 class="card-title mb-1 bg-pink text-white px-3 py-2 rounded-3 d-inline-block">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </h5>
                                    </div>
                                    
                                    <p class="card-text post-list-excerpt text-muted mb-4">
                                        <?php
                                        // 1. Loại bỏ toàn bộ thẻ HTML bằng strip_tags
                                        $plainText = strip_tags($post['content']);
                                        // 2. Cắt chuỗi lấy 150 ký tự
                                        $excerpt = mb_substr($plainText, 0, 150, 'UTF-8');
                                        // 3. Hiển thị văn bản thuần đã được xử lý an toàn
                                        echo htmlspecialchars($excerpt);
                                        // 4. Nếu dài hơn 150 thì hiện dấu ...
                                        if (mb_strlen($plainText, 'UTF-8') > 150) {
                                            echo '...';
                                        }
                                        ?>
                                    </p>

                                    <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
                                        <?php if (SessionHelper::isLoggedIn()): ?>
                                            <div class="reaction-wrapper">
                                                <button class="btn btn-sm btn-reaction-trigger" id="reaction-trigger-<?php echo $post['id']; ?>" data-post-id="<?php echo $post['id']; ?>" data-initial-reaction="<?php echo htmlspecialchars($post['user_reaction'] ?? ''); ?>"></button>
                                                <div class="reaction-options">
                                                    <span class="reaction-icon" data-reaction="like" data-post-id="<?php echo $post['id']; ?>">👍</span>
                                                    <span class="reaction-icon" data-reaction="love" data-post-id="<?php echo $post['id']; ?>">❤️</span>
                                                    <span class="reaction-icon" data-reaction="haha" data-post-id="<?php echo $post['id']; ?>">😂</span>
                                                    <span class="reaction-icon" data-reaction="wow" data-post-id="<?php echo $post['id']; ?>">😮</span>
                                                    <span class="reaction-icon" data-reaction="sad" data-post-id="<?php echo $post['id']; ?>">😢</span>
                                                    <span class="reaction-icon" data-reaction="angry" data-post-id="<?php echo $post['id']; ?>">😡</span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="reaction-summary-display" id="reaction-summary-<?php echo $post['id']; ?>" data-initial-summary="<?php echo htmlspecialchars(json_encode($post['reactions_summary'])); ?>"></div>
                                        <a href="/webdacn_quanlyclb/default/detail/<?php echo $post['id']; ?>" class="btn btn-sm post-list-btn-custom" style="background-color: #1f45efff; color: white;">
                                            <i class="fas fa-eye me-1"></i> Xem chi tiết
                                        </a>
                                        <?php
                                        $canEditDeleteThisPost = false;
                                        if (SessionHelper::isAdmin()) {
                                            $canEditDeleteThisPost = true;
                                        } elseif (SessionHelper::isStaff() && $currentUserId == $post['author_id']) {
                                            if (empty($post['team_id'])) {
                                                $canEditDeleteThisPost = true;
                                            } else {
                                                $database = new Database();
                                                $db = $database->getConnection();
                                                $canEditDeleteThisPost = SessionHelper::isClubManager($post['team_id'], $db);
                                            }
                                        }
                                        ?>
                                        <?php if ($canEditDeleteThisPost): ?>
                                            <a href="/webdacn_quanlyclb/default/edit/<?php echo $post['id']; ?>" class="btn btn-sm post-list-btn-custom" style="background-color: #FFB6C1; color: white;">
                                                <i class="fas fa-edit me-1"></i> Sửa
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger post-list-btn-custom" data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo $post['id']; ?>">
                                                <i class="fas fa-trash me-1"></i> Xóa
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($canManage): ?>
                    <div class="modal fade" id="deleteModal-<?php echo $post['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Xác nhận xóa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">Bạn có chắc muốn xóa bài viết "<?= htmlspecialchars($post['title']); ?>"?</div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <a href="/webdacn_quanlyclb/default/delete/<?php echo $post['id']; ?>" class="btn btn-danger">Xóa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (!empty($team_posts)): ?>
        <h2 class="section-title-pink">Bài Viết Từ Câu Lạc Bộ</h2>
        <div class="row g-4">
            <?php foreach ($team_posts as $post): ?>
                <?php
                $teamCategoryClass = '';
                $teamCategoryLabel = '';
                switch ($post['category'] ?? 'Thông báo') {
                    case 'Thông báo': $teamCategoryClass = 'category-thongbao'; $teamCategoryLabel = 'Thông báo'; break;
                    case 'Chiêu sinh': $teamCategoryClass = 'category-chieu-sinh'; $teamCategoryLabel = 'Chiêu sinh'; break;
                    case 'Sự kiện': $teamCategoryClass = 'category-su-kien'; $teamCategoryLabel = 'Sự kiện'; break;
                }
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="team-post-card <?= $teamCategoryClass ?>">
                        <div class="team-category-badge"><?= htmlspecialchars($teamCategoryLabel) ?></div>
                        <a href="/webdacn_quanlyclb/default/detail/<?php echo $post['id']; ?>">
                            <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($post['firstImage']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        </a>
                        <div class="card-body">
                            <a href="/webdacn_quanlyclb/default/detail/<?php echo $post['id']; ?>" class="text-decoration-none">
                                <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                            </a>
                            
                            <p class="card-text">
                                <?php
                                $plainText = strip_tags($post['content']);
                                $excerpt = mb_substr($plainText, 0, 100, 'UTF-8');
                                echo htmlspecialchars($excerpt);
                                if (mb_strlen($plainText, 'UTF-8') > 100) echo '...';
                                ?>
                            </p>

                            <div class="post-list-actions mt-auto pt-3 border-top d-flex justify-content-end align-items-center flex-wrap gap-2">
                                <?php if (SessionHelper::isLoggedIn()): ?>
                                    <div class="reaction-wrapper">
                                        <button class="btn btn-sm btn-reaction-trigger" id="reaction-trigger-<?php echo $post['id']; ?>" data-post-id="<?php echo $post['id']; ?>" data-initial-reaction="<?php echo htmlspecialchars($post['user_reaction'] ?? ''); ?>"></button>
                                        <div class="reaction-options">
                                            <span class="reaction-icon" data-reaction="like" data-post-id="<?php echo $post['id']; ?>">👍</span>
                                            <span class="reaction-icon" data-reaction="love" data-post-id="<?php echo $post['id']; ?>">❤️</span>
                                            <span class="reaction-icon" data-reaction="haha" data-post-id="<?php echo $post['id']; ?>">😂</span>
                                            <span class="reaction-icon" data-reaction="wow" data-post-id="<?php echo $post['id']; ?>">😮</span>
                                            <span class="reaction-icon" data-reaction="sad" data-post-id="<?php echo $post['id']; ?>">😢</span>
                                            <span class="reaction-icon" data-reaction="angry" data-post-id="<?php echo $post['id']; ?>">😡</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="reaction-summary-display" id="reaction-summary-<?php echo $post['id']; ?>" data-initial-summary="<?php echo htmlspecialchars(json_encode($post['reactions_summary'])); ?>"></div>
                                <a href="/webdacn_quanlyclb/default/detail/<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fas fa-eye me-1"></i>Xem chi tiết
                                </a>
                                <?php if ($isAdmin): ?>
                                    <a href="/webdacn_quanlyclb/default/edit/<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-warning rounded-pill">
                                        <i class="fas fa-edit me-1"></i>Sửa
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo $post['id']; ?>">
                                        <i class="fas fa-trash me-1"></i>Xóa
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="card-author">
                                <img src="<?php echo htmlspecialchars($post['authorAvatar']); ?>" alt="Avatar">
                                <span>
                                    <strong><?php echo htmlspecialchars($post['authorName']); ?></strong><br>
                                    <span class="team-name"><?= htmlspecialchars($post['team_name']) ?></span><br>
                                    <small class="text-muted"><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></small>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($isAdmin): ?>
                    <div class="modal fade" id="deleteModal-<?php echo $post['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Xác nhận xóa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">Bạn có chắc muốn xóa bài viết "<?= htmlspecialchars($post['title']); ?>"?</div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <a href="/webdacn_quanlyclb/default/delete/<?php echo $post['id']; ?>" class="btn btn-danger">Xóa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reactionIcons = {'like': '👍', 'love': '❤️', 'haha': '😂', 'wow': '😮', 'sad': '😢', 'angry': '😡'};
        const reactionLabels = {'like': 'Thích', 'love': 'Yêu thích', 'haha': 'Haha', 'wow': 'Wow', 'sad': 'Buồn', 'angry': 'Phẫn nộ'};
        function updateTriggerButton(postId, reaction) {
            const triggerButton = document.getElementById(`reaction-trigger-${postId}`);
            if (!triggerButton) return;
            triggerButton.className = 'btn btn-sm btn-reaction-trigger';
            if (reaction && reactionIcons[reaction]) {
                triggerButton.innerHTML = `${reactionIcons[reaction]} ${reactionLabels[reaction]}`;
                triggerButton.classList.add(`reacted-${reaction}`);
            } else { triggerButton.innerHTML = '<i class="fas fa-thumbs-up"></i> Thích'; }
        }
        function updateReactionSummary(postId, summary) {
            const summaryContainer = document.getElementById(`reaction-summary-${postId}`);
            if (!summaryContainer) return;
            summaryContainer.innerHTML = '';
            let total = 0;
            if (summary && summary.length > 0) {
                summary.sort((a, b) => { const order = {'love': 1, 'like': 2}; return (order[a.reaction_type] || 99) - (order[b.reaction_type] || 99); });
                summary.slice(0, 3).forEach(item => { if (reactionIcons[item.reaction_type]) { const iconSpan = document.createElement('span'); iconSpan.className = 'reaction-summary-icon'; iconSpan.title = item.reaction_type; iconSpan.textContent = reactionIcons[item.reaction_type]; summaryContainer.appendChild(iconSpan); } });
                summary.forEach(item => { total += parseInt(item.count, 10); });
                if (total > 0) { const countSpan = document.createElement('span'); countSpan.className = 'reaction-summary-count'; countSpan.textContent = total; summaryContainer.appendChild(countSpan); }
            }
        }
        document.querySelectorAll('.btn-reaction-trigger').forEach(button => {
            const postId = button.dataset.postId;
            const initialReaction = button.dataset.initialReaction;
            updateTriggerButton(postId, initialReaction);
        });
        document.querySelectorAll('.reaction-summary-display').forEach(summaryDiv => {
            const postId = summaryDiv.id.replace('reaction-summary-', '');
            try { const initialSummaryJson = summaryDiv.dataset.initialSummary; if (initialSummaryJson && initialSummaryJson !== "null") { updateReactionSummary(postId, JSON.parse(initialSummaryJson)); } } catch (e) { console.error(e); }
        });
        document.querySelectorAll('.reaction-icon').forEach(icon => {
            icon.addEventListener('click', async function(e) {
                e.preventDefault();
                const postId = this.dataset.postId;
                const reactionType = this.dataset.reaction;
                try {
                    const formData = new FormData(); formData.append('post_id', postId); formData.append('reaction_type', reactionType);
                    const response = await fetch('/webdacn_quanlyclb/default/react', { method: 'POST', body: formData });
                    if (!response.ok) throw new Error('Lỗi mạng');
                    const result = await response.json();
                    if (result.success) { updateTriggerButton(postId, result.my_reaction); updateReactionSummary(postId, result.new_summary); }
                } catch (error) { console.error(error); }
            });
        });
        document.querySelectorAll('.btn-reaction-trigger').forEach(button => {
            button.addEventListener('click', async function(e) {
                e.preventDefault();
                const postId = this.dataset.postId;
                try {
                    const formData = new FormData(); formData.append('post_id', postId); formData.append('reaction_type', 'like');
                    const response = await fetch('/webdacn_quanlyclb/default/react', { method: 'POST', body: formData });
                    const result = await response.json();
                    if (result.success) { updateTriggerButton(postId, result.my_reaction); updateReactionSummary(postId, result.new_summary); }
                } catch (error) { console.error(error); }
            });
        });
    });
</script>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/footer.php'; ?>