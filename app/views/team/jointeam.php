<?php
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$team_id = isset($_GET['team_id']) ? (int)$_GET['team_id'] : null;
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu cầu tham gia đội nhóm</title>
    <style>
        :root {
            --primary: #FF6B9E;
            --primary-light: #FFD6E5;
            --primary-dark: #FF4785;
            --bg: #FFF0F5;
            --text-color: #666666;
            --shadow: 0 0.25rem 1.5rem rgba(255, 107, 158, 0.2);
        }

        body {
            background: var(--bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .wrapper {
            max-width: 500px;
            padding: 24px;
            background: #fff;
            border-radius: 18px;
            box-shadow: var(--shadow);
            text-align: center;
        }

        h2 {
            color: var(--primary-dark);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-label {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--primary-light);
            border-radius: 8px;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0.5rem rgba(255, 107, 158, 0.3);
        }

        .modal-buttons {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
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
            background: var(--primary);
            color: #fff;
        }

        .modal-btn-confirm:hover {
            background: var(--primary-dark);
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

        .message {
            margin-top: 10px;
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
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Yêu cầu tham gia đội nhóm</h2>
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
            <a href="/webdacn_quanlyclb/Team" class="modal-btn modal-btn-cancel">Quay lại</a>
        <?php elseif ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <a href="/webdacn_quanlyclb/Team" class="modal-btn modal-btn-cancel">Quay lại</a>
        <?php elseif ($team_id): ?>
            <form method="POST" action="">
                <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team_id); ?>">
                <div class="form-group">
                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="date_of_birth" class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                </div>
                <div class="form-group">
                    <label for="khoa" class="form-label">Khoa</label>
                    <input type="text" class="form-control" id="khoa" name="khoa">
                </div>
                <div class="form-group">
                    <label for="reason" class="form-label">Lý do muốn gia nhập <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="reason" name="reason" required>
                </div>
                <div class="form-group">
                    <label for="talent" class="form-label">Tài năng (không bắt buộc)</label>
                    <input type="text" class="form-control" id="talent" name="talent">
                </div>
                <div class="modal-buttons">
                    <button type="submit" class="modal-btn modal-btn-confirm">Xác nhận</button>
                    <a href="/webdacn_quanlyclb/Team" class="modal-btn modal-btn-cancel">Hủy</a>
                </div>
            </form>
        <?php else: ?>
            <div class="message error">Yêu cầu không hợp lệ. Vui lòng thử lại.</div>
            <a href="/webdacn_quanlyclb/Team" class="modal-btn modal-btn-cancel">Quay lại</a>
        <?php endif; ?>
    </div>
</body>
</html>