<?php include 'app/views/shares/header.php'; ?>

<style>
    /* === CSS (Giữ nguyên toàn bộ CSS đã thiết kế lại) === */
    :root {
        --primary: #E91E63;
        --primary-light: #FCE4EC;
        --primary-dark: #C2185B;
        --bg: #FFF9FB;
        --white: #fff;
        --text-dark: #333;
        --text-medium: #666;
        --border-color: #F8BBD0;
        --shadow: 0 8px 25px rgba(233, 30, 99, 0.1);
        --success: #198754;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #0dcaf0;
    }

    body { background-color: var(--bg); }
    .team-management-container { max-width: 1200px; margin: 2rem auto; }

    /* === CỘT TRÁI: THÔNG TIN CLB === */
    .team-info-sidebar { background: var(--white); border-radius: 16px; box-shadow: var(--shadow); padding: 2rem; text-align: center; border-top: 5px solid var(--primary); position: sticky; top: 80px; }
    .team-avatar { width: 130px; height: 130px; border-radius: 50%; object-fit: cover; margin: 0 auto 1.5rem auto; border: 4px solid var(--primary-light); box-shadow: 0 4px 15px rgba(233, 30, 99, 0.2); }
    .team-avatar-placeholder { width: 130px; height: 130px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 4rem; margin: 0 auto 1.5rem auto; border: 4px solid var(--primary-light); box-shadow: 0 4px 15px rgba(233, 30, 99, 0.2); }
    .team-info-sidebar h1 { font-size: 1.8rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 0.5rem; }
    .team-info-sidebar p.team-description { font-size: 1rem; color: var(--text-medium); margin-bottom: 1.5rem; line-height: 1.6; }
    .info-grid { text-align: left; margin-bottom: 2rem; }
    .info-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 0.95rem; }
    .info-item i { color: var(--primary); font-size: 1.1em; width: 25px; text-align: center; }
    .info-label { font-weight: 600; color: var(--text-dark); }
    .info-value { color: var(--text-medium); }
    .btn-edit-team { display: block; width: 100%; background: var(--primary); color: var(--white); padding: 12px 20px; border-radius: 50px; font-size: 1rem; font-weight: 500; text-decoration: none; border: none; transition: all 0.3s ease; }
    .btn-edit-team:hover { background: var(--primary-dark); box-shadow: 0 4px 12px rgba(233, 30, 99, 0.3); transform: translateY(-2px); }

    /* === CỘT PHẢI: TABS === */
    .team-content-panel { background: var(--white); border-radius: 16px; box-shadow: var(--shadow); overflow: hidden; }
    .nav-tabs { border-bottom: 2px solid var(--primary-light); }
    .nav-tabs .nav-link { border: none; border-bottom: 4px solid transparent; padding: 1rem 1.5rem; font-size: 1.1rem; font-weight: 600; color: var(--text-medium); transition: all 0.3s ease; }
    .nav-tabs .nav-link.active { color: var(--primary); border-bottom-color: var(--primary); background-color: var(--primary-light); border-radius: 8px 8px 0 0; }
    .nav-tabs .nav-link:hover { color: var(--primary-dark); }
    .tab-content { padding: 2rem; }
    .tab-pane { animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Danh sách thành viên */
    .members-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; }
    .member-card { border: 1px solid #eee; border-radius: 12px; padding: 15px; display: flex; align-items: center; gap: 15px; transition: all 0.3s ease; border-left: 4px solid var(--primary-light); }
    .member-card:hover { box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-left-color: var(--primary); }
    .member-avatar { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 2px solid var(--border-color); }
    .member-avatar-placeholder { width: 60px; height: 60px; border-radius: 50%; background: var(--primary-light); color: var(--primary-dark); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 600; flex-shrink: 0; }
    .member-info { flex-grow: 1; }
    .member-name { font-weight: 600; color: var(--text-dark); font-size: 1.1rem; }
    .member-email { font-size: 0.9em; color: var(--text-medium); }
    .btn-details { background: var(--primary-light); color: var(--primary-dark); padding: 5px 12px; border-radius: 20px; font-size: 0.8em; font-weight: 600; border: none; margin-top: 5px; transition: all 0.2s ease; }
    .btn-details:hover { background: var(--primary); color: var(--white); }

    /* Đơn xin gia nhập */
    .request-item { border: 1px solid #eee; border-radius: 12px; padding: 1.5rem; margin-bottom: 1rem; background: #fdfdfd; border-left: 4px solid var(--warning); }
    .request-item h4 { color: var(--primary-dark); margin-bottom: 1rem; }
    .request-info { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 1rem; font-size: 0.95rem; }
    .request-info p { margin-bottom: 5px; }
    .request-info strong { color: var(--text-dark); }
    .request-info span { color: var(--text-medium); }
    .request-reason { background: #f8f9fa; padding: 10px; border-radius: 8px; color: var(--text-medium); font-style: italic; }
    .request-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 1rem; border-top: 1px solid #eee; padding-top: 1rem; }
    .btn { padding: 8px 15px; border-radius: 50px; border: none; cursor: pointer; font-weight: 500; transition: all 0.3s ease; }
    .btn-approve { background: var(--success); color: white; }
    .btn-approve:hover { background: #157347; }
    .btn-reject { background: var(--danger); color: white; }
    .btn-reject:hover { background: #b02a37; }
    .btn-interview { background: var(--warning); color: #000; }
    .btn-interview:hover { background: #ffca2c; }

    /* CSS MODAL TÙY CHỈNH */
    #memberModal, #punishModal, #rewardModal { display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px); align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; pointer-events: none; }
    #memberModal.open, #punishModal.open, #rewardModal.open { display: flex; opacity: 1; pointer-events: auto; }
    #memberModal .modal-content-wrapper, #punishModal .modal-content-wrapper, #rewardModal .modal-content-wrapper { width: 90%; max-width: 800px; max-height: 90vh; display: flex; align-items: center; justify-content: center; }
    #memberModal .modal-content, #memberModal .punish-modal-content, #memberModal .reward-modal-content, #punishModal .modal-content, #punishModal .punish-modal-content, #punishModal .reward-modal-content, #rewardModal .modal-content, #rewardModal .punish-modal-content, #rewardModal .reward-modal-content { background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%); border-radius: 20px; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3); width: 100%; overflow: hidden; transform: translateY(50px) scale(0.9); opacity: 0; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); border: 1px solid rgba(255, 255, 255, 0.2); }
    #memberModal.open .modal-content, #memberModal.open .punish-modal-content, #memberModal.open .reward-modal-content, #punishModal.open .modal-content, #punishModal.open .punish-modal-content, #punishModal.open .reward-modal-content, #rewardModal.open .modal-content, #rewardModal.open .punish-modal-content, #rewardModal.open .reward-modal-content { transform: translateY(0) scale(1); opacity: 1; }
    #memberModal .modal-header, #memberModal .punish-modal-header, #memberModal .reward-modal-header, #punishModal .modal-header, #punishModal .punish-modal-header, #punishModal .reward-modal-header, #rewardModal .modal-header, #rewardModal .punish-modal-header, #rewardModal .reward-modal-header { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 2rem 2.5rem; color: white; display: flex; justify-content: space-between; align-items: center; border-bottom: none; position: relative; overflow: hidden; }
    #memberModal .modal-header h2, #memberModal .punish-modal-header h2, #memberModal .reward-modal-header h2, #punishModal .modal-header h2, #punishModal .punish-modal-header h2, #punishModal .reward-modal-header h2, #rewardModal .modal-header h2, #rewardModal .punish-modal-header h2, #rewardModal .reward-modal-header h2 { color: white; margin: 0; font-size: 1.8rem; font-weight: 700; position: relative; z-index: 1; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
    #memberModal .close, #punishModal .close, #rewardModal .close { color: white; font-size: 2.5rem; font-weight: 300; cursor: pointer; background: none; border: none; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; position: relative; z-index: 1; }
    #memberModal .close:hover, #punishModal .close:hover, #rewardModal .close:hover { background: rgba(255, 255, 255, 0.2); transform: rotate(90deg); }
    #memberModal .modal-body { padding: 2.5rem; max-height: 60vh; overflow-y: auto; background: white; }
    #memberModal .modal-avatar-wrapper { display: flex; flex-direction: column; align-items: center; gap: 20px; margin-bottom: 2rem; padding: 2rem; background: linear-gradient(135deg, #FFF0F5 0%, #FCE4EC 100%); border-radius: 20px; border: 2px dashed var(--primary-light); }
    #memberModal .modal-avatar { width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 6px solid white; box-shadow: 0 10px 30px rgba(255, 107, 158, 0.3); transition: all 0.3s ease; }
    #memberModal .modal-avatar:hover { transform: scale(1.05); box-shadow: 0 15px 40px rgba(255, 107, 158, 0.4); }
    #memberModal .modal-avatar-placeholder { width: 140px; height: 140px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 3.5rem; font-weight: 700; border: 6px solid white; box-shadow: 0 10px 30px rgba(255, 107, 158, 0.3); }
    #memberModal #memberModalName { font-size: 2rem; font-weight: 800; color: var(--primary-dark); text-align: center; margin: 0; }
    #memberModal .modal-details { background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08); border: 1px solid #f0f0f0; }
    #memberModal .modal-details-list { list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem 1.5rem; }
    #memberModal .modal-details-list li { display: flex; flex-direction: column; padding: 1rem; background: rgba(255, 107, 158, 0.05); border-radius: 12px; border-bottom: 3px solid var(--primary-light); transition: all 0.3s ease; }
    #memberModal .modal-details-list li:hover { background: rgba(255, 107, 158, 0.1); transform: translateY(-3px); border-bottom-color: var(--primary); }
    #memberModal .modal-details-list .info-label { color: var(--primary); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.25rem; }
    #memberModal .modal-details-list .info-value { color: var(--text-dark); font-weight: 500; word-break: break-word; font-size: 1.1rem; }
    #memberModal .modal-details-list li.full-width { grid-column: 1 / -1; }
    #memberModal .modal-details-list .badge { font-size: 0.95rem; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; }
    #memberModal .modal-actions { display: flex; gap: 1rem; justify-content: center; margin-top: 2.5rem; padding-top: 2rem; border-top: 2px dashed #f0f0f0; }
    #memberModal .btn-punish, #memberModal .btn-reward { padding: 1rem 2rem; border-radius: 50px; border: none; font-weight: 700; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 0.5rem; min-width: 160px; justify-content: center; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); position: relative; overflow: hidden; }
    #memberModal .btn-punish { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; }
    #memberModal .btn-reward { background: linear-gradient(135deg, #198754 0%, #157347 100%); color: white; }
    #memberModal .btn-punish:hover, #memberModal .btn-reward:hover { transform: translateY(-3px); box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2); }
    #punishModal .punish-form, #rewardModal .reward-form { padding: 2.5rem; }
    #punishModal .punish-form label, #rewardModal .reward-form label { display: block; margin-bottom: 0.8rem; font-weight: 700; color: var(--primary-dark); font-size: 1rem; text-transform: uppercase; letter-spacing: 0.5px; }
    #punishModal .punish-form textarea, #rewardModal .reward-form textarea, #punishModal .punish-form select, #rewardModal .reward-form select { width: 100%; padding: 1rem 1.5rem; margin-bottom: 1.5rem; border: 2px solid #f0f0f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: rgba(255, 107, 158, 0.02); font-family: inherit; }
    #punishModal .punish-form textarea:focus, #rewardModal .reward-form textarea:focus, #punishModal .punish-form select:focus, #rewardModal .reward-form select:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(255, 107, 158, 0.1); outline: none; background: white; transform: translateY(-2px); }
    #punishModal .punish-form textarea, #rewardModal .reward-form textarea { min-height: 120px; resize: vertical; }
    #punishModal .modal-footer, #rewardModal .modal-footer { padding: 1.5rem 2.5rem; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); text-align: right; border-top: 2px solid #e9ecef; display: flex; gap: 1rem; justify-content: flex-end; }
    #punishModal .modal-footer .btn, #rewardModal .modal-footer .btn { padding: 0.8rem 2rem; border-radius: 50px; font-weight: 600; border: 2px solid transparent; transition: all 0.3s ease; min-width: 120px; }
    #punishModal .modal-footer .btn-secondary, #rewardModal .modal-footer .btn-secondary { background: transparent; color: #6c757d; border-color: #6c757d; }
    #punishModal .modal-footer .btn-secondary:hover, #rewardModal .modal-footer .btn-secondary:hover { background: #6c757d; color: white; transform: translateY(-2px); }
    #punishModal .modal-footer .btn-punish, #rewardModal .modal-footer .btn-reward { padding: 0.8rem 2rem; border-radius: 50px; font-weight: 600; border: 2px solid transparent; transition: all 0.3s ease; min-width: 120px; }
    @media (max-width: 768px) { #memberModal .modal-content-wrapper, #punishModal .modal-content-wrapper, #rewardModal .modal-content-wrapper { width: 95%; max-width: 95%; } #memberModal .modal-header, #memberModal .punish-modal-header, #memberModal .reward-modal-header, #punishModal .modal-header, #punishModal .punish-modal-header, #punishModal .reward-modal-header, #rewardModal .modal-header, #rewardModal .punish-modal-header, #rewardModal .reward-modal-header { padding: 1.5rem; } #memberModal .modal-header h2, #memberModal .punish-modal-header h2, #memberModal .reward-modal-header h2, #punishModal .modal-header h2, #punishModal .punish-modal-header h2, #punishModal .reward-modal-header h2, #rewardModal .modal-header h2, #rewardModal .punish-modal-header h2, #rewardModal .reward-modal-header h2 { font-size: 1.5rem; } #memberModal .modal-body { padding: 1.5rem; } #memberModal .modal-details-list { grid-template-columns: 1fr; } #memberModal .modal-details-list li { flex-direction: column; gap: 0.5rem; } #memberModal .modal-details-list .info-label { width: 100%; } #memberModal .modal-actions { flex-direction: column; align-items: center; } #memberModal .btn-punish, #memberModal .btn-reward { width: 100%; max-width: 250px; } #punishModal .modal-footer, #rewardModal .modal-footer { flex-direction: column; gap: 0.8rem; } #punishModal .modal-footer .btn, #rewardModal .modal-footer .btn { width: 100%; } }
    #memberModal .modal-body::-webkit-scrollbar { width: 8px; }
    #memberModal .modal-body::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    #memberModal .modal-body::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }
    #memberModal .modal-body::-webkit-scrollbar-thumb:hover { background: var(--primary-dark); }

    /* === CSS BÀI VIẾT CLB === */
    .post-list-card { margin-bottom: 20px; border-radius: 12px; border: 1px solid #eee; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); transition: transform 0.3s ease, box-shadow 0.3s ease; background: var(--white); }
    .post-list-card:hover { transform: translateY(-5px); box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); }
    .post-list-card .card-header { display: flex; justify-content: space-between; align-items: center; background-color: #fcfcfc; border-bottom: 1px solid #eee; padding: 1rem 1.25rem; }
    .post-list-card .post-author { display: flex; align-items: center; gap: 10px; }
    .post-author-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-light); }
    .post-author-name { font-weight: 600; color: var(--primary-dark); }
    .post-author-time { font-size: 0.85em; color: var(--text-medium); }
    .post-list-category-label { padding: 4px 12px; border-radius: 12px; font-size: 0.9rem; font-weight: 500; color: white; }
    .post-list-card.category-Thong.bao .post-list-category-label { background-color: #dc3545; }
    .post-list-card.category-Chieu.sinh .post-list-category-label { background-color: #ffc107; color: #000; }
    .post-list-card.category-Su.kien .post-list-category-label { background-color: #198754; }
    .post-list-card .card-body { padding: 1.25rem; }
    .post-list-title { font-size: 1.3rem; font-weight: 600; color: var(--text-dark); margin-bottom: 0.75rem; text-decoration: none; }
    .post-list-title:hover { color: var(--primary); }
    .post-list-excerpt { color: var(--text-medium); margin-bottom: 1.5rem; line-height: 1.6; font-size: 0.95rem; }
    .post-list-actions { text-align: right; display: flex; gap: 8px; justify-content: flex-end; }
</style>

<div class="team-management-container">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="team-info-sidebar">
                <?php if ($team['avatar_team']): ?>
                    <img src="/webdacn_quanlyclb/<?= htmlspecialchars($team['avatar_team']) ?>" alt="Team Avatar" class="team-avatar">
                <?php else: ?>
                    <div class="team-avatar-placeholder">
                        <?= substr(htmlspecialchars($team['name']), 0, 1) ?>
                    </div>
                <?php endif; ?>

                <h1><?= htmlspecialchars($team['name']) ?></h1>
                <p class="team-description"><?= htmlspecialchars($team['description']) ?></p>

                <div class="info-grid">
                    <div class="info-item">
                        <i class="fas fa-users"></i>
                        <span class="info-label">Số thành viên:</span>
                        <span class="info-value"><?= count($members) ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-star"></i>
                        <span class="info-label">Tài năng:</span>
                        <span class="info-value"><?= htmlspecialchars($team['talent']) ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-sticky-note"></i>
                        <span class="info-label">Ghi chú:</span>
                        <span class="info-value"><?= htmlspecialchars($team['note']) ?></span>
                    </div>
                </div>

                <a href="/webdacn_quanlyclb/Team/edit/<?= $team['id'] ?>" class="btn-edit-team">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa thông tin CLB
                </a>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="team-content-panel">
                <ul class="nav nav-tabs" id="myTeamTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="members-tab" data-bs-toggle="tab" data-bs-target="#members-pane" type="button" role="tab" aria-controls="members-pane" aria-selected="true">
                            <i class="fas fa-user-friends me-2"></i>Thành viên (<?= count($members) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests-pane" type="button" role="tab" aria-controls="requests-pane" aria-selected="false">
                            <i class="fas fa-user-plus me-2"></i>Đơn xin gia nhập
                            <?php if (count($join_requests) > 0): ?>
                                <span class="badge bg-danger ms-1"><?= count($join_requests) ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="myTeamTabContent">
                    <div class="tab-pane fade show active" id="members-pane" role="tabpanel" aria-labelledby="members-tab" tabindex="0">
                        <?php if (count($members) > 0): ?>
                            <div class="members-grid">
                                <?php foreach ($members as $member): ?>
                                    <div class="member-card">
                                        <?php if ($member['avatar']): ?>
                                            <img src="/webdacn_quanlyclb/<?= htmlspecialchars($member['avatar']) ?>" alt="Member Avatar" class="member-avatar">
                                        <?php else: ?>
                                            <div class="member-avatar-placeholder">
                                                <?= substr(htmlspecialchars($member['fullname'] ?? 'U'), 0, 1) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="member-info">
                                            <div class="member-name"><?= htmlspecialchars($member['fullname'] ?? 'Chưa có tên') ?></div>
                                            <div class="member-email"><?= htmlspecialchars($member['email']) ?></div>
                                            <button class="btn-details" onclick='showMemberDetails(<?= json_encode($member) ?>)'>
                                                <i class="fas fa-info-circle me-1"></i>Chi tiết & Hành động
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center">Chưa có thành viên nào trong đội.</p>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="requests-pane" role="tabpanel" aria-labelledby="requests-tab" tabindex="0">
                        <?php if (count($join_requests) > 0): ?>
                            <?php foreach ($join_requests as $request): ?>
                                <div class="request-item">
                                    <h4><?= htmlspecialchars($request['name']) ?></h4>
                                    <div class="request-info">
                                        <p><strong>Email:</strong> <span><?= htmlspecialchars($request['email']) ?></span></p>
                                        <p><strong>Khoa:</strong> <span><?= htmlspecialchars($request['khoa']) ?></span></p>
                                        <p><strong>Ngày sinh:</strong> <span><?= htmlspecialchars($request['date_of_birth']) ?></span></p>
                                        <p><strong>Tài năng:</strong> <span><?= htmlspecialchars($request['talent']) ?></span></p>
                                    </div>
                                    <div class="request-reason">
                                        <strong>Lý do:</strong> <?= htmlspecialchars($request['reason']) ?>
                                    </div>
                                    <div class="request-actions">
                                        <form action="/webdacn_quanlyclb/Team/userjoin" method="POST" style="display:inline;">
                                            <input type="hidden" name="join_form_id" value="<?= $request['id'] ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-approve">
                                                <i class="fas fa-check me-1"></i> Duyệt
                                            </button>
                                        </form>
                                        <form action="/webdacn_quanlyclb/Team/userjoin" method="POST" style="display:inline;">
                                            <input type="hidden" name="join_form_id" value="<?= $request['id'] ?>">
                                            <button type="submit" name="action" value="schedule" class="btn btn-interview">
                                                <i class="fas fa-calendar-alt me-1"></i> Hẹn phỏng vấn
                                            </button>
                                        </form>
                                        <form action="/webdacn_quanlyclb/Team/userjoin" method="POST" style="display:inline;">
                                            <input type="hidden" name="join_form_id" value="<?= $request['id'] ?>">
                                            <button type="submit" name="action" value="reject" class="btn btn-reject">
                                                <i class="fas fa-times me-1"></i> Từ chối
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center">Hiện không có yêu cầu tham gia nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="team-content-panel">
                    <div class="tab-content" style="padding: 0;">
                        <div class="tab-pane fade show active" id="team-posts-pane" role="tabpanel">
                            <h3 style="padding: 1.5rem 2rem 0 2rem; color: var(--primary-dark); font-weight: 600;">
                                <i class="fas fa-newspaper me-2"></i>Bài viết của CLB
                            </h3>
                            <div class="team-posts-list p-4">
                                <?php if (empty($teamPosts)): ?>
                                    <p class="text-muted text-center fst-italic">CLB này chưa có bài viết nào.</p>
                                <?php else: ?>
                                    <?php $currentStaffId = SessionHelper::getUserId(); ?>
                                    <?php foreach ($teamPosts as $post): ?>
                                        <?php
                                        // Tạo class màu theo category
                                        $catSlug = str_replace(' ', '.', $post['category']);
                                        $categoryClass = 'category-' . $catSlug; 
                                        ?>
                                        <div class="card post-list-card <?= $categoryClass ?>">
                                            <div class="card-header">
                                                <div class="post-author">
                                                    <img src="/webdacn_quanlyclb/<?= htmlspecialchars($post['author_avatar'] ?? 'public/uploads/avatars/default_avatar.jpg') ?>" alt="Avatar" class="post-author-avatar">
                                                    <div>
                                                        <div class="post-author-name"><?= htmlspecialchars($post['author_name']) ?></div>
                                                        <div class="post-author-time"><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></div>
                                                    </div>
                                                </div>
                                                <div class="post-list-category-label">
                                                    <?= htmlspecialchars($post['category']) ?>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <a href="/webdacn_quanlyclb/default/detail/<?php echo $post['id']; ?>" class="post-list-title">
                                                    <?php echo htmlspecialchars($post['title']); ?>
                                                </a>
                                                
                                                <p class="post-list-excerpt">
                                                    <?php
                                                    // 1. Loại bỏ toàn bộ thẻ HTML
                                                    $plainText = strip_tags($post['content']);
                                                    // 2. Cắt 150 ký tự đầu
                                                    $excerpt = mb_substr($plainText, 0, 150, 'UTF-8');
                                                    // 3. Hiển thị văn bản sạch
                                                    echo htmlspecialchars($excerpt);
                                                    // 4. Thêm dấu ... nếu dài hơn
                                                    if (mb_strlen($plainText, 'UTF-8') > 150) echo '...';
                                                    ?>
                                                </p>

                                                <div class="post-list-actions">
                                                    <a href="/webdacn_quanlyclb/default/detail/<?php echo $post['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                                                        <i class="fas fa-eye me-1"></i>Xem chi tiết
                                                    </a>

                                                    <?php
                                                    $isAuthor = ($currentStaffId == $post['author_id']);
                                                    if ($isAuthor): 
                                                    ?>
                                                        <a href="/webdacn_quanlyclb/default/edit/<?php echo $post['id']; ?>" class="btn btn-outline-warning btn-sm rounded-pill">
                                                            <i class="fas fa-edit me-1"></i> Sửa
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#deletePostModal-<?php echo $post['id']; ?>">
                                                            <i class="fas fa-trash me-1"></i> Xóa
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($teamPosts)): ?>
    <?php foreach ($teamPosts as $post): ?>
        <?php
        $currentStaffId = SessionHelper::getUserId();
        $isAuthor = ($currentStaffId == $post['author_id']);
        if ($isAuthor):
        ?>
            <div class="modal fade" id="deletePostModal-<?php echo $post['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 16px;">
                        <div class="modal-header" style="background-color: var(--danger); color: white; border-bottom: none;">
                            <h5 class="modal-title">Xác nhận xóa bài viết</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="padding: 1.5rem;">
                            <p>Bạn có chắc chắn muốn xóa bài viết này không?</p>
                            <p class="fw-bold" style="color: var(--primary-dark);"><?= htmlspecialchars($post['title']); ?></p>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #eee;">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 50px;">Hủy</button>
                            <a href="/webdacn_quanlyclb/default/delete/<?php echo $post['id']; ?>" class="btn btn-danger" style="border-radius: 50px;">Vẫn Xóa</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<div id="memberModal" class="modal">
    <div class="modal-content-wrapper">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-circle me-2"></i>Thông Tin Thành Viên</h2>
                <button class="close" onclick="closeModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="modal-avatar-wrapper">
                    <div id="memberAvatar"></div>
                    <h3 id="memberModalName" class="mb-0"></h3>
                </div>
                <div class="modal-details">
                    <ul class="modal-details-list" id="memberDetailsList"></ul>
                </div>
                <div class="modal-actions">
                    <button class="btn btn-punish" onclick="showPunishForm()"><i class="fas fa-minus-circle me-1"></i> Phạt Thành Viên</button>
                    <button class="btn btn-reward" onclick="showRewardForm()"><i class="fas fa-plus-circle me-1"></i> Thưởng Thành Viên</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="punishModal" class="modal">
    <div class="modal-content-wrapper">
        <div class="punish-modal-content">
            <div class="punish-modal-header">
                <h2><i class="fas fa-minus-circle me-2"></i>Tạo Phiếu Phạt</h2>
                <button class="close" onclick="closePunishModal()">×</button>
            </div>
            <form id="punishForm" class="punish-form" action="/webdacn_quanlyclb/Team/punish" method="POST">
                <input type="hidden" id="punishUserId" name="user_id">
                <input type="hidden" id="punishTeamId" name="team_id" value="<?= htmlspecialchars($team['id']) ?>">
                <label for="reason"><i class="fas fa-clipboard-list me-2"></i>Lý Do Phạt:</label>
                <textarea id="reason" name="reason" rows="4" placeholder="Nhập lý do phạt..." required></textarea>
                <label for="severity"><i class="fas fa-balance-scale me-2"></i>Mức Độ:</label>
                <select id="severity" name="severity" required>
                    <option value="">-- Chọn --</option>
                    <option value="light">Nhẹ (-5 điểm)</option>
                    <option value="medium">Vừa (-10 điểm)</option>
                    <option value="heavy">Nặng (-15 điểm)</option>
                </select>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closePunishModal()">Hủy</button>
                    <button type="submit" class="btn btn-punish">Xác Nhận</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="rewardModal" class="modal">
    <div class="modal-content-wrapper">
        <div class="reward-modal-content">
            <div class="reward-modal-header">
                <h2><i class="fas fa-plus-circle me-2"></i>Tạo Phiếu Thưởng</h2>
                <button class="close" onclick="closeRewardModal()">×</button>
            </div>
            <form id="rewardForm" class="reward-form" action="/webdacn_quanlyclb/Team/reward" method="POST">
                <input type="hidden" id="rewardUserId" name="user_id">
                <input type="hidden" id="rewardTeamId" name="team_id" value="<?= htmlspecialchars($team['id']) ?>">
                <label for="rewardReason"><i class="fas fa-clipboard-list me-2"></i>Lý Do Thưởng:</label>
                <textarea id="rewardReason" name="reason" rows="4" placeholder="Nhập lý do thưởng..." required></textarea>
                <label for="rewardSeverity"><i class="fas fa-trophy me-2"></i>Mức Độ:</label>
                <select id="rewardSeverity" name="severity" required>
                    <option value="">-- Chọn --</option>
                    <option value="temporary">Tạm (+5 điểm)</option>
                    <option value="good">Tốt (+10 điểm)</option>
                    <option value="excellent">Xuất sắc (+15 điểm)</option>
                </select>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeRewardModal()">Hủy</button>
                    <button type="submit" class="btn btn-reward">Xác Nhận</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentMember = null;
    function escapeHTML(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function(m) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; });
    }

    function showMemberDetails(member) {
        currentMember = member;
        const modal = document.getElementById('memberModal');
        const list = document.getElementById('memberDetailsList');
        const avatar = document.getElementById('memberAvatar');
        const name = document.getElementById('memberModalName');
        const roles = {'admin':'Quản trị viên','staff':'Chủ nhiệm CLB','user':'Thành viên'};

        if (member.avatar) avatar.innerHTML = `<img src="/webdacn_quanlyclb/${escapeHTML(member.avatar)}" class="modal-avatar">`;
        else avatar.innerHTML = `<div class="modal-avatar-placeholder">${(member.fullname||'U').charAt(0).toUpperCase()}</div>`;
        
        name.textContent = member.fullname || 'Chưa có tên';
        
        let html = `
            <li><span class="info-label">Username</span><span class="info-value">${escapeHTML(member.username)}</span></li>
            <li><span class="info-label">Vai trò</span><span class="info-value">${escapeHTML(roles[member.role])}</span></li>
            <li><span class="info-label">Email</span><span class="info-value">${escapeHTML(member.email)}</span></li>
            <li><span class="info-label">SĐT</span><span class="info-value">${escapeHTML(member.phone||'--')}</span></li>
            <li><span class="info-label">Điểm</span><span class="info-value"><span class="badge ${member.point>=100?'bg-success':'bg-warning text-dark'}">${member.point||0} điểm</span></span></li>
            <li><span class="info-label">Trạng thái</span><span class="info-value"><span class="badge ${member.status==='active'?'bg-success':'bg-danger'}">${member.status==='active'?'Hoạt động':'Vô hiệu hóa'}</span></span></li>
        `;
        if (member.status === 'disabled') {
            html += `<li class="full-width"><span class="info-label">Lý do:</span><span class="info-value text-danger">${escapeHTML(member.disable_reason)} (${new Date(member.disabled_at).toLocaleString()})</span></li>`;
        }
        list.innerHTML = html;
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('open'), 10);
    }

    function showPunishForm() { if(!currentMember)return; document.getElementById('punishUserId').value=currentMember.id; closeModal(); const m=document.getElementById('punishModal'); m.style.display='flex'; setTimeout(()=>m.classList.add('open'),10); }
    function showRewardForm() { if(!currentMember)return; document.getElementById('rewardUserId').value=currentMember.id; closeModal(); const m=document.getElementById('rewardModal'); m.style.display='flex'; setTimeout(()=>m.classList.add('open'),10); }
    function closeModal() { const m=document.getElementById('memberModal'); m.classList.remove('open'); setTimeout(()=>m.style.display='none',300); }
    function closePunishModal() { const m=document.getElementById('punishModal'); m.classList.remove('open'); setTimeout(()=>m.style.display='none',300); }
    function closeRewardModal() { const m=document.getElementById('rewardModal'); m.classList.remove('open'); setTimeout(()=>m.style.display='none',300); }
    
    window.onclick = function(e) {
        if(e.target.classList.contains('modal')) {
            if(document.getElementById('memberModal').classList.contains('open')) closeModal();
            if(document.getElementById('punishModal').classList.contains('open')) closePunishModal();
            if(document.getElementById('rewardModal').classList.contains('open')) closeRewardModal();
        }
    }
</script>

<?php include 'app/views/shares/footer.php'; ?>