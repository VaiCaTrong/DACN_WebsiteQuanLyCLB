<?php
require_once 'app/helpers/SessionHelper.php';
require_once 'app/models/PostModel.php';

SessionHelper::start();
$postModel = new PostModel();

// Lấy danh sách bài viết
$posts = $postModel->getAllPosts(); // nhớ viết hàm getAllPosts trong PostModel
$isAdmin = SessionHelper::isAdmin();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh sách bài viết</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;

        }

        .post {
            margin-bottom: 30px;
        }

        .post img {
            max-width: 300px;
            height: auto;
            display: block;
            margin-top: 10px;
        }

        footer {
            margin-top: 50px;
            padding: 20px;
            background: #f2f2f2;
            text-align: center;
        }

        iframe {
            width: 100%;
            height: 300px;
            border: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <?php if (empty($posts)): ?>
                <div class="col-12 text-center py-5" style="color: #FF6B9E;">
                    <i class="fas fa-newspaper fa-3x mb-3"></i>
                    <p class="fs-4">Chưa có bài viết nào.</p>
                    <?php if ($isAdmin): ?>
                        <a href="/webdacn_quanlyclb/default/create" class="btn btn-primary" style="background-color: #FF6B9E; border: none; border-radius: 50px; padding: 10px 25px; box-shadow: 0 4px 8px rgba(255, 107, 158, 0.3);">
                            <i class="fas fa-plus me-2"></i>Thêm bài viết
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php if ($isAdmin): ?>
                    <div class="text-end mb-4">
                        <a href="/webdacn_quanlyclb/default/create" class="btn btn-primary" style="background-color: #FF6B9E; border: none; border-radius: 50px; padding: 8px 20px; box-shadow: 0 4px 8px rgba(255, 107, 158, 0.3);">
                            <i class="fas fa-plus me-2"></i>Thêm bài viết
                        </a>
                    </div>
                <?php endif; ?>
                <?php foreach ($posts as $post): ?>
                    <?php
                    $user = $postModel->getUserById($post['author_id']); // Giả định PostModel có hàm này
                    $authorAvatar = $user['avatar'] ?? '/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg';
                    $authorName = $user['username'] ?? 'Người dùng';
                    $firstImage = $postModel->getPostImages($post['id'])[0] ?? null;
                    ?>
                    <div class="col-12 mb-5">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                            <!-- Header người đăng -->
                            <div class="d-flex align-items-center p-3" style="background-color: #fce0ebff; border-bottom: 1px solid #FFE4E8;">
                                <img src="<?php echo htmlspecialchars($authorAvatar); ?>" class="rounded-circle me-3"
                                    style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #FFB6C1;">
                                <div>
                                    <!-- Tên người đăng -->
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
    <?php include 'app/views/shares/footer.php'; ?>
</body>

</html>