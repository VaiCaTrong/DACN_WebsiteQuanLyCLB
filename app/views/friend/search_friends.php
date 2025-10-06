<?php
include 'app/views/shares/header.php';
?>

<div class="container my-5">
    <!-- Header với nút chuyển hướng -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #E91E63;">Tìm kiếm bạn bè</h2>
        <a href="/webdacn_quanlyclb/friend/friendsList" class="btn btn-success">
            <i class="fas fa-users me-2"></i>Danh sách bạn bè
        </a>
    </div>

    <form method="GET" action="/webdacn_quanlyclb/friend/searchFriends" class="mb-4">
        <div class="input-group position-relative">
            <input type="text" name="q" id="searchInput" class="form-control" placeholder="Tìm theo tên hoặc username" value="<?php echo htmlspecialchars($query ?? '', ENT_QUOTES); ?>" autocomplete="off">
            <button type="submit" class="btn btn-primary">Tìm</button>
            <!-- Dropdown gợi ý -->
            <div class="dropdown-menu w-100" id="searchSuggestions" style="max-height: 300px; overflow-y: auto;"></div>
        </div>
    </form>

    <h4 class="mb-3">Danh sách người dùng</h4>
    <?php if (!empty($results)): ?>
        <div class="list-group">
            <?php foreach ($results as $user): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <!-- Hiển thị avatar với xử lý fallback -->
                        <?php
                        $avatarPath = $user['avatar'] ?? 'public/uploads/avatars/default_avatar.jpg';
                        $fullAvatarPath = '/' . $avatarPath;
                        
                        // Kiểm tra file tồn tại
                        if (!empty($user['avatar']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $user['avatar'])) {
                            $avatarDisplay = '/webdacn_quanlyclb/' . $user['avatar'];
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
                            <h5 class="mb-1"><?php echo htmlspecialchars($user['fullname']); ?></h5>
                            <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['role']); ?>)</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary send-friend-request" data-receiver-id="<?php echo $user['id']; ?>">Gửi yêu cầu kết bạn</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Không tìm thấy người dùng nào.</div>
    <?php endif; ?>
</div>

<!-- Modal thông báo gửi yêu cầu kết bạn thành công -->
<div class="modal fade" id="friendRequestSentModal" tabindex="-1" aria-labelledby="friendRequestSentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #E91E63; color: white;">
                <h5 class="modal-title" id="friendRequestSentModalLabel">Yêu cầu kết bạn đã được gửi!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <img id="receiverAvatar" src="/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg" alt="Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                    <div>
                        <p class="mb-0">Yêu cầu kết bạn đã được gửi đến <strong id="receiverName">Người dùng</strong>.</p>
                        <small class="text-muted">Họ sẽ nhận được thông báo của bạn.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const suggestions = document.getElementById('searchSuggestions');

    // Gợi ý tìm kiếm khi nhập
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 2) {
            suggestions.innerHTML = '';
            suggestions.classList.remove('show');
            return;
        }

        fetch(`/webdacn_quanlyclb/friend/searchUsers?q=${encodeURIComponent(query)}`, {
            headers: {
                'X-CSRF-Token': '<?php echo htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
            }
        })
        .then(response => response.json())
        .then(users => {
            suggestions.innerHTML = '';
            if (users.length === 0) {
                suggestions.innerHTML = '<div class="dropdown-item text-muted">Không tìm thấy</div>';
            } else {
                users.forEach(user => {
                    const div = document.createElement('div');
                    div.className = 'dropdown-item d-flex align-items-center';
                    
                    // Xử lý avatar path cho suggestions
                    const avatarPath = user.avatar ? `/webdacn_quanlyclb/${user.avatar}` : '/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg';
                    
                    div.innerHTML = `
                        <img src="${avatarPath}" 
                             class="rounded-circle me-2" 
                             style="width: 30px; height: 30px; object-fit: cover;"
                             onerror="this.src='/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg'">
                        <span>${user.fullname} (@${user.username})</span>
                        <button class="btn btn-sm btn-outline-primary ms-auto send-friend-request" data-receiver-id="${user.id}">Gửi yêu cầu kết bạn</button>
                    `;
                    suggestions.appendChild(div);
                });
            }
            suggestions.classList.add('show');
        })
        .catch(error => {
            console.error('Error:', error);
            // Xử lý lỗi...
        });
    });

    // Ẩn gợi ý khi click ra ngoài
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !suggestions.contains(event.target)) {
            suggestions.classList.remove('show');
        }
    });

    // Gửi yêu cầu kết bạn
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('send-friend-request')) {
            const button = event.target;
            const receiverId = button.dataset.receiverId;
            fetch('/webdacn_quanlyclb/friend/sendFriendRequest', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?php echo htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                },
                body: JSON.stringify({ receiver_id: receiverId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Lấy thông tin người nhận để hiển thị trong modal
                    fetch(`/webdacn_quanlyclb/account/getUserInfo?id=${receiverId}`, {
                        headers: {
                            'X-CSRF-Token': '<?php echo htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                        }
                    })
                    .then(response => response.json())
                    .then(user => {
                        const modal = new bootstrap.Modal(document.getElementById('friendRequestSentModal'));
                        document.getElementById('receiverName').textContent = user.fullname || 'Người dùng';
                        
                        // Cập nhật avatar trong modal
                        const avatarPath = user.avatar ? `/webdacn_quanlyclb/${user.avatar}` : '/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg';
                        document.getElementById('receiverAvatar').src = avatarPath;
                        
                        modal.show();
                        button.disabled = true;
                        button.textContent = 'Đã gửi';
                    })
                    .catch(error => {
                        console.error('Error fetching user info:', error);
                        const modal = new bootstrap.Modal(document.getElementById('friendRequestSentModal'));
                        document.getElementById('receiverName').textContent = 'Người dùng';
                        document.getElementById('receiverAvatar').src = '/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg';
                        modal.show();
                        button.disabled = true;
                        button.textContent = 'Đã gửi';
                    });
                } else {
                    // Xử lý lỗi...
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Xử lý lỗi...
            });
        }
    });
});
</script>