<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/helpers/SessionHelper.php');
// Kiểm tra quyền admin
if (!SessionHelper::isLoggedIn() || SessionHelper::getRole() !== 'admin') {
    header('Location: /webdacn_quanlyclb');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/models/AccountModel.php');
$accountModel = new AccountModel((new Database())->getConnection());
$accounts = $accountModel->getAllAccounts();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
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

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--bg);
        margin: 0;
        padding: 0;
        padding-top: 60px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
        margin-left: 50px;
    }

    h2 {
        padding: 15px 20px;
        background-color: var(--primary);
        color: white;
        margin: 0 auto 20px;
        font-size: 1.5rem;
        border-radius: 0 0 8px 8px;
        width: 80%;
        max-width: 900px;
        box-shadow: var(--shadow);
    }

    .table-wrapper {
        width: 60%;
        margin: 0 auto;
        overflow-x: auto;
        box-shadow: var(--shadow);
        border-radius: 8px;
        background-color: white;
    }

    .table {
        width: 100%;
        min-width: 600px;
        border-collapse: collapse;
        background-color: white;
    }

    .table thead th {
        background-color: var(--primary);
        color: white;
        padding: 10px 12px;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .table tbody td {
        padding: 8px 12px;
        vertical-align: middle;
        border-top: 1px solid var(--primary-light);
        font-size: 0.9rem;
    }

    .table tbody tr:nth-child(even) {
        background-color: var(--bg);
    }

    .table tbody tr:hover {
        background-color: var(--primary-light);
    }

    .avatar-img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--primary-light);
    }

    .btn-action {
        padding: 4px 8px;
        border: none;
        border-radius: 3px;
        font-size: 0.8rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        margin-right: 3px;
        transition: all 0.3s;
    }

    .btn-edit {
        background-color: var(--primary-light);
        color: var(--primary-dark);
    }

    .btn-edit:hover {
        background-color: var(--primary);
        color: white;
    }

    .btn-delete {
        background-color: var(--primary-dark);
        color: white;
    }

    .btn-delete:hover {
        background-color: #e91e63;
    }

    .toast-success {
        background-color: var(--primary) !important;
    }

    .toast-error {
        background-color: var(--primary-dark) !important;
    }

    .toast-header-success {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .toast-header-error {
        background-color: rgba(0, 0, 0, 0.1);
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: var(--bg);
        padding: 20px;
        border-radius: 15px;
        box-shadow: var(--shadow);
        max-width: 400px;
        width: 90%;
        text-align: center;
        position: relative;
    }

    .modal-content h3 {
        color: var(--primary-dark);
        margin-bottom: 15px;
        font-weight: 600;
    }

    .modal-content p {
        color: var(--text-color);
        margin-bottom: 20px;
    }

    .modal-buttons {
        display: flex;
        justify-content: space-around;
    }

    .modal-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .modal-btn-confirm {
        background: var(--gradient);
        color: #fff;
    }

    .modal-btn-confirm:hover {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        transform: translateY(-2px);
    }

    .modal-btn-cancel {
        background: #ddd;
        color: var(--text-color);
    }

    .modal-btn-cancel:hover {
        background: #ccc;
        transform: translateY(-2px);
    }

    @media (max-width: 900px) {
        .table-wrapper {
            width: 90%;
        }
        h2 {
            width: 90%;
        }
    }

    @media (max-width: 768px) {
        .table-wrapper {
            width: 100%;
            border-radius: 0;
        }
        h2 {
            width: 100%;
            border-radius: 0;
        }
        .table {
            min-width: 100%;
        }
        .table td {
            padding: 6px;
        }
        .btn-action {
            display: block;
            width: 100%;
            margin-bottom: 3px;
        }
        .modal-content {
            padding: 15px;
        }
    }
</style>

<body>
    
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php'); ?>
    <div class="table-wrapper">
        <div class="container mt-2">
            <h2 class="mb-4">Quản lý tài khoản</h2>

            <!-- Hiển thị thông báo -->
            <div class="toast-container">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="toast show toast-success" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header toast-header-success">
                            <strong class="me-auto"><i class="fas fa-check-circle me-2"></i>Thành công</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            <i class="fas fa-check me-2"></i><?= htmlspecialchars($_SESSION['message']) ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="toast show toast-error" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header toast-header-error">
                            <strong class="me-auto"><i class="fas fa-exclamation-circle me-2"></i>Lỗi</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            <i class="fas fa-exclamation me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
            </div>

            <!-- Bảng danh sách tài khoản -->
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Avatar</th>
                        <th>Username</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Vai trò</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                        <tr>
                            <td><?= htmlspecialchars($account->id) ?></td>
                            <td>
                                <?php if (!empty($account->avatar) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $account->avatar)): ?>
                                    <img src="/webdacn_quanlyclb/<?= htmlspecialchars($account->avatar) ?>" alt="Avatar" class="avatar-img">
                                <?php else: ?>
                                    <i class="fas fa-user-circle fa-2x"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($account->username) ?></td>
                            <td><?= htmlspecialchars($account->fullname) ?></td>
                            <td><?= htmlspecialchars($account->email ?? '-') ?></td>
                            <td><?= htmlspecialchars($account->phone ?? '-') ?></td>
                            <td><?= htmlspecialchars($account->role) ?></td>
                            <td>
                                <a href="/webdacn_quanlyclb/account/edit/<?= $account->id ?>" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Vô hiệu hóa tài khoản
                                </a>
                                <form action="/webdacn_quanlyclb/account/delete/<?= $account->id ?>" method="POST" style="display:inline;" class="delete-form" data-account-id="<?= $account->id ?>">
                                    <button type="submit" class="btn-action btn-delete delete-btn">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3>Xác nhận xóa tài khoản</h3>
            <p>Bạn có chắc chắn muốn xóa tài khoản này không?</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-confirm" onclick="confirmDelete()">Xác nhận</button>
                <button class="modal-btn modal-btn-cancel" onclick="closeModal()">Hủy</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        let currentForm = null;

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                currentForm = this.closest('.delete-form');
                document.getElementById('confirmModal').style.display = 'flex';
            });
        });

        function confirmDelete() {
            if (currentForm) {
                currentForm.submit();
            }
        }

        function closeModal() {
            document.getElementById('confirmModal').style.display = 'none';
            currentForm = null;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.classList.remove('show');
                    toast.classList.add('hide');
                }, 5000);
            });
        });
    </script>
</body>

</html>