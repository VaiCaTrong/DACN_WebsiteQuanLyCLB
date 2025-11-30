<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/helpers/SessionHelper.php');
SessionHelper::start();

// Kiểm tra session disabled_user
if (!isset($_SESSION['disabled_user'])) {
    header('Location: /webdacn_quanlyclb/account/login');
    exit();
}

$disabledUser = $_SESSION['disabled_user'];
$username = htmlspecialchars($disabledUser['username']);
$reason = htmlspecialchars($disabledUser['disable_reason'] ?? 'Không có lý do cụ thể');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản bị vô hiệu hóa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .disabled-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(220, 53, 69, 0.2);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            position: relative;
        }
        .alert-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .alert-title {
            font-size: 2rem;
            color: #dc3545;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .alert-message {
            font-size: 1.2rem;
            color: #495057;
            margin-bottom: 25px;
        }
        .reason-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
            text-align: left;
            font-style: italic;
        }
        .btn-custom {
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
        }
        .btn-logout {
            background: #6c757d;
            color: white;
        }
        .btn-logout:hover {
            background: #5a6268;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .btn-request {
            background: #0d6efd;
            color: white;
        }
        .btn-request:hover {
            background: #0b5ed7;
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .alert-container {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            z-index: 1050;
        }
    </style>
</head>
<body>
    <div class="disabled-container">
        <div class="alert-container">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        </div>

        <i class="fas fa-ban alert-icon"></i>
        <h1 class="alert-title">Tài khoản bị vô hiệu hóa</h1>
        <p class="alert-message">Tài khoản của bạn (<strong><?= $username ?></strong>) đã bị vô hiệu hóa.</p>
        <div class="reason-box">
            <strong>Lý do:</strong> <?= $reason ?>
        </div>

        <div class="action-buttons">
            <a href="/webdacn_quanlyclb/account/logout" class="btn-custom btn-logout">
                <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
            </a>
            <button type="button" class="btn-custom btn-request" data-bs-toggle="modal" data-bs-target="#reactivationModal">
                <i class="fas fa-paper-plane me-2"></i>Yêu cầu mở lại
            </button>
        </div>
    </div>

    <div class="modal fade" id="reactivationModal" tabindex="-1" aria-labelledby="reactivationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reactivationModalLabel">Yêu cầu kích hoạt lại tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/webdacn_quanlyclb/account/requestReactivation" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reasonTextarea" class="form-label">Lý do bạn muốn mở lại tài khoản:</label>
                            <textarea class="form-control" id="reasonTextarea" name="reason" rows="4" placeholder="Vui lòng trình bày lý do của bạn một cách rõ ràng..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>