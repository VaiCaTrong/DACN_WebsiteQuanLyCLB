<?php include 'app/views/shares/header.php'; ?>

<style>
    .team-management {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .team-info, .members-section, .requests-section {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .team-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    .team-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 20px;
    }
    .team-title {
        font-size: 24px;
        color: #FF6B9E;
        margin: 0;
    }
    .team-description {
        color: #666;
        margin-top: 10px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    .info-item {
        margin-bottom: 10px;
    }
    .info-label {
        font-weight: bold;
        color: #FF6B9E;
    }
    .members-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }
    .member-card {
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 15px;
        display: flex;
        align-items: center;
    }
    .member-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
    }
    .member-name {
        font-weight: bold;
    }
    .member-email {
        font-size: 0.9em;
        color: #666;
    }
    .request-item {
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
    }
    .request-actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
    .btn {
        padding: 8px 15px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        font-weight: bold;
    }
    .btn-approve {
        background: #4CAF50;
        color: white;
    }
    .btn-reject {
        background: #F44336;
        color: white;
    }
    .btn-interview {
        background: #FFC107;
        color: white;
    }
    .btn-details {
        background: #2196F3;
        color: white;
    }
    .section-title {
        color: #FF6B9E;
        border-bottom: 2px solid #FF6B9E;
        padding-bottom: 5px;
        margin-bottom: 15px;
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
        overflow: auto;
    }
    .modal-content, .punish-modal-content, .reward-modal-content {
        background-color: white;
        margin: 15% auto;
        padding: 20px;
        border-radius: 8px;
        width: 80%;
        max-width: 600px;
        position: relative;
        transform: translateY(100%);
        transition: transform 0.3s ease-in-out;
    }
    .punish-modal-content, .reward-modal-content {
        max-width: 500px;
    }
    .modal.open .modal-content,
    .modal.open .punish-modal-content,
    .modal.open .reward-modal-content {
        transform: translateY(0);
    }
    .close {
        color: #aaa;
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
    }
    .modal-content h2, .punish-modal-content h2, .reward-modal-content h2 {
        color: #FF6B9E;
        margin-top: 0;
    }
    .modal-content p {
        margin: 10px 0;
    }
    .modal-content .info-label {
        display: inline-block;
        width: 120px;
        font-weight: bold;
    }
    .modal-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .modal-avatar-placeholder {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: #FF6B9E;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 50px;
        flex-shrink: 0;
    }
    .modal-details {
        flex-grow: 1;
    }
    .modal-actions {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }
    .btn-punish {
        background: #F44336;
        color: white;
    }
    .btn-reward {
        background: #4CAF50;
        color: white;
    }
    .punish-form, .reward-form {
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #4CAF50;
            color: white;
        }
    }
    .punish-form button {
        background: #F44336;
        color: white;
    }
</style>

<div class="team-management">
    <div class="team-info">
        <div class="team-header">
            <?php if ($team['avatar_team']): ?>
                <img src="/webdacn_quanlyclb/<?= htmlspecialchars($team['avatar_team']) ?>" alt="Team Avatar" class="team-avatar">
            <?php else: ?>
                <div class="team-avatar" style="background: #FF6B9E; color: white; display: flex; align-items: center; justify-content: center;">
                    <?= substr(htmlspecialchars($team['name']), 0, 1) ?>
                </div>
            <?php endif; ?>
            <div>
                <h1 class="team-title"><?= htmlspecialchars($team['name']) ?></h1>
                <p class="team-description"><?= htmlspecialchars($team['description']) ?></p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Số lượng thành viên:</span>
                <?= count($members) ?>
            </div>
            <div class="info-item">
                <span class="info-label">Tài năng chính:</span>
                <?= htmlspecialchars($team['talent']) ?>
            </div>
            <div class="info-item">
                <span class="info-label">Ghi chú:</span>
                <?= htmlspecialchars($team['note']) ?>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <a href="/webdacn_quanlyclb/Team/edit/<?= $team['id'] ?>" class="btn" style="background: #FF6B9E; color: white;">Chỉnh sửa thông tin</a>
        </div>
    </div>

    <div class="members-section">
        <h2 class="section-title">Thành viên trong đội</h2>
        <?php if (count($members) > 0): ?>
            <div class="members-grid">
                <?php foreach ($members as $member): ?>
                    <div class="member-card">
                        <?php if ($member['avatar']): ?>
                            <img src="/webdacn_quanlyclb/<?= htmlspecialchars($member['avatar']) ?>" alt="Member Avatar" class="member-avatar">
                        <?php else: ?>
                            <div class="member-avatar" style="background: #FF6B9E; color: white; display: flex; align-items: center; justify-content: center;">
                                <?= substr(htmlspecialchars($member['fullname'] ?? 'U'), 0, 1) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="member-name"><?= htmlspecialchars($member['fullname'] ?? 'Chưa có tên') ?></div>
                            <div class="member-email"><?= htmlspecialchars($member['email']) ?></div>
                            <button class="btn btn-details" onclick='showMemberDetails(<?= json_encode($member) ?>)'>Xem chi tiết</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Chưa có thành viên nào trong đội.</p>
        <?php endif; ?>
    </div>

    <div class="requests-section">
        <h2 class="section-title">Yêu cầu tham gia đội</h2>
        <?php if (count($join_requests) > 0): ?>
            <?php foreach ($join_requests as $request): ?>
                <div class="request-item">
                    <h3><?= htmlspecialchars($request['name']) ?></h3>
                    <p><strong>Khoa:</strong> <?= htmlspecialchars($request['khoa']) ?></p>
                    <p><strong>Lý do:</strong> <?= htmlspecialchars($request['reason']) ?></p>
                    <p><strong>Tài năng:</strong> <?= htmlspecialchars($request['talent']) ?></p>
                    <p><strong>Ngày gửi:</strong> <?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Hiện không có yêu cầu tham gia nào.</p>
        <?php endif; ?>
    </div>

    <!-- Modal for Member Details -->
    <div id="memberModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">×</span>
            <div id="memberAvatar"></div>
            <div class="modal-details">
                <h2>Thông tin thành viên</h2>
                <div id="memberDetails"></div>
                <div class="modal-actions">
                    <button class="btn btn-punish" onclick="showPunishForm()">Phạt</button>
                    <button class="btn btn-reward" onclick="showRewardForm()">Thưởng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Punish Form -->
    <div id="punishModal" class="modal">
        <div class="punish-modal-content">
            <span class="close" onclick="closePunishModal()">×</span>
            <h2>Phiếu phạt</h2>
            <form id="punishForm" class="punish-form" action="/webdacn_quanlyclb/Team/punish" method="POST">
                <input type="hidden" id="punishUserId" name="user_id">
                <input type="hidden" id="punishTeamId" name="team_id">
                <label for="reason">Lý do phạt:</label>
                <textarea id="reason" name="reason" required></textarea>
                <label for="severity">Mức độ:</label>
                <select id="severity" name="severity" required>
                    <option value="light">Nhẹ (5 điểm)</option>
                    <option value="medium">Vừa (10 điểm)</option>
                    <option value="heavy">Nặng (15 điểm)</option>
                </select>
                <button type="submit" class="btn">Gửi</button>
            </form>
        </div>
    </div>

    <!-- Modal for Reward Form -->
    <div id="rewardModal" class="modal">
        <div class="reward-modal-content">
            <span class="close" onclick="closeRewardModal()">×</span>
            <h2>Phiếu thưởng</h2>
            <form id="rewardForm" class="reward-form" action="/webdacn_quanlyclb/Team/reward" method="POST">
                <input type="hidden" id="rewardUserId" name="user_id">
                <input type="hidden" id="rewardTeamId" name="team_id">
                <label for="rewardReason">Lý do thưởng:</label>
                <textarea id="rewardReason" name="reason" required></textarea>
                <label for="rewardSeverity">Mức độ:</label>
                <select id="rewardSeverity" name="severity" required>
                    <option value="temporary">Tạm (5 điểm)</option>
                    <option value="good">Tốt (10 điểm)</option>
                    <option value="excellent">Xuất sắc (15 điểm)</option>
                </select>
                <button type="submit" class="btn">Gửi</button>
            </form>
        </div>
    </div>
</div>

<script>
let currentMember = null;

// Utility function to escape HTML to prevent XSS
function escapeHTML(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function showMemberDetails(member) {
    currentMember = member;
    const modal = document.getElementById('memberModal');
    const detailsContainer = document.getElementById('memberDetails');
    const avatarContainer = document.getElementById('memberAvatar');
    
    // Role mapping
    const roleMap = {
        'admin': 'Quản trị',
        'staff': 'Chủ nhiệm',
        'user': 'Thành viên'
    };
    
    // Build the avatar HTML
    let avatarHTML = '';
    if (member.avatar) {
        avatarHTML = `<img src="/webdacn_quanlyclb/${escapeHTML(member.avatar)}" alt="Member Avatar" class="modal-avatar">`;
    } else {
        avatarHTML = `<div class="modal-avatar-placeholder">${escapeHTML((member.fullname || 'U').charAt(0))}</div>`;
    }
    avatarContainer.innerHTML = avatarHTML;
    
    // Build the details HTML
    let detailsHTML = `
        <p><span class="info-label">Họ và tên:</span> ${escapeHTML(member.fullname || 'Chưa có tên')}</p>
        <p><span class="info-label">Email:</span> ${escapeHTML(member.email)}</p>
        <p><span class="info-label">Điểm:</span> ${member.point !== null ? escapeHTML(member.point.toString()) : '0'}</p>
    `;
    
    // Add additional fields if they exist
    if (member.team_id) {
        detailsHTML += `<p><span class="info-label">ID Đội:</span> ${escapeHTML(member.team_id.toString())}</p>`;
    }
    if (member.role) {
        detailsHTML += `<p><span class="info-label">Vai trò:</span> ${escapeHTML(roleMap[member.role] || 'Không xác định')}</p>`;
    }
    if (member.created_at) {
        detailsHTML += `<p><span class="info-label">Ngày tạo:</span> ${new Date(member.created_at).toLocaleString('vi-VN')}</p>`;
    }
    if (member.updated_at) {
        detailsHTML += `<p><span class="info-label">Cập nhật lần cuối:</span> ${new Date(member.updated_at).toLocaleString('vi-VN')}</p>`;
    }
    
    detailsContainer.innerHTML = detailsHTML;
    modal.style.display = 'block';
    // Trigger animation after a brief delay to ensure display is set
    setTimeout(() => {
        modal.classList.add('open');
    }, 10);
}

function showPunishForm() {
    if (!currentMember) return;
    
    const memberModal = document.getElementById('memberModal');
    const punishModal = document.getElementById('punishModal');
    const userIdInput = document.getElementById('punishUserId');
    const teamIdInput = document.getElementById('punishTeamId');
    
    userIdInput.value = currentMember.id || '';
    teamIdInput.value = currentMember.team_id || '';
    
    memberModal.classList.remove('open');
    setTimeout(() => {
        memberModal.style.display = 'none';
        punishModal.style.display = 'block';
        setTimeout(() => {
            punishModal.classList.add('open');
        }, 10);
    }, 300); // Match transition duration
}

function showRewardForm() {
    if (!currentMember) return;
    
    const memberModal = document.getElementById('memberModal');
    const rewardModal = document.getElementById('rewardModal');
    const userIdInput = document.getElementById('rewardUserId');
    const teamIdInput = document.getElementById('rewardTeamId');
    
    userIdInput.value = currentMember.id || '';
    teamIdInput.value = currentMember.team_id || '';
    
    memberModal.classList.remove('open');
    setTimeout(() => {
        memberModal.style.display = 'none';
        rewardModal.style.display = 'block';
        setTimeout(() => {
            rewardModal.classList.add('open');
        }, 10);
    }, 300); // Match transition duration
}

function closeModal() {
    const modal = document.getElementById('memberModal');
    modal.classList.remove('open');
    setTimeout(() => {
        modal.style.display = 'none';
        currentMember = null;
    }, 300); // Match transition duration
}

function closePunishModal() {
    const modal = document.getElementById('punishModal');
    modal.classList.remove('open');
    setTimeout(() => {
        modal.style.display = 'none';
        currentMember = null;
    }, 300); // Match transition duration
}

function closeRewardModal() {
    const modal = document.getElementById('rewardModal');
    modal.classList.remove('open');
    setTimeout(() => {
        modal.style.display = 'none';
        currentMember = null;
    }, 300); // Match transition duration
}

// Close modal when clicking outside
window.onclick = function(event) {
    const memberModal = document.getElementById('memberModal');
    const punishModal = document.getElementById('punishModal');
    const rewardModal = document.getElementById('rewardModal');
    if (event.target == memberModal) {
        memberModal.classList.remove('open');
        setTimeout(() => {
            memberModal.style.display = 'none';
            currentMember = null;
        }, 300);
    }
    if (event.target == punishModal) {
        punishModal.classList.remove('open');
        setTimeout(() => {
            punishModal.style.display = 'none';
            currentMember = null;
        }, 300);
    }
    if (event.target == rewardModal) {
        rewardModal.classList.remove('open');
        setTimeout(() => {
            rewardModal.style.display = 'none';
            currentMember = null;
        }, 300);
    }
}
</script>

<?php include 'app/views/shares/footer.php'; ?>