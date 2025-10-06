<?php
include 'app/views/shares/header.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/controllers/TeamController.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/models/TeamModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/helpers/SessionHelper.php');
$teamModel = new TeamModel();
$user_id = SessionHelper::getUserId();
$team_id = $teamModel->getTeamIdFromAccount($user_id); // Giả định phương thức mới
// Handle form submission via controller
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/Team/request') !== false) {
    $teamController->requestTeam();
}
?>

<style>
    :root {
        --primary: #FF6B9E;
        --primary-light: #FFD6E5;
        --primary-dark: #FF4785;
        --bg: #FFF0F5;
        --text-color: #666666;
        --shadow: 0 0.25rem 1.5rem rgba(255, 107, 158, 0.2);
        --joined-color: #1cc88a;
        --gradient: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
        --white: #fff;
    }

    body {
        background: var(--bg);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        padding-top: 30px;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .wrapper {
        max-width: 1000px;
        margin: 0 auto;
        padding: 24px;
        background: var(--white);
        border-radius: 18px;
        box-shadow: 0 6px 32px rgba(233, 30, 99, 0.08);
        transition: padding-left 0.3s ease;
    }

    #sidebar.active~#content .wrapper {
        padding-left: 274px;
    }

    .team-container {
        padding: 1.5rem 0;
        flex: 1 0 auto;
        background: var(--bg);
        margin-bottom: 50px;
    }

    .team-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding: 0 1rem;
        border-bottom: 1px solid var(--primary-light);
    }

    .team-header h2 {
        color: var(--primary-dark);
        font-weight: 700;
        font-size: 1.6rem;
        margin: 0;
        padding-bottom: 0.5rem;
        position: relative;
    }

    .team-header h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: var(--primary);
        border-radius: 2px;
    }

    .button-group {
        display: flex;
        gap: 10px;
    }

    .btn-primary {
        background: var(--primary);
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 0.5rem;
        font-weight: 700;
        color: #fff;
        transition: all 0.3s ease;
        box-shadow: 0 0.2rem 0.5rem rgba(255, 107, 158, 0.2);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 0.3rem 0.7rem rgba(255, 107, 158, 0.3);
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.2rem;
        padding: 0 1rem;
    }

    .team-card {
        background: var(--white);
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: all 0.5s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .team-card.joined {
        background: linear-gradient(135deg, var(--primary-light) 0%, var(--white) 100%);
        border: 2px solid var(--joined-color);
    }

    .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(255, 107, 158, 0.3);
    }

    .avatar-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 1.8rem 0.8rem;
        background: var(--white);
        position: relative;
        margin-bottom: 0.75rem;
    }

    .avatar-wrapper {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        border: 6px solid var(--primary-light);
        box-shadow: 0 0.3rem 1rem rgba(255, 107, 158, 0.3);
        overflow: hidden;
        background: var(--gradient);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .team-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 0.3s ease;
    }

    .team-avatar:hover {
        opacity: 0.9;
    }

    .team-info {
        padding: 0 1rem 1rem;
        text-align: center;
        flex-grow: 1;
    }

    .team-name {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--primary-dark);
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .team-actions {
        padding: 1rem;
        border-top: 1px solid var(--primary-light);
        display: flex;
        justify-content: center;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        border-radius: 0.4rem;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 0.1rem 0.3rem rgba(0, 0, 0, 0.1);
    }

    .btn-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.15);
    }

    .btn-warning {
        background-color: #f6c23e;
        border-color: #f6c23e;
        color: #fff;
    }

    .btn-danger {
        background-color: #e74a3b;
        border-color: #e74a3b;
        color: #fff;
    }

    .btn-info {
        background-color: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    .btn-success {
        background-color: var(--joined-color);
        border-color: var(--joined-color);
        color: #fff;
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

    /* Form Styles */
    .request-form {
        display: none;
        background: var(--bg);
        padding: 20px;
        border-radius: 15px;
        box-shadow: var(--shadow);
        max-width: 450px;
        width: 90%;
        text-align: left;
        margin: 20px auto;
    }

    .form-group {
        margin-bottom: 15px;
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

    .error {
        color: #e74a3b;
        font-size: 0.9rem;
        margin-top: 5px;
        display: none;
    }

    .avatar-preview {
        margin-top: 10px;
        max-width: 100px;
        max-height: 100px;
        border-radius: 8px;
        display: none;
    }

    footer {
        flex-shrink: 0;
        margin-top: auto;
        background: var(--white);
        padding: 1rem 1.5rem;
        text-align: center;
        color: var(--text-color);
        border-top: 1px solid var(--primary-light);
    }
</style>

<div class="wrapper">
    <div class="team-container">
        <div class="container">
            <div class="team-header">
                <h2>Danh sách đội nhóm</h2>
                <div class="button-group">
                    <?php if ($this->isAdmin()): ?>
                        <a href="/webdacn_quanlyclb/Team/add" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm đội nhóm
                        </a>
                    <?php endif; ?>
                    <?php if (SessionHelper::isUser() && ($team_id === null || $team_id === 0)): ?>
                        <button class="btn btn-primary" onclick="scrollToRequestForm()">Yêu cầu tạo câu lạc bộ</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (SessionHelper::isLoggedIn()): ?>
                <div class="user-info">
                    <div>
                        <i class="fas fa-user-circle"></i>
                        Xin chào, <strong><?php echo htmlspecialchars($_SESSION['username']); ?> hãy tìm câu lạc bộ cho bản thân nhé!</strong>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!SessionHelper::isLoggedIn()): ?>
                <i>Hãy đăng nhập để liên hệ tham gia nhóm nhé </i>
            <?php endif; ?>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <div class="team-grid">
                <?php
                $user_id = $this->getUserId();
                $stmt = $this->db->prepare("SELECT team_id FROM account WHERE id = :user_id");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $current_team_id = $stmt->fetchColumn();
                ?>
                <?php foreach ($teams as $team): ?>
                    <div class="team-card <?php echo ($current_team_id == $team['id']) ? 'joined' : ''; ?>">
                        <div class="avatar-container">
                            <div class="avatar-wrapper">
                                <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($team['avatar_team'], ENT_QUOTES, 'UTF-8'); ?>"
                                    alt="Avatar đội <?php echo htmlspecialchars($team['name']); ?>"
                                    class="team-avatar" onerror="this.src='/webdacn_quanlyclb/uploads/default_team.jpg';">
                            </div>
                        </div>

                        <div class="team-info">
                            <h3 class="team-name"><?php echo htmlspecialchars($team['name']); ?></h3>
                        </div>

                        <div class="team-actions">
                            <a href="/webdacn_quanlyclb/Team/view/<?php echo $team['id']; ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                            <?php if ($this->isAdmin()): ?>
                                <a href="/webdacn_quanlyclb/Team/edit/<?php echo $team['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <a href="/webdacn_quanlyclb/Team/delete/<?php echo $team['id']; ?>" class="btn btn-danger btn-sm delete-btn" data-team-id="<?php echo $team['id']; ?>">
                                    <i class="fas fa-trash"></i> Xóa
                                </a>
                            <?php else: ?>
                                <?php if ($team['id'] != $team_id): ?>
                                    <?php if (SessionHelper::isUser()): ?>
                                        <a href="/webdacn_quanlyclb/Team/join?team_id=<?php echo $team['id']; ?>" class="btn btn-success btn-sm" onclick="showLoading()">
                                            <i class="fas fa-user-plus"></i> Tham gia đội
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3>Xác nhận xóa đội nhóm</h3>
            <p>Bạn có chắc chắn muốn xóa đội nhóm này không?</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-confirm" onclick="confirmDelete()">Xác nhận</button>
                <button class="modal-btn modal-btn-cancel" onclick="closeModal()">Hủy</button>
            </div>
        </div>
    </div>
    <!-- Request Form -->
    <div id="requestForm" class="request-form">
        <h3>Yêu cầu tạo câu lạc bộ</h3>
        <form id="clubRequestForm" method="POST" action="/webdacn_quanlyclb/Team/request" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($this->getUserId()); ?>">
            <div class="form-group">
                <label for="name" class="form-label">Tên câu lạc bộ <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required>
                <div class="error" id="nameError">Tên câu lạc bộ là bắt buộc.</div>
            </div>
            <div class="form-group">
                <label for="khoa" class="form-label">Khoa</label>
                <input type="text" class="form-control" id="khoa" name="khoa">
            </div>
            <div class="form-group">
                <label for="reason" class="form-label">Lý do <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="reason" name="reason" required>
                <div class="error" id="reasonError">Lý do là bắt buộc.</div>
            </div>
            <div class="form-group">
                <label for="talent" class="form-label">Tài năng <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="talent" name="talent" required>
                <div class="error" id="talentError">Tài năng là bắt buộc.</div>
            </div>
            <div class="form-group">
                <label for="avatar_team" class="form-label">Ảnh đại diện (Avatar)</label>
                <input type="file" class="form-control" id="avatar_team" name="avatar_team" accept="image/*">
                <img id="avatarPreview" class="avatar-preview" src="" alt="Avatar Preview">
            </div>
            <div class="modal-buttons">
                <button type="submit" class="modal-btn modal-btn-confirm">Gửi yêu cầu</button>
                <button type="button" class="modal-btn modal-btn-cancel" onclick="closeRequestForm()">Hủy</button>
            </div>
        </form>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script>
    let deleteUrl = '';

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            deleteUrl = this.getAttribute('href');
            document.getElementById('confirmModal').style.display = 'flex';
        });
    });

    function confirmDelete() {
        if (deleteUrl) {
            window.location.href = deleteUrl;
        }
    }

    function closeModal() {
        document.getElementById('confirmModal').style.display = 'none';
        deleteUrl = '';
    }

    function scrollToRequestForm() {
        document.getElementById('requestForm').style.display = 'block';
        document.getElementById('requestForm').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function closeRequestForm() {
        document.getElementById('requestForm').style.display = 'none';
    }

    // Preview avatar
    document.getElementById('avatar_team').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('avatarPreview').src = event.target.result;
                document.getElementById('avatarPreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('avatarPreview').style.display = 'none';
        }
    });

    document.getElementById('clubRequestForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const reason = document.getElementById('reason').value.trim();
        const talent = document.getElementById('talent').value.trim();
        const name = document.getElementById('name').value.trim();

        let isValid = true;

        if (!name) {
            document.getElementById('nameError').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('nameError').style.display = 'none';
        }
        if (!reason) {
            document.getElementById('reasonError').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('reasonError').style.display = 'none';
        }

        if (!talent) {
            document.getElementById('talentError').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('talentError').style.display = 'none';
        }

        if (isValid) {
            this.submit();
        }
    });
</script>