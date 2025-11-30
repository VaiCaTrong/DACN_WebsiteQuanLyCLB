<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/helpers/SessionHelper.php';
SessionHelper::start();
SessionHelper::requireLogin();

if (!SessionHelper::isAdmin()) {
    header('Location: /webdacn_quanlyclb');
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/models/TeamModel.php';
$teamModel = new TeamModel();
$requests = $teamModel->getPendingRequests();
?>

<?php include 'app/views/shares/header.php'; ?>
<style>
        :root {
            --primary: #E91E63;
            --primary-light: #FCE4EC;
            --primary-dark: #C2185B;
            --success: #00b894;
            --danger: #d63031;
            --warning: #fdcb6e;
            --bg-light: #f8f9fa;
            --text-dark: #2d3436;
            --text-light: #636e72;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1rem 0;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }

        .search-box {
            position: relative;
            margin-bottom: 0;
        }

        .search-box input {
            padding-left: 2.5rem;
            border-radius: 50px;
            border: 1px solid rgba(0, 0, 0, 0.2);
            background-color: rgb(45, 43, 43);
            color: white;
            width: 250px;
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header .badge {
            background-color: var(--primary);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
        }

        .table-responsive {
            border-radius: 0 0 12px 12px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(233, 30, 99, 0.05);
        }

        .table tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-top: 1px solid rgba(0, 0, 0, 0.03);
            font-size: 0.9rem;
        }

        .avatar-column img {
            max-width: 50px;
            max-height: 50px;
            border-radius: 20px;
        }

        .btn-approve {
            background-color: var(--success);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .btn-reject {
            background-color: var(--danger);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .btn-approve:hover,
        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            opacity: 0.9;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-light);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 12px;
        }

        .empty-state i {
            font-size: 2.5rem;
            color: var(--primary-light);
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .table-responsive {
                border-radius: 0;
            }

            .table thead {
                display: none;
            }

            .table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border: none;
                padding: 0.5rem 1rem;
            }

            .table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--primary);
                margin-right: 1rem;
                min-width: 100px;
            }

            .avatar-column img {
                max-width: 40px;
                max-height: 40px;
            }

            .action-buttons {
                display: flex;
                justify-content: flex-end;
                width: 100%;
            }

            .header .container {
                flex-direction: column;
                gap: 1rem;
            }

            .search-box input {
                width: 100%;
            }
        }

        .card {
            margin-left: 50px;
            margin-right: 50px;
            max-width: 1035px;
            margin: 0 auto;
        }

        .table thead th {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }

        .table tbody td {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }

        .btn-approve,
        .btn-reject {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .search-box {
            padding-left: 20px;
        }

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
            min-width: 300px;
        }

        .toast {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .toast-success {
            background-color: #28a745;
            color: white;
        }

        .toast-error {
            background-color: #d63031;
            color: white;
        }

        .toast-header {
            border-bottom: none;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 0.75rem 1rem;
        }

        .toast-body {
            padding: 1rem;
        }

        .toast-icon {
            font-size: 1.25rem;
            margin-right: 10px;
        }

        .btn-close-white {
            filter: invert(1) brightness(100%);
        }

        /* Animation */
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        .toast.show {
            animation: slideIn 0.3s ease-out;
        }

        .toast.hide {
            animation: fadeOut 0.5s ease-out;
        }
    </style>

<div class="toast-container"></div>
    <div class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-clipboard-check me-2"></i>Duyệt Yêu Cầu Câu Lạc Bộ</h1>
                <div class="search-box">
                    <i class="fas fa-search" style="padding-left: 20px;"></i>
                    <input type="text" class="form-control" placeholder="Tìm kiếm yêu cầu..." style="width: 250px;">
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list-check me-2"></i>Danh sách yêu cầu chờ duyệt</span>
                <span class="badge"><?= count($requests) ?> yêu cầu</span>
            </div>

            <div class="card-body p-0">
                <?php if (empty($requests)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h4>Không có yêu cầu nào để duyệt</h4>
                        <p class="text-muted">Hiện tại không có yêu cầu tạo câu lạc bộ nào đang chờ duyệt</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Avatar</th>
                                    <th>Mã Đội</th>
                                    <th>Người Tạo</th>
                                    <th>Tên CLB</th>
                                    <th>Khoa</th>
                                    <th>Lý Do</th>
                                    <th>Tài Năng</th>
                                    <th>Ngày Tạo</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td class="avatar-column" data-label="Avatar">
                                            <?php if ($request['avatar_team']): ?>
                                                <img src="/webdacn_quanlyclb/<?= htmlspecialchars($request['avatar_team'], ENT_QUOTES, 'UTF-8') ?>" alt="Avatar <?= htmlspecialchars($request['name']) ?>" onerror="this.src='/webdacn_quanlyclb/uploads/default_team.jpg';">
                                            <?php else: ?>
                                                <img src="/webdacn_quanlyclb/uploads/default_team.jpg" alt="No Avatar">
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Mã Đội"><?= htmlspecialchars($request['team_id']) ?></td>
                                        <td data-label="Người Tạo"><?= htmlspecialchars($request['creator_name'] ?? 'Không xác định') ?></td>
                                        <td data-label="Tên CLB"><?= htmlspecialchars($request['name']) ?></td>
                                        <td data-label="Khoa"><?= htmlspecialchars($request['khoa'] ?? 'Chưa có') ?></td>
                                        <td data-label="Lý Do"><?= htmlspecialchars($request['reason']) ?></td>
                                        <td data-label="Tài Năng"><?= htmlspecialchars($request['talent']) ?></td>
                                        <td data-label="Ngày Tạo"><?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></td>
                                        <td data-label="Hành Động">
                                            <div class="action-buttons">
                                                <button class="btn btn-approve me-2" style="background-color: #28a745;" onclick="approveRequest('<?= htmlspecialchars($request['team_id']) ?>')">
                                                    <i class="fas fa-check me-1"></i>Duyệt
                                                </button>
                                                <button class="btn btn-reject" style="background-color: #d63031;" onclick="rejectRequest('<?= htmlspecialchars($request['team_id']) ?>')">
                                                    <i class="fas fa-times me-1"></i>Từ chối
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-circle me-2"></i>Xác nhận</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalMessage"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>

<script>
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        let currentTeamId = null;
        let actionType = null;

        function showToast(message, type) {
            const toastContainer = document.querySelector('.toast-container');
            const toastId = `toast-${Date.now()}`;
            const toastHtml = `
                <div id="${toastId}" class="toast ${type === 'success' ? 'toast-success' : 'toast-error'} align-items-center" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <i class="toast-icon ${type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'}"></i>
                        <strong class="me-auto ms-2">${type === 'success' ? 'Thành công' : 'Lỗi'}</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);

            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                delay: 5000
            });
            toast.show();

            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }

        function approveRequest(teamId) {
            currentTeamId = teamId;
            actionType = 'approve';
            document.getElementById('modalMessage').innerHTML =
                `<p>Bạn có chắc chắn muốn duyệt yêu cầu này?</p>
                 <p class="text-muted">Câu lạc bộ sẽ được tạo và thông báo sẽ được gửi tới người đăng ký.</p>`;
            document.getElementById('confirmAction').className = 'btn btn-success';
            document.getElementById('confirmAction').innerHTML = '<i class="fas fa-check me-1"></i>Duyệt';
            confirmModal.show();
        }

        function rejectRequest(teamId) {
            currentTeamId = teamId;
            actionType = 'reject';
            document.getElementById('modalMessage').innerHTML =
                `<p>Bạn có chắc chắn muốn từ chối yêu cầu này?</p>
                 <p class="text-muted">Yêu cầu sẽ bị xóa và thông báo sẽ được gửi tới người đăng ký.</p>`;
            document.getElementById('confirmAction').className = 'btn btn-danger';
            document.getElementById('confirmAction').innerHTML = '<i class="fas fa-times me-1"></i>Từ chối';
            confirmModal.show();
        }

        document.getElementById('confirmAction').addEventListener('click', function() {
            if (actionType === 'approve') {
                fetch('/webdacn_quanlyclb/team/approveRequest', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': '<?= htmlspecialchars(SessionHelper::getCsrfToken()) ?>'
                        },
                        body: JSON.stringify({
                            team_id: currentTeamId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        confirmModal.hide();
                        if (data.success) {
                            showToast('Đã duyệt yêu cầu và tạo CLB thành công!', 'success');
                            // Load lại trang ngay lập tức để xóa phiếu
                            setTimeout(() => location.reload(), 1000); // Giảm thời gian chờ xuống 1 giây
                        } else {
                            showToast('Có lỗi xảy ra: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        confirmModal.hide();
                        showToast('Có lỗi xảy ra khi gửi yêu cầu', 'error');
                    });
            } else if (actionType === 'reject') {
                fetch('/webdacn_quanlyclb/team/rejectRequest', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': '<?= htmlspecialchars(SessionHelper::getCsrfToken()) ?>'
                        },
                        body: JSON.stringify({
                            team_id: currentTeamId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        confirmModal.hide();
                        if (data.success) {
                            showToast('Đã từ chối yêu cầu thành công!', 'success');
                            // Load lại trang ngay lập tức để xóa phiếu
                            setTimeout(() => location.reload(), 1000); // Giảm thời gian chờ xuống 1 giây
                        } else {
                            showToast('Có lỗi xảy ra: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        confirmModal.hide();
                        showToast('Có lỗi xảy ra khi gửi yêu cầu', 'error');
                    });
            }
        });

        document.querySelector('.search-box input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
