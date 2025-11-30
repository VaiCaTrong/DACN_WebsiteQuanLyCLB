<?php
// PHP để lấy dữ liệu (Giữ nguyên)
require_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/helpers/SessionHelper.php');
if (!SessionHelper::isLoggedIn() || SessionHelper::getRole() !== 'admin') {
    header('Location: /webdacn_quanlyclb');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/models/AccountModel.php');
$accountModel = new AccountModel((new Database())->getConnection());
$accounts = $accountModel->getAllAccounts();

// Include header (Đã có)
include_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản</title>
    <style>
        :root {
            --primary: #FF6B9E;
            --primary-light: #FFD6E5;
            --primary-dark: #FF4785;
            --bg: #FFF0F5;
            --text-color: #555;
            --shadow: 0 0.25rem 1.5rem rgba(255, 107, 158, 0.2);
            --gradient: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
        }

        .container {
            max-width: 1200px;
            /* margin, padding đã có trong header.php */
            /* margin-left: 50px; <-- Có thể cần giữ lại nếu bạn muốn layout lệch trái */
             padding-bottom: 100px;
        }

        h2.page-title {
            /* Đổi tên class để tránh xung đột */
            padding: 15px 20px;
            background-color: var(--primary);
            color: white;
            margin: 0 auto 20px;
            font-size: 1.5rem;
            border-radius: 0 0 8px 8px;
            width: 80%;
            max-width: 900px;
            box-shadow: var(--shadow);
            text-align: center;
            /* Thêm căn giữa */
        }

        .table-wrapper {
            width: 95%;
            /* Mở rộng hơn một chút */
            margin: 0 auto;
            overflow-x: auto;
            box-shadow: var(--shadow);
            border-radius: 8px;
            background-color: white;
        }

        .table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
            background-color: white;
            margin-bottom: 0;
            /* Xóa margin mặc định của table */
        }

        .table thead th {
            background-color: var(--primary);
            color: white;
            padding: 12px 15px;
            /* Tăng padding */
            font-weight: 600;
            /* Đậm hơn */
            font-size: 0.95rem;
            /* To hơn chút */
            text-align: left;
            /* Căn trái */
            border-bottom: 2px solid var(--primary-dark);
        }

        .table tbody td {
            padding: 10px 15px;
            /* Tăng padding */
            vertical-align: middle;
            border-top: 1px solid var(--primary-light);
            font-size: 0.95rem;
            /* To hơn chút */
            color: var(--text-color);
        }

        .table tbody tr:nth-child(even) {
            background-color: #fdf7f9;
            /* Màu nền nhạt hơn */
        }

        .table tbody tr:hover {
            background-color: var(--primary-light);
        }

        .avatar-img {
            width: 40px;
            /* To hơn */
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-light);
        }

        .btn-action {
            padding: 5px 10px;
            /* Tăng padding */
            border: none;
            border-radius: 5px;
            /* Vuông hơn */
            font-size: 0.85rem;
            /* To hơn */
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            /* Dùng flex để icon và text căn giữa */
            align-items: center;
            gap: 5px;
            /* Khoảng cách giữa icon và text */
            margin-right: 5px;
            /* Khoảng cách giữa các nút */
            margin-bottom: 5px;
            transition: all 0.2s ease-in-out;
        }

        .btn-action i {
            font-size: 0.9em;
            /* Icon nhỏ hơn text chút */
        }

        .btn-action:hover {
            opacity: 0.85;
            transform: translateY(-1px);
            /* Hiệu ứng nhẹ */
        }

        .btn-detail {
            background-color: #0dcaf0;
            color: white;
        }

        /* Màu info */
        .btn-edit {
            background-color: #ffc107;
            color: #000;
        }

        /* Màu warning */
        .btn-disable {
            background-color: #fd7e14;
            color: white;
        }

        /* Màu orange */
        .btn-enable {
            background-color: #198754;
            color: white;
        }

        /* Màu success đậm */
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        /* Màu danger */
        .badge {
            font-size: 0.8rem;
            padding: 0.4em 0.7em;
        }

        /* Badge to hơn */
        .status-badge-active {
            background-color: #198754;
        }

        .status-badge-disabled {
            background-color: #6c757d;
        }

        /* Màu secondary */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            /* Khoảng cách đều hơn */
        }

        /* Modal Styles (Giữ nguyên hoặc dùng style của header.php) */
        /* Nếu muốn dùng modal riêng thì giữ lại CSS modal ở đây */
        /* ... CSS Modal cũ của bạn ... */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            /* Nền tối hơn */
            z-index: 1060;
            /* Cao hơn backdrop của header */
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            /* Nền trắng */
            padding: 0;
            /* Bỏ padding cũ */
            border-radius: 10px;
            /* Bo góc */
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            max-width: 450px;
            width: 90%;
            overflow: hidden;
            /* Tránh tràn viền */
        }

        .modal-header {
            /* Style lại header modal */
            background: var(--gradient);
            color: white;
            padding: 1rem 1.5rem;
            border-bottom: none;
        }

        .modal-header h3 {
            color: white;
            /* Đảm bảo chữ trắng */
            margin-bottom: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .modal-body {
            /* Style lại body modal */
            padding: 1.5rem;
            color: var(--text-color);
        }

        .modal-footer {
            /* Style lại footer modal */
            padding: 1rem 1.5rem;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: flex-end;
            /* Căn phải các nút */
            gap: 10px;
        }

        .modal-btn {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .modal-btn-confirm {
            background-color: #dc3545;
            color: #fff;
        }

        /* Màu đỏ cho xóa */
        .modal-btn-confirm:hover {
            background-color: #c82333;
        }

        .modal-btn-confirm-enable {
            background: #198754;
            color: white;
        }

        /* Xanh lá cho kích hoạt */
        .modal-btn-confirm-enable:hover {
            background: #157347;
        }

        .modal-btn-confirm-disable {
            background: #fd7e14;
            color: white;
        }

        /* Cam cho vô hiệu hóa */
        .modal-btn-confirm-disable:hover {
            background: #e66f00;
        }

        .modal-btn-cancel {
            background: #6c757d;
            color: white;
        }

        /* Xám */
        .modal-btn-cancel:hover {
            background: #5a6268;
        }

        /* CSS cho textarea lý do */
        #disableReason {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 0.5rem 0.75rem;
        }

        #disableReason:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem var(--primary-light);
        }


        @media (max-width: 900px) {
            .table-wrapper {
                width: 95%;
            }

            h2.page-title {
                width: 95%;
            }
        }

        @media (max-width: 768px) {
            .table-wrapper {
                width: 100%;
                border-radius: 0;
            }

            h2.page-title {
                width: 100%;
                border-radius: 0;
            }

            .table {
                min-width: 100%;
            }

            .table td,
            .table th {
                padding: 8px 10px;
            }

            /* Giảm padding trên mobile */
            .action-buttons {
                flex-direction: row;
            }

            /* Giữ lại hàng ngang */
            .btn-action {
                margin-bottom: 5px;
            }

            /* Vẫn giữ khoảng cách dưới */
            .modal-content {
                padding: 0;
            }

            /* Reset padding */
        }
    </style>
</head>
<div class="container mt-4">
    <h2 class="page-title">Quản lý tài khoản</h2>

    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="toast show align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['message']) ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="toast show align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <div class="table-wrapper">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Avatar</th>
                    <th>Username</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($accounts)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted fst-italic py-4">Không có tài khoản nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($accounts as $account): ?>
                        <tr>
                            <td><?= htmlspecialchars($account->id) ?></td>
                            <td>
                                <?php
                                $avatarPath = '/webdacn_quanlyclb/' . ($account->avatar ?? '');
                                $fullAvatarPath = $_SERVER['DOCUMENT_ROOT'] . $avatarPath;
                                if (!empty($account->avatar) && file_exists($fullAvatarPath) && is_file($fullAvatarPath)):
                                ?>
                                    <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Avatar" class="avatar-img">
                                <?php else: ?>
                                    <i class="fas fa-user-circle fa-2x text-secondary"></i> <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($account->username) ?></td>
                            <td><?= htmlspecialchars($account->fullname) ?></td>
                            <td><?= htmlspecialchars($account->email ?? '-') ?></td>
                            <td>
                                <span class="badge 
                                        <?= $account->role == 'admin' ? 'bg-danger' : ($account->role == 'staff' ? 'bg-warning text-dark' : 'bg-info text-dark') ?>">
                                    <?= htmlspecialchars(ucfirst($account->role)) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge 
                                        <?= $account->status == 'active' ? 'status-badge-active' : 'status-badge-disabled' ?>">
                                    <?= $account->status == 'active' ? 'Hoạt động' : 'Vô hiệu hóa' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="/webdacn_quanlyclb/account/manage/<?= $account->id ?>" class="btn-action btn-detail" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i> <span>Chi tiết</span>
                                    </a>

                                    <?php if ($account->status == 'active'): ?>
                                        <button type="button" class="btn-action btn-disable"
                                            onclick="openDisableModal(<?= $account->id ?>, '<?= htmlspecialchars($account->username) ?>')" title="Vô hiệu hóa">
                                            <i class="fas fa-ban"></i> <span>Vô hiệu hóa</span>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn-action btn-enable"
                                            onclick="openEnableModal(<?= $account->id ?>, '<?= htmlspecialchars($account->username) ?>')" title="Kích hoạt lại">
                                            <i class="fas fa-check-circle"></i> <span>Kích hoạt</span>
                                        </button>
                                    <?php endif; ?>

                                    <button type="button" class="btn-action btn-delete delete-btn"
                                        onclick="openDeleteModal(<?= $account->id ?>, '<?= htmlspecialchars($account->username) ?>')" title="Xóa tài khoản">
                                        <i class="fas fa-trash-alt"></i> <span>Xóa</span>
                                    </button>
                                    <form id="deleteForm-<?= $account->id ?>" action="/webdacn_quanlyclb/account/delete/<?= $account->id ?>" method="POST" style="display:none;"></form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div id="disableModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-lock me-2"></i>Vô hiệu hóa tài khoản</h3>
        </div>
        <form id="disableForm" method="POST" action="">
            <div class="modal-body">
                <p>Bạn đang vô hiệu hóa tài khoản: <strong id="disableUsername" class="text-danger"></strong></p>
                <div class="mb-3 text-start">
                    <label for="disableReason" class="form-label fw-bold">Lý do vô hiệu hóa: <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="disableReason" name="reason" rows="3" required
                        placeholder="Nhập lý do..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDisableModal()">Hủy</button>
                <button type="submit" class="modal-btn modal-btn-confirm-disable">Xác nhận</button>
            </div>
        </form>
    </div>
</div>

<div id="enableModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-check me-2"></i>Kích hoạt lại tài khoản</h3>
        </div>
        <form id="enableForm" method="POST" action="">
            <div class="modal-body">
                <p>Bạn có chắc muốn kích hoạt lại tài khoản: <strong id="enableUsername" class="text-success"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="closeEnableModal()">Hủy</button>
                <button type="submit" class="modal-btn modal-btn-confirm-enable">Xác nhận</button>
            </div>
        </form>
    </div>
</div>

<div id="confirmModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle text-danger me-2"></i>Xác nhận xóa tài khoản</h3>
        </div>
        <div class="modal-body">
            <p>Bạn có chắc chắn muốn xóa vĩnh viễn tài khoản: <strong id="deleteUsername" class="text-danger"></strong>?</p>
            <p class="text-danger small">Hành động này không thể hoàn tác!</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">Hủy</button>
            <button type="button" class="modal-btn modal-btn-confirm" onclick="confirmDelete()">Xác nhận xóa</button>
        </div>
    </div>
</div>

<script>
    // === JAVASCRIPT ĐÃ ĐƯỢC CẬP NHẬT ===

    // Hàm đóng chung cho các modal
    function closeModalGeneral(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Xử lý modal vô hiệu hóa
    function openDisableModal(accountId, username) {
        document.getElementById('disableUsername').textContent = username;
        document.getElementById('disableForm').action = '/webdacn_quanlyclb/account/disable/' + accountId;
        document.getElementById('disableModal').style.display = 'flex';
    }

    function closeDisableModal() {
        closeModalGeneral('disableModal');
        document.getElementById('disableReason').value = ''; // Reset textarea
    }

    // Xử lý modal kích hoạt
    function openEnableModal(accountId, username) {
        document.getElementById('enableUsername').textContent = username;
        document.getElementById('enableForm').action = '/webdacn_quanlyclb/account/enable/' + accountId;
        document.getElementById('enableModal').style.display = 'flex';
    }

    function closeEnableModal() {
        closeModalGeneral('enableModal');
    }

    // Xử lý modal xóa (Dùng form ẩn thay vì currentForm)
    let accountIdToDelete = null;

    function openDeleteModal(accountId, username) {
        accountIdToDelete = accountId;
        document.getElementById('deleteUsername').textContent = username;
        document.getElementById('confirmModal').style.display = 'flex';
    }

    function confirmDelete() {
        if (accountIdToDelete !== null) {
            const form = document.getElementById('deleteForm-' + accountIdToDelete);
            if (form) {
                form.submit();
            }
        }
        closeDeleteModal(); // Đóng modal sau khi submit
    }

    function closeDeleteModal() {
        closeModalGeneral('confirmModal');
        accountIdToDelete = null; // Reset ID
    }

    // Đóng modal khi click ra ngoài (Thêm vào)
    window.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target == modal) {
                modal.style.display = "none";
                // Reset các giá trị nếu cần (ví dụ: textarea, ID xóa)
                if (modal.id === 'disableModal') document.getElementById('disableReason').value = '';
                if (modal.id === 'confirmModal') accountIdToDelete = null;
            }
        });
    });

    // Tự động ẩn toast (Giữ nguyên)
    document.addEventListener('DOMContentLoaded', function() {
        // Sử dụng Bootstrap 5 Toast API
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function(toastEl) {
            const toast = new bootstrap.Toast(toastEl, {
                delay: 5000
            });
            // Không cần gọi show() nếu đã có class 'show'
            return toast;
        });

        // Nếu bạn muốn tự động ẩn sau khi hiện, dùng setTimeout
        // setTimeout(() => {
        //     toastList.forEach(toast => toast.hide());
        // }, 5000); // Ẩn sau 5 giây
    });
</script>
