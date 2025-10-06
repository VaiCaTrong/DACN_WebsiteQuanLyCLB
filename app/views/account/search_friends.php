<?php
include 'app/views/shares/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4" style="color: #E91E63;">Tìm kiếm bạn bè</h2>
    <form method="GET" action="/webdacn_quanlyclb/friend/searchFriends" class="mb-4">
        <div class="input-group position-relative">
            <input type="text" name="q" id="searchInput" class="form-control" placeholder="Tìm theo tên hoặc username" value="<?php echo htmlspecialchars($query ?? '', ENT_QUOTES); ?>" autocomplete="off">
            <button type="submit" class="btn btn-primary">Tìm</button>
            <!-- Dropdown gợi ý -->
            <div class="dropdown-menu w-100" id="searchSuggestions" style="max-height: 300px; overflow-y: auto;"></div>
        </div>
    </form>

    <?php if (!empty($results)): ?>
        <div class="list-group">
            <?php foreach ($results as $user): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo htmlspecialchars($user['avatar'] ?? 'public/uploads/default_avatar.jpg', ENT_QUOTES); ?>" alt="Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                        <div>
                            <h5 class="mb-1"><?php echo htmlspecialchars($user['fullname']); ?></h5>
                            <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['role']); ?>)</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary send-friend-request" data-receiver-id="<?php echo $user['id']; ?>">Gửi kết bạn</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (!empty($query)): ?>
        <div class="alert alert-info">Không tìm thấy kết quả cho "<?php echo htmlspecialchars($query); ?>".</div>
    <?php endif; ?>
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
                    div.innerHTML = `
                        <img src="${user.avatar || 'public/uploads/default_avatar.jpg'}" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                        <span>${user.fullname} (@${user.username})</span>
                        <button class="btn btn-sm btn-outline-primary ms-auto send-friend-request" data-receiver-id="${user.id}">Gửi kết bạn</button>
                    `;
                    suggestions.appendChild(div);
                });
            }
            suggestions.classList.add('show');
        })
        .catch(error => {
            console.error('Error:', error);
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
                    alert('Yêu cầu kết bạn đã được gửi!');
                    button.disabled = true;
                    button.textContent = 'Đã gửi';
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi yêu cầu kết bạn.');
            });
        }
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>