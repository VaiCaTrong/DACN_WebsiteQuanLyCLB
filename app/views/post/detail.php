<div class="container mt-4" style="max-width: 900px;">
    <h1 class="text-center mb-4" style="color: #FF6B9E; font-weight: 700; text-shadow: 1px 1px 3px rgba(0,0,0,0.1);">
        <i class="fas fa-file-alt me-2"></i>Chi tiết bài viết
    </h1>

    <div class="card mb-5 border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header" style="background-color: #FFF5F9; border-bottom: 2px solid #FF6B9E;">
            <h3 class="mb-0" style="color: #D23369; font-weight: 600;">
                <?php echo htmlspecialchars($post['title'] ?? 'Tiêu đề không có'); ?>
            </h3>
        </div>

        <div class="card-body" style="background-color: #FFF9FB;">
            <?php if (!empty($images)): ?>
                <div id="carousel_<?php echo $post['id'] ?? ''; ?>"
                    class="carousel slide mb-4 post-detail-carousel"
                    data-bs-ride="false" 
                    data-bs-interval="false"
                    style="border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(255,107,158,0.2);">

                    <div class="carousel-inner">
                        <?php foreach ($images as $index => $image): ?>
                            <?php
                            $filePath = $image['image_path'] ?? $image;
                            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                            $videoExtensions = ['mp4', 'webm', 'mov', 'avi', 'mkv'];
                            $isVideo = in_array($ext, $videoExtensions);
                            $dbIsVideo = isset($image['type']) && $image['type'] === 'video';
                            
                            // Kết hợp kiểm tra đuôi file và database để chắc chắn
                            $isRealVideo = $isVideo || $dbIsVideo;
                            ?>

                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <?php if ($isRealVideo): ?>
                                    <div class="video-wrapper paused" onclick="togglePlayPause(this)">
                                        <video class="d-block w-100 post-video" playsinline style="max-height: 500px; object-fit: contain; background: black;">
                                            <source src="/webdacn_quanlyclb/<?php echo htmlspecialchars($filePath); ?>" type="video/mp4">
                                            Trình duyệt không hỗ trợ video.
                                        </video>
                                        <div class="btn-center-play"><i class="fas fa-play"></i></div>
                                        <div class="video-controls-bar" onclick="event.stopPropagation()">
                                            <button type="button" class="btn-control-custom btn-sound" onclick="toggleVideoSound(this)" title="Bật/Tắt tiếng"><i class="fas fa-volume-up"></i></button>
                                            <button type="button" class="btn-control-custom btn-fullscreen" onclick="toggleFullscreen(this)" title="Toàn màn hình"><i class="fas fa-expand"></i></button>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($filePath); ?>"
                                        class="d-block w-100" alt="Ảnh bài viết"
                                        style="max-height: 500px; object-fit: cover;">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($images) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel_<?php echo $post['id'] ?? ''; ?>" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carousel_<?php echo $post['id'] ?? ''; ?>" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="content-box p-3 mb-4" style="background-color: white; border-radius: 10px; border-left: 4px solid #FF6B9E;">
                <div class="card-text ck-content" style="color: #333; line-height: 1.7; font-size: 1.05rem;">
                    <?php 
                        // Hiển thị HTML từ CKEditor
                        echo !empty($post['content']) ? $post['content'] : 'Nội dung không có'; 
                    ?>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge rounded-pill me-2" style="background-color: #FFE4E8; color: #D23369; padding: 8px 15px;">
                        <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($post['author_name'] ?? 'Không xác định'); ?>
                    </span>
                    <span class="badge rounded-pill" style="background-color: #FFE4E8; color: #D23369; padding: 8px 15px;">
                        <i class="fas fa-calendar-alt me-1"></i> <?php echo htmlspecialchars($post['created_at'] ?? 'Không xác định'); ?>
                    </span>
                </div>

                <div>
                    <a href="/webdacn_quanlyclb" class="btn btn-sm" style="background-color: #FFB6C1; color: white; border-radius: 50px;">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <a href="#comments" class="btn btn-sm ms-2" style="background-color: #FFB6C1; color: white; border-radius: 50px;">
                        <i class="fas fa-comment me-1"></i> Comment
                    </a>
                    
                    <?php if (SessionHelper::isAdmin() || (SessionHelper::isStaff() && $currentUserId == $post['author_id'])): ?>
                        <a href="/webdacn_quanlyclb/default/edit/<?php echo $post['id'] ?? ''; ?>" class="btn btn-sm ms-2" style="background-color: #FF6B9E; color: white; border-radius: 50px;">
                            <i class="fas fa-edit me-1"></i> Sửa
                        </a>
                        <a href="/webdacn_quanlyclb/default/delete/<?php echo $post['id'] ?? ''; ?>" class="btn btn-sm btn-danger ms-2" style="border-radius: 50px;" data-bs-toggle="modal" data-bs-target="#deletePostModal-<?php echo $post['id'] ?? ''; ?>">
                            <i class="fas fa-trash me-1"></i> Xóa
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="reaction-container" id="reaction-section-<?php echo $post['id']; ?>">
                <div class="reaction-summary-display mb-2" id="reaction-summary-<?php echo $post['id']; ?>"></div>
                <?php if (SessionHelper::isLoggedIn()): ?>
                    <div class="reaction-wrapper">
                        <button class="btn btn-reaction-trigger" id="reaction-trigger-<?php echo $post['id']; ?>" data-post-id="<?php echo $post['id']; ?>">
                            <i class="fas fa-thumbs-up"></i> Thích
                        </button>
                        <div class="reaction-options">
                            <span class="reaction-icon" data-reaction="like" data-post-id="<?php echo $post['id']; ?>">👍</span>
                            <span class="reaction-icon" data-reaction="love" data-post-id="<?php echo $post['id']; ?>">❤️</span>
                            <span class="reaction-icon" data-reaction="haha" data-post-id="<?php echo $post['id']; ?>">😂</span>
                            <span class="reaction-icon" data-reaction="wow" data-post-id="<?php echo $post['id']; ?>">😮</span>
                            <span class="reaction-icon" data-reaction="sad" data-post-id="<?php echo $post['id']; ?>">😢</span>
                            <span class="reaction-icon" data-reaction="angry" data-post-id="<?php echo $post['id']; ?>">😡</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div> <?php if (!empty($subPosts)): ?>
        <div class="sub-posts-wrapper mb-5">
            <div class="text-center mb-4 position-relative">
                <hr style="border-top: 2px dashed #FF6B9E; width: 100%; position: absolute; top: 50%; z-index: 1;">
                <span class="badge bg-white text-uppercase shadow-sm" style="color: #FF6B9E; border: 2px solid #FF6B9E; padding: 10px 25px; position: relative; z-index: 2; font-size: 1rem;">
                    <i class="fas fa-book-open me-2"></i>Nội dung tiếp theo
                </span>
            </div>

            <?php foreach ($subPosts as $index => $sub): ?>
                <div class="card mb-4 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; background-color: #fff; border-left: 5px solid #FF6B9E !important;">
                    <div class="card-body p-4">
                        <h4 class="mb-3" style="color: #D23369; font-weight: 700;">
                            <?php echo htmlspecialchars($sub['title']); ?>
                        </h4>

                        <?php if (!empty($sub['images'])): ?>
                            <?php $carouselId = 'carousel_sub_' . $sub['id']; ?>
                            <div id="<?php echo $carouselId; ?>" 
                                 class="carousel slide mb-3 post-detail-carousel" 
                                 data-bs-ride="false"
                                 data-bs-interval="false"
                                 style="border-radius: 10px; overflow: hidden; border: 1px solid #eee;">
                                <div class="carousel-inner">
                                    <?php foreach ($sub['images'] as $i => $img): ?>
                                        <?php 
                                            $path = $img['image_path'];
                                            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                            $isVideo = in_array($ext, ['mp4', 'webm', 'mov', 'avi', 'mkv']);
                                            $dbIsVideo = isset($img['type']) && $img['type'] === 'video';
                                            $sIsVid = $isVideo || $dbIsVideo;
                                        ?>
                                        <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>">
                                            <?php if ($sIsVid): ?>
                                                <div class="video-wrapper paused" onclick="togglePlayPause(this)">
                                                    <video class="d-block w-100 post-video" playsinline style="max-height: 400px; object-fit: contain; background: black;">
                                                        <source src="/webdacn_quanlyclb/<?php echo htmlspecialchars($path); ?>">
                                                    </video>
                                                    <div class="btn-center-play"><i class="fas fa-play"></i></div>
                                                    <div class="video-controls-bar" onclick="event.stopPropagation()">
                                                        <button type="button" class="btn-control-custom btn-sound" onclick="toggleVideoSound(this)"><i class="fas fa-volume-up"></i></button>
                                                        <button type="button" class="btn-control-custom btn-fullscreen" onclick="toggleFullscreen(this)"><i class="fas fa-expand"></i></button>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($path); ?>" class="d-block w-100" style="max-height: 400px; object-fit: contain; background: #f8f9fa;">
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($sub['images']) > 1): ?>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.3); border-radius: 50%;"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.3); border-radius: 50%;"></span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="content-text text-secondary ck-content" style="font-size: 1rem; line-height: 1.6;">
                            <?php echo !empty($sub['content']) ? $sub['content'] : 'Nội dung không có'; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="comments-section mt-5 mb-5" id="comments">
        <h4 class="mb-4" style="color: #D23369;"><i class="fas fa-comments me-2"></i>Bình luận (<?php echo is_array($comments) ? count($comments) : 0; ?>)</h4>
        
        <?php if (empty($comments) || !is_array($comments)): ?>
            <p class="text-muted">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment mb-3 p-3" style="background-color: #f8f9fa; border-radius: 10px; border-left: 4px solid #FF6B9E;">
                    <div class="d-flex align-items-center mb-2">
                        <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($comment['avatar'] ?? 'uploads/default_avatar.jpg'); ?>"
                            class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                        <div>
                            <strong style="color: #D23369;"><?php echo htmlspecialchars($comment['fullname'] ?? 'Người dùng'); ?></strong>
                            <small class="text-muted ms-2"><?php echo isset($comment['created_at']) ? date('d/m/Y H:i', strtotime($comment['created_at'])) : ''; ?></small>
                        </div>
                        <?php if (SessionHelper::isAdmin() || (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id'])): ?>
                            <a href="/webdacn_quanlyclb/default/deleteComment/<?php echo $comment['id']; ?>?post_id=<?php echo $post['id']; ?>" 
                               class="ms-auto text-danger" onclick="return confirm('Xóa bình luận này?');">
                               <i class="fas fa-trash"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($comment['content'] ?? '')); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (SessionHelper::isLoggedIn()): ?>
            <form action="/webdacn_quanlyclb/default/comment/<?php echo $post['id']; ?>" method="POST" class="mt-4">
                <div class="input-group">
                    <textarea name="content" class="form-control" rows="3" placeholder="Viết bình luận của bạn..." required></textarea>
                    <button type="submit" class="btn" style="background-color: #FF6B9E; color: white;">
                        <i class="fas fa-paper-plane"></i> Gửi
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php if (SessionHelper::isAdmin()): ?>
    <div class="modal fade" id="deletePostModal-<?php echo $post['id'] ?? ''; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Bạn có chắc chắn muốn xóa bài viết này không?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <a href="/webdacn_quanlyclb/default/delete/<?php echo $post['id'] ?? ''; ?>" class="btn btn-danger">Xóa</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    .ck-content ul, .ck-content ol { padding-left: 20px; margin-bottom: 1rem; }
    .ck-content blockquote { border-left: 4px solid #ccc; padding-left: 15px; color: #555; font-style: italic; }
    .ck-content table { border-collapse: collapse; width: 100%; margin-bottom: 1rem; }
    .ck-content table td, .ck-content table th { border: 1px solid #ddd; padding: 8px; }
    
    /* CSS Carousel & Video */
    .carousel-control-prev, .carousel-control-next { width: 10%; opacity: 1; z-index: 100; }
    .carousel-control-prev-icon, .carousel-control-next-icon {
        background-color: rgba(0,0,0,0.6); border-radius: 50%; padding: 20px; background-size: 50%;
    }
    .carousel-control-prev:hover, .carousel-control-next:hover { background-color: rgba(0,0,0,0.1); }

    .video-wrapper { position: relative; background: #000; cursor: pointer; overflow: hidden; }
    .btn-center-play {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 80px; height: 80px; background-color: rgba(233, 30, 99, 0.9);
        border-radius: 50%; display: flex; justify-content: center; align-items: center;
        color: white; font-size: 35px; box-shadow: 0 0 20px rgba(233, 30, 99, 0.5);
        opacity: 0; transition: all 0.3s; pointer-events: none; z-index: 20;
    }
    .video-wrapper.paused .btn-center-play { opacity: 1; transform: translate(-50%, -50%) scale(1); }
    .video-controls-bar {
        position: absolute; bottom: 0; left: 0; right: 0; padding: 15px 20px;
        display: flex; justify-content: flex-end; gap: 15px;
        background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
        opacity: 0; transform: translateY(10px); transition: all 0.3s ease; z-index: 30;
    }
    .video-wrapper:hover .video-controls-bar, .video-wrapper.paused .video-controls-bar { opacity: 1; transform: translateY(0); }
    .btn-control-custom {
        width: 45px; height: 45px; border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.25); backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.4); color: white;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; cursor: pointer; font-size: 18px;
    }
    .btn-control-custom:hover { background-color: #E91E63; border-color: #E91E63; transform: scale(1.1); }
    
    /* Reaction */
    .reaction-container { padding-top: 15px; border-top: 1px solid #eee; }
    .reaction-wrapper { position: relative; display: inline-block; padding-top: 10px; }
    .reaction-options { display: none; position: absolute; bottom: 100%; left: 0; background: white; border-radius: 20px; padding: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 1000; }
    .reaction-wrapper:hover .reaction-options { display: flex; }
    .btn-reaction-trigger { border-radius: 20px; background: #f0f2f5; border: none; font-weight: 600; padding: 8px 15px; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. SETUP CAROUSEL & VIDEO LOGIC ---
        var postCarousels = document.querySelectorAll('.post-detail-carousel');

        postCarousels.forEach(function(myCarouselEl) {
            // CẤU HÌNH TẮT HOÀN TOÀN AUTO SLIDE (interval: false)
            var myCarouselBS = new bootstrap.Carousel(myCarouselEl, {
                pause: 'hover',
                interval: false 
            });

            // Khi chuyển slide, tắt video ở slide cũ
            myCarouselEl.addEventListener('slide.bs.carousel', function(e) {
                var allVideos = myCarouselEl.querySelectorAll('video');
                allVideos.forEach(function(v) {
                    if (!v.paused) {
                        v.pause();
                        var wrapper = v.closest('.video-wrapper');
                        if (wrapper) updateUI(wrapper, false);
                    }
                });
            });
        });

        function updateUI(wrapper, isPlaying) {
            var centerBtn = wrapper.querySelector('.btn-center-play');
            if (!centerBtn) return;
            if (isPlaying) {
                wrapper.classList.remove('paused');
                centerBtn.style.opacity = '0';
            } else {
                wrapper.classList.add('paused');
                centerBtn.style.opacity = '1';
            }
        }

        // --- 2. LOGIC REACTION (Giữ nguyên) ---
        const initialUserReaction = <?php echo json_encode($user_reaction); ?>;
        const initialSummary = <?php echo json_encode($reactions_summary); ?>;
        const postId = <?php echo json_encode($post['id']); ?>;
        const reactionIcons = {'like': '👍', 'love': '❤️', 'haha': '😂', 'wow': '😮', 'sad': '😢', 'angry': '😡'};
        const reactionLabels = {'like': 'Thích', 'love': 'Yêu thích', 'haha': 'Haha', 'wow': 'Wow', 'sad': 'Buồn', 'angry': 'Phẫn nộ'};

        function updateTriggerButton(postId, reaction) {
            const btn = document.getElementById(`reaction-trigger-${postId}`);
            if (btn) {
                btn.className = 'btn btn-reaction-trigger';
                if (reaction && reactionIcons[reaction]) {
                    btn.innerHTML = `${reactionIcons[reaction]} ${reactionLabels[reaction]}`;
                    btn.classList.add(`reacted-${reaction}`);
                } else {
                    btn.innerHTML = '<i class="fas fa-thumbs-up"></i> Thích';
                }
            }
        }

        function updateReactionSummary(postId, summary) {
            const div = document.getElementById(`reaction-summary-${postId}`);
            if (div) {
                div.innerHTML = '';
                let total = 0;
                if (summary) summary.forEach(item => {
                    if (reactionIcons[item.reaction_type]) {
                        const s = document.createElement('span'); s.className='me-1'; s.textContent=reactionIcons[item.reaction_type]; div.appendChild(s);
                    }
                    total += parseInt(item.count);
                });
                if (total > 0) { const c=document.createElement('span'); c.className='ms-1 text-muted'; c.textContent=total; div.appendChild(c); }
            }
        }
        updateTriggerButton(postId, initialUserReaction);
        updateReactionSummary(postId, initialSummary);

        document.querySelectorAll('.reaction-icon').forEach(icon => {
            icon.addEventListener('click', async function(e) {
                e.preventDefault();
                const postId = this.dataset.postId;
                const reactionType = this.dataset.reaction;
                try {
                    const formData = new FormData();
                    formData.append('post_id', postId);
                    formData.append('reaction_type', reactionType);
                    const response = await fetch('/webdacn_quanlyclb/default/react', { method: 'POST', body: formData });
                    const result = await response.json();
                    if (result.success) { updateTriggerButton(postId, result.my_reaction); updateReactionSummary(postId, result.new_summary); }
                } catch (error) { console.error(error); }
            });
        });

        const triggerBtn = document.getElementById(`reaction-trigger-${postId}`);
        if (triggerBtn) {
            triggerBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                try {
                    const formData = new FormData();
                    formData.append('post_id', postId);
                    formData.append('reaction_type', 'like');
                    const response = await fetch('/webdacn_quanlyclb/default/react', { method: 'POST', body: formData });
                    const result = await response.json();
                    if (result.success) { updateTriggerButton(postId, result.my_reaction); updateReactionSummary(postId, result.new_summary); }
                } catch (error) { console.error(error); }
            });
        }
    });

    // --- CÁC HÀM CLICK VIDEO PLAYER (Global Functions) ---
    function togglePlayPause(wrapper) {
        var video = wrapper.querySelector('video');
        var carouselEl = wrapper.closest('.carousel');
        // var carouselBS = bootstrap.Carousel.getInstance(carouselEl); // Không cần instance để pause vì interval đã = false

        if (video.paused) {
            video.play();
            wrapper.classList.remove('paused');
            var centerBtn = wrapper.querySelector('.btn-center-play');
            if(centerBtn) centerBtn.style.opacity = '0';
        } else {
            video.pause();
            wrapper.classList.add('paused');
            var centerBtn = wrapper.querySelector('.btn-center-play');
            if(centerBtn) centerBtn.style.opacity = '1';
        }
    }

    function toggleVideoSound(btn) {
        var wrapper = btn.closest('.video-wrapper');
        var video = wrapper.querySelector('video');
        var icon = btn.querySelector('i');
        if (video.muted) { video.muted = false; icon.className = 'fas fa-volume-up'; } 
        else { video.muted = true; icon.className = 'fas fa-volume-mute'; }
    }

    function toggleFullscreen(btn) {
        var wrapper = btn.closest('.video-wrapper');
        var video = wrapper.querySelector('video');
        if (video.requestFullscreen) video.requestFullscreen();
        else if (video.webkitRequestFullscreen) video.webkitRequestFullscreen();
        else if (video.msRequestFullscreen) video.msRequestFullscreen();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">