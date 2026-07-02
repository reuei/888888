<div class="page-header">
    <h2>积分规则</h2>
    <button type="button" class="btn" onclick="openModal()">+ 新增规则</button>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>规则名称</th>
                <th>类型</th>
                <th>积分</th>
                <th>成长值</th>
                <th>限制</th>
                <th>排序</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['name']); ?></td>
                <td><?php echo h($item['type']); ?></td>
                <td><?php echo $item['points']; ?></td>
                <td><?php echo $item['growth_value']; ?></td>
                <td>
                    <?php
                    $limitMap = ['day' => '每日', 'week' => '每周', 'month' => '每月', 'once' => '一次性', 'total' => '累计'];
                    echo $limitMap[$item['limit_type']] ?? $item['limit_type'];
                    echo ' ' . ($item['limit_count'] ?: '不限') . ' 次';
                    ?>
                </td>
                <td><?php echo $item['sort']; ?></td>
                <td><?php echo $item['status'] ? '<span class="tag tag-green">启用</span>' : '<span class="tag tag-orange">禁用</span>'; ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline" onclick="editRule(<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES); ?>)">编辑</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteRule(<?php echo $item['id']; ?>, '<?php echo h($item['name']); ?>')">删除</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="ruleModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:200; align-items:center; justify-content:center;">
    <div style="background:#fff; width:480px; max-width:90%; border-radius:8px; padding:24px;">
        <h3 style="margin-bottom:16px;">积分规则</h3>
        <form id="ruleForm">
            <input type="hidden" name="id" id="ruleId">
            <div style="margin-bottom:12px;">
                <label>规则名称</label>
                <input type="text" name="name" id="ruleName" required style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:12px;">
                <label>规则类型</label>
                <select name="type" id="ruleType" required style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                    <option value="register">注册</option>
                    <option value="login">登录</option>
                    <option value="order">下单</option>
                    <option value="review">评价</option>
                    <option value="invite">邀请</option>
                    <option value="system">系统</option>
                </select>
            </div>
            <div style="display:flex; gap:12px; margin-bottom:12px;">
                <div style="flex:1;">
                    <label>积分</label>
                    <input type="number" name="points" id="rulePoints" value="0" required style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                </div>
                <div style="flex:1;">
                    <label>成长值</label>
                    <input type="number" name="growth_value" id="ruleGrowth" value="0" required style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                </div>
            </div>
            <div style="display:flex; gap:12px; margin-bottom:12px;">
                <div style="flex:1;">
                    <label>限制周期</label>
                    <select name="limit_type" id="ruleLimitType" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                        <option value="day">每日</option>
                        <option value="week">每周</option>
                        <option value="month">每月</option>
                        <option value="once">一次性</option>
                        <option value="total">累计</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <label>限制次数（0不限）</label>
                    <input type="number" name="limit_count" id="ruleLimitCount" value="1" min="0" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                </div>
            </div>
            <div style="display:flex; gap:12px; margin-bottom:12px;">
                <div style="flex:1;">
                    <label>排序</label>
                    <input type="number" name="sort" id="ruleSort" value="0" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                </div>
                <div style="flex:1;">
                    <label>状态</label>
                    <select name="status" id="ruleStatus" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                        <option value="1">启用</option>
                        <option value="0">禁用</option>
                    </select>
                </div>
            </div>
            <div style="text-align:right;">
                <button type="button" class="btn btn-outline" onclick="closeModal()" style="margin-right:8px;">取消</button>
                <button type="submit" class="btn">保存</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('ruleForm').reset();
    document.getElementById('ruleId').value = '';
    document.getElementById('ruleModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('ruleModal').style.display = 'none';
}
function editRule(item) {
    openModal();
    document.getElementById('ruleId').value = item.id;
    document.getElementById('ruleName').value = item.name;
    document.getElementById('ruleType').value = item.type;
    document.getElementById('rulePoints').value = item.points;
    document.getElementById('ruleGrowth').value = item.growth_value;
    document.getElementById('ruleLimitType').value = item.limit_type;
    document.getElementById('ruleLimitCount').value = item.limit_count;
    document.getElementById('ruleSort').value = item.sort;
    document.getElementById('ruleStatus').value = item.status;
}
function deleteRule(id, name) {
    if (!confirm('确认删除规则：' + name + '？')) return;
    fetch('<?php echo url("admin/points/deleteRule"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    });
}
document.getElementById('ruleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = new FormData(this);
    const params = new URLSearchParams(form);
    fetch('<?php echo url("admin/points/saveRule"); ?>', {
        method: 'POST',
        body: params
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    });
});
</script>
