<?php include 'app/views/shares/header.php'; ?>
<link rel="stylesheet" href="/webdacn_quanlyclb/public/css/team/edit.css">

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

    .edit-container {
        max-width: 700px;
        margin: 40px auto;
        padding: 30px;
        background: var(--bg);
        border-radius: 15px;
        box-shadow: var(--shadow);
        position: relative;
    }

    .edit-container::before {
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

    .preview-image {
        max-width: 200px;
        max-height: 200px;
        object-fit: cover;
        border-radius: 10px;
        margin-top: 10px;
        display: none;
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
        .edit-container {
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

<div class="container mt-5">
    <div class="edit-container">
        <h2>Sửa đội nhóm</h2>
        <form id="editTeamForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($team['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Tên đội</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($team['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($team['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="quantity_user" class="form-label">Số lượng thành viên</label>
                <input type="number" class="form-control" id="quantity_user" name="quantity_user" value="<?php echo htmlspecialchars($team['quantity_user'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="talent" class="form-label">Tài năng</label>
                <input type="text" class="form-control" id="talent" name="talent" value="<?php echo htmlspecialchars($team['talent'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="mb-3">
                <label for="note" class="form-label">Ghi chú</label>
                <textarea class="form-control" id="note" name="note"><?php echo htmlspecialchars($team['note'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="avatar_team" class="form-label">Ảnh đội</label>
                <input type="file" class="form-control" id="avatar_team" name="avatar_team" onchange="previewImage(event)">
                <input type="hidden" name="avatar_team_old" value="<?php echo htmlspecialchars($team['avatar_team'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <img id="preview-image" class="preview-image" src="/webdacn_quanlyclb/<?php echo htmlspecialchars($team['avatar_team'] ?? '/Uploads/default_team.jpg', ENT_QUOTES, 'UTF-8'); ?>" alt="Preview ảnh đội">
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
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
    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('preview-image');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }

    const modal = document.getElementById('confirmModal');
    const form = document.getElementById('editTeamForm');

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