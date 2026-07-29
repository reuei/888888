<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">开发者管理</h1>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-code"/></svg>开发者申请列表
        </h3>
    </div>
    <?php if (!empty($developers)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>用户名</th>
                    <th>真实姓名</th>
                    <th>申请理由</th>
                    <th>状态</th>
                    <th>申请时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($developers as $dev): ?>
                <tr>
                    <td><?= $dev['id'] ?? '' ?></td>
                    <td><?= htmlspecialchars($dev['username'] ?? '') ?></td>
                    <td><?= htmlspecialchars($dev['real_name'] ?? '—') ?></td>
                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($dev['reason'] ?? '') ?></td>
                    <td>
                        <?php $status = $dev['status'] ?? 0; ?>
                        <span class="badge <?= $status === 1 ? 'badge-success' : ($status === -1 ? 'badge-danger' : 'badge-warning') ?>">
                            <?= $status === 1 ? '已通过' : ($status === -1 ? '已驳回' : '待审核') ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($dev['created_at'] ?? '') ?></td>
                    <td>
                        <?php if (($dev['status'] ?? 0) === 0): ?>
                        <form method="POST" action="/admin/approveDeveloper" data-ajax="true" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $dev['id'] ?? 0 ?>">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-check"/></svg> 通过
                            </button>
                        </form>
                        <a href="javascript:void(0)" class="btn btn-sm" style="background: #ff4d4f; color: #fff;" onclick="openRejectModal(<?= $dev['id'] ?? 0 ?>)">
                            <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-close"/></svg> 驳回
                        </a>
                        <?php else: ?>
                        <span style="color: #687690; font-size: 13px;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无开发者申请数据</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Reject Modal -->
<div class="announcement-modal" id="rejectModal">
    <div class="am-overlay" onclick="closeRejectModal()"></div>
    <div class="am-dialog" style="max-width: 460px;">
        <div class="am-header">
            <h3>驳回申请</h3>
            <button class="am-close" onclick="closeRejectModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="am-body">
            <form method="POST" action="/admin/rejectDeveloper" data-ajax="true" id="rejectForm">
                <input type="hidden" name="id" id="reject_id">
                <div class="form-group">
                    <label class="form-label" for="reject_reason">驳回理由</label>
                    <textarea class="form-control textarea" id="reject_reason" name="reason" rows="4" placeholder="请输入驳回理由" required></textarea>
                </div>
                <button type="submit" class="btn btn-danger btn-block">确认驳回</button>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(id) {
    document.getElementById('reject_id').value = id;
    document.getElementById('reject_reason').value = '';
    document.getElementById('rejectModal').classList.add('show');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('show');
}
</script>