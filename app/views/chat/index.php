<?php
// Đảm bảo bạn đã include header ở đầu file
include_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php';
?>

<style>
    :root {
        --primary-color: #E91E63;
        --secondary-color: #f1f0f0;
        --background-color: #fff;
        --border-color: #e9e9e9;
        --text-dark: #333;
        --text-light: #888;
        --font-family: 'Segoe UI', system-ui, sans-serif;
        --highlight-color: #050504ff;
        /* Màu vàng nhạt để làm nổi bật */
    }

    .chat-container {
        display: flex;
        height: calc(100vh - 120px);
        max-height: 850px;
        min-height: 600px;
        font-family: var(--font-family);
        background-color: var(--background-color);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.07);
        margin: 20px auto;
    }

    /* --- Cột bên trái: Danh sách liên hệ --- */
    .contact-sidebar {
        width: 350px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--border-color);
        background-color: #fcfcfc;
    }

    .sidebar-header {
        padding: 24px;
        border-bottom: 1px solid var(--border-color);
    }

    .sidebar-header h3 {
        margin: 0;
        color: var(--primary-color);
        font-weight: 600;
        font-size: 1.5rem;
    }

    .contact-list {
        overflow-y: auto;
        flex-grow: 1;
        padding: 10px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .contact-item:hover {
        background-color: var(--secondary-color);
    }

    .contact-item.active {
        background-color: var(--primary-color);
        color: white;
    }

    .contact-item.active .text-muted {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    .contact-avatar {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 50%;
        margin-right: 15px;
    }

    .team-icon {
        color: var(--primary-color);
        margin-right: 5px;
    }

    .contact-item.active .team-icon {
        color: white;
    }

    .chat-health-badge {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 10px;
        margin-left: auto; /* Đẩy huy hiệu về bên phải */
        color: white;
    }

    /* --- Cột bên phải: Khung chat --- */
    .chat-panel {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .chat-header {
        display: flex;
        align-items: center;
        padding: 15px 25px;
        border-bottom: 1px solid var(--border-color);
        background-color: #fff;
    }

    .chat-body {
        flex-grow: 1;
        padding: 30px;
        overflow-y: auto;
        background-color: #f5f7fa;
        display: flex;
        flex-direction: column;
    }

    .welcome-screen {
        margin: auto;
        text-align: center;
        color: var(--text-light);
    }

    .welcome-screen i {
        font-size: 4rem;
        margin-bottom: 1.5rem;
    }

    /* --- Bong bóng chat --- */
    .message {
        display: flex;
        margin-bottom: 20px;
        max-width: 70%;
        position: relative;
    }

    .message-content {
        position: relative;
    }

    .message-bubble {
        padding: 12px 18px;
        border-radius: 20px;
        font-size: 0.95rem;
        line-height: 1.5;
        word-wrap: break-word;
    }

    .sender-name {
        font-size: 0.8rem;
        font-weight: 600;
        margin-left: 10px;
        margin-bottom: 5px;
        color: var(--text-dark);
    }

    .message-time {
        font-size: 0.75rem;
        color: var(--text-light);
        margin-top: 6px;
    }

    .received {
        align-self: flex-start;
    }

    .received .message-bubble {
        background-color: #e9eaf1;
        border-bottom-left-radius: 5px;
    }

    .received .message-time {
        text-align: left;
        margin-left: 5px;
    }

    .sent {
        align-self: flex-end;
    }

    .sent .message-bubble {
        background-color: var(--primary-color);
        color: white;
        border-bottom-right-radius: 5px;
    }

    .sent .message-time {
        text-align: right;
        margin-right: 5px;
    }

    .chat-footer {
        padding: 15px 25px;
        border-top: 1px solid var(--border-color);
        background-color: #fff;
    }

    #imagePreviewContainer {
        position: relative;
        display: none;
        margin-bottom: 15px;
        padding: 10px;
        background: var(--secondary-color);
        border-radius: 12px;
        width: fit-content;
    }

    #imagePreview {
        max-height: 120px;
        max-width: 100%;
        border-radius: 8px;
    }

    #removeImageBtn {
        position: absolute;
        top: -10px;
        right: -10px;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        cursor: pointer;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chat-input-group {
        display: flex;
        align-items: center;
        background-color: var(--secondary-color);
        border-radius: 25px;
        padding: 5px;
    }


    #messageInput {
        flex-grow: 1;
        border: none;
        background: transparent;
        padding: 10px 15px;
        outline: none;
        font-size: 0.95rem;
    }

    #attachFileBtn {
        font-size: 1.3rem;
        color: var(--text-light);
        cursor: pointer;
        padding: 10px 15px;
    }

    #attachFileBtn:hover {
        color: var(--primary-color);
    }

    #chatImageInput {
        display: none;
    }

    #sendMessageBtn {
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        flex-shrink: 0;
    }

    .message-image {
        max-width: 100%;
        max-height: 280px;
        border-radius: 15px;
        cursor: pointer;
        margin-bottom: 5px;
    }

    .message:hover .message-actions {
        opacity: 1;
    }

    .message-actions {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: #fff;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        opacity: 0;
        transition: opacity 0.2s ease;
        padding: 4px;
        z-index: 10;
    }

    .sent .message-actions {
        right: 100%;
        margin-right: 8px;
    }

    .received .message-actions {
        left: 100%;
        margin-left: 8px;
    }

    .message-action-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-light);
        font-size: 1rem;
        padding: 6px 8px;
    }

    .message-action-btn:hover {
        color: var(--primary-color);
        background-color: var(--secondary-color);
        border-radius: 50%;
    }

    /* --- Phần trả lời tin nhắn --- */

    .reply-context {
        background-color: rgba(0, 0, 0, 0.05);
        padding: 8px 12px;
        margin-bottom: 6px;
        border-radius: 10px;
        border-left: 3px solid var(--primary-color);
        font-size: 0.85rem;
    }

    .reply-context.clickable {
        cursor: pointer;
    }

    .reply-context.clickable:hover {
        background-color: rgba(0, 0, 0, 0.08);
    }

    .message.highlighted .message-bubble {
        animation: highlight-fade 2.5s ease-out;
    }

    @keyframes highlight-fade {
        0% {
            background-color: var(--highlight-color);
        }

        100% {
            background-color: initial;
        }

        /* Sẽ quay về màu ban đầu */
    }

    .sent .reply-context {
        border-left-color: #fff;
    }

    .sent .reply-context strong {
        color: #fff;
    }

    .reply-context strong {
        color: var(--primary-color);
    }

    .reply-context-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        color: var(--text-light);
    }

    .sent .reply-context-text {
        color: rgba(255, 255, 255, 0.8);
    }

    #replyPreviewContainer {
        display: none;
        padding: 10px 0;
        margin-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
    }

    .scroll-to-bottom {
        position: absolute;
        bottom: 80px;
        /* Adjust based on your footer height */
        right: 30px;
        background-color: rgba(255, 255, 255, 0.9);
        color: var(--primary-color);
        border: 1px solid var(--border-color);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        display: none;
        /* Hidden by default */
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .message-loader-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 100%;
    }

    .triangle-loader {
        width: 0;
        height: 0;
        border-left: 25px solid transparent;
        /* Điều chỉnh kích thước tam giác */
        border-right: 25px solid transparent;
        border-bottom: 40px solid var(--primary-color);
        /* Màu hồng */
        animation: spin 1s linear infinite;
        /* Sử dụng lại animation cũ */
        position: relative;
        /* Thêm để xoay quanh tâm */
        transform-origin: 50% 60%;
        /* Điều chỉnh tâm xoay */
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
<?php
// Hàm trợ giúp để tạo huy hiệu, dựa trên group_health_score
function getChatHealthBadge($score)
{
    if ($score === null) {
        return ''; // Không có tin nhắn nào, không hiển thị gì
    }
    $score = floatval($score);
    if ($score < 20) {
        return '<span class="chat-health-badge bg-danger" title="Điểm sức khỏe: ' . number_format($score) . '">Cảnh báo</span>';
    } elseif ($score < 50) {
        return '<span class="chat-health-badge bg-warning text-dark" title="Điểm sức khỏe: ' . number_format($score) . '">Chú ý</span>';
    } else {
        return '<span class="chat-health-badge bg-success" title="Điểm sức khỏe: ' . number_format($score) . '">Tốt</span>';
    }
}
?>

<div class="container my-4">
    <div class="chat-container">
        <div class="contact-sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-comments me-2"></i>Trò chuyện</h3>
            </div>
            <div class="contact-list" id="contactList">
                <?php if (empty($friends) && empty($teams)): ?>
                    <div class="text-center text-muted p-5">
                        <i class="fas fa-users-slash fa-3x mb-3"></i>
                        <p>Bạn chưa có bạn bè hoặc nhóm nào.</p>
                        <a href="/webdacn_quanlyclb/friend/searchFriends" class="btn btn-sm btn-primary">Tìm bạn bè</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($teams as $team):
                        $avatarPath = !empty($team['avatar_team']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $team['avatar_team'])
                            ? '/webdacn_quanlyclb/' . $team['avatar_team']
                            : '/webdacn_quanlyclb/public/uploads/avatars/team_default.jpg';
                        // Sử dụng group_health_score để đánh giá
                        $healthBadge = getChatHealthBadge($team['group_health_score']);
                    ?>
                        <div class="contact-item" data-team-id="<?= $team['id'] ?>" data-name="<?= htmlspecialchars($team['name']) ?>" data-avatar="<?= htmlspecialchars($avatarPath, ENT_QUOTES) ?>" data-leader-id="<?= $team['leader_id'] ?>" data-is-locked="<?= $team['is_chat_locked'] ?>">
                            <img src="<?= htmlspecialchars($avatarPath, ENT_QUOTES) ?>" alt="Team Avatar" class="contact-avatar">
                            <div>
                                <h6 class="mb-0"><i class="fas fa-users team-icon"></i><?= htmlspecialchars($team['name']) ?></h6>
                                <small class="text-muted">Nhóm chat</small>
                            </div>
                            <?= $healthBadge ?>
                        </div>
                    <?php endforeach; ?>
                    <?php foreach ($friends as $friend):
                        $avatarDisplay = (!empty($friend['avatar']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $friend['avatar']))
                            ? '/webdacn_quanlyclb/' . $friend['avatar']
                            : '/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg';
                    ?> 
                        <div class="contact-item" data-friend-id="<?= $friend['id'] ?>" data-name="<?= htmlspecialchars($friend['fullname']) ?>" data-avatar="<?= htmlspecialchars($avatarDisplay, ENT_QUOTES) ?>">
                            <img src="<?= htmlspecialchars($avatarDisplay, ENT_QUOTES) ?>" alt="Avatar" class="contact-avatar">
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($friend['fullname']) ?></h6>
                                <small class="text-muted">@<?= htmlspecialchars($friend['username']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="chat-panel">
            <div class="chat-header" id="chatHeader" style="display: none;">
                <div class="d-flex align-items-center">
                    <img id="active-chat-avatar" src="" alt="Avatar" class="contact-avatar">
                    <div>
                        <h5 class="mb-0" id="active-chat-name"></h5>
                        <small class="text-muted" id="active-chat-status"></small>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <!-- Nút dọn dẹp -->
                    <button id="clearChatBtn" class="btn btn-sm" title="Dọn dẹp cuộc trò chuyện" style="display: none;">
                        <i class="fas fa-broom"></i> Dọn dẹp
                    </button>
                    <!-- Nút mở khóa -->
                    <button id="unlockChatBtn" class="btn btn-sm" title="Mở khóa và reset điểm" style="display: none;">
                        <i class="fas fa-key"></i> Mở khóa
                    </button>
                </div>
            </div>

            <div class="chat-body" id="chatMessages">
                <div class="welcome-screen" id="welcomeScreen">
                    <i class="fas fa-paper-plane text-black"></i>
                    <h4>Bắt đầu cuộc trò chuyện</h4>
                    <p>Chọn một người bạn hoặc một nhóm từ danh sách bên trái.</p>
                </div>
            </div>

            <div class="scroll-to-bottom" id="scrollToBottomBtn"><i class="fas fa-chevron-down"></i></div>

            <div class="chat-footer" id="chatInputContainer" style="display: none;">
                <!-- Vùng thông báo khóa chat -->
                <div id="chatLockedWarning" class="alert alert-danger text-center" style="display: none;">
                    <i class="fas fa-lock me-2"></i>
                    <strong>Cuộc trò chuyện này đã bị tạm khóa.</strong><br>
                    <small>Vui lòng liên hệ quản trị viên để biết thêm chi tiết.</small>
                </div>

                <!-- Vùng nhập tin nhắn -->
                <div id="replyPreviewContainer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-primary fw-bold">Đang trả lời <strong id="replyToName"></strong></small>
                            <div id="replyPreviewText" class="text-muted small" style="max-width: 500px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
                        </div>
                        <button id="cancelReplyBtn" class="btn-close btn-sm"></button>
                    </div>
                </div>

                <div id="imagePreviewContainer">
                    <img id="imagePreview" src="" alt="Xem trước ảnh" />
                    <button id="removeImageBtn">&times;</button>
                </div>

                <div class="chat-input-group">
                    <label id="attachFileBtn" for="chatImageInput">
                        <i class="fas fa-paperclip"></i>
                    </label>
                    <input type="file" id="chatImageInput" accept="image/png, image/jpeg, image/gif">

                    <input type="text" id="messageInput" placeholder="Nhập tin nhắn..." autocomplete="off">

                    <button type="button" id="sendMessageBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteMessageModal" tabindex="-1" aria-labelledby="deleteMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMessageModalLabel">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Xác nhận xóa tin nhắn
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa tin nhắn này không? Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Xóa tin nhắn</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal xác nhận dọn dẹp cuộc trò chuyện -->
<div class="modal fade" id="clearChatModal" tabindex="-1" aria-labelledby="clearChatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="clearChatModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Hành động nguy hiểm</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn <strong>xóa toàn bộ lịch sử</strong> của cuộc trò chuyện này không? <br>
                <strong class="text-danger">Hành động này không thể hoàn tác.</strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmClearChatBtn">Xác nhận xóa tất cả</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="toxicityWarningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-circle me-2"></i>Tin nhắn bị chặn</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-danger">
                    <i class="fas fa-shield-alt fa-3x"></i>
                </div>
                <h5 class="mb-3">Phát hiện nội dung không phù hợp</h5>
                <p class="text-muted" id="toxicityMessageContent">Tin nhắn của bạn có chứa ngôn từ vi phạm tiêu chuẩn cộng đồng.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Đã hiểu</button>
            </div>
        </div>
    </div>
</div>
<script>
    let currentFriendId = null;
    let currentTeamId = null;
    let currentChatName = null;
    const currentUserId = <?= SessionHelper::getUserId(); ?>;
    let currentReplyId = null;
    let currentUserIsAdmin = false; // Biến toàn cục để lưu trạng thái admin
    let isLoading = false;
    let autoScroll = true;
    let messageCheckInterval = null; // Biến lưu interval

    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chatMessages');
        const scrollToBottomBtn = document.getElementById('scrollToBottomBtn');
        const deleteModalEl = document.getElementById('deleteMessageModal');
        const deleteModal = new bootstrap.Modal(deleteModalEl);
        const clearChatModalEl = document.getElementById('clearChatModal');
        const clearChatModal = new bootstrap.Modal(clearChatModalEl);
        
        // Khởi tạo Modal cảnh báo độc hại
        const toxicityModalEl = document.getElementById('toxicityWarningModal');
        const toxicityModal = new bootstrap.Modal(toxicityModalEl);

        // Gắn sự kiện cho các item trong danh bạ
        document.querySelectorAll('.contact-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.contact-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                openChat(this.dataset.friendId || null, this.dataset.teamId || null, this.dataset.name, this.dataset.avatar);
            });
        });

        // Xử lý các hành động trên tin nhắn (trả lời, xóa)
        chatMessages.addEventListener('click', function(e) {
            const replyBtn = e.target.closest('.btn-reply');
            if (replyBtn) {
                const messageEl = replyBtn.closest('.message');
                startReply(messageEl.dataset.messageId, messageEl.dataset.senderName, messageEl.dataset.content, messageEl.dataset.messageType);
            }

            const deleteBtn = e.target.closest('.btn-delete');
            if (deleteBtn) {
                const messageEl = deleteBtn.closest('.message');
                const messageId = messageEl.dataset.messageId;
                const messageType = currentTeamId ? 'group' : 'private';

                // Mở Modal xóa
                const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
                confirmDeleteBtn.dataset.messageId = messageId;
                confirmDeleteBtn.dataset.messageType = messageType;
                deleteModal.show();
            }
            const replyContext = e.target.closest('.reply-context.clickable');
            if (replyContext) {
                const targetId = replyContext.dataset.scrollToId;
                const targetMessage = document.querySelector(`.message[data-message-id='${targetId}']`);
                if (targetMessage) {
                    // Cuộn tới tin nhắn
                    targetMessage.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    // Thêm class để highlight
                    targetMessage.classList.add('highlighted');
                    // Xóa class sau 2.5 giây
                    setTimeout(() => {
                        targetMessage.classList.remove('highlighted');
                    }, 2500);
                }
            }
        });

        // Gắn sự kiện cho nút xác nhận xóa trong Modal
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const messageId = this.dataset.messageId;
            const messageType = this.dataset.messageType;
            if (messageId && messageType) {
                deleteMessage(messageId, messageType);
                deleteModal.hide();
            }
        });

        // Gắn sự kiện cho nút dọn dẹp
        document.getElementById('clearChatBtn').addEventListener('click', function() {
            clearChatModal.show();
        });

        // Gắn sự kiện cho nút xác nhận dọn dẹp trong modal
        document.getElementById('confirmClearChatBtn').addEventListener('click', function() {
            clearChatHistory();
            clearChatModal.hide();
        });

        // Gắn sự kiện cho nút mở khóa
        document.getElementById('unlockChatBtn').addEventListener('click', function() {
            unlockGroupChat();
        });
        // Xử lý nút gửi tin nhắn
        document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);
        document.getElementById('messageInput').addEventListener('keypress', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        document.getElementById('cancelReplyBtn').addEventListener('click', cancelReply);

        // Xử lý input ảnh
        const chatImageInput = document.getElementById('chatImageInput');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');
        chatImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        document.getElementById('removeImageBtn').addEventListener('click', () => {
            chatImageInput.value = '';
            imagePreviewContainer.style.display = 'none';
        });

        // Xử lý cuộn
        chatMessages.addEventListener('scroll', () => {
            const isAtBottom = chatMessages.scrollHeight - chatMessages.scrollTop <= chatMessages.clientHeight + 50;
            autoScroll = isAtBottom;
            scrollToBottomBtn.style.display = isAtBottom ? 'none' : 'flex';
        });
        scrollToBottomBtn.addEventListener('click', () => chatMessages.scrollTop = chatMessages.scrollHeight);

        // Tải tin nhắn mới định kỳ
        setInterval(loadMessages, 3000);
    });

    function safeSetDisplay(id, value) {
        const el = document.getElementById(id);
        if (el) el.style.display = value;
        else console.warn(`❗ Không tìm thấy phần tử: ${id}`);
    }

    function openChat(friendId, teamId, name, avatar) {
        currentFriendId = friendId;
        currentTeamId = teamId;
        currentChatName = name;
        autoScroll = true;
        cancelReply();

        // Hiển thị header và input chat
        document.getElementById('chatHeader').style.display = 'flex';
        document.getElementById('active-chat-name').textContent = name;
        document.getElementById('active-chat-avatar').src = avatar;
        document.getElementById('active-chat-status').textContent = teamId ? 'Nhóm chat' : 'Đang hoạt động';

        // Hiển thị nút dọn dẹp nếu là admin
        const contactItem = document.querySelector(`.contact-item.active`);
        const isLocked = contactItem ? contactItem.dataset.isLocked === '1' : false;

        const clearChatBtn = document.getElementById('clearChatBtn');
        const unlockChatBtn = document.getElementById('unlockChatBtn');
        const chatInputGroup = document.querySelector('.chat-input-group');
        const chatLockedWarning = document.getElementById('chatLockedWarning');

        let canClear = false;
        if (friendId) {
            canClear = true; // Bất kỳ ai cũng có thể dọn dẹp chat riêng
        } else if (teamId) {
            const leaderId = contactItem ? contactItem.dataset.leaderId : null;
            canClear = currentUserIsAdmin || (leaderId && parseInt(leaderId) === currentUserId);
        }
        clearChatBtn.style.display = canClear ? 'inline-block' : 'none';

        // Xử lý hiển thị cho nhóm bị khóa
        unlockChatBtn.style.display = currentUserIsAdmin && isLocked ? 'inline-block' : 'none';
        chatInputGroup.style.display = isLocked ? 'none' : 'flex';
        chatLockedWarning.style.display = isLocked ? 'block' : 'none';

        const welcomeScreen = document.getElementById('welcomeScreen');
        const chatInputContainer = document.getElementById('chatInputContainer');
        const messagesContainer = document.getElementById('chatMessages');

        // Ẩn màn hình welcome khi vào box chat
        if (welcomeScreen) welcomeScreen.style.display = 'none';
        if (chatInputContainer) chatInputContainer.style.display = 'block';

        // Hiển thị hiệu ứng đang tải tin nhắn
        if (messagesContainer) {
            messagesContainer.innerHTML = `
            <div class="message-loader-container">
                <div class="triangle-loader"></div>
            </div>
        `;
        }

        // Giả lập load tin nhắn (hoặc gọi API)
        loadMessages(true);
        if (!messagesContainer.innerHTML.trim()) {
            messagesContainer.innerHTML = `
        <div class="no-messages-screen">
            <p>Chưa có tin nhắn nào trong cuộc trò chuyện này.</p>
        </div>
    `;
        }

    }

    function sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const imageInput = document.getElementById('chatImageInput');
        const message = messageInput.value.trim();
        const imageFile = imageInput.files[0];

        if (!message && !imageFile || (!currentFriendId && !currentTeamId)) return;

        const formData = new FormData();
        if (currentFriendId) formData.append('receiver_id', currentFriendId);
        if (currentTeamId) formData.append('team_id', currentTeamId);
        if (message) formData.append('message', message);
        if (imageFile) formData.append('chat_image', imageFile);
        if (currentReplyId) formData.append('reply_to_message_id', currentReplyId);

        fetch('/webdacn_quanlyclb/chat/apiSendMessage', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageInput.value = ''; // Xóa nội dung ô chat
                    imageInput.value = ''; // Reset input file
                    document.getElementById('imagePreviewContainer').style.display = 'none'; // Ẩn preview ảnh
                    cancelReply(); // Hủy trạng thái trả lời
                    autoScroll = true;
                    loadMessages();
                } else {
                    // === LOGIC XỬ LÝ LỖI & HIỂN THỊ MODAL CẢNH BÁO ===
                    if (data.error_type === 'moderation_failed') {
                        // Nếu lỗi do kiểm duyệt nội dung
                        document.getElementById('toxicityMessageContent').textContent = data.message;
                        var toxicityModal = new bootstrap.Modal(document.getElementById('toxicityWarningModal'));
                        toxicityModal.show();
                    } else {
                        // Lỗi khác thì dùng alert
                        alert(data.message || 'Đã xảy ra lỗi khi gửi tin nhắn.');
                    }
                }
            })
            .catch(error => console.error('Lỗi khi gửi tin nhắn:', error));
    }

    function loadMessages() {
        if (isLoading || (!currentFriendId && !currentTeamId)) return;
        isLoading = true;

        const url = currentTeamId ? `/webdacn_quanlyclb/chat/apiGetMessages?team_id=${currentTeamId}` : `/webdacn_quanlyclb/chat/apiGetMessages?friend_id=${currentFriendId}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                currentUserIsAdmin = data.isAdmin; // Cập nhật trạng thái admin
                if (data.success) { 
                    displayMessages(data.messages, data.isAdmin);
                }
            })
            .finally(() => isLoading = false);
    }

    function displayMessages(messages, isAdmin) {
        const messagesContainer = document.getElementById('chatMessages');
        let messagesHTML = '';

        if (messages.length === 0) {
            messagesContainer.innerHTML = `<div class="welcome-screen"><p>Chưa có tin nhắn nào. Hãy bắt đầu!</p></div>`;
            return;
        }

        messages.forEach(message => {
            const isSent = parseInt(message.sender_id) === currentUserId;
            const messageTime = new Date(message.timestamp).toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });
            const senderInfo = !isSent && currentTeamId ? `<div class="sender-name">${escapeHtml(message.sender_fullname)}</div>` : '';

            let contentHTML = '';
            if (message.deleted_at) {
                contentHTML = `<div class="fst-italic text-muted">Tin nhắn đã bị xóa</div>`;
            } else if (message.message_type === 'image') {
                contentHTML = `<a href="/webdacn_quanlyclb/${message.content}" target="_blank"><img src="/webdacn_quanlyclb/${message.content}" class="message-image" alt="Hình ảnh"></a>`;
            } else {
                // Xóa bỏ logic hiển thị điểm trên từng tin nhắn
                contentHTML = `<div>${escapeHtml(message.content)}</div>`;
            }

            let replyHTML = '';
            if (message.replied_content) {
                const repliedContent = message.replied_message_type === 'image' ? '<i class="fas fa-image"></i> Hình ảnh' : escapeHtml(message.replied_content);
                replyHTML = `<div class="reply-context clickable" data-scroll-to-id="${message.reply_to_message_id}">
                                <strong>${escapeHtml(message.replied_sender_fullname)}</strong>
                                <span class="reply-context-text">${repliedContent}</span>
                             </div>`;
            }

            let actionsHTML = '';
            if (!message.deleted_at) {
                const deleteBtn = isSent ? `<button class="message-action-btn btn-delete" title="Xóa"><i class="fas fa-trash"></i></button>` : '';
                actionsHTML = `<div class="message-actions"><button class="message-action-btn btn-reply" title="Trả lời"><i class="fas fa-reply"></i></button>${deleteBtn}</div>`;
            }

            messagesHTML += `<div class="message ${isSent ? 'sent' : 'received'}" data-message-id="${message.id}" data-sender-name="${escapeHtml(message.sender_fullname)}" data-content="${escapeHtml(message.content)}" data-message-type="${message.message_type}">${actionsHTML}<div class="message-content">${senderInfo}<div class="message-bubble">${replyHTML}${contentHTML}</div><div class="message-time">${messageTime}</div></div></div>`;
        });

        if (messagesContainer.innerHTML.includes('message-loader-container') || messagesContainer.innerHTML !== messagesHTML) {
            messagesContainer.innerHTML = messagesHTML;
            if (autoScroll) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }
    }

    function startReply(messageId, senderName, content, messageType) {
        currentReplyId = messageId;
        document.getElementById('replyPreviewContainer').style.display = 'block';
        document.getElementById('replyToName').textContent = senderName;
        document.getElementById('replyPreviewText').innerHTML = messageType === 'image' ? '<i class="fas fa-image"></i> Một hình ảnh' : escapeHtml(content);
        document.getElementById('messageInput').focus();
    }

    function cancelReply() {
        currentReplyId = null;
        document.getElementById('replyPreviewContainer').style.display = 'none';
    }

    function deleteMessage(messageId, messageType) {
        fetch('/webdacn_quanlyclb/chat/apiDeleteMessage', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    messageId,
                    messageType
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadMessages();
                } else {
                    alert(data.message);
                }
            })
            .catch(err => console.error("Lỗi xóa tin nhắn:", err));
    }

    function clearChatHistory() {
        const formData = new FormData();
        if (currentFriendId) formData.append('friend_id', currentFriendId);
        if (currentTeamId) formData.append('team_id', currentTeamId);

        fetch('/webdacn_quanlyclb/chat/apiClearChatHistory', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadMessages(); // Tải lại tin nhắn (sẽ trống)
                alert('Đã xóa toàn bộ lịch sử cuộc trò chuyện!');
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(err => console.error("Lỗi dọn dẹp cuộc trò chuyện:", err));
    }

    function unlockGroupChat() {
        if (!currentUserIsAdmin || !currentTeamId) {
            alert("Bạn không có quyền thực hiện hành động này.");
            return;
        }

        if (!confirm("Bạn có chắc muốn mở khóa và reset điểm của nhóm này về 50?")) {
            return;
        }

        const formData = new FormData();
        formData.append('team_id', currentTeamId);

        fetch('/webdacn_quanlyclb/chat/apiUnlockGroupChat', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload(); // Tải lại trang để cập nhật trạng thái
        })
        .catch(err => console.error("Lỗi mở khóa nhóm:", err));
    }
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
</script>