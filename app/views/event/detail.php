<?php
// === LOGIC XỬ LÝ STYLE DỰA TRÊN CATEGORY ===
// (Đảm bảo biến $event đã được truyền từ Controller)

$categoryStyles = [
    'truong' => [ // Sự kiện trường (Đỏ)
        'label' => 'Sự kiện Trường',
        'color' => '#dc3545', // Màu chữ/viền
        'bg' => '#f8d7da',    // Màu nền header
        'icon' => 'fas fa-university'
    ],
    'clb' => [ // Sự kiện CLB (Xanh lá)
        'label' => 'Sự kiện Câu lạc bộ',
        'color' => '#198754',
        'bg' => '#d1e7dd',
        'icon' => 'fas fa-users'
    ],
    'sponsor' => [ // Sự kiện Nhà tài trợ (Vàng)
        'label' => 'Sự kiện Nhà tài trợ',
        'color' => '#664d03', // Màu chữ đậm hơn cho dễ đọc
        'bg' => '#fff3cd',
        'icon' => 'fas fa-handshake'
    ]
];
// Lấy style tương ứng, mặc định là CLB nếu không tìm thấy
$style = $categoryStyles[$event['category'] ?? 'clb'] ?? $categoryStyles['clb'];

// Các biến $canDelete, $canJoin, $hasJoined cũng cần được truyền từ Controller
?>

<div class="container mt-4" style="max-width: 900px;">
    
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 15px; border-left: 6px solid <?php echo $style['color']; ?>;">
        
        <div class="card-header" style="background-color: <?php echo $style['bg']; ?>; border-bottom: 2px solid <?php echo $style['color']; ?>; border-radius: 15px 15px 0 0; padding: 1.25rem;">
            <span class="badge mb-2" style="background-color: <?php echo $style['color']; ?>; font-size: 0.95rem; padding: 0.5em 0.9em;">
                <i class="<?php echo $style['icon']; ?> me-2"></i><?php echo $style['label']; ?>
            </span>
            
            <h2 class="mt-1 mb-0" style="color: <?php echo $style['color']; ?>; font-weight: 600;">
                <?php echo htmlspecialchars($event['title'] ?? 'Tiêu đề sự kiện'); ?>
            </h2>
        </div>

        <div class="card-body" style="background-color: #FFFDFE; padding: 2rem;">
            
            <div class="mb-4 text-center" style="border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.15);">
                <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($event['image_path']); ?>"
                     class="img-fluid"
                     alt="Ảnh sự kiện <?php echo htmlspecialchars($event['title']); ?>"
                     style="max-height: 450px; object-fit: cover;">
            </div>

            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <div class="info-box p-3 h-100 d-flex flex-column" style="background: #f8f9fa; border-radius: 8px;">
                        <h6 class="mb-2" style="color: #D23369;"><i class="fas fa-calendar-alt me-2 fa-fw"></i>Thời gian</h6>
                        <p class="mb-0 fs-5 fw-medium"><?php echo date('H:i \n\gà\y d/m/Y', strtotime($event['event_date'])); ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box p-3 h-100 d-flex flex-column" style="background: #f8f9fa; border-radius: 8px;">
                        <h6 class="mb-2" style="color: #D23369;"><i class="fas fa-map-marker-alt me-2 fa-fw"></i>Địa điểm</h6>
                        <p class="mb-0 fs-5 fw-medium"><?php echo htmlspecialchars($event['location']); ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box p-3 h-100 d-flex flex-column" style="background: #f8f9fa; border-radius: 8px;">
                        <h6 class="mb-2" style="color: #D23369;"><i class="fas fa-users me-2 fa-fw"></i>CLB Tổ chức</h6>
                        <p class="mb-0 fs-5 fw-medium">
                            <?php 
                                // Hiển thị tên CLB hoặc "Không áp dụng"
                                if (!empty($event['team_name'])) {
                                    echo htmlspecialchars($event['team_name']);
                                } else {
                                    echo '<span class="text-muted">Không áp dụng</span>'; 
                                }
                            ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box p-3 h-100 d-flex flex-column" style="background: #f8f9fa; border-radius: 8px;">
                        <h6 class="mb-2" style="color: #D23369;"><i class="fas fa-user-tie me-2 fa-fw"></i>Người tạo</h6>
                        <p class="mb-0 fs-5 fw-medium"><?php echo htmlspecialchars($event['author_name'] ?? 'Không rõ'); ?></p>
                    </div>
                </div>
            </div>

            <div class="content-box p-4 mb-4" style="background-color: white; border-radius: 10px; border: 1px solid #eee;">
                <h5 style="color: #D23369; font-weight: 600;">Mô tả chi tiết</h5>
                <hr style="border-top: 1px solid #FFEBEE;">
                <p class="card-text mt-3" style="color: #444; line-height: 1.8; font-size: 1.05rem;">
                    <?php echo nl2br(htmlspecialchars($event['description'] ?? 'Không có mô tả chi tiết.')); ?>
                </p>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-5 flex-wrap gap-2">
                <a href="/webdacn_quanlyclb" class="btn btn-secondary" style="border-radius: 50px; padding: 10px 25px;">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại Danh sách
                </a>
                
                <div> 
                    <?php if ($canJoin): // Chỉ hiện cho user thường đã đăng nhập ?>
                        <?php if ($hasJoined): // Nếu đã tham gia ?>
                            <button type="button" class="btn btn-success" style="border-radius: 50px; padding: 10px 25px;" disabled>
                                <i class="fas fa-check-circle me-2"></i> Bạn đã đăng ký tham gia
                            </button>
                        <?php else: // Nếu chưa tham gia ?>
                            <button type="button" class="btn btn-primary" style="border-radius: 50px; padding: 10px 25px;"
                                    data-bs-toggle="modal" data-bs-target="#joinEventModal">
                                <i class="fas fa-calendar-check me-2"></i> Tham gia sự kiện
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($canDelete): // Nút Xóa (chỉ Admin) ?>
                        <button type="button" class="btn btn-danger ms-2" style="border-radius: 50px; padding: 10px 25px;"
                                data-bs-toggle="modal" data-bs-target="#deleteEventModal">
                            <i class="fas fa-trash-alt me-2"></i> Xóa sự kiện
                        </button>
                    <?php endif; ?>
                </div>
            </div> </div> </div> </div> <?php if ($canJoin && !$hasJoined): ?>
<div class="modal fade" id="joinEventModal" tabindex="-1" aria-labelledby="joinEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title" id="joinEventModalLabel"><i class="fas fa-calendar-check me-2"></i>Xác nhận tham gia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 px-4">
                <p>Bạn có chắc chắn muốn đăng ký tham gia sự kiện:</p>
                <p class="fs-5 fw-bold text-primary">"<?php echo htmlspecialchars($event['title']); ?>"</p>
                <p><i class="fas fa-clock me-2 text-muted"></i>Diễn ra vào: <?php echo date('H:i \n\gà\y d/m/Y', strtotime($event['event_date'])); ?></p>
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 20px;">Hủy bỏ</button>
                <a href="/webdacn_quanlyclb/event/join/<?php echo $event['id']; ?>" class="btn btn-primary" style="border-radius: 20px;">
                    <i class="fas fa-check me-1"></i> Xác nhận tham gia
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($canDelete): ?>
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-labelledby="deleteEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header bg-danger text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title" id="deleteEventModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Xác nhận xóa sự kiện</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 px-4">
                <p>Bạn có chắc chắn muốn xóa vĩnh viễn sự kiện:</p> 
                <p class="fs-5 fw-bold text-danger">"<?php echo htmlspecialchars($event['title']); ?>"</p>
                <p class="text-danger small"><i class="fas fa-info-circle me-1"></i>Hành động này không thể hoàn tác và sẽ xóa cả ảnh liên quan!</p>
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 20px;">Hủy bỏ</button>
                <a href="/webdacn_quanlyclb/event/delete/<?php echo $event['id']; ?>" class="btn btn-danger" style="border-radius: 20px;">
                    <i class="fas fa-trash-alt me-1"></i> Xóa vĩnh viễn
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>