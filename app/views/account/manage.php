<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/helpers/SessionHelper.php');
SessionHelper::requireLogin();
if (!SessionHelper::isAdmin()) {
    header('Location: /webdacn_quanlyclb');
    exit;
}

// Lấy dữ liệu (Giữ nguyên)
$account = isset($account) ? $account : [];
$userTeams = isset($userTeams) ? $userTeams : [];
// (Bạn có thể thêm $leaderTeams, $reactivationRequests nếu cần)

// Include header (Đã có)
include_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php'); 
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản - <?= htmlspecialchars($account['username'] ?? '') ?></title>
    <style>
        /* ... (Toàn bộ CSS của bạn giữ nguyên) ... */
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding-top đã có trong header.php */
        }
        .container {
            max-width: 1200px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%);
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 15px 20px;
        }
        .card-title {
            margin: 0;
            font-size: 1.5rem;
        }
        .card-body {
            padding: 25px;
        }
        .avatar-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .info-item {
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        .info-item strong {
            color: #343a40;
            min-width: 150px;
            display: inline-block;
        }
        .btn-action {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
    </style>
</head>
<div class="container mt-4">
        <h2 class="mb-4 text-center text-secondary">Quản lý tài khoản: <strong><?= htmlspecialchars($account['username'] ?? '') ?></strong></h2>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-circle me-2"></i>Thông tin tài khoản</h3>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-4 mb-md-0">
                        <?php 
                            // Kiểm tra avatar an toàn hơn
                            $avatarPath = '/webdacn_quanlyclb/' . ($account['avatar'] ?? '');
                            $fullAvatarPath = $_SERVER['DOCUMENT_ROOT'] . $avatarPath;
                            if (!empty($account['avatar']) && file_exists($fullAvatarPath) && is_file($fullAvatarPath)): 
                        ?>
                            <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Avatar" class="avatar-img">
                        <?php else: ?>
                            <i class="fas fa-user-circle fa-7x text-muted"></i>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-9">
                        <div class="info-item"><strong>Username:</strong> <?= htmlspecialchars($account['username'] ?? '-') ?></div>
                        <div class="info-item"><strong>Họ tên:</strong> <?= htmlspecialchars($account['fullname'] ?? '-') ?></div>
                        <div class="info-item"><strong>Email:</strong> <?= htmlspecialchars($account['email'] ?? '-') ?></div>
                        <div class="info-item"><strong>Số điện thoại:</strong> <?= htmlspecialchars($account['phone'] ?? '-') ?></div>
                        <div class="info-item"><strong>Vai trò:</strong> <span class="badge bg-info fs-6 text-dark"><?= htmlspecialchars(ucfirst($account['role'] ?? '-')) ?></span></div>
                        <div class="info-item"><strong>Trạng thái:</strong> 
                            <span class="badge fs-6 <?= ($account['status'] ?? 'disabled') == 'active' ? 'bg-success' : 'bg-danger' ?>">
                                <?= ($account['status'] ?? 'disabled') == 'active' ? 'Đang hoạt động' : 'Đã vô hiệu hóa' ?>
                            </span>
                        </div>
                        <?php if (($account['status'] ?? 'disabled') == 'disabled'): ?>
                            <div class="info-item"><strong>Lý do:</strong> <em class="text-danger"><?= htmlspecialchars($account['disable_reason'] ?? 'Không có') ?></em></div>
                            <div class="info-item"><strong>Thời gian:</strong> <em class="text-muted"><?= isset($account['disabled_at']) ? date('d/m/Y H:i', strtotime($account['disabled_at'])) : '-' ?></em></div>
                        <?php endif; ?>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                            <div>
                                <?php if (($account['status'] ?? 'disabled') == 'disabled'): ?>
                                    <button type="button" class="btn btn-success btn-action" data-bs-toggle="modal" data-bs-target="#enableAccountModal">
                                        <i class="fas fa-check-circle me-2"></i> Kích hoạt lại
                                    </button>
                                <?php endif; ?>
                                 <a href="/webdacn_quanlyclb/account" class="btn btn-secondary btn-action ms-2">
                                    <i class="fas fa-arrow-left me-2"></i> Quay lại
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users me-2"></i>Câu lạc bộ đã tham gia</h3>
            </div>
            <div class="card-body">
                <?php if (empty($userTeams)): ?>
                    <p class="text-muted fst-italic">Người dùng này chưa tham gia câu lạc bộ nào.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($userTeams as $team): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h5 class="mb-1"><a href="/webdacn_quanlyclb/team/detail/<?= $team['id'] ?>" class="text-decoration-none" style="color: #E91E63;"><?= htmlspecialchars($team['name']) ?></a></h5>
                                    <small class="text-muted">Tham gia vào: <?= isset($team['joined_at']) ? date('d/m/Y H:i', strtotime($team['joined_at'])) : 'N/A' ?></small>
                                </div>
                                <span class="badge bg-primary rounded-pill fs-6">Điểm: <?= htmlspecialchars($team['point'] ?? 'N/A') ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        </div> <div class="modal fade" id="enableAccountModal" tabindex="-1" aria-labelledby="enableAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header bg-success text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title" id="enableAccountModalLabel"><i class="fas fa-check-circle me-2"></i>Xác nhận kích hoạt tài khoản</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    Bạn có chắc chắn muốn kích hoạt lại tài khoản <strong><?= htmlspecialchars($account['username'] ?? '') ?></strong>?
                </div>
                <div class="modal-footer" style="background-color: #f8f9fa; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 20px;">Hủy bỏ</button>
                    <form action="/webdacn_quanlyclb/account/enable/<?= htmlspecialchars($account['id'] ?? '') ?>" method="POST" style="display:inline;">
                        <button type="submit" class="btn btn-success" style="border-radius: 20px;"><i class="fas fa-check me-1"></i> Xác nhận</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/footer.php'); 
?>