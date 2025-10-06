<?php include 'app/views/shares/header.php'; ?>
<link rel="stylesheet" href="/webdacn_quanlyclb/public/css/team/view.css">

<style>
    :root {
        --primary: #FF6B9E; /* Màu hồng chủ đạo */
        --primary-light: #FFD6E5;
        --primary-dark: #FF4785;
        --bg: #FFF0F5;
        --text-color: #555;
        --shadow: 0 0.25rem 1.5rem rgba(255, 107, 158, 0.2);
        --gradient: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
    }

    .team-detail {
        max-width: 900px;
        margin: 40px auto;
        padding: 30px;
        background: var(--bg);
        border-radius: 15px;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
    }

    .team-detail::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: var(--gradient);
    }

    .team-avatar {
        width: 200px;
        height: 200px;
        object-fit: cover;
        border-radius: 50%;
        border: 6px solid var(--primary-light);
        box-shadow: 0 0.3rem 1rem rgba(255, 107, 158, 0.3);
        margin-bottom: 25px;
        transition: transform 0.3s ease;
    }

    .team-avatar:hover {
        transform: scale(1.05);
    }

    .detail-item {
        margin-bottom: 20px;
        padding: 10px 15px;
        background: #fff;
        border-left: 4px solid var(--primary-light);
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .detail-item:hover {
        background: var(--primary-light);
        border-left-color: var(--primary);
    }

    .detail-item label {
        font-weight: 600;
        color: var(--primary-dark);
        margin-right: 10px;
        display: inline-block;
        min-width: 120px;
    }

    .detail-item span {
        color: var(--text-color);
        word-break: break-word;
    }

    .back-btn, .btn-warning, .btn-danger {
        margin-top: 20px;
        padding: 10px 20px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .back-btn {
        background: #6c757d;
        color: #fff;
        margin-right: 10px;
    }

    .back-btn:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.2);
    }

    .btn-warning {
        background: #f6c23e;
        color: #fff;
    }

    .btn-warning:hover {
        background: #dda10a;
        transform: translateY(-2px);
        box-shadow: 0 0.2rem 0.5rem rgba(246, 194, 62, 0.3);
    }

    .btn-danger {
        background: #e74a3b;
        color: #fff;
    }

    .btn-danger:hover {
        background: #c9302c;
        transform: translateY(-2px);
        box-shadow: 0 0.2rem 0.5rem rgba(231, 74, 59, 0.3);
    }

    @media (max-width: 768px) {
        .team-detail {
            margin: 20px;
            padding: 20px;
        }

        .team-avatar {
            width: 150px;
            height: 150px;
        }

        .detail-item label {
            min-width: 90px;
        }
    }
</style>

<div class="container mt-5">
    <div class="team-detail">
        <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($team['avatar_team'] ?? '/uploads/default_team.jpg', ENT_QUOTES, 'UTF-8'); ?>" 
             alt="Avatar đội <?php echo htmlspecialchars($team['name'] ?? 'Đội không tên', ENT_QUOTES, 'UTF-8'); ?>" 
             class="team-avatar" onerror="this.src='/webdacn_quanlyclb/uploads/default_team.jpg';">
        <div class="detail-item">
            <label>Tên đội:</label>
            <span><?php echo htmlspecialchars($team['name'] ?? 'Chưa có tên', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <div class="detail-item">
            <label>Mô tả:</label>
            <span><?php echo htmlspecialchars($team['description'] ?? 'Chưa có mô tả', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <div class="detail-item">
            <label>Số lượng thành viên:</label>
            <span><?php echo htmlspecialchars($team['quantity_user'] ?? '0', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <div class="detail-item">
            <label>Tài năng:</label>
            <span><?php echo htmlspecialchars($team['talent'] ?? 'Chưa cập nhật', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <div class="detail-item">
            <label>Ghi chú:</label>
            <span><?php echo htmlspecialchars($team['note'] ?? 'Chưa có ghi chú', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <div class="detail-item">
            <label>Chủ nhiệm:</label>
            <span><?php echo htmlspecialchars($team['creator_name'] ?? 'Chưa rõ', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <a href="/webdacn_quanlyclb/Team" class="btn btn-secondary back-btn">Quay lại</a>
        <?php if ($this->isAdmin()): ?>
            <a href="/webdacn_quanlyclb/Team/edit/<?php echo $team['id']; ?>" class="btn btn-warning btn-sm">Sửa</a>
            <a href="/webdacn_quanlyclb/Team/delete/<?php echo $team['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
        <?php endif; ?>
    </div>
    
</div>
<?php include 'app/views/shares/footer.php'; ?>