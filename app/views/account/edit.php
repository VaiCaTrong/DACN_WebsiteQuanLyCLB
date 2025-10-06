<?php include 'app/views/shares/header.php'; ?>


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

    .container {
        max-width: 700px;
        margin: 40px auto;
        padding: 30px;
        background: var(--bg);
        border-radius: 15px;
        box-shadow: var(--shadow);
        position: relative;
    }

    .container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: var(--gradient);
    }

    h2 {
        color: var(--primary-dark);
        text-align: center;
        margin-bottom: 25px;
        font-weight: 700;
    }

    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: var(--shadow);
        padding: 20px;
    }

    .form-label {
        font-weight: 600;
        color: var(--primary-dark);
        margin-bottom: 5px;
        display: block;
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0.5rem rgba(255, 107, 158, 0.3);
        outline: none;
    }

    .form-control[readonly] {
        background-color: #f0f0f0;
        cursor: not-allowed;
    }

    .mb-3 {
        margin-bottom: 20px;
    }

    .btn-primary {
        background: var(--gradient);
        border: none;
        color: #fff;
        padding: 12px 25px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        transform: translateY(-2px);
        box-shadow: 0 0.2rem 0.5rem rgba(255, 107, 158, 0.4);
    }

    .alert-danger {
        background: #ffe6ea;
        color: var(--primary-dark);
        border: 1px solid var(--primary);
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 20px;
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
        max-width: 450px;
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

    @media (max-width: 768px) {
        .container {
            margin: 20px;
            padding: 20px;
        }

        h2 {
            font-size: 1.5rem;
        }

        .btn-primary {
            padding: 10px 15px;
        }

        .modal-content {
            padding: 15px;
        }
    }
</style>

<div class="container my-4">
    <h2>Chỉnh sửa thông tin cá nhân</h2>
    <?php if (isset($errors['general'])): ?>
        <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
    <?php endif; ?>
    <div class="card">
        <div class="card-body">
            <form id="editAccountForm" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="username" class="form-label">Tên đăng nhập</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($account['username'] ?? ''); ?>" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label for="fullname" class="form-label">Họ và tên</label>
                    <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($account['fullname'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($account['email'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($account['phone'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="avatar" class="form-label">Ảnh đại diện</label>
                    <input type="file" class="form-control" id="avatar" name="avatar">
                </div>
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </form>
        </div>
    </div>
    <!-- Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3>Xác nhận lưu thay đổi</h3>
            <p>Bạn có chắc chắn muốn lưu các thay đổi này không?</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-confirm" onclick="confirmSubmit()">Xác nhận</button>
                <button class="modal-btn modal-btn-cancel" onclick="closeModal()">Hủy</button>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script>
    const modal = document.getElementById('confirmModal');
    const form = document.getElementById('editAccountForm');

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        modal.style.display = 'flex';
    });

    function confirmSubmit() {
        form.submit();
    }

    function closeModal() {
        modal.style.display = 'none';
    }
</script>