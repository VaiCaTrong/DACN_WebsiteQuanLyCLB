<?php
require_once 'app/models/TeamModel.php';
include 'app/views/shares/header.php';
$teamModel = new TeamModel();
?>

<style>
    :root {
        --primary: #FF6B9E;
        /* Màu hồng chủ đạo */
        --primary-light: #FFD6E5;
        --primary-dark: #FF4785;
        --bg: #FFF0F5;
        --white: #fff;
        --success: #FCE4EC;
        --success-border: #FF6B9E;
        --error: #FFEBEE;
        --error-border: #F44336;
        --text-medium: #666666;
    }

    body {
        background: var(--bg);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--primary-dark);
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 900px;
        margin: 0 auto 0 auto;
        padding: 24px;
    }

    h2 {
        text-align: center;
        color: var(--primary);
        font-size: 2.2em;
        letter-spacing: 1px;
        margin-bottom: 32px;
    }

    .card {
        background: var(--white);
        border-radius: 18px;
        box-shadow: 0 6px 32px rgba(255, 107, 158, 0.08);
        padding: 24px;
        border-left: 6px solid var(--primary);
    }

    .card-title {
        color: var(--primary-dark);
        font-size: 1.5em;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--primary-light);
    }

    .card-body p {
        margin-bottom: 16px;
        font-size: 1.1em;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-body p strong {
        color: var(--primary);
        min-width: 150px;
        font-weight: 600;
    }

    .card-body p img {
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(255, 107, 158, 0.06);
        max-width: 120px;
        max-height: 120px;
        object-fit: cover;
    }

    .card-body p span {
        color: var(--text-medium);
        flex-grow: 1;
    }

    .btn-primary {
        background: var(--primary);
        color: #fff;
        padding: 12px 28px;
        border-radius: 24px;
        font-size: 1.1em;
        font-weight: bold;
        text-decoration: none;
        border: none;
        box-shadow: 0 2px 8px rgba(255, 107, 158, 0.08);
        transition: background 0.2s, box-shadow 0.2s;
        display: block;
        width: fit-content;
        margin: 20px auto 0 auto;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        box-shadow: 0 4px 16px rgba(255, 107, 158, 0.12);
    }
</style>

<div class="container my-4">
    <h2><i class="fas fa-user"></i> Thông tin cá nhân</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Thông tin tài khoản</h5>
            <p><strong>Tên đăng nhập:</strong> <span><?php echo htmlspecialchars($account['username']); ?></span></p>
            <p><strong>Họ và tên:</strong> <span><?php echo htmlspecialchars($account['fullname'] ?? 'Chưa cập nhật'); ?></span></p>
            <p><strong>Email:</strong> <span><?php echo htmlspecialchars($account['email'] ?? 'Chưa cập nhật'); ?></span></p>
            <p><strong>Số điện thoại:</strong> <span><?php echo htmlspecialchars($account['phone'] ?? 'Chưa cập nhật'); ?></span></p>
            <p><strong>Vai trò:</strong> <span>
                <?php
                $roleMap = [
                    'admin' => 'Quản trị',
                    'staff' => 'Chủ nhiệm',
                    'user' => 'Thành viên'
                ];
                echo htmlspecialchars($roleMap[$account['role']] ?? 'Không xác định');
                ?>
            </span></p>
            <p><strong>Ảnh đại diện:</strong>
                <?php if ($account['avatar']): ?>
                    <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($account['avatar']); ?>" alt="Avatar" style="width: 120px; height: 120px; object-fit: cover;">
                <?php else: ?>
                    <span>Chưa có ảnh</span>
                <?php endif; ?>
            </p>
            <p><strong>Ngày tạo:</strong> <span><?php echo htmlspecialchars($account['created_at']); ?></span></p>
            <p><strong>Cập nhật lần cuối:</strong> <span><?php echo htmlspecialchars($account['updated_at'] ?? 'Chưa cập nhật'); ?></span></p>
            <p><strong>Đội của bạn:</strong> <span>
                <?php
                $team_id = $account['team_id'];
                if ($team_id) {
                    $team = $teamModel->getTeamById($team_id);
                    echo htmlspecialchars($team['name'] ?? 'Đội không tồn tại');
                } else {
                    echo 'Chưa tham gia đội nào';
                }
                ?>
            </span></p>
            <p><strong>Điểm:</strong> <span>
                <?php
                if ($team_id) {
                    $points = $teamModel->getUserPoints($account['id'], $team_id);
                    echo htmlspecialchars($points);
                } else {
                    echo '0';
                }
                ?>
            </span></p>
            <a href="/webdacn_quanlyclb/account/edit" class="btn-primary">Chỉnh sửa thông tin</a>
        </div>
    </div>
    <?php if (SessionHelper::isUser()) : ?>
        <a href="/webdacn_quanlyclb/team/myTeam" class="btn-primary">Câu lạc bộ đã tham gia</a>
    <?php endif; ?>
    <?php if (SessionHelper::isStaff()) : ?>
        <a href="/webdacn_quanlyclb/Team/manageTeam" class="btn-primary">Quản lý câu lạc bộ</a>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>