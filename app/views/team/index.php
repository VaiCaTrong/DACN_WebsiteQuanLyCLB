<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php'; 
?>

<style>
    .requests-container {
        padding: 30px;
        background-color: #f5f7fa;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.07);
    }
    .card-header {
        background-color: #E91E63;
        color: white;
        font-weight: 600;
    }
    .table-responsive {
        border: none;
    }
    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    .table img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
    }
</style>

<div class="requests-container">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-clipboard-list me-2"></i>Danh sách yêu cầu tạo CLB
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID Yêu cầu</th>
                            <th>Tên CLB</th>
                            <th>Người tạo</th>
                            <th>Ngày tạo</th>
                            <th>Avatar</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $r): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($r['team_id']) ?></strong></td>
                                <td><?= htmlspecialchars($r['name']) ?></td>
                                <td><?= htmlspecialchars($r['creator_name']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                                <td>
                                    <?php if (!empty($r['avatar_team'])): ?>
                                        <img src="/webdacn_quanlyclb/<?= $r['avatar_team'] ?>" alt="Avatar">
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="/webdacn_quanlyclb/team/detail/<?= $r['id'] ?>" class="btn btn-sm btn-info">Chi tiết</a>
                                    </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/footer.php'; 
?>