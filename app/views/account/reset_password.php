<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/helpers/SessionHelper.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        :root {
            --primary-pink: #E91E63;
            --secondary-pink: #FF4081;
            --light-pink: #FCE4EC;
            --light-bg: #f8f9fa;
            --text-dark: #343A40;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            padding-top: 70px;
        }

        .container {
            background-color: #FFFFFF;
            border-radius: 0.5rem;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
            margin-top: 20px;
            padding: 30px;
            max-width: 500px;
        }

        .btn-submit {
            background-color: var(--primary-pink);
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-submit:hover {
            background-color: var(--secondary-pink);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(233, 30, 99, 0.3);
        }

        .form-control:focus {
            border-color: var(--primary-pink);
            box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
        }

        .error-message {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php'); ?>

    <div class="container">
        <h2 class="mb-4 text-center">Đặt lại mật khẩu</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form action="/webdacn_quanlyclb/account/reset_password?email=<?= urlencode($_GET['email'] ?? '') ?>" method="POST">
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu mới</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-submit">Đặt lại mật khẩu</button>
        </form>
        <div class="mt-3 text-center">
            <a href="/webdacn_quanlyclb/account/login">Quay lại đăng nhập</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>