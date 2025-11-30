<?php include 'app/views/shares/header.php'; ?>

<style>
    :root {
        --primary: #FF6B9E;
        --primary-light: #FFD6E5;
        --primary-dark: #FF4785;
        --gradient: linear-gradient(135deg, #FF6B9E 0%, #FF8E53 100%);
        --bg: linear-gradient(135deg, #FFF0F5 0%, #F8F9FA 100%);
        --white: #fff;
        --card-shadow: 0 20px 40px rgba(255, 107, 158, 0.15);
        --hover-shadow: 0 25px 50px rgba(255, 107, 158, 0.25);
        --text-dark: #333;
        --text-medium: #666;
        --border-radius: 20px;
    }

    body {
        background: var(--bg);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }

    .edit-container {
        max-width: 1200px;
        margin: 10px auto;
        padding: 30px 20px;
        padding-top: 50px;
    }

    .edit-header {
        text-align: center;
        margin-bottom: 40px;
        position: relative;
    }

    .edit-header h2 {
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 15px;
        letter-spacing: -0.5px;
    }

    .edit-header::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: var(--gradient);
        border-radius: 2px;
    }

    .edit-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    @media (max-width: 768px) {
        .edit-grid {
            grid-template-columns: 1fr;
        }
    }

    .edit-card {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        padding: 30px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 107, 158, 0.1);
        position: relative;
        overflow: hidden;
    }

    .edit-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient);
    }

    .edit-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--hover-shadow);
    }

    .card-title {
        color: var(--primary-dark);
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 1.6rem;
    }

    /* Avatar Section */
    .avatar-section {
        text-align: center;
        padding: 20px;
    }

    .avatar-container {
        position: relative;
        display: inline-block;
        margin-bottom: 25px;
    }

    .avatar-preview {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--white);
        box-shadow: 0 15px 35px rgba(255, 107, 158, 0.3);
        transition: all 0.3s ease;
    }

    .avatar-preview:hover {
        transform: scale(1.05);
        box-shadow: 0 20px 45px rgba(255, 107, 158, 0.4);
    }

    .avatar-overlay {
        position: absolute;
        bottom: 15px;
        right: 15px;
        width: 50px;
        height: 50px;
        background: var(--gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        border: 3px solid var(--white);
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(255, 107, 158, 0.3);
    }

    .avatar-overlay:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 20px rgba(255, 107, 158, 0.4);
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .form-label {
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 8px;
        display: block;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control {
        border: 2px solid #f0f0f0;
        border-radius: 12px;
        padding: 15px 20px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: rgba(255, 107, 158, 0.02);
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(255, 107, 158, 0.1);
        outline: none;
        background: var(--white);
    }

    .form-control[readonly] {
        background-color: #f8f9fa;
        cursor: not-allowed;
        color: #6c757d;
        border-color: #e9ecef;
    }

    .form-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary);
        font-size: 1.1rem;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    .btn-gradient {
        background: var(--gradient);
        color: white;
        padding: 15px 35px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        box-shadow: 0 8px 25px rgba(255, 107, 158, 0.3);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .btn-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }

    .btn-gradient:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(255, 107, 158, 0.4);
    }

    .btn-gradient:hover::before {
        left: 100%;
    }

    .btn-outline {
        background: transparent;
        color: var(--primary);
        padding: 15px 35px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        border: 2px solid var(--primary);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .btn-outline:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255, 107, 158, 0.3);
    }

    /* Alert Styles */
    .alert-danger {
        background: rgba(244, 67, 54, 0.1);
        color: #f44336;
        border: 1px solid rgba(244, 67, 54, 0.2);
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-danger i {
        font-size: 1.2rem;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(5px);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        padding: 30px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        position: relative;
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-content h3 {
        color: var(--primary-dark);
        margin-bottom: 15px;
        font-size: 1.5rem;
    }

    .modal-content p {
        color: var(--text-medium);
        margin-bottom: 25px;
        line-height: 1.6;
    }

    .modal-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
    }

    .modal-btn {
        padding: 12px 25px;
        border: none;
        border-radius: 25px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 100px;
    }

    .modal-btn-confirm {
        background: var(--gradient);
        color: white;
        box-shadow: 0 4px 15px rgba(255, 107, 158, 0.3);
    }

    .modal-btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 158, 0.4);
    }

    .modal-btn-cancel {
        background: #6c757d;
        color: white;
    }

    .modal-btn-cancel:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .user-info {
        text-align: center;
        margin-top: 20px;
    }

    .user-info h4 {
        color: var(--primary-dark);
        margin-bottom: 5px;
        font-weight: 700;
    }

    .user-info p {
        color: var(--text-medium);
        margin-bottom: 0;
    }
</style>

<div class="edit-container">
    <div class="edit-header">
        <h2><i class="fas fa-user-edit"></i> Chỉnh Sửa Thông Tin</h2>
        <p class="text-muted">Cập nhật thông tin cá nhân và ảnh đại diện của bạn</p>
    </div>

    <div class="edit-grid">
        <!-- Avatar Card -->
        <div class="edit-card">
            <h3 class="card-title">
                <i class="fas fa-camera"></i>
                Ảnh Đại Diện
            </h3>
            
            <div class="avatar-section">
                <div class="avatar-container">
                    <img src="<?php echo htmlspecialchars($account['avatar'] ? '/webdacn_quanlyclb/' . $account['avatar'] : '/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg'); ?>" 
                         alt="Avatar" 
                         class="avatar-preview" 
                         id="avatarPreview">
                    
                    <div class="avatar-overlay" onclick="document.getElementById('avatar').click()">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>

                <div class="user-info">
                    <h4><?php echo htmlspecialchars($account['fullname'] ?? $account['username']); ?></h4>
                    <p>@<?php echo htmlspecialchars($account['username']); ?></p>
                </div>

                <p class="text-muted mt-3">
                    <small>Nhấn vào biểu tượng camera để thay đổi ảnh đại diện</small>
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="edit-card">
            <h3 class="card-title">
                <i class="fas fa-edit"></i>
                Thông Tin Cá Nhân
            </h3>
            
            <?php if (isset($errors['general'])): ?>
                <div class="alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo $errors['general']; ?>
                </div>
            <?php endif; ?>
            
            <form id="editAccountForm" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username" class="form-label">Tên đăng nhập</label>
                    <input type="text" name="username" 
                           value="<?php echo htmlspecialchars($account['username'] ?? ''); ?>" 
                           class="form-control" 
                           readonly>
                    <i class="fas fa-lock form-icon"></i>
                </div>

                <div class="form-group">
                    <label for="fullname" class="form-label">Họ và tên</label>
                    <input type="text" name="fullname" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($account['fullname'] ?? ''); ?>" 
                           required
                           placeholder="Nhập họ và tên của bạn">
                    <i class="fas fa-user form-icon"></i>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($account['email'] ?? ''); ?>" 
                           required
                           placeholder="Nhập địa chỉ email">
                    <i class="fas fa-envelope form-icon"></i>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($account['phone'] ?? ''); ?>"
                           placeholder="Nhập số điện thoại">
                    <i class="fas fa-phone form-icon"></i>
                </div>
                
                <input type="file" id="avatar" name="avatar" style="display: none;" accept="image/*">
                
                <div class="action-buttons">
                    <a href="/webdacn_quanlyclb/account/profile" class="btn-outline">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </a>
                    <button type="submit" class="btn-gradient">
                        <i class="fas fa-save"></i>
                        Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <h3><i class="fas fa-question-circle text-warning"></i> Xác Nhận</h3>
        <p>Bạn có chắc chắn muốn lưu các thay đổi này không?</p>
        <div class="modal-buttons">
            <button class="modal-btn modal-btn-cancel" onclick="closeModal()">Hủy</button>
            <button class="modal-btn modal-btn-confirm" onclick="confirmSubmit()">Xác Nhận</button>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script>
    const modal = document.getElementById('confirmModal');
    const form = document.getElementById('editAccountForm');
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');

    // Xử lý submit form với modal xác nhận
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

    // Xem trước ảnh đại diện
    avatarInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
                
                // Thêm hiệu ứng khi thay đổi ảnh
                avatarPreview.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    avatarPreview.style.transform = 'scale(1)';
                }, 300);
            }
            reader.readAsDataURL(file);
        }
    });

    // Đóng modal khi click bên ngoài
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Thêm hiệu ứng cho các input khi focus
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateX(5px)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateX(0)';
        });
    });
</script>