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

    .friend-search-container {
        background-color: var(--background-color);
        padding: 40px 20px;
        min-height: 80vh;
    }

    .search-wrapper {
        max-width: 800px;
        margin: 0 auto;
        background-color: var(--card-bg);
        padding: 30px;
        border-radius: 16px;
        box-shadow: var(--shadow);
    }

    .search-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .search-header h2 {
        color: var(--primary-color);
        font-weight: 700;
        margin: 0;
    }

    .user-list .user-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border: 1px solid #eee;
        border-radius: 12px;
        margin-bottom: 15px;
        transition: all 0.2s ease;
    }

    .user-list .user-card:hover {
        border-color: var(--primary-color);
        background-color: #fcf4f7;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #888;
    }
    
    /* CSS cho gợi ý tìm kiếm */
    #searchSuggestions {
        position: absolute;
        top: 100%;
        z-index: 1000;
    }
</style>

<div class="friend-search-container">
    <div class="search-wrapper">
        <div class="search-header">
            <h2><i class="fas fa-search me-2"></i>Tìm kiếm bạn bè</h2>
            <a href="/webdacn_quanlyclb/friend/friendsList" class="btn btn-outline-success">
                <i class="fas fa-users me-2"></i>Danh sách bạn bè
            </a>
        </div>

        <form method="GET" action="/webdacn_quanlyclb/friend/searchFriends" class="mb-4">
            <div class="input-group position-relative">
                <input type="text" name="q" id="searchInput" class="form-control" placeholder="Nhập tên hoặc username để tìm kiếm..." value="<?= htmlspecialchars($query ?? '', ENT_QUOTES); ?>" autocomplete="off">
                <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color); border-color: var(--primary-color);">Tìm</button>
                <div class="dropdown-menu w-100" id="searchSuggestions"></div>
            </div>
        </form>

        <h4 class="mb-3">Kết quả</h4>
        <div class="user-list">
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $user): ?>
                    <div class="user-card">
                        <div class="d-flex align-items: center">
                            <?php
                            $avatarDisplay = (!empty($user['avatar']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $user['avatar']))
                                ? '/webdacn_quanlyclb/' . $user['avatar']
                                : '/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg';
                            ?>
                            <img src="<?= htmlspecialchars($avatarDisplay, ENT_QUOTES); ?>" alt="Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($user['fullname']); ?></h5>
                                <small class="text-muted">@<?= htmlspecialchars($user['username']); ?></small>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary send-friend-request" data-receiver-id="<?= $user['id']; ?>">
                            <i class="fas fa-user-plus me-1"></i> Gửi yêu cầu
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user-slash fa-3x mb-3"></i>
                    <p>Không tìm thấy người dùng nào phù hợp.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="friendRequestSentModal" tabindex="-1" aria-labelledby="friendRequestSentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #E91E63; color: white;">
                <h5 class="modal-title" id="friendRequestSentModalLabel">Yêu cầu đã được gửi!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Yêu cầu kết bạn của bạn đã được gửi đi.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" style="background-color: var(--primary-color); border-color: var(--primary-color);">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const suggestionsContainer = document.getElementById('searchSuggestions');
    const friendRequestModal = new bootstrap.Modal(document.getElementById('friendRequestSentModal'));

    // --- LOGIC GỢI Ý TÌM KIẾM (LIVE SEARCH) ---
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        if (query.length < 2) { // Chỉ tìm khi có ít nhất 2 ký tự
            suggestionsContainer.classList.remove('show');
            return;
        }

        fetch(`/webdacn_quanlyclb/friend/searchUsers?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(users => {
                suggestionsContainer.innerHTML = ''; // Xóa gợi ý cũ
                if (users.length > 0) {
                    users.forEach(user => {
                        const avatarUrl = user.avatar 
                            ? `/webdacn_quanlyclb/${user.avatar}` 
                            : '/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg';
                        
                        const suggestionItem = document.createElement('a');
                        suggestionItem.href = '#'; // Hoặc link đến profile user
                        suggestionItem.classList.add('dropdown-item', 'd-flex', 'align-items-center');
                        suggestionItem.innerHTML = `
                            <img src="${avatarUrl}" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                            <span>${user.fullname} (@${user.username})</span>
                        `;
                        // Khi click vào gợi ý, điền vào ô tìm kiếm và submit form
                        suggestionItem.addEventListener('click', (e) => {
                            e.preventDefault();
                            searchInput.value = user.fullname;
                            suggestionsContainer.classList.remove('show');
                            searchInput.closest('form').submit();
                        });
                        suggestionsContainer.appendChild(suggestionItem);
                    });
                    suggestionsContainer.classList.add('show');
                } else {
                    suggestionsContainer.classList.remove('show');
                }
            })
            .catch(error => console.error('Lỗi khi tải gợi ý:', error));
    });

    // Ẩn gợi ý khi click ra ngoài
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target)) {
            suggestionsContainer.classList.remove('show');
        }
    });

    // --- LOGIC GỬI YÊU CẦU KẾT BẠN ---
    document.addEventListener('click', function(event) {
        const targetButton = event.target.closest('.send-friend-request');
        if (targetButton) {
            const receiverId = targetButton.dataset.receiverId;
            
            fetch('/webdacn_quanlyclb/friend/sendFriendRequest', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                },
                body: JSON.stringify({ receiver_id: receiverId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    friendRequestModal.show();
                    targetButton.textContent = 'Đã gửi';
                    targetButton.disabled = true;
                    targetButton.classList.remove('btn-outline-primary');
                    targetButton.classList.add('btn-success');
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể gửi yêu cầu.'));
                }
            })
            .catch(error => console.error('Lỗi khi gửi yêu cầu kết bạn:', error));
        }
    });
});
</script>