<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php';
?>

<style>
    .locked-groups-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .locked-groups-container h1 {
        color: #dc3545;
        font-weight: 700;
        text-align: center;
        margin-bottom: 2rem;
    }

    .table thead th {
        background-color: #343a40;
        color: white;
        border-bottom: none;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn-unlock {
        background-color: #198754;
        color: white;
        border-radius: 50px;
        padding: 5px 15px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .btn-unlock:hover {
        background-color: #146c43;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
    }
</style>

<div class="locked-groups-container">
    <h1><i class="fas fa-lock me-3"></i>Quản lý Nhóm bị khóa</h1>

    <?php if (empty($lockedGroups)) : ?>
        <div class="alert alert-success text-center">
            <i class="fas fa-check-circle fa-2x mb-3"></i>
            <h4 class="alert-heading">Tuyệt vời!</h4>
            <p>Hiện tại không có nhóm nào bị khóa.</p>
        </div>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="text-center">
                    <tr>
                        <th>#</th>
                        <th>Tên Nhóm</th>
                        <th>Trưởng Nhóm</th>
                        <th>Điểm Sức Khỏe</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php foreach ($lockedGroups as $index => $group) : ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="text-start"><?= htmlspecialchars($group['name']) ?></td>
                            <td><?= htmlspecialchars($group['leader_name']) ?></td>
                            <td>
                                <span class="badge bg-danger"><?= $group['group_health_score'] ?></span>
                            </td>
                            <td>
                                <button class="btn btn-unlock" data-team-id="<?= $group['id'] ?>">
                                    <i class="fas fa-key me-1"></i> Mở khóa
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-unlock').forEach(button => {
            button.addEventListener('click', function() {
                const teamId = this.dataset.teamId;
                if (confirm('Bạn có chắc chắn muốn mở khóa và reset điểm của nhóm này về 50 không?')) {
                    const formData = new FormData();
                    formData.append('team_id', teamId);

                    fetch('/webdacn_quanlyclb/chat/apiUnlockGroupChat', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            alert(data.message);
                            if (data.success) location.reload();
                        })
                        .catch(err => console.error("Lỗi khi mở khóa nhóm:", err));
                }
            });
        });
    });
</script>