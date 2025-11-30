<div class="container" style="max-width: 800px; margin-top: 20px;">
    <div class="card shadow-sm" style="border-radius: 15px; border: none;">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%); border-radius: 15px 15px 0 0;">
            <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Tạo sự kiện mới</h4>
        </div>
        <div class="card-body" style="background-color: #FFF9FB; padding: 2rem;">
            
            <form action="/webdacn_quanlyclb/event/store" method="POST" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold" style="color: #D23369;">Tên sự kiện</label>
                    <input type="text" class="form-control" id="title" name="title" required
                           style="border-radius: 10px;">
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label fw-bold" style="color: #D23369;">Loại sự kiện <span class="text-danger">*</span></label>
                    <select class="form-select" id="category" name="category" required style="border-radius: 10px;">
                        <option value="" disabled selected>-- Vui lòng chọn loại sự kiện trước --</option>
                        <option value="clb">Sự kiện Câu lạc bộ (Xanh)</option>
                        <option value="truong">Sự kiện Trường (Đỏ)</option>
                        <option value="sponsor">Sự kiện Nhà tài trợ (Vàng)</option>
                    </select>
                </div>

                <div class="mb-3" id="team-wrapper" style="display: none;">
                    <label for="team_id" class="form-label fw-bold" style="color: #D23369;">CLB tổ chức <span class="text-danger">*</span></label>
                    <select class="form-select" id="team_id" name="team_id" style="border-radius: 10px;">
                        <option value="" disabled selected>-- Chọn Câu lạc bộ --</option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?php echo htmlspecialchars($team['id']); ?>">
                                <?php echo htmlspecialchars($team['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="event_date" class="form-label fw-bold" style="color: #D23369;">Ngày giờ diễn ra</label>
                        <input type="datetime-local" class="form-control" id="event_date" name="event_date" required
                               style="border-radius: 10px;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label fw-bold" style="color: #D23369;">Địa điểm</label>
                        <input type="text" class="form-control" id="location" name="location" required
                               style="border-radius: 10px;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold" style="color: #D23369;">Mô tả sự kiện</label>
                    <textarea class="form-control" id="description" name="description" rows="5" required
                              style="border-radius: 10px;"></textarea>
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label fw-bold" style="color: #D23369;">Ảnh bìa sự kiện (Bắt buộc)</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required
                           style="border-radius: 10px;">
                </div>

                <div class="text-end">
                    <a href="/webdacn_quanlyclb" class="btn" style="background-color: #6c757d; color: white; border-radius: 50px; padding: 10px 25px;">Hủy</a>
                    <button type="submit" class="btn" style="background-color: #E91E63; color: white; border-radius: 50px; padding: 10px 25px;">
                        <i class="fas fa-paper-plane me-2"></i> Đăng sự kiện
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const teamWrapper = document.getElementById('team-wrapper');
    const teamSelect = document.getElementById('team_id');

    // Thêm sự kiện 'change' cho ô Loại sự kiện
    categorySelect.addEventListener('change', function() {
        if (this.value === 'clb') {
            // Nếu là 'Câu lạc bộ', hiện ô chọn CLB và đặt là bắt buộc
            teamWrapper.style.display = 'block';
            teamSelect.required = true;
        } else {
            // Nếu là 'Trường' hoặc 'Tài trợ', ẩn ô chọn CLB và bỏ bắt buộc
            teamWrapper.style.display = 'none';
            teamSelect.required = false;
            teamSelect.value = ''; // Xóa giá trị đã chọn (nếu có)
        }
    });
});
</script>