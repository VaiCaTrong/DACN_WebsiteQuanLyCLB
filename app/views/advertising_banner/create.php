<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php'; ?>

<style>
    .form-label {
        font-weight: bold;
        color: #E91E63;
    }

    .form-control {
        border-radius: 8px;
    }

    .form-control:focus {
        border-color: #C2185B;
        box-shadow: 0 0 5px rgba(233, 30, 99, 0.3);
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%);
        color: white;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .btn-primary {
        background-color: #E91E63;
        border-color: #E91E63;
    }

    .btn-primary:hover {
        background-color: #C2185B;
        border-color: #C2185B;
    }
</style>

<div class="container mt-4" style="max-width: 700px;">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0 fs-5"><i class="fas fa-plus-circle me-2"></i>Thêm Ảnh Quảng cáo Mới</h2>
        </div>
        <div class="card-body p-4">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php unset($_SESSION['error']);
            endif; ?>

            <form action="/webdacn_quanlyclb/advertisingbanner/store" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="banner_image" class="form-label">Chọn Ảnh <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="banner_image" name="banner_image" accept="image/jpeg, image/png, image/gif, image/webp" required>
                    <div class="form-text">Ảnh sẽ được tự động cắt/thay đổi kích thước thành 1200x400 pixels. Kích thước tối đa 10MB.</div>
                </div>
                <div class="mb-3">
                    <label for="alt_text" class="form-label">Văn bản thay thế (Alt Text)</label>
                    <input type="text" class="form-control" id="alt_text" name="alt_text" placeholder="Mô tả ngắn gọn về ảnh (quan trọng cho SEO)">
                </div>
                <div class="mb-3">
                    <label for="link_url" class="form-label">Link URL (Tùy chọn)</label>
                    <input type="url" class="form-control" id="link_url" name="link_url" placeholder="https://...">
                    <div class="form-text">Nếu nhập, người dùng click vào ảnh sẽ được chuyển đến link này.</div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="/webdacn_quanlyclb/advertisingbanner" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Tải lên & Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/footer.php'; ?>