<div class="breadcrumb">财务结算 / 费率分组</div>
<div class="page-header">
    <h2>费率分组</h2>
    <div>
        <a href="<?php echo url('admin/finance/flow'); ?>" class="btn btn-outline">资金流水</a>
        <a href="<?php echo url('admin/finance/settle'); ?>" class="btn" style="margin-left: 8px;">结算打款</a>
    </div>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">新增/编辑费率分组</h3>
    <form id="rateForm" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
        <input type="hidden" name="id" id="rateId" value="0">
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">分组名称</label>
            <input type="text" name="name" id="rateName" required style="width: 160px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">手续费率（0-1）</label>
            <input type="number" name="rate" id="rateValue" value="0.0200" step="0.0001" min="0" max="1" style="width: 130px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">封顶费率</label>
            <input type="number" name="max_fee" id="rateMaxFee" value="50.00" step="0.01" min="0" style="width: 120px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">成本费率（0-1）</label>
            <input type="number" name="cost_rate" id="rateCost" value="0.0060" step="0.0001" min="0" max="1" style="width: 130px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">默认分组</label>
            <select name="is_default" id="rateDefault" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <option value="0">否</option>
                <option value="1">是</option>
            </select>
        </div>
        <button type="submit" class="btn" id="saveBtn">保存</button>
        <button type="button" class="btn btn-outline" id="resetBtn" style="display: none;">取消</button>
    </form>
</div>

<div class="card">
    <table>
        <tr>
            <th>ID</th>
            <th>分组名称</th>
            <th>手续费率</th>
            <th>封顶费率</th>
            <th>成本费率</th>
            <th>默认</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="7" style="text-align: center; color: #64748B; padding: 40px;">暂无费率分组</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['name']); ?></td>
            <td><?php echo ($item['rate'] * 100); ?>%</td>
            <td>¥ <?php echo $item['max_fee']; ?></td>
            <td><?php echo ($item['cost_rate'] * 100); ?>%</td>
            <td><?php echo $item['is_default'] ? '<span class="tag tag-green">默认</span>' : '<span class="tag">-</span>'; ?></td>
            <td>
                <a href="javascript:;" class="btn btn-sm btn-outline" onclick="editRate(<?php echo $item['id']; ?>, '<?php echo h(addslashes($item['name'])); ?>', '<?php echo $item['rate']; ?>', '<?php echo $item['max_fee']; ?>', '<?php echo $item['cost_rate']; ?>', <?php echo $item['is_default']; ?>)">编辑</a>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteRate(<?php echo $item['id']; ?>)">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<script>
const form = document.getElementById('rateForm');
const saveBtn = document.getElementById('saveBtn');
const resetBtn = document.getElementById('resetBtn');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    saveBtn.disabled = true;
    saveBtn.textContent = '保存中...';
    const formData = new FormData(form);
    try {
        const res = await fetch('<?php echo url('admin/finance/rateSave'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = '保存';
    }
});

function editRate(id, name, rate, maxFee, costRate, isDefault) {
    document.getElementById('rateId').value = id;
    document.getElementById('rateName').value = name;
    document.getElementById('rateValue').value = rate;
    document.getElementById('rateMaxFee').value = maxFee;
    document.getElementById('rateCost').value = costRate;
    document.getElementById('rateDefault').value = isDefault;
    saveBtn.textContent = '更新';
    resetBtn.style.display = 'inline-block';
}

resetBtn.addEventListener('click', () => {
    form.reset();
    document.getElementById('rateId').value = 0;
    saveBtn.textContent = '保存';
    resetBtn.style.display = 'none';
});

async function deleteRate(id) {
    if (!confirm('确认删除该费率分组？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('admin/finance/rateDelete'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
