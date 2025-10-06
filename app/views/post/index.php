<?php
require_once __DIR__ . '/app/helpers/SessionHelper.php';
require_once __DIR__ . '/app/models/PostModel.php';

SessionHelper::start();
$postModel = new PostModel();
$posts = $postModel->getAllPosts();
$isAdmin = SessionHelper::isAdmin();
?>

<div class="wrapper container-fluid px-0" style="min-height: 100vh; background: #fbeff3;">
    <div class="content-wrapper mx-auto my-4 p-4" style="max-width: 1400px; background: white; border-radius: 16px; border: 1px solid #f0c4d2; box-shadow: 0 4px 12px rgba(255, 107, 158, 0.2);">
        
        <h1 class="text-center mb-5" style="color: rgb(249, 0, 108); font-weight: 700; letter-spacing: 1px;">
            <i class="fas fa-heart me-2"></i>Chào mừng đến với trang chủ<i class="fas fa-heart ms-2"></i>
        </h1>

        <!-- Danh sách bài viết -->
        <div class="row">
            <?php if (empty($posts)): ?>
                <div class="col-12 text-center py-5" style="color: #FF6B9E;">
                    <i class="fas fa-newspaper fa-3x mb-3"></i>
                    <p class="fs-4">Chưa có bài viết nào.</p>
                    <?php if (SessionHelper::isAdmin()): ?>
                        <a href="/webdacn_quanlyclb/default/create" class="btn btn-primary" style="background-color: #FF6B9E; border: none; border-radius: 50px; padding: 10px 25px; box-shadow: 0 4px 8px rgba(255, 107, 158, 0.3);">
                            <i class="fas fa-plus me-2"></i>Thêm bài viết
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Nút Thêm bài viết (phía trên bên phải) -->
                <?php if (SessionHelper::isAdmin()): ?>
                    <div class="text-end mb-4">
                        <a href="/webdacn_quanlyclb/default/create" class="btn btn-primary" style="background-color: #FF6B9E; border: none; border-radius: 50px; padding: 8px 20px; box-shadow: 0 4px 8px rgba(255, 107, 158, 0.3);">
                            <i class="fas fa-plus me-2"></i>Thêm bài viết
                        </a>
                    </div>
                <?php endif; ?>
                <?php foreach ($posts as $post): ?>
                    <?php
                    $firstImage = $postModel->getPostImages($post['id'])[0] ?? null;
                    $authorAvatar = $post['author_avatar'] ?? '/webdacn_quanlyclb/uploads/default_avatar.jpg';
                    $authorName = $post['author_name'] ?? 'Người dùng';
                    ?>
                    <div class="col-12 mb-5">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                            <!-- Header người đăng -->
                            <div class="d-flex align-items-center p-3" style="background-color: #FFF5F9; border-bottom: 1px solid #FFE4E8;">
                                <img src="<?php echo htmlspecialchars($authorAvatar); ?>" class="rounded-circle me-3"
                                     style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #FFB6C1;">
                                <div>
                                    <div style="font-weight: 600; color: #D23369;"><?php echo htmlspecialchars($authorName); ?></div>
                                    <div style="font-size: 0.8rem; color: #FF6B9E;">
                                        <i class="fas fa-clock me-1"></i><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($post['created_at']))); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-0">
                                <?php if ($firstImage): ?>
                                    <div class="col-md-5">
                                        <a href="/webdacn_quanlyclb/default/detail/<?php echo $post['id']; ?>" class="d-block" style="height: 250px; overflow: hidden;">
                                            <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars(is_array($firstImage) ? $firstImage['image_path'] : $firstImage); ?>"
                                                 class="w-100 h-100"
                                                 style="object-fit: cover; object-position: center; transition: transform 0.5s ease;">
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="<?php echo $firstImage ? 'col-md-7' : 'col-12'; ?>">
                                    <div class="card-body p-4" style="background-color: #FFF9FB;">
                                        <h3 class="card-title mb-3" style="color: #D23369; font-weight: 600;"><?php echo htmlspecialchars($post['title']); ?></h3>
                                        <p class="card-text text-muted mb-4" style="line-height: 1.6;">
                                            <?php echo htmlspecialchars(substr($post['content'], 0, 150)) . (strlen($post['content']) > 150 ? '...' : ''); ?>
                                        </p>
                                        <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
                                            <a href="/webdacn_quanlyclb/default/detail/<?php echo $post['id']; ?>" class="btn btn-sm" style="background-color: #FF6B9E; color: white; border-radius: 50px; padding: 6px 15px;">
                                                <i class="fas fa-eye me-1"></i> Xem chi tiết
                                            </a>
                                            <?php if ($isAdmin): ?>
                                                <a href="/webdacn_quanlyclb/default/edit/<?php echo $post['id']; ?>" class="btn btn-sm" style="background-color: #FFB6C1; color: white; border-radius: 50px; padding: 6px 15px;">
                                                    <i class="fas fa-edit me-1"></i> Sửa
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" style="border-radius: 50px; padding: 6px 15px;" data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo $post['id']; ?>">
                                                    <i class="fas fa-trash me-1"></i> Xóa
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<style>
    body {
        background-color: rgb(245, 228, 235);
        padding-left: 0px;
        display: flex;
        justify-content: center;
    }

    .card {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(255, 107, 158, 0.15) !important;
    }

    .card:hover img:not(.rounded-circle) {
        transform: scale(1.05);
        opacity: 0.95;
    }

    .btn {
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(210, 51, 105, 0.3);
    }

    .btn-primary:hover {
        background-color: #E91E63 !important;
    }

    .card-text {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Modal Animation */
    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }

    .modal.show .modal-dialog {
        animation: slideIn 0.3s ease-out;
    }

    .modal.hide .modal-dialog {
        animation: fadeOut 0.3s ease-out;
    }

    @media (max-width: 768px) {
        .container {
            max-width: 100% !important;
            margin-left: 0 !important;
            padding: 0 15px;
        }

        body {
            padding-left: 0;
        }

        .card-body .btn {
            margin-bottom: 5px;
            width: 100%;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>