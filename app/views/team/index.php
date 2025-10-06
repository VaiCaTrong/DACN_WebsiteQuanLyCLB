<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách yêu cầu CLB</title>
</head>
<body>
    <h2>Danh sách yêu cầu tạo CLB</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Mã Team</th>
            <th>Tên CLB</th>
            <th>Khoa</th>
            <th>Lý do</th>
            <th>Tài năng</th>
            <th>Người tạo</th>
            <th>Ngày tạo</th>
            <th>Avatar</th>
            <th>Hành động</th>
        </tr>
        <?php foreach ($requests as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['id']) ?></td>
                <td><?= htmlspecialchars($r['team_id']) ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['khoa']) ?></td>
                <td><?= htmlspecialchars($r['reason']) ?></td>
                <td><?= htmlspecialchars($r['talent']) ?></td>
                <td><?= htmlspecialchars($r['creator_name']) ?></td>
                <td><?= htmlspecialchars($r['created_at']) ?></td>
                <td>
                    <?php if (!empty($r['avatar_team'])): ?>
                        <img src="/webdacn_quanlyclb/<?= $r['avatar_team'] ?>" width="60">
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/webdacn_quanlyclb/team/detail/<?= $r['id'] ?>">Chi tiết</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
