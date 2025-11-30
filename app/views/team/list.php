<?php
include $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php';
// Các biến $teams, $current_team_id, $current_team_name được truyền từ TeamController
?>

<style>
    :root {
        --primary: #E91E63; /* Màu hồng HUTECH */
        --primary-light: #FCE4EC; /* Hồng rất nhạt */
        --primary-dark: #C2185B; /* Hồng đậm */
        --gradient: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        --gradient-light: linear-gradient(135deg, #F06292 0%, #E91E63 100%);
        --bg: #FFF9FB; /* Nền trang (thay vì #FFF0F5) */
        --white: #fff;
        --card-shadow: 0 10px 30px rgba(233, 30, 99, 0.1);
        --hover-shadow: 0 15px 40px rgba(233, 30, 99, 0.2);
        --text-dark: #333;
        --text-medium: #6c757d; /* Xám dịu */
        --success: #198754; /* Xanh lá */
        --border-radius-lg: 16px; /* Bo góc lớn */
        --border-radius-md: 12px; /* Bo góc vừa */
    }

    body {
        background: var(--bg);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
    }

    .team-page-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* === TIÊU ĐỀ TRANG === */
    .page-header {
        text-align: center;
        margin-bottom: 40px;
        position: relative;
    }
    .page-header h2 {
        color: var(--primary-dark);
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 15px;
        letter-spacing: -0.5px;
    }
    .page-header p {
        font-size: 1.1rem;
        color: var(--text-medium);
        max-width: 600px;
        margin: 0 auto;
    }
    .page-header::after {
        content: '';
        position: absolute;
        bottom: -15px; /* Đẩy xuống dưới p */
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: var(--gradient);
        border-radius: 2px;
    }

    /* Nút Yêu cầu tạo CLB */
    .header-actions {
        display: flex;
        justify-content: center;
        margin-top: 40px; /* Tách biệt khỏi tiêu đề */
    }
    .btn-gradient {
        background: var(--gradient);
        color: white;
        padding: 15px 35px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        box-shadow: 0 8px 25px rgba(233, 30, 99, 0.3);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .btn-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s ease-out;
    }
    .btn-gradient:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(233, 30, 99, 0.4);
    }
    .btn-gradient:hover::before {
        left: 100%;
    }

    /* === GRID DANH SÁCH CLB === */
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }

    /* Thẻ CLB */
    .team-card {
        background: var(--white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(255, 107, 158, 0.1);
        position: relative;
        display: flex;
        flex-direction: column; /* Quan trọng để flex-grow */
    }
    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--hover-shadow);
    }
    
    /* Viền trên màu */
    .team-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 5px;
        background: var(--gradient);
        z-index: 10;
    }

    /* Ảnh bìa */
    .team-banner {
        height: 160px; /* Giảm chiều cao banner */
        overflow: hidden;
        position: relative;
    }
    .team-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .team-card:hover .team-banner img {
        transform: scale(1.05);
    }

    /* Avatar CLB */
    .team-avatar-wrapper {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        border: 4px solid var(--white);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin: -45px auto 15px auto; /* Treo avatar lên */
        position: relative;
        z-index: 5;
        background: var(--white); /* Nền cho ảnh */
    }
    .team-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    /* Thân thẻ */
    .team-card-body {
        padding: 0 25px 25px 25px; /* Giảm padding trên */
        text-align: center;
        flex-grow: 1; /* Đẩy nút xuống dưới */
        display: flex;
        flex-direction: column;
    }
    .team-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 5px;
    }
    .team-talent {
        color: var(--text-medium);
        font-size: 1rem;
        margin-bottom: 20px;
        line-height: 1.5;
        min-height: 48px;
        flex-grow: 1; /* Đẩy nút xuống */
    }

    /* Nút bấm */
    .team-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .btn-outline, .btn-success, .btn-disabled {
        padding: 10px 22px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        border: 2px solid;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
    }
    .btn-outline {
        background: transparent;
        color: var(--primary);
        border-color: var(--primary);
    }
    .btn-outline:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 107, 158, 0.3);
    }
    .btn-success {
        background: var(--success);
        color: white;
        border-color: var(--success);
    }
    .btn-success:hover {
        background: #157347;
        border-color: #157347;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(25, 135, 84, 0.3);
    }
    .btn-disabled {
        background: #6c757d;
        color: white;
        border-color: #6c757d;
        cursor: not-allowed;
        opacity: 0.7;
    }
    
    /* Xử lý thẻ "Đã tham gia" */
    .team-card.joined {
        border-color: var(--success);
    }
    .team-card.joined::before {
        background: linear-gradient(135deg, #198754 0%, #157347 100%);
    }

    /* Thông báo */
    .alert-container { max-width: 800px; margin: 0 auto 30px auto; }
    .alert { border-radius: var(--border-radius-md); border: none; box-shadow: var(--card-shadow); padding: 20px 25px; }
    .alert-success { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724; border-left: 4px solid var(--success); }
    .alert-danger { background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%); color: #721c24; border-left: 4px solid #dc3545; }

    /* === CSS CHO MODAL (ĐÃ THIẾT KẾ LẠI) === */
    .modal-content {
        border-radius: var(--border-radius-lg);
        border: none;
        box-shadow: var(--hover-shadow);
        overflow: hidden;
    }
    .modal-header {
        background: var(--gradient);
        color: white;
        border-bottom: none;
        padding: 25px 30px;
    }
    .modal-header .modal-title {
        font-weight: 700;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .btn-close-white { filter: invert(1) brightness(100%); }
    .modal-body { padding: 30px; }
    
    /* Form trong Modal */
    .form-label {
        font-weight: 600;
        color: var(--primary-dark);
        margin-bottom: 8px;
        font-size: 0.95rem;
    }
    .form-control {
        border: 2px solid #f0f0f0;
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: rgba(255, 107, 158, 0.02);
    }
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(255, 107, 158, 0.1);
        outline: none;
        background: var(--white);
    }
    .form-text { color: var(--text-medium); font-size: 0.85rem; margin-top: 5px; }

    .modal-footer {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: 20px 30px;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    .modal-footer .btn {
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: 600;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    .modal-footer .btn-secondary {
        background: transparent;
        color: #6c757d;
        border-color: #6c757d;
    }
    .modal-footer .btn-secondary:hover { background: #6c757d; color: white; }

    /* Modal Thông báo (Already Joined) */
    .info-modal-body {
        text-align: center;
        padding: 30px;
    }
    .info-modal-body i {
        font-size: 4rem;
        color: var(--primary);
        margin-bottom: 20px;
        /* Thêm hiệu ứng */
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    .info-modal-body h5 {
        color: var(--primary-dark);
        font-weight: 700;
        margin-bottom: 15px;
    }
    .info-modal-body p {
        color: var(--text-medium);
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .team-grid { grid-template-columns: 1fr; gap: 20px; }
        .page-header h2 { font-size: 2.2rem; }
        .team-card-body { padding: 20px; }
        .team-actions { flex-direction: column; align-items: stretch; }
        .team-actions .btn { justify-content: center; }
        .modal-dialog { margin: 20px; }
    }
</style>

<div class="team-page-container">
    <div class="page-header">
        <h2><i class="fas fa-users"></i> Danh Sách Câu Lạc Bộ</h2>
        <p>Khám phá và tham gia các câu lạc bộ phù hợp với đam mê của bạn</p>
        
        <?php if (SessionHelper::isUser() && !$current_team_id): ?>
        <div class="header-actions">
            <button type="button" class="btn-gradient" data-bs-toggle="modal" data-bs-target="#requestTeamModal">
                <i class="fas fa-plus me-2"></i>Yêu Cầu Thành Lập CLB
            </button>
        </div>
        <?php endif; ?>
    </div>

    <div class="alert-container">
        <?php 
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($_SESSION['message']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>' . htmlspecialchars($_SESSION['error']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            unset($_SESSION['error']);
        }
        ?>
    </div>

    <div class="team-grid">
        <?php foreach ($teams as $team): ?>
            <div class="team-card <?= ($current_team_id == $team['id']) ? 'joined' : '' ?>">
                <div class="team-banner">
                    <img src="/webdacn_quanlyclb/<?= htmlspecialchars($team['avatar_team']) ?>" 
                         onerror="this.src='/webdacn_quanlyclb/public/uploads/default_team.jpg';" 
                         alt="<?= htmlspecialchars($team['name']) ?>">
                </div>
                
                <div class="team-avatar-wrapper">
                     <img src="/webdacn_quanlyclb/<?= htmlspecialchars($team['avatar_team']) ?>" 
                         onerror="this.src='/webdacn_quanlyclb/public/uploads/default_team.jpg';" 
                         alt="Avatar" class="team-avatar">
                </div>
                
                <div class="team-card-body">
                    <h3 class="team-name"><?= htmlspecialchars($team['name']) ?></h3>
                    <p class="team-talent"><?= htmlspecialchars($team['talent']) ?></p>
                    
                    <div class="team-actions">
                        <a href="/webdacn_quanlyclb/Team/view/<?= $team['id'] ?>" class="btn-outline">
                            <i class="fas fa-eye me-1"></i>Xem chi tiết
                        </a>
                        
                        <?php if (SessionHelper::isUser()): ?>
                            <?php if ($current_team_id): // User đã ở trong một CLB ?>
                                <?php if ($current_team_id == $team['id']): // Đây là CLB của user ?>
                                    <a href="/webdacn_quanlyclb/Team/myTeam" class="btn-success">
                                        <i class="fas fa-check-circle me-1"></i>CLB của bạn
                                    </a>
                                <?php else: // Đây là một CLB khác ?>
                                    <button class="btn-disabled" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#alreadyJoinedModal" 
                                            data-new-team-name="<?= htmlspecialchars($team['name']) ?>">
                                        <i class="fas fa-user-plus me-1"></i>Tham gia
                                    </button>
                                <?php endif; ?>
                            <?php else: // User chưa ở trong CLB nào ?>
                                <a href="/webdacn_quanlyclb/Team/join?team_id=<?= $team['id'] ?>" class="btn-gradient" style="padding: 10px 20px; font-size: 0.9rem;">
                                    <i class="fas fa-user-plus me-1"></i>Tham gia
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="alreadyJoinedModal" tabindex="-1" aria-labelledby="alreadyJoinedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alreadyJoinedModalLabel">
                    <i class="fas fa-info-circle me-2"></i>Thông Báo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body info-modal-body">
                <i class="fas fa-users"></i>
                <h5>Bạn đã tham gia một CLB</h5>
                <div id="alreadyJoinedModalBody"></div>
            </div>
            <div class="modal-footer">
                <a href="/webdacn_quanlyclb/Team/myTeam" class="btn-gradient" style="padding: 10px 25px;">
                    <i class="fas fa-arrow-right me-1"></i>Đến CLB của tôi
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<?php if (SessionHelper::isUser() && !$current_team_id): ?>
<div class="modal fade" id="requestTeamModal" tabindex="-1" aria-labelledby="requestTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="/webdacn_quanlyclb/team/request" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="requestTeamModalLabel">
                        <i class="fas fa-bullhorn me-2"></i>Yêu Cầu Thành Lập CLB Mới
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?php echo SessionHelper::getUserId(); ?>">

                    <div class="mb-4">
                        <label for="request_name" class="form-label">Tên Câu Lạc Bộ Đề Xuất <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="request_name" name="name" required 
                               placeholder="Nhập tên CLB mong muốn">
                    </div>
                    
                    <div class="mb-4">
                        <label for="request_khoa" class="form-label">Thuộc Khoa (nếu có)</label>
                        <input type="text" class="form-control" id="request_khoa" name="khoa" 
                               placeholder="Ví dụ: Công nghệ thông tin, Kinh tế, Ngoại ngữ...">
                    </div>
                    
                    <div class="mb-4">
                        <label for="request_reason" class="form-label">Mục Tiêu & Lý Do Thành Lập <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="request_reason" name="reason" rows="4" required 
                                  placeholder="Mô tả chi tiết mục tiêu hoạt động và lý do thành lập CLB..."></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="request_talent" class="form-label">Lĩnh Vực Hoạt Động Chính <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="request_talent" name="talent" required 
                               placeholder="Ví dụ: Âm nhạc, Thể thao, Học thuật, Tình nguyện...">
                    </div>
                    
                    <div class="mb-4">
                        <label for="request_avatar" class="form-label">Ảnh Đại Diện CLB (Logo)</label>
                        <input type="file" class="form-control" id="request_avatar" name="avatar_team" accept="image/*">
                        <div class="form-text">Chọn ảnh logo hoặc ảnh đại diện cho CLB (tối đa 5MB)</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy Bỏ
                    </button>
                    <button type="submit" class="btn-gradient">
                        <i class="fas fa-paper-plane me-2"></i>Gửi Yêu Cầu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alreadyJoinedModal = document.getElementById('alreadyJoinedModal');
    if (alreadyJoinedModal) {
        const currentTeamName = '<?= htmlspecialchars($current_team_name ?? '', ENT_QUOTES) ?>';
        alreadyJoinedModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const newTeamName = button.getAttribute('data-new-team-name');
            const modalBody = alreadyJoinedModal.querySelector('#alreadyJoinedModalBody');
            modalBody.innerHTML = `
                <p>Bạn đang là thành viên của CLB <strong style="color: var(--primary);">${currentTeamName}</strong>.</p>
                <p>Hãy rời CLB hiện tại nếu bạn muốn tham gia CLB <strong style="color: var(--primary);">${newTeamName}</strong>.</p>
            `;
        });
    }

    // Add loading state to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                // Thêm class 'btn-loading' của header.php nếu có
                // Nếu không, tự định nghĩa:
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
                submitBtn.disabled = true;
            }
        });
    });
});
</script>

<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/footer.php'; 
?>