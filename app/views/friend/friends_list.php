<?php
include 'app/views/shares/header.php';
?>
<style>
    .chat-modal .modal-dialog {
        max-width: 550px; /* Tăng nhẹ chiều rộng modal */
    }

    .chat-header {
        background: linear-gradient(135deg, #E91E63, #9C27B0);
        color: white;
        border-radius: 0;
        padding: 12px 15px; /* Giảm padding để gọn hơn */
    }

    .chat-messages {
        height: 450px; /* Tăng chiều cao để hiển thị nhiều tin nhắn hơn */
        overflow-y: auto;
        padding: 12px;
        background-color: #f8f9fa;
    }

    .message {
        margin-bottom: 12px; /* Giảm margin để tiết kiệm không gian */
        display: flex;
        align-items: flex-end;
    }

    .message.sent {
        justify-content: flex-end;
    }

    .message.received {
        justify-content: flex-start;
    }

    .message-content {
        max-width: 85%; /* Tăng chiều rộng để chứa nhiều chữ hơn */
    }

    .message-bubble {
        padding: 8px 12px; /* Giảm padding để tin nhắn gọn hơn */
        border-radius: 15px; /* Giảm border-radius để trông hiện đại hơn */
        position: relative;
        font-size: 0.85rem; /* Giảm kích thước chữ */
        line-height: 1.4; /* Tăng khoảng cách dòng để dễ đọc */
    }

    .message.sent .message-bubble {
        background: linear-gradient(135deg, #E91E63, #9C27B0);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .message.received .message-bubble {
        background: white;
        color: #333;
        border: 1px solid #e0e0e0;
        border-bottom-left-radius: 4px;
    }

    .message-time {
        font-size: 0.65rem; /* Giảm kích thước chữ thời gian */
        opacity: 0.7;
        margin-top: 4px;
        text-align: right;
    }

    .sender-name {
        font-weight: 500;
        font-size: 0.75rem; /* Giảm kích thước chữ tên người gửi */
        margin-left: 5px;
        margin-bottom: 2px;
        color: #555;
    }

    .chat-input-container {
        border-top: 1px solid #e0e0e0;
        padding: 12px;
        background: white;
    }

    /* Scrollbar styling */
    .chat-messages::-webkit-scrollbar {
        width: 5px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .list-group-item {
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .list-group-item:hover {
        transform: translateY(-3px);
    }

    .typing-indicator {
        display: none;
        font-style: italic;
        color: #666;
        padding: 5px 12px;
        font-size: 0.75rem;
    }

    /* Input field styling */
    .chat-input-container .form-control {
        font-size: 0.85rem; /* Giảm kích thước chữ input */
    }

    .chat-input-container small {
        font-size: 0.7rem; /* Giảm kích thước chữ hướng dẫn */
    }
</style>

<div class="container my-5">
    <!-- Header với nút chuyển hướng -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #E91E63;">Danh sách bạn bè</h2>
        <a href="/webdacn_quanlyclb/friend/searchFriends" class="btn btn-primary">
            <i class="fas fa-search me-2"></i>Tìm bạn bè
        </a>
    </div>

    <?php if (!empty($friends)): ?>
        <div class="list-group">
            <?php foreach ($friends as $friend): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <?php
                        // Xử lý avatar path
                        $avatarPath = $friend['avatar'] ?? 'public/uploads/avatars/default_avatar.jpg';
                        if (!empty($friend['avatar']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $friend['avatar'])) {
                            $avatarDisplay = '/webdacn_quanlyclb/' . $friend['avatar'];
                        } else {
                            $avatarDisplay = '/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg';
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($avatarDisplay, ENT_QUOTES); ?>"
                            alt="Avatar"
                            class="rounded-circle me-3"
                            style="width: 50px; height: 50px; object-fit: cover;"
                            onerror="this.src='/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg'">
                        <div>
                            <h5 class="mb-1"><?php echo htmlspecialchars($friend['fullname']); ?></h5>
                            <small class="text-muted">@<?php echo htmlspecialchars($friend['username']); ?></small>
                        </div>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary chat-btn"
                            data-friend-id="<?php echo $friend['id']; ?>"
                            data-friend-name="<?php echo htmlspecialchars($friend['fullname']); ?>"
                            data-friend-avatar="<?php echo htmlspecialchars($avatarDisplay, ENT_QUOTES); ?>">
                            <i class="fas fa-comment me-1"></i>Tin nhắn
                        </button>
                        <button class="btn btn-sm btn-outline-danger remove-friend" data-friend-id="<?php echo $friend['id']; ?>">
                            <i class="fas fa-user-times me-1"></i>Xóa bạn
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-users fa-2x mb-3 d-block" style="color: #E91E63;"></i>
            <h4>Bạn chưa có bạn bè nào</h4>
            <p class="mb-3">Hãy kết bạn với mọi người để xem danh sách tại đây!</p>
            <a href="/webdacn_quanlyclb/friend/searchFriends" class="btn btn-primary">
                <i class="fas fa-search me-2"></i>Tìm bạn bè ngay
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Chat Modal -->
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
                    <!-- Tin nhắn sẽ được load ở đây -->
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
                    <input type="text"
                        class="form-control"
                        id="messageInput"
                        placeholder="Nhập tin nhắn..."
                        maxlength="1000">
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
        // Xử lý nút mở chat
        document.querySelectorAll('.chat-btn').forEach(button => {
            button.addEventListener('click', function() {
                const friendId = this.dataset.friendId;
                const friendName = this.dataset.friendName;
                const friendAvatar = this.dataset.friendAvatar;

                openChat(friendId, friendName, friendAvatar);
            });
        });

        // Xử lý gửi tin nhắn
        document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);

        // Gửi tin nhắn bằng Enter
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Đóng modal thì dừng polling
        document.getElementById('chatModal').addEventListener('hidden.bs.modal', function() {
            if (messagePolling) {
                clearInterval(messagePolling);
                messagePolling = null;
            }
            currentFriendId = null;
        });
    });

    function openChat(friendId, friendName, friendAvatar) {
        currentFriendId = friendId;
        currentFriendName = friendName;

        // Cập nhật modal header
        document.getElementById('chatFriendName').textContent = friendName;
        document.getElementById('chatFriendAvatar').src = friendAvatar;

        // Load tin nhắn
        loadMessages();
        // Bắt đầu auto-refresh mỗi 3 giây
        if (messagePolling) {
            clearInterval(messagePolling);
        }
        messagePolling = setInterval(loadMessages, 3000);

        // Hiển thị modal
        const modal = new bootstrap.Modal(document.getElementById('chatModal'));
        modal.show();

        // Focus vào input
        setTimeout(() => {
            document.getElementById('messageInput').focus();
        }, 500);
    }

    function loadMessages() {
        if (!currentFriendId) {
            console.log('No friend ID');
            return;
        }

        console.log('Loading messages for friend:', currentFriendId);

        // Sử dụng action parameter
        fetch(`/webdacn_quanlyclb/friend/apiGetMessages?friend_id=${currentFriendId}`)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Messages data:', data);
                if (data.success) {
                    displayMessages(data.messages);
                } else {
                    console.error('Failed to load messages:', data.message);
                }
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                alert('Lỗi tải tin nhắn: ' + error.message);
            });
    }

    function sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();

        if (!message || !currentFriendId) {
            alert('Vui lòng nhập tin nhắn!');
            return;
        }

        console.log('Sending message to:', currentFriendId, 'Content:', message);

        const formData = new FormData();
        formData.append('receiver_id', currentFriendId);
        formData.append('message', message);

        // Sử dụng action parameter
        fetch('/webdacn_quanlyclb/friend/apiSendMessage', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Send message result:', data);
                if (data.success) {
                    messageInput.value = '';
                    loadMessages(); // Load lại tin nhắn sau khi gửi thành công
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Connection error:', error);
                alert('Lỗi kết nối: ' + error.message);
            });
    }

    function displayMessages(messages) {
        const messagesContainer = document.getElementById('chatMessages');
        const currentUserId = <?php echo SessionHelper::getUserId(); ?>;

        console.log('Displaying', messages.length, 'messages');

        if (messages.length === 0) {
            messagesContainer.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-comments fa-2x mb-2"></i>
                <p>Chưa có tin nhắn nào. Hãy bắt đầu trò chuyện!</p>
            </div>
        `;
            return;
        }

        let messagesHTML = '';

        messages.forEach(message => {
            const isSent = parseInt(message.sender_id) === currentUserId;
            const messageTime = new Date(message.timestamp).toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });

            // Hiển thị tên người gửi cho tin nhắn nhận được
            const senderInfo = !isSent ?
                `<div class="sender-name small text-muted mb-1">${message.sender_fullname || message.sender_username}</div>` :
                '';

            messagesHTML += `
            <div class="message ${isSent ? 'sent' : 'received'}">
                <div class="message-content">
                    ${senderInfo}
                    <div class="message-bubble">
                        <div class="message-text">${escapeHtml(message.content)}</div>
                        <div class="message-time">${messageTime}</div>
                    </div>
                </div>
            </div>
        `;
        });

        messagesContainer.innerHTML = messagesHTML;

        // Scroll xuống cuối
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Hàm utility
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Xử lý xóa bạn bè
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-friend')) {
            const friendId = event.target.dataset.friendId;
            if (confirm('Bạn có chắc muốn xóa bạn bè này?')) {
                fetch('/webdacn_quanlyclb/friend/removeFriend', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': '<?php echo htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                        },
                        body: JSON.stringify({
                            friend_id: friendId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Lỗi khi xóa bạn bè.');
                        }
                    });
            }
        }
    });
</script>