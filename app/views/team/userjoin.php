<?php
include 'app/views/shares/header.php';
$join_requests = $join_requests ?? [];
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);
// Kiểm tra quyền - chỉ staff và admin mới được truy cập
if (!SessionHelper::isStaff() && !SessionHelper::isAdmin()) {
    SessionHelper::set('error', "Bạn không có quyền truy cập trang này!");
    header('Location: /webdacn_quanlyclb');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách phiếu gia nhập</title>
    <style>
        :root {
            --primary: #FF6B9E;
            --primary-light: #FFD6E5;
            --primary-dark: #FF4785;
            --bg: #FFF0F5;
            --text-color: #666666;
            --shadow: 0 0.25rem 1.5rem rgba(255, 107, 158, 0.2);
            --green: #1cc88a;
            --yellow: #f6c23e;
            --red: #e74a3b;
        }

        body {
            background: var(--bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding-top: 70px;
        }

        .wrapper {
            width: 100%;
            max-width: 1500px;
            padding: 24px;
            background: #fff;
            border-radius: 18px;
            box-shadow: var(--shadow);
            text-align: left;
        }

        h2 {
            color: var(--primary-dark);
            margin-bottom: 20px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid var(--primary-light);
            text-align: left;
        }

        th {
            background-color: var(--primary-light);
            color: var(--primary-dark);
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr.interview {
            background-color: #fff3cd;
        }

        .status-pending {
            color: #f6c23e;
            font-weight: bold;
        }

        .status-approved {
            color: #1cc88a;
            font-weight: bold;
        }

        .status-rejected {
            color: #e74a3b;
            font-weight: bold;
        }

        .status-interview {
            color: #f6c23e;
            font-weight: bold;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            margin-right: 5px;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
        }

        .approve-btn {
            background-color: var(--green);
        }

        .approve-btn:hover {
            background-color: #1a9a6e;
            /* Tạm thay darken */
            transform: translateY(-1px);
        }

        .schedule-btn {
            background-color: var(--yellow);
        }

        .schedule-btn:hover {
            background-color: #d4a81c;
            /* Tạm thay darken */
            transform: translateY(-1px);
        }

        .reject-btn {
            background-color: var(--red);
        }

        .reject-btn:hover {
            background-color: #c0392b;
            /* Tạm thay darken */
            transform: translateY(-1px);
        }

        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .back-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            background: #ddd;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #ccc;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Danh sách phiếu gia nhập</h2>
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php elseif ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <div class="table-container">
            <?php if (empty($join_requests)): ?>
                <p>Không có phiếu gia nhập nào.</p>
            <?php else: ?>
                <!-- Trong file userjoin.php, cập nhật phần table header và body -->
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Ngày sinh</th>
                            <th>Khoa</th>
                            <th>Lý do</th>
                            <th>Tài năng</th>
                            <th>Team</th>
                            <th>Người gửi</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($join_requests as $request): ?>
                            <tr class="<?php echo $request['status'] === 'interview' ? 'interview' : ''; ?>">
                                <td><?php echo htmlspecialchars($request['id']); ?></td>
                                <td><?php echo htmlspecialchars($request['name']); ?></td>
                                <td><?php echo htmlspecialchars($request['date_of_birth'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($request['khoa'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($request['reason']); ?></td>
                                <td><?php echo htmlspecialchars($request['talent'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($request['team_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($request['requester_username'] ?? 'N/A'); ?></td>
                                <td class="status-<?php echo strtolower($request['status']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($request['status'])); ?>
                                </td>
                                <td><?php echo htmlspecialchars($request['created_at']); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="join_form_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                                        <?php if ($request['status'] === 'pending' || $request['status'] === 'interview'): ?>
                                            <button type="submit" name="action" value="approve" class="action-btn approve-btn">Duyệt ngay</button>
                                            <button type="submit" name="action" value="schedule" class="action-btn schedule-btn">Hẹn phỏng vấn</button>
                                            <button type="submit" name="action" value="reject" class="action-btn reject-btn">Từ chối</button>
                                        <?php else: ?>
                                            <span>Đã xử lý</span>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <a href="/webdacn_quanlyclb/Team" class="back-btn">Quay lại</a>
    </div>
</body>

</html>