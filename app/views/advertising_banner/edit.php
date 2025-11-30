<!-- app/views/advertising_banner/edit.php -->
<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php'; ?>

<style>
    /* === ĐỒNG BỘ VỚI INDEX.PHP & CREATE.PHP === */
    .form-label {
        font-weight: bold;
        color: #E91E63;
    }

    .form-control {
        border-radius: 8px;
        border: 1.5px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #E91E63;
        box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .card-header {
        background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%);
        color: white;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        padding: 1rem 1.5rem;
    }

    .card-header h2 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
    }

    .btn-primary {
        background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%);
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(233, 30, 99, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(233, 30, 99, 0.4);
    }

    .btn-secondary {
        background-color: #f8f9fa;
        color: #6c757d;
        border: 1.5px solid #dee2e6;
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background-color: #e9ecef;
        transform: translateY(-1px);
    }

    .img-thumbnail {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        transition: all 0.2s ease;
    }

    .img-thumbnail:hover {
        border-color: #E91E63;
        transform: scale(1.05);
    }

    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>

<div class="container mt-4" style="max-width: 700px;">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0 fs-5"><i class="fas fa-edit me-2"></i>Chỉnh sửa Ảnh Quảng cáo</h2>
        </div>
        <div class="card-body p-4">

            <!-- Alert nếu có lỗi -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="/webdacn_quanlyclb/advertisingbanner/update/<?= $banner['id'] ?>" method="POST" enctype="multipart/form-data">
                
                <!-- Ảnh hiện tại -->
                <div class="mb-4">
                    <label class="form-label">Ảnh hiện tại</label>
                    <div class="mt-2">
                        <img src="/webdacn_quanlyclb/<?= htmlspecialchars($banner['image_path']) ?>" 
                             class="img-thumbnail" 
                             alt="<?= htmlspecialchars($banner['alt_text']) ?>"
                             style="max-height: 120px; object-fit: cover;">
                    </div>
                </div>

                <!-- Upload ảnh mới -->
                <div class="mb-3">
                    <label for="banner_image" class="form-label">Thay ảnh mới <small class="text-muted">(không bắt buộc)</small></label>
                    <input type="file" 
                           class="form-control" 
                           id="banner_image" 
                           name="banner_image" 
                           accept="image/jpeg,image/png,image/gif,image/webp">
                    <div class="form-text">
                        Định dạng: JPG, PNG, GIF, WEBP. Kích thước tối đa: 10MB.
                    </div>
                </div>

                <!-- Alt Text -->
                <div class="mb-3">
                    <label for="alt_text" class="form-label">Văn bản thay thế (Alt Text)</label>
                    <input type="text" 
                           class="form-control" 
                           id="alt_text" 
                           name="alt_text" 
                           value="<?= htmlspecialchars($banner['alt_text']) ?>"
                           placeholder="Mô tả ngắn gọn về ảnh (tối ưu SEO)">
                </div>

                <!-- Link URL -->
                <div class="mb-4">
                    <label for="link_url" class="form-label">Link URL <small class="text-muted">(tùy chọn)</small></label>
                    <input type="url" 
                           class="form-control" 
                           id="link_url" 
                           name="link_url" 
                           value="<?= htmlspecialchars($banner['link_url']) ?>"
                           placeholder="https://example.com">
                    <div class="form-text">
                        Người dùng sẽ được chuyển hướng khi nhấn vào banner.
                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="/webdacn_quanlyclb/advertisingbanner" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Hủy
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/footer.php'; ?>