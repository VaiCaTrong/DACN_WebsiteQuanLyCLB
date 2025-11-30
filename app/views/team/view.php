<?php include 'app/views/shares/header.php'; ?>

<style>
    :root {
        --primary: #E91E63;
        --primary-light: #FCE4EC;
        --primary-dark: #C2185B;
        --bg: #FFF9FB; /* Nền nhạt hơn */
        --white: #fff;
        --text-dark: #333;
        --text-medium: #666;
        --border-color: #F8BBD0;
        --shadow: 0 8px 25px rgba(233, 30, 99, 0.1);
        --gradient: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        --danger: #dc3545;
        --warning: #ffc107;
    }

    body {
        background-color: var(--bg); /* Áp dụng nền cho toàn trang */
    }

    .team-view-container {
        max-width: 1000px;
        margin: 2rem auto;
    }

    .team-view-card {
        background: var(--white);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden; /* Giữ bo góc */
        border: 1px solid var(--primary-light);
    }

    /* === HEADER CỦA CARD (CHỨA ẢNH NỀN VÀ AVATAR) === */
    .team-view-header {
        position: relative;
        height: 250px; /* Chiều cao ảnh bìa */
        background: linear-gradient(135deg, #FCE4EC 0%, #F48FB1 100%); /* Nền dự phòng */
    }
    
    .team-cover-image {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Ảnh bìa sẽ che phủ */
        opacity: 0.5; /* Làm mờ ảnh bìa */
    }
    
    .team-avatar-wrapper {
        position: absolute;
        bottom: -75px; /* Đẩy 1 nửa avatar xuống dưới */
        left: 50%;
        transform: translateX(-50%);
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid var(--white);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        overflow: hidden;
        background-color: var(--white); /* Nền cho ảnh */
    }
    
    .team-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* === BODY CỦA CARD (CHỨA THÔNG TIN) === */
    .team-view-body {
        padding: 2.5rem;
        padding-top: 90px; /* Khoảng trống cho avatar */
        text-align: center;
    }

    .team-view-name {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin: 0;
    }

    .team-view-creator {
        font-size: 1.1rem;
        color: var(--text-medium);
        margin-bottom: 1.5rem;
    }
    .team-view-creator strong {
        color: var(--text-dark);
    }
    
    /* Danh sách thông tin chi tiết */
    .team-details-list {
        list-style: none;
        padding: 0;
        margin: 2.5rem 0;
        text-align: left;
        display: grid;
        grid-template-columns: 1fr; /* 1 cột mặc định */
        gap: 1rem;
    }

    .detail-item {
        display: flex;
        align-items: flex-start;
        padding: 1.25rem;
        background: rgba(255, 107, 158, 0.05); /* Nền hồng siêu nhạt */
        border-radius: 12px;
        border-left: 4px solid var(--primary);
    }
    
    .detail-item i {
        color: var(--primary);
        font-size: 1.2rem;
        width: 30px;
        text-align: center;
        margin-right: 15px;
        margin-top: 3px; /* Căn chỉnh icon */
    }

    .detail-content {
        flex: 1;
    }

    .detail-label {
        font-weight: 700;
        color: var(--primary-dark);
        display: block;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        color: var(--text-dark);
        font-size: 1.1rem;
        line-height: 1.6;
        word-break: break-word;
    }

    /* Nút bấm ở cuối */
    .team-view-actions {
        display: flex;
        flex-wrap: wrap; /* Cho phép xuống hàng trên mobile */
        justify-content: center;
        gap: 15px;
        margin-top: 1.5rem;
        padding-top: 2rem;
        border-top: 1px solid var(--primary-light);
    }
    
    .btn-action {
        padding: 12px 28px;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-back {
        background-color: #6c757d;
        color: white;
    }
    .btn-back:hover { background-color: #5a6268; }
    
    .btn-admin-edit {
        background-color: #ffcd36ff;
        color: #000;
    }
     .btn-admin-edit:hover { background-color: #efba1cff; }
     
    .btn-admin-delete {
        background-color: #f22136ff;
        color: black;
    }
    .btn-admin-delete:hover { background-color: #e30a20ff; }

    /* Responsive */
    @media (max-width: 768px) {
        .team-view-container { margin: 1rem; }
        .team-view-body { padding: 2rem 1.5rem; padding-top: 80px; }
        .team-avatar-wrapper { width: 120px; height: 120px; bottom: -60px; }
        .team-view-name { font-size: 1.8rem; }
        .team-details-list { grid-template-columns: 1fr; }
        .btn-action { width: 100%; justify-content: center; } /* Nút chiếm 100% */
    }
</style>

<div class="team-view-container">
    <div class="team-view-card">
        
        <div class="team-view-header">
            <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($team['avatar_team'] ?? '/uploads/default_team.jpg', ENT_QUOTES, 'UTF-8'); ?>" 
                 alt="Ảnh bìa" 
                 class="team-cover-image"
                 onerror="this.src='/webdacn_quanlyclb/uploads/default_team.jpg';">
            
            <div class="team-avatar-wrapper">
                 <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($team['avatar_team'] ?? '/uploads/default_team.jpg', ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="Avatar đội" 
                     class="team-avatar"
                     onerror="this.src='/webdacn_quanlyclb/uploads/default_team.jpg';">
            </div>
        </div>

        <div class="team-view-body">
            
            <h1 class="team-view-name"><?php echo htmlspecialchars($team['name'] ?? 'Chưa có tên'); ?></h1>
            <p class="team-view-creator">
                Thành lập bởi <strong><?php echo htmlspecialchars($team['creator_name'] ?? 'Chưa rõ'); ?></strong>
            </p>

            <ul class="team-details-list">
                <li class="detail-item" style="grid-column: 1 / -1;">
                    <i class="fas fa-info-circle"></i>
                    <div class="detail-content">
                        <span class="detail-label">Mô tả</span>
                        <span class="detail-value"><?php echo htmlspecialchars($team['description'] ?? 'Chưa có mô tả'); ?></span>
                    </div>
                </li>
                <li class="detail-item">
                    <i class="fas fa-star"></i>
                    <div class="detail-content">
                        <span class="detail-label">Lĩnh vực / Tài năng</span>
                        <span class="detail-value"><?php echo htmlspecialchars($team['talent'] ?? 'Chưa cập nhật'); ?></span>
                    </div>
                </li>
                <li class="detail-item">
                    <i class="fas fa-users"></i>
                    <div class="detail-content">
                        <span class="detail-label">Số lượng thành viên</span>
                        <span class="detail-value"><?php echo htmlspecialchars($team['quantity_user'] ?? '0'); ?></span>
                    </div>
                </li>
                 <li class="detail-item" style="grid-column: 1 / -1;">
                    <i class="fas fa-sticky-note"></i>
                    <div class="detail-content">
                        <span class="detail-label">Ghi chú</span>
                        <span class="detail-value"><?php echo htmlspecialchars($team['note'] ?? 'Chưa có ghi chú'); ?></span>
                    </div>
                </li>
            </ul>

            <div class="team-view-actions">
                <a href="/webdacn_quanlyclb/Team" class="btn btn-action btn-back">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
                
                <?php if ($this->isAdmin()): // Giả sử controller có phương thức isAdmin() ?>
                    <a href="/webdacn_quanlyclb/Team/edit/<?php echo $team['id']; ?>" class="btn btn-action btn-admin-edit">
                        <i class="fas fa-edit me-1"></i> Sửa (Admin)
                    </a>
                    
                    <button type="button" class="btn btn-action btn-admin-delete" data-bs-toggle="modal" data-bs-target="#deleteTeamModal">
                        <i class="fas fa-trash-alt me-1"></i> Xóa (Admin)
                    </button>
                <?php endif; ?>
            </div>

        </div> </div> </div> <?php if ($this->isAdmin()): ?>
<div class="modal fade" id="deleteTeamModal" tabindex="-1" aria-labelledby="deleteTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header bg-danger text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title" id="deleteTeamModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Xác nhận xóa CLB</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 px-4">
                <p>Bạn có chắc chắn muốn xóa vĩnh viễn CLB:</p>
                <p class="fs-5 fw-bold text-danger">"<?php echo htmlspecialchars($team['name']); ?>"</p>
                <p class="text-danger small">
                    <i class="fas fa-info-circle me-1"></i> 
                    Hành động này không thể hoàn tác! Tất cả thành viên, điểm số, và dữ liệu liên quan sẽ bị mất.
                </p>
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 20px;">Hủy bỏ</button>
                
                <form action="/webdacn_quanlyclb/Team/delete/<?php echo $team['id']; ?>" method="POST" style="display:inline;">
                    <button type="submit" class="btn btn-danger" style="border-radius: 20px;">
                        <i class="fas fa-trash-alt me-1"></i> Xóa vĩnh viễn
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php include 'app/views/shares/footer.php'; ?>