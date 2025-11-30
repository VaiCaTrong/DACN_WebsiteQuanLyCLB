<?php
include $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php';
?>

<style>
    :root {
        --primary-color: #E91E63;
        --background-color: #f5f7fa;
        --card-bg: #fff;
        --shadow: 0 8px 25px rgba(0,0,0,0.07);
    }

    /* === BỐ CỤC CHUNG === */
    .friends-list-container {
        background-color: var(--background-color);
        padding: 40px 20px;
        min-height: 80vh;
    }

    .list-wrapper {
        max-width: 800px;
        margin: 0 auto;
        background-color: var(--card-bg);
        padding: 30px;
        border-radius: 16px;
        box-shadow: var(--shadow);
    }

    .list-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .list-header h2 {
        color: var(--primary-color);
        font-weight: 700;
        margin: 0;
    }

    /* === THẺ BẠN BÈ === */
    .friend-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border: 1px solid #eee;
        border-radius: 12px;
        margin-bottom: 15px;
        transition: all 0.2s ease;
    }

    .friend-card:hover {
        border-color: var(--primary-color);
        background-color: #fcf4f7;
        transform: translateY(-3px);
    }

    /* === TRẠNG THÁI TRỐNG === */
    .empty-state {
        text-align: center;
        padding: 50px;
        color: #888;
    }
    
    .empty-state h4 {
        color: #555;
        font-weight: 600;
    }
    
    /* === CSS CỦA MODAL NHẮN TIN (GIỮ NGUYÊN) === */
    .chat-modal .modal-dialog { max-width: 550px; }
    .chat-header { background: linear-gradient(135deg, #E91E63, #9C27B0); color: white; border-radius: 0; padding: 12px 15px; }
    .chat-messages { height: 450px; overflow-y: auto; padding: 12px; background-color: #f8f9fa; }
    .message { margin-bottom: 12px; display: flex; align-items: flex-end; }
    .message.sent { justify-content: flex-end; }
    .message.received { justify-content: flex-start; }
    .message-content { max-width: 85%; }
    .message-bubble { padding: 8px 12px; border-radius: 15px; position: relative; font-size: 0.85rem; line-height: 1.4; }
    .message.sent .message-bubble { background: linear-gradient(135deg, #E91E63, #9C27B0); color: white; border-bottom-right-radius: 4px; }
    .message.received .message-bubble { background: white; color: #333; border: 1px solid #e0e0e0; border-bottom-left-radius: 4px; }
    .message-time { font-size: 0.65rem; opacity: 0.7; margin-top: 4px; text-align: right; }
    .sender-name { font-weight: 500; font-size: 0.75rem; margin-left: 5px; margin-bottom: 2px; color: #555; }
    .chat-input-container { border-top: 1px solid #e0e0e0; padding: 12px; background: white; }
    .chat-messages::-webkit-scrollbar { width: 5px; }
    .chat-messages::-webkit-scrollbar-track { background: #f1f1f1; }
    .chat-messages::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
    .chat-messages::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
    .typing-indicator { display: none; font-style: italic; color: #666; padding: 5px 12px; font-size: 0.75rem; }
    .chat-input-container .form-control { font-size: 0.85rem; }
    .chat-input-container small { font-size: 0.7rem; }
</style>

<div class="friends-list-container">
    <div class="list-wrapper">
        <div class="list-header">
            <h2><i class="fas fa-users me-2"></i>Danh sách bạn bè</h2>
            <a href="/webdacn_quanlyclb/friend/searchFriends" class="btn btn-primary" style="background-color: var(--primary-color); border-color: var(--primary-color);">
                <i class="fas fa-search me-2"></i>Tìm bạn mới
            </a>
        </div>

        <div class="friend-list">
            <?php if (!empty($friends)): ?>
                <?php foreach ($friends as $friend): ?>
                    <div class="friend-card">
                        <div class="d-flex align-items-center">
                            <?php
                            $avatarDisplay = (!empty($friend['avatar']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $friend['avatar']))
                                ? '/webdacn_quanlyclb/' . $friend['avatar']
                                : '/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg';
                            ?>
                            <img src="<?= htmlspecialchars($avatarDisplay, ENT_QUOTES); ?>" alt="Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($friend['fullname']); ?></h5>
                                <small class="text-muted">@<?= htmlspecialchars($friend['username']); ?></small>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary chat-btn" data-friend-id="<?= $friend['id']; ?>" data-friend-name="<?= htmlspecialchars($friend['fullname']); ?>" data-friend-avatar="<?= htmlspecialchars($avatarDisplay, ENT_QUOTES); ?>">
                                <i class="fas fa-comment me-1"></i>Nhắn tin
                            </button>
                            <button class="btn btn-sm btn-outline-danger remove-friend" data-friend-id="<?= $friend['id']; ?>">
                                <i class="fas fa-user-times"></i> Hủy kết bạn
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user-friends fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h4>Danh sách bạn bè trống</h4>
                    <p>Hãy kết bạn với mọi người để bắt đầu trò chuyện nhé!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade chat-modal" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header chat-header">
                <div class="d-flex align-items-center">
                    <img id="chatFriendAvatar" src="" alt="Avatar" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                    <div>
                        <h5 class="modal-title mb-0" id="chatFriendName"></h5>
                        <small class="opacity-75" id="chatStatus">Đang trực tuyến</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="chat-messages" id="chatMessages">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <p>Bắt đầu trò chuyện với bạn bè</p>
                    </div>
                </div>
                <div class="typing-indicator" id="typingIndicator">
                    <span id="typingText">Đang soạn tin nhắn...</span>
                </div>
            </div>
            <div class="chat-input-container">
                <div class="input-group">
                    <input type="text" class="form-control" id="messageInput" placeholder="Nhập tin nhắn..." maxlength="1000">
                    <button class="btn btn-primary" type="button" id="sendMessageBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <small class="text-muted mt-2">Nhấn Enter để gửi tin nhắn</small>
            </div>
        </div>
    </div>
</div>

<script>
    let currentFriendId = null;
    let currentFriendName = null;
    let messagePolling = null;

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.chat-btn').forEach(button => {
            button.addEventListener('click', function() {
                const friendId = this.dataset.friendId;
                const friendName = this.dataset.friendName;
                const friendAvatar = this.dataset.friendAvatar;
                openChat(friendId, friendName, friendAvatar);
            });
        });

        document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        document.getElementById('chatModal').addEventListener('hidden.bs.modal', function() {
            if (messagePolling) {
                clearInterval(messagePolling);
                messagePolling = null;
            }
            currentFriendId = null;
        });
        
        document.querySelectorAll('.remove-friend').forEach(button => {
            button.addEventListener('click', function() {
                const friendId = this.dataset.friendId;
                if (confirm('Bạn có chắc muốn xóa bạn bè này?')) {
                    fetch('/webdacn_quanlyclb/friend/removeFriend', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': '<?= htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                        },
                        body: JSON.stringify({ friend_id: friendId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Lỗi khi xóa bạn bè: ' + (data.message || 'Vui lòng thử lại.'));
                        }
                    })
                    .catch(error => console.error('Lỗi:', error));
                }
            });
        });
    });

    function openChat(friendId, friendName, friendAvatar) {
        currentFriendId = friendId;
        currentFriendName = friendName;

        document.getElementById('chatFriendName').textContent = friendName;
        document.getElementById('chatFriendAvatar').src = friendAvatar;

        loadMessages();
        if (messagePolling) clearInterval(messagePolling);
        messagePolling = setInterval(loadMessages, 3000);

        const modal = new bootstrap.Modal(document.getElementById('chatModal'));
        modal.show();

        setTimeout(() => document.getElementById('messageInput').focus(), 500);
    }

    function loadMessages() {
        if (!currentFriendId) return;

        fetch(`/webdacn_quanlyclb/friend/apiGetMessages?friend_id=${currentFriendId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMessages(data.messages);
                } else {
                    console.error('Failed to load messages:', data.message);
                }
            })
            .catch(error => console.error('Error loading messages:', error));
    }

    function sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();

        if (!message || !currentFriendId) return;

        const formData = new FormData();
        formData.append('receiver_id', currentFriendId);
        formData.append('message', message);

        fetch('/webdacn_quanlyclb/friend/apiSendMessage', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageInput.value = '';
                    loadMessages();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => console.error('Connection error:', error));
    }

    function displayMessages(messages) {
        const messagesContainer = document.getElementById('chatMessages');
        const currentUserId = <?= SessionHelper::getUserId(); ?>;

        if (messages.length === 0) {
            messagesContainer.innerHTML = `<div class="text-center text-muted py-4"><i class="fas fa-comments fa-2x mb-2"></i><p>Chưa có tin nhắn nào. Hãy bắt đầu trò chuyện!</p></div>`;
            return;
        }

        let messagesHTML = '';
        messages.forEach(message => {
            const isSent = parseInt(message.sender_id) === currentUserId;
            const messageTime = new Date(message.timestamp).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
            const senderInfo = !isSent ? `<div class="sender-name small text-muted mb-1">${message.sender_fullname || message.sender_username}</div>` : '';

            messagesHTML += `
            <div class="message ${isSent ? 'sent' : 'received'}">
                <div class="message-content">
                    ${senderInfo}
                    <div class="message-bubble">
                        <div class="message-text">${escapeHtml(message.content)}</div>
                        <div class="message-time">${messageTime}</div>
                    </div>
                </div>
            </div>`;
        });

        messagesContainer.innerHTML = messagesHTML;
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function escapeHtml(unsafe) {
        return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }
</script>