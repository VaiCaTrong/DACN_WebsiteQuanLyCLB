<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php'; ?>

<style>
    .banner-preview {
        width: 120px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        border: 2px solid #e9ecef;
        transition: transform 0.2s ease;
    }

    .banner-preview:hover {
        transform: scale(1.05);
        border-color: #E91E63;
    }

    .table th {
        background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%);
        color: white;
        font-weight: 600;
        border: none;
    }

    .table td {
        border-color: #f1f3f4;
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 12px;
    }

    .action-btn {
        padding: 6px 12px;
        font-size: 0.875rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .action-btn:hover {
        transform: translateY(-1px);
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(233, 30, 99, 0.1);
    }

    /* Cải thiện modal xóa */
    .modal-content {
        border-radius: 12px;
    }

    .modal-header .btn-close {
        filter: invert(1);
    }

    .modal-body img {
        transition: transform 0.2s ease;
    }

    .modal-body img:hover {
        transform: scale(1.05);
    }

    /* === SWEETALERT2 THEME HỒNG GRADIENT (Tích hợp modal xóa) === */
    .swal2-popup {
        border-radius: 12px !important;
        /* Bo tròn giống modal */
        box-shadow: 0 4px 12px rgba(233, 30, 99, 0.3) !important;
        /* Shadow hồng nhạt */
        background: #fff !important;
        /* Nền trắng */
    }

    .swal2-title {
        color: #E91E63 !important;
        /* Màu hồng chính */
        font-weight: 600 !important;
        padding: 1rem 1rem 0 !important;
        /* Padding giống header */
    }

    .swal2-html-container {
        color: #495057 !important;
        /* Màu text xám */
        padding: 1rem !important;
    }

    /* Nút Xác nhận (Gradient hồng đậm) */
    .swal2-confirm {
        background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 6px !important;
        padding: 0.5rem 1.5rem !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
    }

    .swal2-confirm:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(233, 30, 99, 0.4) !important;
    }

    /* Nút Hủy (Xám giống modal) */
    .swal2-cancel {
        background-color: #f8f9fa !important;
        color: #6c757d !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 6px !important;
        padding: 0.5rem 1.5rem !important;
    }

    .swal2-cancel:hover {
        background-color: #e9ecef !important;
    }

    /* Icon màu hồng */
    .swal2-icon.swal2-question {
        border-color: #E91E63 !important;
        color: #E91E63 !important;
    }

    .swal2-icon.swal2-success {
        border-color: #28a745 !important;
        color: #28a745 !important;
    }

    /* Overlay (Nền mờ giống modal) */
    .swal2-container.swal2-backdrop-show {
        background: rgba(0, 0, 0, 0.5) !important;
        /* Giống Bootstrap backdrop */
    }

    /* Animation mượt (fade-in giống modal) */
    .swal2-popup.swal2-show {
        animation: swal2-show 0.3s ease-in-out !important;
    }

    .swal2-popup.swal2-hide {
        animation: swal2-hide 0.15s ease-in-out !important;
    }
</style>

<div class="container-fluid px-4 mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-1"><i class="fas fa-images me-2" style="color: #E91E63;"></i>Quản lý Banner Quảng cáo</h2>
            <p class="text-muted mb-0">Quản lý và cập nhật các banner hiển thị trên trang chủ</p>
        </div>
        <a href="/webdacn_quanlyclb/advertisingbanner/create" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Thêm Banner Mới
        </a>
    </div>

    <!-- Alerts -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2 fs-5"></i>
            <div class="flex-grow-1"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php unset($_SESSION['message']);
    endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
            <div class="flex-grow-1"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php unset($_SESSION['error']);
    endif; ?>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fs-4 fw-bold"><?= count($banners) ?></div>
                            <div>Tổng số banner</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-images fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fs-4 fw-bold" id="active-banner-count">
                                <?= count(array_filter($banners, fn($b) => $b['is_active'] == 1)) ?>
                            </div>
                            <div>Đang hoạt động</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-play-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 text-dark">
                <i class="fas fa-list me-2 text-primary"></i>Danh sách Banner
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($banners)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-images fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có banner nào</h5>
                    <p class="text-muted mb-4">Hãy thêm banner đầu tiên để bắt đầu</p>
                    <a href="/webdacn_quanlyclb/advertisingbanner/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Thêm Banner Đầu Tiên
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Hình ảnh</th>
                                <th>Thông tin</th>
                                <th>Liên kết</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th class="text-center pe-4">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($banners as $banner): ?>
                                <tr>
                                    <td class="ps-4">
                                        <img src="/webdacn_quanlyclb/<?= htmlspecialchars($banner['image_path']) ?>"
                                            alt="<?= htmlspecialchars($banner['alt_text'] ?? 'Banner') ?>"
                                            class="banner-preview shadow-sm"
                                            data-bs-toggle="tooltip"
                                            title="Click để xem lớn">
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($banner['alt_text'] ?? 'Không có tiêu đề') ?></div>
                                        <small class="text-muted">ID: <?= $banner['id'] ?></small>
                                    </td>
                                    <td>
                                        <?php if (!empty($banner['link_url'])): ?>
                                            <a href="<?= htmlspecialchars($banner['link_url']) ?>"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-decoration-none d-flex align-items-center"
                                                title="<?= htmlspecialchars($banner['link_url']) ?>">
                                                <i class="fas fa-external-link-alt me-2 text-primary"></i>
                                                <span class="text-truncate" style="max-width: 150px;">
                                                    <?= htmlspecialchars($banner['link_url']) ?>
                                                </span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Không có liên kết</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input toggle-banner"
                                                    type="checkbox"
                                                    role="switch"
                                                    id="toggle_<?= $banner['id'] ?>"
                                                    data-id="<?= $banner['id'] ?>"
                                                    <?= $banner['is_active'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="toggle_<?= $banner['id'] ?>">
                                                    <span class="status-text"><?= $banner['is_active'] ? 'Bật' : 'Tắt' ?></span>
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark"><?= date('d/m/Y', strtotime($banner['created_at'])) ?></div>
                                        <small class="text-muted"><?= date('H:i', strtotime($banner['created_at'])) ?></small>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- Nút sửa -->
                                            <a href="/webdacn_quanlyclb/advertisingbanner/edit/<?= $banner['id'] ?>"
                                                class="btn btn-outline-primary btn-sm action-btn" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Nút xóa (chỉ để trigger modal) -->
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm action-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteBannerModal_<?= $banner['id'] ?>"
                                                title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <!-- === MODALS KHU VỰC RIÊNG (NGOÀI BẢNG) === -->
                                    <?php foreach ($banners as $banner): ?>
                                        <div class="modal fade" id="deleteBannerModal_<?= $banner['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>Xác nhận xóa
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center py-4">
                                                        <i class="fas fa-trash text-danger mb-3" style="font-size: 3rem;"></i>
                                                        <h5 class="text-dark mb-3">Xóa banner vĩnh viễn?</h5>
                                                        <div class="mb-3 p-2 bg-light rounded">
                                                            <img src="/webdacn_quanlyclb/<?= htmlspecialchars($banner['image_path']) ?>"
                                                                class="img-fluid rounded shadow-sm"
                                                                style="max-height: 120px;">
                                                        </div>
                                                        <p class="text-muted mb-2 fw-semibold">
                                                            <?= htmlspecialchars($banner['alt_text'] ?? 'Banner không tên') ?>
                                                        </p>
                                                        <p class="text-danger small mb-0">
                                                            <i class="fas fa-info-circle"></i> Hành động này <strong>không thể hoàn tác</strong>.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer border-0 justify-content-center">
                                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-1"></i>Hủy
                                                        </button>
                                                        <form action="/webdacn_quanlyclb/advertisingbanner/delete/<?= $banner['id'] ?>" method="POST" class="d-inline">
                                                            <button type="submit" class="btn btn-danger px-4">
                                                                <i class="fas fa-trash me-1"></i>Xóa
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <!-- === KẾT THÚC MODALS === -->
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-borderless/borderless.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Add click to view larger image
        document.querySelectorAll('.banner-preview').forEach(function(img) {
            img.style.cursor = 'pointer';
            img.addEventListener('click', function() {
                var modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Xem trước banner</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${this.src}" style="max-width: 100%; max-height: 70vh;" class="rounded">
                        </div>
                    </div>
                </div>
            `;
                document.body.appendChild(modal);
                var bsModal = new bootstrap.Modal(modal);
                bsModal.show();
                modal.addEventListener('hidden.bs.modal', function() {
                    document.body.removeChild(modal);
                });
            });
        });
        // Toggle banner với AJAX
        document.querySelectorAll('.toggle-banner').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const bannerId = this.dataset.id;
                const isChecked = this.checked;
                const statusText = this.closest('td').querySelector('.status-text');

                // Hỏi xác nhận với theme hồng
                Swal.fire({
                    title: isChecked ? 'Bật banner?' : 'Tắt banner?',
                    text: isChecked ? 'Banner sẽ hiển thị trên trang chủ' : 'Banner sẽ bị ẩn',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-check me-1"></i> Có',
                    cancelButtonText: '<i class="fas fa-times me-1"></i> Hủy',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal2-popup', // Áp class popup
                        title: 'swal2-title', // Áp class title
                        confirmButton: 'swal2-confirm', // Áp nút xác nhận hồng gradient
                        cancelButton: 'swal2-cancel' // Áp nút hủy xám
                    },
                    buttonsStyling: false, // Tắt style mặc định để dùng CSS tùy chỉnh
                    heightAuto: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Gửi AJAX (giữ nguyên)
                        fetch('/webdacn_quanlyclb/advertisingbanner/toggle-ajax', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: 'id=' + bannerId
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Cập nhật text "Bật/Tắt"
                                    statusText.textContent = isChecked ? 'Bật' : 'Tắt';
                                    statusText.className = 'status-text ' + (isChecked ? 'text-success' : 'text-secondary');

                                    // === CẬP NHẬT SỐ ĐẾM ĐANG HOẠT ĐỘNG ===
                                    const countElement = document.getElementById('active-banner-count');
                                    if (countElement) {
                                        let currentCount = parseInt(countElement.textContent);
                                        countElement.textContent = isChecked ? (currentCount + 1) : (currentCount - 1);
                                    }

                                    // Hiệu ứng nhấp nháy số đếm
                                    countElement.style.transition = 'all 0.3s ease';
                                    countElement.style.transform = 'scale(1.2)';
                                    setTimeout(() => {
                                        countElement.style.transform = 'scale(1)';
                                    }, 300);

                                    // Thông báo thành công
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công!',
                                        text: data.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    toggle.checked = !isChecked;
                                    Swal.fire('Lỗi', data.message, 'error');
                                }
                            })
                            .catch(() => {
                                toggle.checked = !isChecked;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi',
                                    text: 'Không thể kết nối server',
                                    customClass: {
                                        popup: 'swal2-popup'
                                    },
                                    buttonsStyling: false
                                });
                            });
                    } else {
                        toggle.checked = !isChecked;
                    }
                });
            });
        });
    });
</script>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/footer.php'; ?>