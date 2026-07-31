<div class="page-header">
    <div>
        <h1 class="page-title">反馈管理</h1>
        <div class="page-subtitle">查看与处理用户提交的反馈信息。</div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-search-bar">
        <div class="search-input">
            <svg><use href="#i-search"/></svg>
            <input type="text" class="form-control" placeholder="搜索反馈标题、内容...">
        </div>
        <div class="admin-filters">
            <select class="form-control" style="max-width:140px;">
                <option value="">全部状态</option>
                <option value="0">待处理</option>
                <option value="1">已处理</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>标题</th>
                    <th>内容</th>
                    <th>提交用户</th>
                    <th>状态</th>
                    <th>提交时间</th>
                    <th style="width:180px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($feedbacks)): ?>
                    <?php foreach ($feedbacks as $fb): ?>
                    <tr>
                        <td><?= $fb['id'] ?? '' ?></td>
                        <td style="font-weight:500;"><?= htmlspecialchars($fb['title'] ?? '') ?></td>
                        <td style="max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-secondary);"><?= htmlspecialchars(mb_substr($fb['content'] ?? '', 0, 60)) ?></td>
                        <td><?= htmlspecialchars($fb['username'] ?? '') ?></td>
                        <td>
                            <?php if (($fb['status'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>已处理</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><span class="tag-dot"></span>待处理</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($fb['created_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <form method="POST" action="/admin/resolveFeedback" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认标记为已处理？');">
                                    <input type="hidden" name="id" value="<?= $fb['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-outline btn-sm">标记已处理</button>
                                </form>
                                <form method="POST" action="/admin/deleteFeedback" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该反馈？');">
                                    <input type="hidden" name="id" value="<?= $fb['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-message"/></svg></div><div class="empty-text">暂无反馈数据</div></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>