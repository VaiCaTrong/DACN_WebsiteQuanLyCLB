<?php
include 'app/views/shares/header.php';
require_once __DIR__ . '/../../helpers/SessionHelper.php';
SessionHelper::start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Bạn không có quyền truy cập trang này!";
    header("Location: /webdacn_quanlyclb");
    exit();
}
?>



<main class="container my-4" style="padding-top: 70px;">
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; background-color: #FFF9FB;">
        <div class="card-body p-4">
            <h2 class="text-center mb-4" style="color: #E91E63; font-weight: 600;">Thêm bài viết mới</h2>

            <form action="/webdacn_quanlyclb/default/create" method="POST" enctype="multipart/form-data" id="postForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(SessionHelper::getCsrfToken()) ?>">

                <div class="mb-3">
                    <label for="title" class="form-label" style="color: #E91E63; font-weight: 600;">Tiêu đề</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label" style="color: #E91E63; font-weight: 600;">Nội dung</label>
                    <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label" style="color: #E91E63; font-weight: 600;">Hình ảnh (tùy chọn, nhiều ảnh, tối đa 20MB mỗi ảnh)</label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/jpeg,image/png,image/gif,image/bmp,image/webp">
                </div>

                <div class="mb-3">
                    <label for="team_id" class="form-label" style="color: #E91E63; font-weight: 600;">Đội/CLB</label>
                    <select class="form-control" id="team_id" name="team_id">
                        <option value="">Không thuộc đội nào</option>
                        <?php
                        require_once __DIR__ . '/../../models/TeamModel.php';
                        $teamModel = new TeamModel();
                        $teams = $teamModel->getAllTeams();
                        foreach ($teams as $team) {
                            echo "<option value='{$team['id']}'>" . htmlspecialchars($team['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary" id="submitBtn" style="background-color: #E91E63; border: none; border-radius: 50px; padding: 10px 25px; box-shadow: 0 4px 8px rgba(233, 30, 99, 0.3);">
                        <i class="fas fa-plus me-2"></i>Thêm bài viết
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('postForm').addEventListener('submit', function(event) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang xử lý...';
    });

    // Kiểm tra file ảnh ở phía client
    document.getElementById('images').addEventListener('change', function(event) {
        const files = event.target.files;
        for (let file of files) {
            if (file.size > 20 * 1024 * 1024) {
                alert(`File ${file.name} vượt quá 20MB!`);
                event.target.value = '';
                return;
            }
            if (!file.type.startsWith('image/')) {
                alert(`File ${file.name} không phải là ảnh!`);
                event.target.value = '';
                return;
            }
        }
    });
</script>
</body>
</html>