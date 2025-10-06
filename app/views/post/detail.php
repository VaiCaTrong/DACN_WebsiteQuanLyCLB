<div class="container mt-4" style="max-width: 900px;">
    <h1 class="text-center mb-4" style="color: #FF6B9E; font-weight: 700; text-shadow: 1px 1px 3px rgba(0,0,0,0.1);">
        <i class="fas fa-file-alt me-2"></i>Chi tiết bài viết
    </h1>

    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header" style="background-color: #FFF5F9; border-bottom: 2px solid #FF6B9E;">
            <h3 class="mb-0" style="color: #D23369; font-weight: 600;">
                <?php echo htmlspecialchars($post['title'] ?? 'Tiêu đề không có'); ?>
            </h3>
        </div>

        <div class="card-body" style="background-color: #FFF9FB;">
            <!-- Carousel ảnh -->
            <?php if (!empty($images)): ?>
                <div id="carousel_<?php echo $post['id'] ?? ''; ?>"
                    class="carousel slide mb-4"
                    data-bs-ride="carousel"
                    style="border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(255,107,158,0.2);">

                    <div class="carousel-inner">
                        <?php foreach ($images as $index => $image): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($image['image_path'] ?? $image); ?>"
                                    class="d-block w-100"
                                    alt="Ảnh bài viết"
                                    style="max-height: 500px; object-fit: cover; transition: transform 0.5s ease;">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($images) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel_<?php echo $post['id'] ?? ''; ?>" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: drop-shadow(0 0 2px rgba(0,0,0,0.5));"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carousel_<?php echo $post['id'] ?? ''; ?>" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true" style="filter: drop-shadow(0 0 2px rgba(0,0,0,0.5));"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Nội dung -->
            <div class="content-box p-3 mb-4" style="background-color: white; border-radius: 10px; border-left: 4px solid #FF6B9E;">
                <p class="card-text" style="color: #555; line-height: 1.7;">
                    <?php echo nl2br(htmlspecialchars($post['content'] ?? 'Nội dung không có')); ?>
                </p>
            </div>

            <!-- Thông tin tác giả -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge rounded-pill me-2" style="background-color: #FFE4E8; color: #D23369; padding: 8px 15px;">
                        <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($post['author_id'] ?? 'Không xác định'); ?>
                    </span>
                    <span class="badge rounded-pill" style="background-color: #FFE4E8; color: #D23369; padding: 8px 15px;">
                        <i class="fas fa-calendar-alt me-1"></i> <?php echo htmlspecialchars($post['created_at'] ?? 'Không xác định'); ?>
                    </span>
                </div>

                <!-- Nút bấm -->
                <div>
                    <a href="/webdacn_quanlyclb" class="btn btn-sm" style="background-color: #FFB6C1; color: white; border-radius: 50px;">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>

                    <?php if (SessionHelper::isAdmin()): ?>
                        <a href="/webdacn_quanlyclb/default/edit/<?php echo $post['id'] ?? ''; ?>" class="btn btn-sm ms-2" style="background-color: #FF6B9E; color: white; border-radius: 50px;">
                            <i class="fas fa-edit me-1"></i> Sửa
                        </a>
                        <a href="/webdacn_quanlyclb/default/delete/<?php echo $post['id'] ?? ''; ?>" class="btn btn-sm btn-danger ms-2" style="border-radius: 50px;" data-bs-toggle="modal" data-bs-target="#deletePostModal-<?php echo $post['id'] ?? ''; ?>">
                            <i class="fas fa-trash me-1"></i> Xóa
                        </a>
                    <?php endif; ?>
                    <a href="#comments" class="btn btn-sm ms-2" style="background-color: #FFB6C1; color: white; border-radius: 50px;">
                        <i class="fas fa-comment me-1"></i> Comment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bình luận -->
    <div class="comments-section mt-5" id="comments">
        <h4 class="mb-4" style="color: #D23369;"><i class="fas fa-comments me-2"></i>Bình luận (<?php echo is_array($comments) ? count($comments) : 0; ?>)</h4>

        <?php if (empty($comments) || !is_array($comments)): ?>
            <p class="text-muted">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
        <?php else: ?>
            <?php
            // Phân loại và sắp xếp bình luận
            $adminComments = [];
            $otherComments = [];

            foreach ($comments as $comment) {
                if (isset($comment['role']) && $comment['role'] === 'admin') {
                    $adminComments[] = $comment;
                } else {
                    $otherComments[] = $comment;
                }
            }

            // Sắp xếp các bình luận khác theo thời gian (mới nhất lên trên)
            usort($otherComments, function ($a, $b) {
                $timeA = strtotime($a['created_at'] ?? '0000-00-00 00:00:00');
                $timeB = strtotime($b['created_at'] ?? '0000-00-00 00:00:00');
                return $timeA - $timeB; // Giảm dần (mới nhất lên đầu)
            });

            // Gộp mảng: admin comments trước, sau đó là other comments
            $sortedComments = array_merge($adminComments, $otherComments);
            ?>

            <?php foreach ($sortedComments as $comment): ?>
                <div class="comment mb-3 p-3" style="background-color: <?php echo isset($comment['role']) && $comment['role'] === 'admin' ? '#f87e80ff' : (isset($comment['role']) && $comment['role'] === 'staff' ? '#98cdf8ff' : '#cec9c9ff'); ?>; border-radius: 10px; border-left: 4px solid #FF6B9E; position: relative;">
                    <div class="d-flex align-items-center mb-2">
                        <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars(isset($comment['avatar']) ? $comment['avatar'] : 'uploads/default_avatar.jpg'); ?>"
                            class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;"
                            onerror="this.src='/webdacn_quanlyclb/uploads/default_avatar.jpg';">
                        <div>
                            <strong style="color: #D23369;"><?php echo htmlspecialchars(isset($comment['fullname']) ? $comment['fullname'] : 'Người dùng'); ?></strong>
                            <?php if (isset($comment['role']) && $comment['role'] === 'admin'): ?>
                                <span class="badge bg-danger ms-2" style="font-size: 0.7em;">ADMIN</span>
                            <?php elseif (isset($comment['role']) && $comment['role'] === 'staff'): ?>
                                <span class="badge bg-primary ms-2" style="font-size: 0.7em;">STAFF</span>
                            <?php endif; ?>
                            <small class="text-muted ms-2"><?php echo isset($comment['created_at']) ? date('d/m/Y H:i', strtotime($comment['created_at'])) : 'Không xác định'; ?></small>
                        </div>
                    </div>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars(isset($comment['content']) ? $comment['content'] : '')); ?></p>
                    <?php if (SessionHelper::isLoggedIn()): ?>
                        <?php
                        $currentUserId = $_SESSION['user_id'];
                        $isAdmin = SessionHelper::isAdmin();
                        $canDelete = $isAdmin || ($currentUserId == $comment['user_id']);
                        ?>
                        <?php if ($canDelete): ?>
                            <!-- Nút mở modal xóa comment -->
                            <button type="button" class="btn btn-sm btn-danger" style="position: absolute; top: 10px; right: 10px; border-radius: 50px; padding: 2px 10px;"
                                data-bs-toggle="modal" data-bs-target="#deleteCommentModal-<?php echo $comment['id']; ?>">
                                <i class="fas fa-trash"></i>
                            </button>

                            <!-- Modal xác nhận xóa comment -->
                            <div class="modal fade" id="deleteCommentModal-<?php echo $comment['id']; ?>" tabindex="-1" aria-labelledby="deleteCommentModalLabel-<?php echo $comment['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteCommentModalLabel-<?php echo $comment['id']; ?>" style="color: #D23369;">Xác nhận xóa</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body" style="color: #555;">
                                            Bạn có chắc chắn muốn xóa bình luận này không?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 20px;">Hủy</button>
                                            <a href="/webdacn_quanlyclb/default/deleteComment/<?php echo $comment['id']; ?>?post_id=<?php echo $post['id']; ?>" class="btn btn-danger" style="border-radius: 20px;">
                                                Xóa
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (SessionHelper::isLoggedIn()): ?>
            <form action="/webdacn_quanlyclb/default/comment/<?php echo $post['id']; ?>" method="POST" class="mt-4">
                <div class="input-group">
                    <textarea name="content" class="form-control" rows="3" placeholder="Viết bình luận của bạn..." required style="border-radius: 20px 0 0 20px;"></textarea>
                    <button type="submit" class="btn" style="background-color: #FF6B9E; color: white; border-radius: 0 20px 20px 0;">
                        <i class="fas fa-paper-plane"></i> Gửi
                    </button>
                </div>
            </form>
        <?php else: ?>
            <p class="text-muted mt-3">Vui lòng <a href="/webdacn_quanlyclb/account/login">đăng nhập</a> để bình luận.</p>
        <?php endif; ?>
    </div>

    <!-- Modal xóa bài viết (nếu là admin) -->
    <?php if (SessionHelper::isAdmin()): ?>
        <div class="modal fade" id="deletePostModal-<?php echo $post['id'] ?? ''; ?>" tabindex="-1" aria-labelledby="deletePostModalLabel-<?php echo $post['id'] ?? ''; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePostModalLabel-<?php echo $post['id'] ?? ''; ?>" style="color: #D23369;">Xác nhận xóa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="color: #555;">
                        Bạn có chắc chắn muốn xóa bài viết này không?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 20px;">Hủy</button>
                        <a href="/webdacn_quanlyclb/default/delete/<?php echo $post['id'] ?? ''; ?>" class="btn btn-danger" style="border-radius: 20px;">
                            Xóa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    body {
        background-color: #FFFDFE;
    }

    .carousel-item img:hover {
        transform: scale(1.02);
    }

    .btn {
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(210, 51, 105, 0.3);
    }

    .content-box {
        transition: all 0.3s ease;
    }

    .content-box:hover {
        box-shadow: 0 5px 15px rgba(255, 107, 158, 0.1);
    }
</style>

<!-- Thêm Bootstrap JS và Font Awesome -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">