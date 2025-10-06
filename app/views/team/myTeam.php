<?php include 'app/views/shares/header.php'; ?>
<link rel="stylesheet" href="/webdacn_quanlyclb/public/css/team/myTeam.css">

<style>
    :root {
        --primary: #FF6B9E; /* Màu hồng chủ đạo */
        --primary-light: #FFD6E5;
        --primary-dark: #FF4785;
        --bg: #FFF0F5;
        --white: #fff;
        --text-medium: #666666;
        --gradient: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
    }
    body {
        background: var(--bg);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--primary-dark);
        margin: 0;
        padding: 0;
        padding-top: 80px; /* Đảm bảo không bị header che */
    }

    .team-container {
        background: var(--bg);
    }

    .team-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 0 1.5rem;
        border-bottom: 1px solid var(--primary-light);
    }

    .team-header h2 {
        color: var(--primary-dark);
        font-weight: 700;
        font-size: 1.8rem;
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

    .team-details {
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
        padding: 0 1.5rem;
    }

    .team-info-card {
        background: var(--white);
        border-radius: 2rem;
        overflow: hidden;
        box-shadow: 0 0.25rem 1.5rem rgba(255, 107, 158, 0.2);
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: transform 0.3s ease;
    }

    .team-info-card:hover {
        transform: translateY(-5px);
    }

    .avatar-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 2rem;
        position: relative;
    }

    .avatar-wrapper {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        border: 6px solid var(--primary-light);
        box-shadow: 0 0.5rem 1.5rem rgba(255, 107, 158, 0.3);
        overflow: hidden;
        background: var(--gradient);
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
        text-align: center;
        width: 100%;
    }

    .team-name {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--primary-dark);
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .team-info p {
        margin-bottom: 1rem;
        font-size: 1.1em;
        color: var(--text-medium);
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
    }

    .team-info p strong {
        color: var(--primary);
        font-weight: 700;
        min-width: 120px;
    }

    .leave-button {
        margin-top: 1rem;
    }

    .leave-button .btn-danger {
        background: #FF4D4D;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 700;
        color: #fff;
        transition: all 0.3s ease;
        box-shadow: 0 0.2rem 0.5rem rgba(255, 77, 77, 0.2);
    }

    .leave-button .btn-danger:hover {
        background: #FF1A1A;
        transform: translateY(-2px);
        box-shadow: 0 0.3rem 0.7rem rgba(255, 77, 77, 0.3);
    }

    .team-members h4 {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-light);
    }

    .member-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        justify-content: center;
    }

    .member-card {
        background: var(--white);
        border-radius: 1rem;
        box-box-shadow: 0 0.25rem 1rem rgba(255, 107, 158, 0.2);
        width: 150px;
        padding: 1rem;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .member-card:hover {
        transform: translateY(-5px);
    }

    .member-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid var(--primary-light);
        object-fit: cover;
        margin: 0 auto 0.75rem;
    }

    .member-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--primary-dark);
        margin: 0;
    }

    .chat-section {
        background: var(--white);
        border-radius: 2rem;
        overflow: hidden;
        box-shadow: 0 0.25rem 1.5rem rgba(255, 107, 158, 0.2);
        padding: 2rem;
        transition: transform 0.3s ease;
    }

    .chat-section:hover {
        transform: translateY(-5px);
    }

    .chat-section h4 {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-light);
    }

    .chat-box {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .chat-messages {
        max-height: 350px;
        overflow-y: auto;
        padding: 1.5rem;
        background: var(--primary-light);
        border-radius: 1.5rem;
        box-shadow: inset 0 0.1rem 0.5rem rgba(0, 0, 0, 0.05);
    }

    .chat-messages div {
        background: var(--white);
        padding: 0.75rem 1rem;
        margin-bottom: 0.75rem;
        border-radius: 1rem;
        box-shadow: 0 0.1rem 0.3rem rgba(255, 107, 158, 0.1);
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chat-form .input-group {
        display: flex;
        gap: 1rem;
    }

    .chat-form .btn-primary {
        background: var(--primary);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 700;
        color: #fff;
        transition: all 0.3s ease;
        box-shadow: 0 0.2rem 0.5rem rgba(255, 107, 158, 0.2);
    }

    .chat-form .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 0.3rem 0.7rem rgba(255, 107, 158, 0.3);
    }

    .chat-form .form-control {
        border: 1px solid var(--primary-light);
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .chat-form .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0.5rem rgba(255, 107, 158, 0.2);
    }

    @media (max-width: 768px) {
        .avatar-wrapper {
            width: 150px;
            height: 150px;
        }

        .team-name {
            font-size: 1.5rem;
        }

        .team-info p {
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
        }

        .team-info p strong {
            margin-right: 0;
            margin-bottom: 0.25rem;
        }

        .member-card {
            width: 120px;
        }

        .member-avatar {
            width: 60px;
            height: 60px;
        }
    }
</style>

<div class="team-container">
    <div class="container">
        <div class="team-header">
            <h2>Đội của tôi</h2>
        </div>

        <div class="team-details">
            <div class="team-info-card">
                <div class="avatar-container">
                    <div class="avatar-wrapper">
                        <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($team['avatar_team'], ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar đội <?php echo htmlspecialchars($team['name']); ?>" class="team-avatar" onerror="this.src='/webdacn_quanlyclb/uploads/default_team.jpg';">
                    </div>
                </div>
                <div class="team-info">
                    <h3 class="team-name"><?php echo htmlspecialchars($team['name']); ?></h3>
                    <p><strong>Mô tả:</strong> <?php echo htmlspecialchars($team['description']); ?></p>
                    <p><strong>Số lượng thành viên:</strong> <?php echo $this->teamModel->countTeamMembers($team['id']); ?></p>
                    <p><strong>Tài năng:</strong> <?php echo htmlspecialchars($team['talent']); ?></p>
                    <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($team['note']); ?></p>
                    <div class="leave-button">
                        <form method="POST" action="" onsubmit="return confirm('Bạn có chắc chắn muốn rời đội không?');">
                            <input type="hidden" name="action" value="leave">
                            <button type="submit" class="btn btn-danger">Rời đội</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="team-members">
                <h4>Thành viên trong đội</h4>
                <div class="member-cards">
                    <?php foreach ($members as $member): ?>
                        <div class="member-card">
                            <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($member['avatar'] ?? 'uploads/default_user.jpg', ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar của <?php echo htmlspecialchars($member['fullname'] ?: $member['username']); ?>" class="member-avatar" onerror="this.src='/webdacn_quanlyclb/uploads/default_user.jpg';">
                            <p class="member-name"><?php echo htmlspecialchars($member['fullname'] ?: $member['username']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Hiển thị thông báo nếu có
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    echo "<script>alert('" . addslashes($_SESSION['error']) . "');</script>";
    unset($_SESSION['error']);
}
?>

<?php include 'app/views/shares/footer.php'; ?>

<script>
    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const messageInput = document.getElementById('message');
        const message = messageInput.value.trim();

        if (message) {
            // Gửi tin nhắn đến server (cần API backend)
            fetch('/webdacn_quanlyclb/Team/sendMessage', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'message=' + encodeURIComponent(message) + '&team_id=<?php echo $team['id']; ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const chatMessages = document.querySelector('.chat-messages');
                        const messageDiv = document.createElement('div');
                        messageDiv.textContent = data.message;
                        chatMessages.appendChild(messageDiv);
                        messageInput.value = '';
                        chatMessages.scrollTop = chatMessages.scrollHeight; // Cuộn xuống tin nhắn mới
                    } else {
                        alert('Gửi tin nhắn thất bại.');
                    }
                })
                .catch(error => console.error('Lỗi:', error));
        }
    });
</script>