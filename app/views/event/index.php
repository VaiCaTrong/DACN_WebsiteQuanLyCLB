<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php'; ?>

<style>
    /* === CSS GIỐNG BANNER INDEX.PHP === */
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

    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
    }

    .form-switch .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }

    .status-text {
        font-weight: 600;
        font-size: 0.9rem;
        color: #495057;
    }

    .form-switch .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
</style>

<div class="container-fluid px-4 mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-1"><i class="fas fa-calendar-check me-2" style="color: #E91E63;"></i>Quản lý Sự kiện</h2>
            <p class="text-muted mb-0">Quản lý và cập nhật các sự kiện của CLB</p>
        </div>
        <a href="/webdacn_quanlyclb/event/create" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Thêm Sự kiện Mới
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
                            <div class="fs-4 fw-bold"><?= count($events) ?></div>
                            <div>Tổng số sự kiện</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-check fs-1 opacity-50"></i>
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
                            <div class="fs-4 fw-bold" id="active-event-count"><?= count(array_filter($events, fn($e) => $e['is_active'] == 1)) ?></div>
                            <div>Đang hoạt động</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Thêm thống kê khác nếu cần -->
    </div>

    <!-- Table -->
    <div class="table-responsive shadow-sm bg-white p-3 rounded" style="border-radius: 12px;">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Ảnh</th>
                    <th>Thông tin</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Chưa có sự kiện nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $index => $event): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <img src="/webdacn_quanlyclb/<?= htmlspecialchars($event['image_path']) ?>"
                                    alt="<?= htmlspecialchars($event['title']) ?>"
                                    class="banner-preview">
                            </td>
                            <td>
                                <div class="fw-bold" style="color: #E91E63;"><?= htmlspecialchars($event['title']) ?></div>
                                <small class="text-muted">Phân loại: <?= htmlspecialchars(ucfirst($event['category'])) ?></small><br>
                                <small class="text-muted">Vị trí: <?= htmlspecialchars($event['location']) ?></small>
                            </td>
                            <td>
                                <small class="text-muted">Tạo: <?= date('d/m/Y', strtotime($event['created_at'])) ?></small><br>
                                <small class="text-muted">Diễn ra: <?= date('d/m/Y H:i', strtotime($event['event_date'])) ?></small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input toggle-event"
                                            type="checkbox"
                                            role="switch"
                                            id="toggle_<?= $event['id'] ?>"
                                            data-id="<?= $event['id'] ?>"
                                            <?= $event['is_active'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="toggle_<?= $event['id'] ?>">
                                            <span class="status-text"><?= $event['is_active'] ? 'Bật' : 'Tắt' ?></span>
                                        </label>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="/webdacn_quanlyclb/event/edit/<?= $event['id'] ?>"
                                    class="btn btn-sm btn-outline-primary action-btn" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger action-btn"
                                    data-bs-toggle="modal" data-bs-target="#deleteEventModal_<?= $event['id'] ?>" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- === MODALS XÓA (NGOÀI BẢNG) === -->
<?php foreach ($events as $event): ?>
    <div class="modal fade" id="deleteEventModal_<?= $event['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Xác nhận xóa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash text-danger mb-3" style="font-size: 3rem;"></i>
                    <h5 class="text-dark mb-3">Xóa sự kiện vĩnh viễn?</h5>
                    <div class="mb-3 p-2 bg-light rounded">
                        <img src="/webdacn_quanlyclb/<?= htmlspecialchars($event['image_path']) ?>"
                            style="max-height: 100px; border-radius: 4px;">
                    </div>
                    <p class="text-muted mb-0 fw-semibold"><?= htmlspecialchars($event['title']) ?></p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Hủy</button>
                    <form action="/webdacn_quanlyclb/event/delete/<?= $event['id'] ?>" method="POST" class="d-inline">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Toggle sự kiện với AJAX
    document.querySelectorAll('.toggle-event').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const eventId = this.dataset.id;
            const isChecked = this.checked;
            const statusText = this.closest('td').querySelector('.status-text');

            Swal.fire({
                title: isChecked ? 'Bật sự kiện?' : 'Tắt sự kiện?',
                text: isChecked ? 'Sự kiện sẽ hiển thị công khai' : 'Sự kiện sẽ bị ẩn',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Có',
                cancelButtonText: 'Hủy',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/webdacn_quanlyclb/event/toggle-ajax', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'id=' + eventId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                statusText.textContent = isChecked ? 'Bật' : 'Tắt';
                                statusText.className = 'status-text ' + (isChecked ? 'text-success' : 'text-secondary');

                                // CẬP NHẬT SỐ ĐẾM ĐANG HOẠT ĐỘNG
                                const countEl = document.getElementById('active-event-count');
                                if (countEl) {
                                    let current = parseInt(countEl.textContent);
                                    countEl.textContent = isChecked ? current + 1 : current - 1;

                                    // Hiệu ứng nhấp nháy
                                    countEl.style.transition = 'all 0.3s ease';
                                    countEl.style.transform = 'scale(1.2)';
                                    setTimeout(() => countEl.style.transform = 'scale(1)', 300);
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công!',
                                    text: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    // GỬI SỰ KIỆN TỚI CÁC TAB KHÁC
                                    localStorage.setItem('event_toggle', JSON.stringify({
                                        event_id: eventId,
                                        is_active: isChecked,
                                        timestamp: Date.now()
                                    }));
                                });
                            } else {
                                toggle.checked = !isChecked;
                                Swal.fire('Lỗi', data.message, 'error');
                            }
                        })
                        .catch(() => {
                            toggle.checked = !isChecked;
                            Swal.fire('Lỗi', 'Không thể kết nối server', 'error');
                        });
                } else {
                    toggle.checked = !isChecked;
                }
            });
        });
    });
</script>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/footer.php'; ?>